<?php

namespace App\Http\Controllers;

use App\Exceptions\InsufficientStockException;
use App\Models\Order;
use App\Models\Store;
use App\Models\TrackingHistory;
use App\Services\OrderService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\View\View;

class OrderController extends Controller
{
    public function __construct(private readonly OrderService $orderService) {}

    /**
     * Display a listing of orders with correct tab counts and eager loading.
     */
    public function index(Request $request): View|\Illuminate\Http\Response|string
    {
        // ── Tab counts (single query via groupBy for efficiency) ──────────
        $countQuery = Order::query();

        if ($storeId = $request->input('store_id')) {
            $countQuery->where('store_id', $storeId);
        }
        if ($search = $request->input('search')) {
            $countQuery->where(function ($q) use ($search) {
                $q->where('midtrans_order_id', 'like', "%{$search}%")
                    ->orWhere('customer_name', 'like', "%{$search}%");
            });
        }
        if ($date = $request->input('date')) {
            $countQuery->whereDate('created_at', $date);
        }

        $statusCounts = (clone $countQuery)
            ->selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status')
            ->toArray();

        $tabCounts = [
            'all'        => array_sum($statusCounts),
            Order::STATUS_PENDING        => $statusCounts[Order::STATUS_PENDING]        ?? 0,
            Order::STATUS_PERLU_DIPROSES => $statusCounts[Order::STATUS_PERLU_DIPROSES] ?? 0,
            Order::STATUS_PROCESSING     => $statusCounts[Order::STATUS_PROCESSING]     ?? 0,
            Order::STATUS_SHIPPING       => $statusCounts[Order::STATUS_SHIPPING]       ?? 0,
            Order::STATUS_COMPLETED      => $statusCounts[Order::STATUS_COMPLETED]      ?? 0,
            Order::STATUS_CANCELLED      => $statusCounts[Order::STATUS_CANCELLED]      ?? 0,
        ];

        // ── Main query — only load what the table needs ───────────────────
        $query = Order::with(['store', 'orderItems.product']);

        $sort = $request->input('sort', 'desc');
        if ($sort === 'asc') {
            $query->oldest();
        } elseif ($sort === 'total_high') {
            $query->orderBy('total_amount', 'desc');
        } elseif ($sort === 'total_low') {
            $query->orderBy('total_amount', 'asc');
        } else {
            $query->latest();
        }

        $tab = $request->input('tab', 'all');
        if ($tab !== 'all') {
            $query->where('status', $tab);
        }

        if ($storeId) {
            $query->where('store_id', $storeId);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('midtrans_order_id', 'like', "%{$search}%")
                    ->orWhere('customer_name', 'like', "%{$search}%")
                    ->orWhereHas('orderItems.product', function ($q2) use ($search) {
                        $q2->where('name', 'like', "%{$search}%");
                    });
            });
        }

        if ($date) {
            $query->whereDate('created_at', $date);
        }

        $perPage = (int) $request->input('per_page', 10);
        $orders  = $query->paginate($perPage)->appends($request->query());

        if ($request->ajax()) {
            return view('orders.partials._table_rows', compact('orders'))->render();
        }

        // Hanya tampilkan toko aktif yang memiliki pesanan
        $stores = Store::where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('orders.index', compact('orders', 'stores', 'tab', 'tabCounts'));
    }

    /**
     * Display the specified order.
     */
    public function show(Order $order): View
    {
        // Pre-load the transaction so syncPaymentWithMidtrans can read
        // the stored transaction_id without an extra query.
        $order->load('transaction');

        // Auto-sync with Midtrans if payment is still pending or order ID is missing
        if ($order->payment_status === 'pending' || empty($order->midtrans_order_id)) {
            $this->syncPaymentWithMidtrans($order);
        }

        $order->load([
            'store',
            'transaction',
            'orderItems.product.images',
            'trackingHistories' => fn($q) => $q->with('admin')->latest(),
        ]);

        $couriers = \App\Models\Courier::where('is_active', true)->orderBy('name')->get();

        return view('orders.show', compact('order', 'couriers'));
    }

    /**
     * Print the shipping label (Resi Pengiriman) for the order.
     */
    public function printShippingLabel(Order $order)
    {
        if (in_array($order->status, [Order::STATUS_PENDING, Order::STATUS_CANCELLED])) {
            return back()->with('error', 'Label pengiriman hanya dapat dicetak untuk pesanan yang sudah dibayar (minimal status Perlu Diproses).');
        }

        $order->load([
            'store',
            'orderItems.product',
        ]);

        return view('orders.print', compact('order'));
    }

    /**
     * Update order status.
     */
    public function updateStatus(\App\Http\Requests\OrderUpdateRequest $request, Order $order): RedirectResponse
    {
        $newStatus = $request->status;

        // Wrap in transaction with pessimistic lock to prevent race with webhooks
        try {
            $result = \Illuminate\Support\Facades\DB::transaction(function () use ($request, $order, $newStatus) {
                // Re-fetch with lock to get the true current state
                $order = Order::lockForUpdate()->find($order->id);

                // Guard: block updates on cancelled orders
                if ($order->status === Order::STATUS_CANCELLED) {
                    return back()->with('error', "Pesanan {$order->order_number} sudah dibatalkan dan tidak dapat diubah statusnya.");
                }

                // Guard: prevent backward status transitions
                $statusOrder = [
                    Order::STATUS_PENDING        => 1,
                    Order::STATUS_PERLU_DIPROSES => 2,
                    Order::STATUS_PROCESSING     => 3,
                    Order::STATUS_SHIPPING       => 4,
                    Order::STATUS_COMPLETED      => 5,
                ];
                $currentLevel = $statusOrder[$order->status] ?? 0;
                $newLevel     = $statusOrder[$newStatus] ?? 0;

                if ($newLevel <= $currentLevel) {
                    return back()->with('error', "Status tidak dapat diubah mundur dari \"{$order->status_label}\" ke status sebelumnya.");
                }

                // Deduct stock if moving to a status that requires it
                if (in_array($newStatus, [Order::STATUS_PERLU_DIPROSES, Order::STATUS_PROCESSING, Order::STATUS_SHIPPING, Order::STATUS_COMPLETED]) && !$order->is_stock_deducted) {
                    $this->orderService->processOrderStock($order);
                }

                $updateData = ['status' => $newStatus];

                // If manually verifying to "Perlu Diproses", also mark payment as settlement
                if ($newStatus === Order::STATUS_PERLU_DIPROSES && $order->payment_status !== 'settlement') {
                    $updateData['payment_status'] = 'settlement';

                    // Sync with Transaction table to ensure financial reports (revenue) are accurate
                    $transaction = \App\Models\Transaction::firstOrNew(['order_id' => $order->id]);

                    if (!$transaction->transaction_id) {
                        $transaction->transaction_id = 'MANUAL-' . strtoupper(uniqid());
                    }

                    $transaction->status = 'paid';
                    $transaction->amount = $order->total_amount;
                    $transaction->payment_date = now();
                    $transaction->payment_method = $order->payment_type ?? 'manual';
                    $transaction->notes = 'Status pembayaran diverifikasi manual oleh Admin via Detail Pesanan.';

                    $transaction->save();
                }

                $order->update($updateData);

                return null; // Success — continue after transaction
            });

            // If the transaction returned a redirect (guard failure), return it immediately
            if ($result !== null) {
                return $result;
            }

            // Re-fetch the updated order for notifications
            $order = $order->fresh();
        } catch (InsufficientStockException $e) {
            return back()->withErrors(['stock' => $e->getMessage()]);
        }

        $notes = match ($newStatus) {
            Order::STATUS_PERLU_DIPROSES => 'Pesanan telah diverifikasi dan siap diproses.',
            Order::STATUS_PROCESSING => 'Barang sedang dipersiapkan dan dikemas.',
            Order::STATUS_SHIPPING => 'Pesanan telah diserahkan ke kurir.',
            Order::STATUS_COMPLETED => 'Pesanan telah sampai dan diterima oleh pelanggan.',
            Order::STATUS_CANCELLED => 'Pesanan dibatalkan.',
            default => 'Status pesanan berhasil diperbarui oleh sistem Admin.',
        };

        TrackingHistory::create([
            'order_id' => $order->id,
            'admin_id' => auth()->id(),
            'status'   => $newStatus,
            'notes'    => $notes,
        ]);

        // Notify Admins
        $admins = \App\Models\User::where('role', 'admin')->get();
        \Illuminate\Support\Facades\Notification::send($admins, new \App\Notifications\GeneralOrderNotification([
            'order_id' => $order->id,
            'title'    => 'Status Pesanan: ' . $order->status_label . ' (' . $order->midtrans_order_id . ')',
            'message'  => "Status pesanan {$order->midtrans_order_id} diperbarui menjadi {$order->status_label}.",
            'type'     => 'status_update',
        ]));

        // Notify Customer
        if ($order->customer) {
            $order->customer->notify(new \App\Notifications\GeneralOrderNotification([
                'order_id' => $order->id,
                'title'    => 'Status Pesanan Diperbarui',
                'message'  => "Pesanan Anda ({$order->midtrans_order_id}) kini berstatus: {$order->status_label}.",
                'type'     => 'status_update',
            ]));
        }

        return back()->with('success', "Proses berhasil! Status pesanan dengan nomor {$order->order_number} telah diubah menjadi {$order->status_label}.");
    }

    /**
     * Cancel order.
     */
    public function cancel(\App\Http\Requests\OrderCancelRequest $request, Order $order): RedirectResponse
    {
        // Wrap in transaction with pessimistic lock to prevent race conditions
        $result = \Illuminate\Support\Facades\DB::transaction(function () use ($request, $order) {
            // Re-fetch with lock to get the true current state
            $order = Order::lockForUpdate()->find($order->id);

            // Guard: prevent double-cancellation or cancelling completed orders
            if ($order->status === Order::STATUS_CANCELLED) {
                return back()->with('error', "Pesanan {$order->order_number} sudah dibatalkan sebelumnya.");
            }
            if ($order->status === Order::STATUS_COMPLETED) {
                return back()->with('error', "Pesanan {$order->order_number} sudah selesai dan tidak dapat dibatalkan.");
            }

            $order->update([
                'status'        => Order::STATUS_CANCELLED,
                'cancel_reason' => $request->cancel_reason,
            ]);

            // Sync with Transaction table to ensure pending payments are failed/cancelled
            if ($order->transaction && $order->transaction->status === 'pending') {
                $order->transaction->update([
                    'status' => 'failed',
                    'notes'  => 'Dibatalkan manual oleh Admin via Detail Pesanan. Alasan: ' . $request->cancel_reason,
                ]);
            }

            TrackingHistory::create([
                'order_id' => $order->id,
                'admin_id' => auth()->id(),
                'status'   => Order::STATUS_CANCELLED,
                'notes'    => "Pesanan dibatalkan. Alasan: " . $request->cancel_reason,
            ]);

            $this->orderService->restoreOrderStock($order, 'cancellation');

            return null; // Success
        });

        if ($result !== null) {
            return $result;
        }

        // Re-fetch for notifications (outside transaction to avoid holding locks)
        $order = $order->fresh();

        // Notify Admins
        $admins = \App\Models\User::where('role', 'admin')->get();
        \Illuminate\Support\Facades\Notification::send($admins, new \App\Notifications\GeneralOrderNotification([
            'order_id' => $order->id,
            'title'    => 'Pesanan Dibatalkan: ' . $order->midtrans_order_id,
            'message'  => "Pesanan {$order->midtrans_order_id} dibatalkan: " . $request->cancel_reason,
            'type'     => 'cancel',
        ]));

        // Notify Customer
        if ($order->customer) {
            $order->customer->notify(new \App\Notifications\GeneralOrderNotification([
                'order_id' => $order->id,
                'title'    => 'Pesanan Dibatalkan',
                'message'  => "Mohon maaf, pesanan Anda ({$order->midtrans_order_id}) telah dibatalkan dengan alasan: {$request->cancel_reason}.",
                'type'     => 'cancel',
            ]));
        }

        return back()->with('success', "Pembatalan berhasil diproses. Pesanan {$order->order_number} telah dibatalkan dengan alasan yang tercatat.");
    }

    /**
     * Update shipping type.
     */
    public function updateShipping(\App\Http\Requests\OrderUpdateRequest $request, Order $order): RedirectResponse
    {
        $result = \Illuminate\Support\Facades\DB::transaction(function () use ($request, $order) {
            // Re-fetch with lock for atomicity
            $order = Order::lockForUpdate()->find($order->id);

            // Guard: prevent updates on cancelled orders
            if ($order->status === Order::STATUS_CANCELLED) {
                return back()->with('error', "Pesanan {$order->order_number} sudah dibatalkan dan tidak dapat diubah pengirimannya.");
            }

            // Guard: prevent updates on completed orders
            if ($order->status === Order::STATUS_COMPLETED) {
                return back()->with('error', "Pesanan {$order->order_number} sudah selesai dan tidak dapat diubah.");
            }

            if ($order->shipping_type === $request->shipping_type) {
                return back()->with('info', "Jenis pengiriman tidak berubah.");
            }

            $order->update(['shipping_type' => $request->shipping_type]);

            \App\Models\TrackingHistory::create([
                'order_id' => $order->id,
                'admin_id' => auth()->id(),
                'status'   => $order->status,
                'notes'    => "Jenis pengiriman diperbarui menjadi " . strtoupper($request->shipping_type) . ".",
            ]);

            return null; // Success
        });

        if ($result !== null) {
            return $result;
        }

        $order = $order->fresh();
        return back()->with('success', "Data logistik berhasil diperbarui. Jenis pengiriman pesanan {$order->order_number} kini menggunakan " . ucfirst($request->shipping_type) . ".");
    }

    /**
     * Update tracking number and set status to shipping.
     */
    public function updateTrackingNumber(\App\Http\Requests\OrderTrackingRequest $request, Order $order): RedirectResponse
    {
        $result = \Illuminate\Support\Facades\DB::transaction(function () use ($request, $order) {
            // Re-fetch with lock for atomicity
            $order = Order::lockForUpdate()->find($order->id);

            // Guard: prevent updates on cancelled orders
            if ($order->status === Order::STATUS_CANCELLED) {
                return back()->with('error', "Pesanan {$order->order_number} sudah dibatalkan dan tidak dapat di-update.");
            }

            // Guard: prevent updates on completed orders
            if ($order->status === Order::STATUS_COMPLETED) {
                return back()->with('error', "Pesanan {$order->order_number} sudah selesai, tidak perlu update resi.");
            }

            if ($order->status === Order::STATUS_SHIPPING && 
                $order->tracking_number === $request->tracking_number && 
                $order->shipping_courier === $request->shipping_courier) {
                return back()->with('info', "Resi pengiriman tidak berubah.");
            }

            $order->update([
                'tracking_number' => $request->tracking_number,
                'shipping_courier' => $request->shipping_courier,
                'status'          => Order::STATUS_SHIPPING,
            ]);

            TrackingHistory::create([
                'order_id' => $order->id,
                'admin_id' => auth()->id(),
                'status'   => Order::STATUS_SHIPPING,
                'notes'    => "Pesanan telah dikirim melalui " . $request->shipping_courier . ". Nomor resi: " . $request->tracking_number,
            ]);

            return null; // Success
        });

        if ($result !== null) {
            return $result;
        }

        $order = $order->fresh();

        // Notify Admins
        $admins = \App\Models\User::where('role', 'admin')->get();
        \Illuminate\Support\Facades\Notification::send($admins, new \App\Notifications\GeneralOrderNotification([
            'order_id' => $order->id,
            'title'    => 'Pesanan Dikirim (' . ($order->midtrans_order_id ?? $order->order_number) . ')',
            'message'  => "Pesanan telah dikirim via {$request->shipping_courier} dengan resi {$request->tracking_number}.",
            'type'     => 'shipping',
        ]));

        return back()->with('success', "Pengiriman berhasil diproses. Status pesanan diubah menjadi 'Dikirim'.");
    }

    /**
     * Manual Midtrans check.
     */
    public function checkPaymentStatus(Request $request, Order $order): RedirectResponse
    {
        // We no longer update midtrans_id on the order model because it doesn't exist.
        // We will pass the manual_id to the sync method which will handle saving it to Transactions.
        $result = $this->syncPaymentWithMidtrans($order, $request->manual_id);

        if ($result['success']) {
            return back()->with($result['type'], $result['message']);
        }

        return back()->with('error', $result['message']);
    }

    /**
     * Sync order payment status with Midtrans API.
     */
    private function syncPaymentWithMidtrans(Order $order, ?string $manualId = null): array
    {
        $serverKey = config('midtrans.server_key');
        $isProduction = config('midtrans.is_production');
        $baseUrl = $isProduction
            ? 'https://api.midtrans.com/v2/'
            : 'https://api.sandbox.midtrans.com/v2/';

        // Priority for lookup ID:
        // 1. Manual Input ID (if provided)
        // 2. Already stored Transaction ID (UUID) - Most reliable
        // 3. Already stored Midtrans Order ID (Long)
        // 4. Local Order Number (Short)
        $midtransOrderId = $manualId
            ?? ($order->transaction->transaction_id ?? null)
            ?? $order->midtrans_order_id
            ?? $order->order_number;

        try {
            $response = Http::withHeaders([
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
                'Authorization' => 'Basic ' . base64_encode($serverKey . ':'),
            ])->get($baseUrl . $midtransOrderId . '/status');

            if ($response->successful()) {
                $data = $response->json();
                $transactionStatus = $data['transaction_status'] ?? null;
                $paymentType = $data['payment_type'] ?? $order->payment_type;
                $transactionId = $data['transaction_id'] ?? null;

                // Prevent downgrading already paid orders
                if (in_array($order->payment_status, ['settlement', 'capture']) && !in_array($transactionStatus, ['settlement', 'capture', 'refund'])) {
                    return ['success' => true, 'type' => 'info', 'message' => 'Pesanan sudah lunas sebelumnya.'];
                }

                $txStatus = match (true) {
                    in_array($transactionStatus, ['settlement', 'capture']) => 'paid',
                    $transactionStatus === 'pending'                        => 'pending',
                    in_array($transactionStatus, ['deny', 'cancel', 'expire', 'failure']) => 'failed',
                    $transactionStatus === 'refund'                         => 'refund',
                    default                                                 => 'pending',
                };

                // Sync Transaction Model
                $transaction = \App\Models\Transaction::firstOrNew(['order_id' => $order->id]);

                if (!$transaction->transaction_id) {
                    $transaction->transaction_id = $transactionId ?? ('SYNC-' . strtoupper(uniqid()));
                }

                $transaction->payment_method  = $paymentType;
                $transaction->payment_details = $data;
                $transaction->amount          = $order->total_amount;
                $transaction->status          = $txStatus;
                $transaction->notes           = "Auto-sync Midtrans: {$transactionStatus}";

                if (in_array($transactionStatus, ['settlement', 'capture'])) {
                    $transaction->payment_date = isset($data['transaction_time'])
                        ? \Carbon\Carbon::parse($data['transaction_time'], 'Asia/Jakarta')
                        : now();
                } elseif (in_array($txStatus, ['failed', 'refund'])) {
                    $transaction->refunded_at = now();
                }

                $transaction->save();

                $orderUpdates = ['payment_status' => $transactionStatus];
                if (empty($order->midtrans_order_id) && isset($data['order_id'])) {
                    $orderUpdates['midtrans_order_id'] = $data['order_id'];
                }
                if (empty($order->payment_type) && $paymentType) {
                    $orderUpdates['payment_type'] = $paymentType;
                }

                if (in_array($transactionStatus, ['settlement', 'capture'])) {
                    $orderUpdates['payment_status'] = 'settlement';

                    // Only advance order status if it's currently pending or cancelled
                    if (in_array($order->status, [\App\Models\Order::STATUS_PENDING, \App\Models\Order::STATUS_CANCELLED])) {
                        $orderUpdates['status'] = \App\Models\Order::STATUS_PERLU_DIPROSES;
                    }

                    $order->update($orderUpdates);

                    // Deduct stock safely on payment confirmation
                    if (! $order->is_stock_deducted) {
                        try {
                            $this->orderService->processOrderStock($order);
                        } catch (\App\Exceptions\InsufficientStockException $e) {
                            \Illuminate\Support\Facades\Log::error('[CheckPayment] Insufficient stock for paid order #' . $order->id . ': ' . $e->getMessage());
                            $order->update([
                                'notes' => ltrim($order->notes . "\n[SISTEM] Pembayaran lunas tapi stok tidak mencukupi: " . $e->getMessage())
                            ]);
                        }
                    }

                    // Always add tracking history entry for successful payments
                    \App\Models\TrackingHistory::create([
                        'order_id' => $order->id,
                        'admin_id' => auth() ? auth()->id() : null,
                        'status'   => $order->fresh()->status,
                        'notes'    => 'Terverifikasi via Auto-Sync Midtrans.',
                    ]);

                    return ['success' => true, 'type' => 'success', 'message' => 'Pembayaran terverifikasi!'];
                } elseif (in_array($transactionStatus, ['deny', 'cancel', 'expire', 'failure', 'refund'])) {
                    if (in_array($order->status, [\App\Models\Order::STATUS_PENDING, \App\Models\Order::STATUS_PERLU_DIPROSES, \App\Models\Order::STATUS_PROCESSING, \App\Models\Order::STATUS_SHIPPING])) {
                        $orderUpdates['status']        = \App\Models\Order::STATUS_CANCELLED;
                        $orderUpdates['cancel_reason'] = 'Pembayaran ' . $transactionStatus . ' via Midtrans (Sync).';
                    }

                    $order->update($orderUpdates);

                    // Restore stock safely using OrderService
                    if ($order->is_stock_deducted) {
                        $this->orderService->restoreOrderStock($order, $transactionStatus);
                    }

                    \App\Models\TrackingHistory::create([
                        'order_id' => $order->id,
                        'admin_id' => auth() ? auth()->id() : null,
                        'status'   => $order->status,
                        'notes'    => 'Pembayaran ' . $transactionStatus . ' via Auto-Sync Midtrans.',
                    ]);

                    return ['success' => true, 'type' => 'error', 'message' => "Pembayaran dibatalkan/gagal: {$transactionStatus}"];
                }

                $order->update($orderUpdates);
                return ['success' => true, 'type' => 'info', 'message' => "Status Midtrans: {$transactionStatus}"];
            }

            return ['success' => false, 'message' => 'Pesanan tidak ditemukan di Midtrans.'];
        } catch (\Exception $e) {
            return ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
        }
    }
}
