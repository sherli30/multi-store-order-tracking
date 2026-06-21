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

use App\Services\BiteshipService;

class OrderController extends Controller
{
    public function __construct(private readonly OrderService $orderService, private readonly BiteshipService $biteshipService) {}

    /**
     * Display a listing of orders with correct tab counts and eager loading.
     */
    public function index(Request $request): View|\Illuminate\Http\Response|string
    {
        $storeId = $request->input('store_id');
        $search = $request->input('search');
        $date = $request->input('date');
        $tab = $request->input('tab', 'all');

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

        // ── 1. Auto-sync pending orders on the current page so they appear immediately
        foreach ($orders as $order) {
            if ($order->payment_status === 'pending' || empty($order->midtrans_order_id)) {
                $this->syncPaymentWithMidtrans($order);
                $order->refresh(); // Reload to reflect any status changes in the view
            }
        }

        // ── 2. Tab counts (single query via groupBy for efficiency) ──────────
        // MUST BE EXECUTED AFTER SYNC TO GET ACCURATE COUNTS
        $countQuery = Order::query();

        if ($storeId) {
            $countQuery->where('store_id', $storeId);
        }
        if ($search) {
            $countQuery->where(function ($q) use ($search) {
                $q->where('midtrans_order_id', 'like', "%{$search}%")
                    ->orWhere('customer_name', 'like', "%{$search}%");
            });
        }
        if ($date) {
            $countQuery->whereDate('created_at', $date);
        }

        $statusCounts = (clone $countQuery)
            ->selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status')
            ->toArray();

        $tabCounts = [
            'all'                              => array_sum($statusCounts),
            Order::STATUS_PENDING              => $statusCounts[Order::STATUS_PENDING]              ?? 0,
            Order::STATUS_WAITING_CONFIRMATION => $statusCounts[Order::STATUS_WAITING_CONFIRMATION] ?? 0,
            Order::STATUS_PERLU_DIPROSES       => $statusCounts[Order::STATUS_PERLU_DIPROSES]       ?? 0,
            Order::STATUS_PROCESSING           => $statusCounts[Order::STATUS_PROCESSING]           ?? 0,
            Order::STATUS_SHIPPING             => ($statusCounts[Order::STATUS_SHIPPING] ?? 0) + ($statusCounts[Order::STATUS_READY_TO_SHIP] ?? 0),
            Order::STATUS_COMPLETED            => ($statusCounts[Order::STATUS_COMPLETED] ?? 0) + ($statusCounts[Order::STATUS_DELIVERED] ?? 0),
            Order::STATUS_CANCELLED            => $statusCounts[Order::STATUS_CANCELLED]            ?? 0,
            Order::STATUS_REFUNDED             => $statusCounts[Order::STATUS_REFUNDED]             ?? 0,
        ];

        if ($request->ajax()) {
            return response()->json([
                'html' => view('orders.partials._table_rows', compact('orders'))->render(),
                'counts' => $tabCounts,
                'pagination' => (string) $orders->links('pagination::bootstrap-4') // Or whichever pagination view you use
            ]);
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
        // the transaction_id if needed, and for rendering.
        $order->load('transaction');

        $order->load([
            'store',
            'transaction',
            'orderItems.product.images',
            'trackingHistories' => fn($q) => $q->with('admin')->latest(),
            'shipmentTrackingHistories' => fn($q) => $q->latest('tracked_at'),
        ]);

        $couriers = \App\Models\Courier::where('is_active', true)->orderBy('name')->get();

        // Use locally synchronized tracking history instead of live API
        $biteshipHistory = [];
        if ($order->shipmentTrackingHistories) {
            foreach ($order->shipmentTrackingHistories as $history) {
                $biteshipHistory[] = [
                    'status' => $history->status,
                    'note' => $history->note,
                    'updated_at' => $history->tracked_at,
                ];
            }
        }

        return view('orders.show', compact('order', 'couriers', 'biteshipHistory'));
    }

    /**
     * Print the shipping label (Resi Pengiriman) for the order.
     */
    public function printShippingLabel(Order $order)
    {
        if (in_array($order->status, [Order::STATUS_PENDING, Order::STATUS_CANCELLED, Order::STATUS_REFUNDED])) {
            return back()->with('error', [
                'title' => 'Label Gagal Dicetak',
                'list' => [
                    'Label pengiriman hanya dapat dicetak untuk pesanan yang sudah dibayar (minimal status Perlu Diproses).'
                ]
            ]);
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

                // Guard: block updates on cancelled or refunded orders
                if (in_array($order->status, [Order::STATUS_CANCELLED, Order::STATUS_REFUNDED])) {
                    return back()->with('error', [
                        'title' => 'Status Terkunci',
                        'list' => [
                            "Pesanan <strong>{$order->order_number}</strong> sudah dibatalkan atau dikembalikan.",
                            'Status pesanan ini tidak dapat diubah lagi.'
                        ]
                    ]);
                }

                // Guard: cancellation must use the dedicated cancel route
                if ($newStatus === Order::STATUS_CANCELLED) {
                    return back()->with('error', [
                        'title' => 'Aksi Tidak Diizinkan',
                        'list' => [
                            'Pembatalan pesanan harus dilakukan melalui tombol <strong>Batalkan Pesanan</strong> yang tersedia.'
                        ]
                    ]);
                }

                // Guard: cannot transition to pending
                if ($newStatus === Order::STATUS_PENDING) {
                    return back()->with('error', [
                        'title' => 'Aksi Tidak Diizinkan',
                        'list' => [
                            'Status pesanan tidak dapat diubah kembali ke Belum Bayar.'
                        ]
                    ]);
                }

                // Guard: transition to perlu_diproses is only allowed from menunggu_konfirmasi_admin (Admin Confirmation)
                if ($newStatus === Order::STATUS_PERLU_DIPROSES && $order->status !== Order::STATUS_WAITING_CONFIRMATION) {
                    return back()->with('error', [
                        'title' => 'Aksi Tidak Diizinkan',
                        'list' => [
                            'Pesanan harus berstatus <strong>Menunggu Konfirmasi</strong> sebelum dapat dikonfirmasi ke Perlu Diproses.',
                            'Sistem akan otomatis mengubah status setelah pembayaran Midtrans berhasil.'
                        ]
                    ]);
                }

                // Guard: prevent backward status transitions AND forward status skipping
                $statusOrder = [
                    Order::STATUS_PENDING              => 1,
                    Order::STATUS_WAITING_CONFIRMATION => 2,
                    Order::STATUS_PERLU_DIPROSES       => 3,
                    Order::STATUS_PROCESSING           => 4,
                    Order::STATUS_READY_TO_SHIP        => 5,
                    Order::STATUS_SHIPPING             => 6,
                    Order::STATUS_DELIVERED             => 7,
                    Order::STATUS_COMPLETED            => 8,
                ];
                $currentLevel = $statusOrder[$order->status] ?? 0;
                $newLevel     = $statusOrder[$newStatus] ?? 0;

                if ($newStatus !== Order::STATUS_REFUNDED) {
                    if ($newLevel <= $currentLevel) {
                        return back()->with('error', [
                            'title' => 'Status Tidak Valid',
                            'list' => [
                                "Status tidak dapat diubah mundur dari <strong>{$order->status_label}</strong> ke status sebelumnya."
                            ]
                        ]);
                    }
                    if ($newLevel > $currentLevel + 1) {
                        return back()->with('error', [
                            'title' => 'Status Tidak Valid',
                            'list' => [
                                'Status tidak dapat dilompati.',
                                'Harap ikuti urutan proses (Belum Bayar &rarr; Konfirmasi Administrator &rarr; Perlu Diproses &rarr; Dikemas &rarr; Siap Dikirim &rarr; Dikirim &rarr; Pesanan Tiba &rarr; Selesai).'
                            ]
                        ]);
                    }
                }

                $updateData = ['status' => $newStatus];

                // Handle Manual Refunds
                if ($newStatus === Order::STATUS_REFUNDED) {
                    $transaction = $order->transaction()->first();
                    $isManual = $transaction && str_starts_with($transaction->transaction_id, 'MANUAL-');
                    
                    if (!$isManual) {
                        return back()->with('error', [
                            'title' => 'Refund Tidak Diizinkan',
                            'list' => [
                                'Pengembalian dana untuk pesanan Midtrans harus diproses melalui Dashboard Midtrans.',
                                'Aksi ini diblokir untuk mencegah ketidaksesuaian data finansial.'
                            ]
                        ]);
                    }

                    if ($order->is_stock_deducted) {
                        $this->orderService->restoreOrderStock($order, 'refund');
                    }
                    
                    if ($transaction) {
                        $transaction->update([
                            'status' => 'refund',
                            'refunded_at' => now(),
                            'notes' => 'Pengembalian dana diproses secara manual oleh Administrator.',
                        ]);
                    }
                    
                    // Exclude from revenue reports
                    $updateData['payment_status'] = 'refund';
                }

                // Guard: prevent transitioning to shipping or completed without tracking number
                if (in_array($newStatus, [Order::STATUS_SHIPPING, Order::STATUS_COMPLETED]) && empty($order->tracking_number)) {
                    return back()->with('error', [
                        'title' => 'Resi Pengiriman Kosong',
                        'list' => [
                            'Nomor resi harus diinput (melalui fitur <strong>Input Resi & Kirim</strong>) sebelum pesanan dapat diubah menjadi Dikirim atau Selesai.'
                        ]
                    ]);
                }

                // Deduct stock if moving to a status that requires it
                if (in_array($newStatus, [Order::STATUS_PERLU_DIPROSES, Order::STATUS_PROCESSING, Order::STATUS_SHIPPING, Order::STATUS_COMPLETED]) && !$order->is_stock_deducted) {
                    $this->orderService->processOrderStock($order);
                }

                // If manually verifying to "Perlu Diproses", also mark payment as settlement
                if ($newStatus === Order::STATUS_PERLU_DIPROSES && $order->payment_status !== 'settlement') {
                    $updateData['payment_status'] = 'settlement';

                    // Sync with Transaction table to ensure financial reports (revenue) are accurate
                    $transaction = \App\Models\Transaction::firstOrNew([
                        'invoice_id' => $order->invoice_id,
                        'order_id' => $order->invoice_id ? null : $order->id
                    ]);

                    if (!$transaction->transaction_id) {
                        $transaction->transaction_id = 'MANUAL-' . strtoupper(uniqid());
                    }

                    $transaction->status = 'paid';
                    $transaction->amount = $order->invoice ? $order->invoice->grand_total : $order->total_amount;
                    $transaction->payment_date = now();
                    $transaction->payment_method = $order->payment_type ?? 'manual';
                    $transaction->notes = 'Status pembayaran diverifikasi manual oleh Administrator via Detail Pesanan.';

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
            Order::STATUS_PERLU_DIPROSES => 'Pesanan telah dikonfirmasi oleh Administrator dan siap diproses oleh toko.',
            Order::STATUS_PROCESSING => 'Barang sedang dipersiapkan dan dikemas.',
            Order::STATUS_READY_TO_SHIP => 'Pesanan telah siap dikirim dan resi berhasil dibuat.',
            Order::STATUS_SHIPPING => 'Pesanan telah diserahkan ke kurir.',
            Order::STATUS_DELIVERED => 'Pesanan telah tiba di tujuan (Delivered). Menunggu konfirmasi penerimaan dari customer.',
            Order::STATUS_COMPLETED => 'Pesanan telah selesai dan dikonfirmasi diterima.',
            Order::STATUS_CANCELLED => 'Pesanan dibatalkan.',
            Order::STATUS_REFUNDED  => 'Dana telah dikembalikan kepada customer (Refund manual).',
            default => 'Status pesanan berhasil diperbarui oleh sistem Administrator.',
        };

        TrackingHistory::create([
            'order_id' => $order->id,
            'admin_id' => auth()->id(),
            'status'   => $newStatus,
            'notes'    => $notes,
        ]);

        // Notify Admins
        $admins = \App\Models\User::where('role', 'administrator')->get();
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

        return back()->with('success', [
            'title' => 'Status Pesanan Diperbarui',
            'list' => [
                "Proses berhasil! Status pesanan dengan nomor <strong>{$order->order_number}</strong> telah diubah menjadi <strong>{$order->status_label}</strong>.",
                "Status pengiriman berhasil diperbarui."
            ]
        ]);
    }

    /**
     * Cancel order.
     */
    public function cancel(\App\Http\Requests\OrderCancelRequest $request, Order $order): RedirectResponse
    {
        if ($order->status !== Order::STATUS_PENDING) {
            return back()->with('error', [
                'title' => 'Gagal Dibatalkan',
                'list' => [
                    'Pesanan hanya dapat dibatalkan secara manual jika statusnya masih Belum Bayar.'
                ]
            ]);
        }

        try {
            $order = $this->orderService->cancelOrder($order, $request->cancel_reason, auth()->id());
        } catch (\Exception $e) {
            return back()->with('error', [
                'title' => 'Gagal Dibatalkan',
                'list' => [
                    $e->getMessage()
                ]
            ]);
        }

        // Re-fetch for notifications (outside transaction to avoid holding locks)
        $order = $order->fresh();

        // Notify Admins
        $admins = \App\Models\User::where('role', 'administrator')->get();
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

        return back()->with('success', [
            'title' => 'Pesanan Dibatalkan',
            'list' => [
                "Pembatalan berhasil diproses. Pesanan <strong>{$order->order_number}</strong> telah dibatalkan dengan alasan yang tercatat."
            ]
        ]);
    }

    /**
     * Update shipping type.
     */
    public function updateShipping(\App\Http\Requests\OrderUpdateRequest $request, Order $order): RedirectResponse
    {
        $result = \Illuminate\Support\Facades\DB::transaction(function () use ($request, $order) {
            // Re-fetch with lock for atomicity
            $order = Order::lockForUpdate()->find($order->id);

            // Guard: prevent updates on cancelled or refunded orders
            if (in_array($order->status, [Order::STATUS_CANCELLED, Order::STATUS_REFUNDED])) {
                return back()->with('error', [
                    'title' => 'Pengiriman Terkunci',
                    'list' => [
                        "Pesanan <strong>{$order->order_number}</strong> sudah dibatalkan atau dikembalikan.",
                        'Data pengiriman tidak dapat diubah lagi.'
                    ]
                ]);
            }

            // Guard: prevent updates on completed orders
            if ($order->status === Order::STATUS_COMPLETED) {
                return back()->with('error', [
                    'title' => 'Pesanan Selesai',
                    'list' => [
                        "Pesanan <strong>{$order->order_number}</strong> sudah selesai dan tidak dapat diubah."
                    ]
                ]);
            }

            if ($order->shipping_type === $request->shipping_type) {
                return back()->with('info', [
                    'title' => 'Data Sudah Sesuai',
                    'list' => [
                        'Jenis pengiriman tidak berubah.'
                    ]
                ]);
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
        return back()->with('success', [
            'title' => 'Pengiriman Diperbarui',
            'list' => [
                "Data logistik berhasil diperbarui. Jenis pengiriman pesanan <strong>{$order->order_number}</strong> kini menggunakan <strong>" . ucfirst($request->shipping_type) . "</strong>.",
                "Informasi pengiriman berhasil diperbarui."
            ]
        ]);
    }

    /**
     * Update tracking number and set status to shipping.
     */
    public function updateTrackingNumber(\App\Http\Requests\OrderTrackingRequest $request, Order $order): RedirectResponse
    {
        $result = \Illuminate\Support\Facades\DB::transaction(function () use ($request, $order) {
            // Re-fetch with lock for atomicity
            $order = Order::lockForUpdate()->find($order->id);

            // Guard: Tracking number can only be inputted during processing or shipping states
            // This prevents AWB assignment for unpaid (pending) or unverified (perlu_diproses) orders.
            if (!in_array($order->status, [Order::STATUS_PROCESSING, Order::STATUS_SHIPPING])) {
                return back()->with('error', [
                    'title' => 'Resi Ditolak',
                    'list' => [
                        'Resi pengiriman hanya dapat diinput untuk pesanan yang sedang diproses (Dikemas) atau sudah dikirim.'
                    ]
                ]);
            }

            if ($order->status === Order::STATUS_SHIPPING && 
                $order->tracking_number === $request->tracking_number) {
                return back()->with('info', [
                    'title' => 'Data Sudah Sesuai',
                    'list' => [
                        'Resi pengiriman tidak berubah.'
                    ]
                ]);
            }

            $order->update([
                'tracking_number' => $request->tracking_number,
                'status'          => Order::STATUS_SHIPPING,
            ]);

            TrackingHistory::create([
                'order_id' => $order->id,
                'admin_id' => auth()->id(),
                'status'   => Order::STATUS_SHIPPING,
                'notes'    => "Pesanan telah dikirim melalui " . ($order->shipping_courier ?? 'Kurir') . ". Nomor resi: " . $request->tracking_number,
            ]);

            return null; // Success
        });

        if ($result !== null) {
            return $result;
        }

        $order = $order->fresh();

        // Notify Admins
        $admins = \App\Models\User::where('role', 'administrator')->get();
        \Illuminate\Support\Facades\Notification::send($admins, new \App\Notifications\GeneralOrderNotification([
            'order_id' => $order->id,
            'title'    => 'Pesanan Dikirim (' . ($order->midtrans_order_id ?? $order->order_number) . ')',
            'message'  => "Pesanan telah dikirim via " . ($order->shipping_courier ?? 'Kurir') . " dengan resi {$request->tracking_number}.",
            'type'     => 'shipping',
            'status'   => $order->status,
        ]));

        // Notify Customer
        if ($order->customer) {
            $order->customer->notify(new \App\Notifications\GeneralOrderNotification([
                'order_id' => $order->id,
                'title'    => 'Pesanan Anda Dikirim',
                'message'  => "Pesanan Anda (" . ($order->midtrans_order_id ?? $order->order_number) . ") telah dikirim via " . ($order->shipping_courier ?? 'Kurir') . " dengan resi {$request->tracking_number}.",
                'type'     => 'shipping',
                'status'   => $order->status,
            ]));
        }

        return back()->with('success', [
            'title' => 'Pengiriman Diproses',
            'list' => [
                "Nomor resi berhasil diinput. Status pesanan diubah menjadi <strong>Dikirim</strong>.",
                "Status pengiriman berhasil diperbarui."
            ]
        ]);
    }

    /**
     * Generate AWB through Biteship API.
     */
    public function generateResi(Request $request, Order $order): RedirectResponse
    {
        // Must be in Processing status
        if ($order->status !== Order::STATUS_PROCESSING) {
            return back()->with('error', [
                'title' => 'Resi Ditolak',
                'list' => [
                    'Resi otomatis hanya dapat digenerate untuk pesanan yang sedang diproses (Dikemas).'
                ]
            ]);
        }

        try {
            $response = $this->biteshipService->createShipment($order);

            $order->update([
                'tracking_number' => $response['id'] ?? null, // Often Biteship uses their ID or waybill as tracking initially
                'shipment_id' => $response['id'] ?? null,
                'shipment_status' => $response['status'] ?? 'allocated',
                'courier_name' => $response['courier']['company'] ?? null,
                'courier_service' => $response['courier']['type'] ?? null,
                'shipment_created_at' => now(),
                'status' => Order::STATUS_READY_TO_SHIP,
            ]);
            
            // If the waybill_id is available immediately, use it
            if (!empty($response['courier']['waybill_id'])) {
                $order->update(['tracking_number' => $response['courier']['waybill_id']]);
            }

            TrackingHistory::create([
                'order_id' => $order->id,
                'admin_id' => auth()->id(),
                'status'   => Order::STATUS_READY_TO_SHIP,
                'notes'    => "Resi otomatis berhasil dibuat via Biteship. " . ($order->tracking_number ? "AWB: {$order->tracking_number}" : ""),
            ]);

            $order = $order->fresh();
            
            // Notify Admins
            $admins = \App\Models\User::where('role', 'administrator')->get();
            \Illuminate\Support\Facades\Notification::send($admins, new \App\Notifications\GeneralOrderNotification([
                'order_id' => $order->id,
                'title'    => 'Resi Otomatis Berhasil (' . ($order->midtrans_order_id ?? $order->order_number) . ')',
                'message'  => "Resi otomatis berhasil dibuat. Status pesanan diubah menjadi Siap Dikirim.",
                'type'     => 'status_update',
                'status'   => $order->status,
            ]));

            // Notify Customer
            if ($order->customer) {
                $order->customer->notify(new \App\Notifications\GeneralOrderNotification([
                    'order_id' => $order->id,
                    'title'    => 'Pesanan Siap Dikirim',
                    'message'  => "Pesanan Anda (" . ($order->midtrans_order_id ?? $order->order_number) . ") sedang dipersiapkan untuk dikirim oleh kurir.",
                    'type'     => 'status_update',
                    'status'   => $order->status,
                ]));
            }

            return back()->with('success', [
                'title' => 'Resi Berhasil Digenerate',
                'list' => [
                    "Resi Biteship berhasil dibuat. Status pesanan diubah menjadi <strong>Siap Dikirim</strong>."
                ]
            ]);

        } catch (\Exception $e) {
            return back()->with('error', [
                'title' => 'Gagal Generate Resi',
                'list' => [
                    'Terjadi kesalahan saat menghubungi Biteship: ' . $e->getMessage()
                ]
            ]);
        }
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
            ?? $order->invoice?->midtrans_order_id
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

                $txStatus = match (true) {
                    in_array($transactionStatus, ['settlement', 'capture']) => 'paid',
                    $transactionStatus === 'pending'                        => 'pending',
                    in_array($transactionStatus, ['deny', 'cancel', 'expire', 'failure']) => 'failed',
                    $transactionStatus === 'refund'                         => 'refund',
                    default                                                 => 'pending',
                };

                return \Illuminate\Support\Facades\DB::transaction(function () use ($order, $transactionStatus, $txStatus, $transactionId, $paymentType, $data) {
                    $order = \App\Models\Order::lockForUpdate()->find($order->id);

                    // Prevent downgrading already paid orders
                    if (in_array($order->payment_status, ['settlement', 'capture']) && !in_array($transactionStatus, ['settlement', 'capture', 'refund'])) {
                        return ['success' => true, 'type' => 'info', 'message' => 'Pesanan sudah lunas sebelumnya.'];
                    }

                    // Determine if order belongs to an invoice
                    $invoice = $order->invoice_id ? \App\Models\Invoice::lockForUpdate()->find($order->invoice_id) : null;

                    // Sync Transaction Model
                    $transaction = \App\Models\Transaction::firstOrNew([
                        'invoice_id' => $order->invoice_id,
                        'order_id' => $invoice ? null : $order->id
                    ]);

                    if (!$transaction->transaction_id) {
                        $transaction->transaction_id = $transactionId ?? ('SYNC-' . strtoupper(uniqid()));
                    }

                    $transaction->payment_method  = $paymentType;
                    $transaction->payment_details = $data;
                    $transaction->amount          = $invoice ? $invoice->grand_total : $order->total_amount;
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

                    // Update Invoice if exists
                    if ($invoice) {
                        $invoiceUpdates = ['payment_status' => $transactionStatus];
                        if (empty($invoice->payment_type) && $paymentType) {
                            $invoiceUpdates['payment_type'] = $paymentType;
                        }
                        $invoice->update($invoiceUpdates);
                    }

                    // Cascade to all child orders if it's an invoice, otherwise just this order
                    $ordersToUpdate = $invoice ? $invoice->orders()->get() : collect([$order]);

                    foreach ($ordersToUpdate as $childOrder) {
                        $orderUpdates = ['payment_status' => $transactionStatus];
                        if (empty($childOrder->midtrans_order_id) && isset($data['order_id']) && !$invoice) {
                            $orderUpdates['midtrans_order_id'] = $data['order_id'];
                        }
                        if (empty($childOrder->payment_type) && $paymentType) {
                            $orderUpdates['payment_type'] = $paymentType;
                        }

                        if (in_array($transactionStatus, ['settlement', 'capture'])) {
                            $orderUpdates['payment_status'] = 'settlement';

                            if ($childOrder->status === \App\Models\Order::STATUS_PENDING) {
                                $orderUpdates['status'] = \App\Models\Order::STATUS_WAITING_CONFIRMATION;
                            }

                            $childOrder->update($orderUpdates);

                            if (! $childOrder->is_stock_deducted) {
                                try {
                                    $this->orderService->processOrderStock($childOrder);
                                } catch (\App\Exceptions\InsufficientStockException $e) {
                                    $childOrder->update([
                                        'notes' => ltrim($childOrder->notes . "\n[SISTEM] Pembayaran lunas tapi stok tidak mencukupi: " . $e->getMessage())
                                    ]);
                                }
                            }

                            if (isset($orderUpdates['status']) && $orderUpdates['status'] === \App\Models\Order::STATUS_WAITING_CONFIRMATION) {
                                if (!$childOrder->trackingHistories()->where('status', \App\Models\Order::STATUS_WAITING_CONFIRMATION)->exists()) {
                                    \App\Models\TrackingHistory::create([
                                        'order_id' => $childOrder->id,
                                        'admin_id' => auth() ? auth()->id() : null,
                                        'status'   => \App\Models\Order::STATUS_WAITING_CONFIRMATION,
                                        'notes'    => 'Pembayaran telah berhasil (Settlement). Menunggu konfirmasi admin.',
                                    ]);
                                }
                            }
                        } elseif (in_array($transactionStatus, ['deny', 'cancel', 'expire', 'failure', 'refund'])) {
                            if (in_array($childOrder->status, [\App\Models\Order::STATUS_PENDING, \App\Models\Order::STATUS_WAITING_CONFIRMATION, \App\Models\Order::STATUS_PERLU_DIPROSES, \App\Models\Order::STATUS_PROCESSING, \App\Models\Order::STATUS_SHIPPING])) {
                                if ($transactionStatus === 'refund') {
                                    $orderUpdates['status'] = \App\Models\Order::STATUS_REFUNDED;
                                } else {
                                    $orderUpdates['status'] = \App\Models\Order::STATUS_CANCELLED;
                                }
                                $orderUpdates['cancel_reason'] = 'Pembayaran ' . $transactionStatus . ' via Midtrans (Sync).';
                            }

                            $childOrder->update($orderUpdates);

                            if ($childOrder->is_stock_deducted) {
                                $this->orderService->restoreOrderStock($childOrder, $transactionStatus);
                            }

                            \App\Models\TrackingHistory::create([
                                'order_id' => $childOrder->id,
                                'admin_id' => auth() ? auth()->id() : null,
                                'status'   => $childOrder->fresh()->status,
                                'notes'    => 'Pembayaran ' . $transactionStatus . ' via Auto-Sync Midtrans.',
                            ]);
                        } else {
                            $childOrder->update($orderUpdates);
                        }
                    }

                    if (in_array($transactionStatus, ['settlement', 'capture'])) {
                        return ['success' => true, 'type' => 'success', 'message' => 'Pembayaran terverifikasi!'];
                    } elseif (in_array($transactionStatus, ['deny', 'cancel', 'expire', 'failure', 'refund'])) {
                        return ['success' => true, 'type' => 'error', 'message' => "Pembayaran dibatalkan/gagal: {$transactionStatus}"];
                    }
                    
                    return ['success' => true, 'type' => 'info', 'message' => "Status Midtrans: {$transactionStatus}"];
                });
            }

            return ['success' => false, 'message' => 'Pesanan tidak ditemukan di Midtrans.'];
        } catch (\Exception $e) {
            return ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
        }
    }

    public function handleReturn(\App\Http\Requests\HandleReturnRequest $request, Order $order)
    {

        if ($order->return_status !== 'requested') {
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Pesanan ini tidak memiliki pengajuan pengembalian yang sedang menunggu persetujuan.'
                ], 400);
            }
            return back()->with('error', [
                'title' => 'Aksi Ditolak',
                'list' => ['Pesanan ini tidak memiliki pengajuan pengembalian yang sedang menunggu persetujuan.']
            ]);
        }

        $order->update([
            'return_status' => $request->return_status,
        ]);

        $statusText = $request->return_status === 'approved' ? 'disetujui' : 'ditolak';
        
        \App\Models\TrackingHistory::create([
            'order_id' => $order->id,
            'admin_id' => auth()->id(),
            'status' => $order->status,
            'notes' => "Pengajuan pengembalian {$statusText} oleh Administrator.",
        ]);

        $order = $order->fresh();

        // Notify Customer
        if ($order->customer) {
            $order->customer->notify(new \App\Notifications\GeneralOrderNotification([
                'order_id' => $order->id,
                'title'    => 'Status Pengembalian Diperbarui',
                'message'  => "Pengajuan pengembalian untuk pesanan Anda (" . ($order->midtrans_order_id ?? $order->order_number) . ") telah {$statusText}.",
                'type'     => 'status_update',
                'status'   => $order->status,
            ]));
        }

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => "Pengajuan pengembalian telah {$statusText}.",
            ]);
        }

        return back()->with('success', [
            'title' => 'Pengajuan Diproses',
            'list' => ["Pengajuan pengembalian telah {$statusText}."]
        ]);
    }
}
