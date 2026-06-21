<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Services\OrderService;
use App\Services\StatusService;

class PaymentController extends Controller
{
    private $serverKey;
    private $isProduction;
    private $snapUrl;

    private $orderService;

    public function __construct(OrderService $orderService)
    {
        $this->serverKey = config('midtrans.server_key');
        $this->isProduction = config('midtrans.is_production');
        $this->snapUrl = $this->isProduction
            ? 'https://app.midtrans.com/snap/v1/transactions'
            : 'https://app.sandbox.midtrans.com/snap/v1/transactions';
        $this->orderService = $orderService;
    }

    public function getSnapToken(Request $request)
    {
        try {
            // Load order with all necessary relationships and ensure ownership
            $order = Order::with(['orderItems.product', 'customer'])
                ->where('user_id', auth()->id())
                ->findOrFail($request->order_id);

            $itemDetails = [];
            $itemsSum = 0;

            // 1. Loop through order items dynamically
            foreach ($order->orderItems as $item) {
                $price = (int) $item->price;
                $quantity = (int) $item->quantity;

                $productName = $item->product->name ?? 'Produk';
                $fullName = $productName;

                // Limit name length to 50 characters as per Midtrans best practices
                $finalName = strlen($fullName) > 50 ? substr($fullName, 0, 47) . '...' : $fullName;

                $itemDetails[] = [
                    'id' => (string) ($item->product_id ?? $item->id),
                    'price' => $price,
                    'quantity' => $quantity,
                    'name' => $finalName,
                ];

                $itemsSum += ($price * $quantity);
            }

            // Add Shipping Cost as an item if exists
            if ($order->shipping_cost > 0) {
                $shippingCost = (int) $order->shipping_cost;
                $itemDetails[] = [
                    'id' => 'SHIPPING',
                    'price' => $shippingCost,
                    'quantity' => 1,
                    'name' => 'Ongkos Kirim',
                ];
                $itemsSum += $shippingCost;
            }

            // Add Packing Cost as an item if exists
            if ($order->packing_cost > 0) {
                $packingCost = (int) $order->packing_cost;
                $itemDetails[] = [
                    'id' => 'PACKING',
                    'price' => $packingCost,
                    'quantity' => 1,
                    'name' => 'Biaya Packing',
                ];
                $itemsSum += $packingCost;
            }

            // 2. Resolve customer contact data from real account data
            // Priority: order.customer_phone > customer.phone (from user account)
            $customerPhone = $order->customer_phone
                ?? $order->customer?->phone
                ?? null;

            // Resolve email: priority = order.customer_email > customer.email
            $customerEmail = $order->customer_email
                ?? $order->customer?->email
                ?? null;

            // 3. Validate required address fields before sending to Midtrans
            $missingFields = [];
            if (empty($order->shipping_address)) $missingFields[] = 'shipping_address';
            if (empty($order->city))             $missingFields[] = 'city';
            if (empty($order->postal_code))      $missingFields[] = 'postal_code';
            if (empty($customerPhone))           $missingFields[] = 'customer_phone';

            if (!empty($missingFields)) {
                Log::warning('PaymentController: Data alamat tidak lengkap untuk order #' . $order->midtrans_order_id, [
                    'missing_fields' => $missingFields,
                ]);
                return response()->json([
                    'status'  => 'error',
                    'message' => 'Data alamat pengiriman tidak lengkap: ' . implode(', ', $missingFields) . '. Mohon lengkapi profil Anda.',
                ], 422);
            }

            // 3. Prepare Addresses — semua field diambil dari data order asli
            $shippingAddress = [
                'first_name'  => $order->customer_name,
                'phone'       => $customerPhone,
                'address'     => $order->shipping_address,
                'city'        => $order->city,
                'postal_code' => $order->postal_code,
                'country_code' => 'IDN',
            ];

            $billingAddress = $shippingAddress;

            // 4. Combine all params
            $midtransOrderId = $order->midtrans_order_id;
            $params = [
                'transaction_details' => [
                    'order_id'     => $midtransOrderId,
                    'gross_amount' => $itemsSum,
                ],
                'item_details'     => $itemDetails,
                'customer_details' => [
                    'first_name'       => $order->customer_name,
                    'email'            => $customerEmail,
                    'phone'            => $customerPhone,
                    'shipping_address' => $shippingAddress,
                    'billing_address'  => $billingAddress,
                ],
                // Tambahkan Callbacks agar WebView bisa menangkap redirect dengan benar
                'callbacks' => [
                    'finish' => 'https://ayam-bebek.mobi/payment/finish',
                    'unfinish' => 'https://ayam-bebek.mobi/payment/unfinish',
                    'error' => 'https://ayam-bebek.mobi/payment/error',
                ]
            ];

            // Log payload for debugging
            Log::info('Midtrans Request Payload:', $params);

            // Optional: Specific payment method selection
            if ($request->payment_method && $request->payment_method != 'midtrans') {
                $params['enabled_payments'] = [$request->payment_method];
            }

            $response = Http::withHeaders([
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
                'Authorization' => 'Basic ' . base64_encode($this->serverKey . ':'),
            ])->post($this->snapUrl, $params);

            if ($response->failed()) {
                Log::error('Midtrans API Error:', $response->json());
                return response()->json([
                    'status' => 'error',
                    'message' => $response->json()['error_messages'][0] ?? 'Midtrans API Error'
                ], 400);
            }

            $snapToken = $response->json()['token'];

            // Lock order and update snap_token atomically to prevent races
            DB::transaction(function () use ($order, $snapToken) {
                $order = Order::lockForUpdate()->find($order->id);
                $order->update(['snap_token' => $snapToken]);
            });

            return response()->json([
                'status' => 'success',
                'message' => 'Token pembayaran berhasil dibuat. Silakan lanjutkan pembayaran melalui gerbang Midtrans.',
                'snap_token' => $snapToken
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Pesanan tidak ditemukan atau Anda tidak memiliki akses.',
            ], 404);
        } catch (\Exception $e) {
            Log::error('PaymentController Exception:', ['message' => $e->getMessage()]);
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan pada server. Silakan coba lagi.'
            ], 500);
        }
    }


    public function checkStatus($id)
    {
        try {
            $order = Order::where('user_id', auth()->id())->findOrFail($id);
            $midtransOrderId = $order->invoice?->midtrans_order_id ?? $order->midtrans_order_id ?? $order->order_number;

            $response = Http::timeout(15)->withHeaders([
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
                'Authorization' => 'Basic ' . base64_encode($this->serverKey . ':'),
            ])->get(($this->isProduction ? 'https://api.midtrans.com/v2/' : 'https://api.sandbox.midtrans.com/v2/') . $midtransOrderId . "/status");

            if ($response->successful()) {
                $data = $response->json();
                $transactionStatus = $data['transaction_status'] ?? null;
                $paymentType = $data['payment_type'] ?? $order->payment_type;
                $transactionId = $data['transaction_id'] ?? null;

                $txStatus = StatusService::midtransToTransactionStatus($transactionStatus);

                $result = DB::transaction(function () use ($order, $transactionId, $paymentType, $data, $txStatus, $transactionStatus) {
                    // Lock FIRST before any checks
                    $order = Order::lockForUpdate()->find($order->id);

                    // THEN check: Prevent downgrading already paid orders (under lock)
                    if (in_array($order->payment_status, ['settlement', 'capture']) && !in_array($transactionStatus, ['settlement', 'capture', 'refund'])) {
                        return ['status' => 'info', 'message' => 'Pesanan sudah lunas sebelumnya.', 'order' => $order];
                    }

                    $transaction = Transaction::firstOrNew([
                        'invoice_id' => $order->invoice_id,
                        'order_id' => $order->invoice_id ? null : $order->id
                    ]);

                    if (!$transaction->transaction_id) {
                        $transaction->transaction_id = $transactionId ?? ('SYNC-' . strtoupper(uniqid()));
                    }

                    $transaction->payment_method  = $paymentType;
                    $transaction->payment_details = $data;
                    $transaction->amount          = $order->invoice ? $order->invoice->grand_total : $order->total_amount;
                    $transaction->status          = $txStatus;
                    $transaction->notes           = "Auto-sync Midtrans: {$transactionStatus}";

                    if (in_array($transactionStatus, ['settlement', 'capture'])) {
                        $transaction->payment_date = isset($data['transaction_time'])
                            ? Carbon::parse($data['transaction_time'], 'Asia/Jakarta')
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

                    $oldStatus = $order->status;
                    $oldPaymentStatus = $order->payment_status;

                    if (in_array($transactionStatus, ['settlement', 'capture'])) {
                        $orderUpdates['payment_status'] = 'settlement';

                        if ($order->status === Order::STATUS_PENDING) {
                            $orderUpdates['status'] = Order::STATUS_WAITING_CONFIRMATION;
                        }

                        $order->update($orderUpdates);

                        // Deduct stock safely to prevent negative inventory
                        $stockError = null;
                        if (! $order->is_stock_deducted) {
                            try {
                                app(\App\Services\OrderService::class)->processOrderStock($order);
                            } catch (\App\Exceptions\InsufficientStockException $e) {
                                Log::error('[API CheckStatus] Insufficient stock for paid order #' . $order->id . ': ' . $e->getMessage());
                                $stockError = $e->getMessage();
                                $order->update([
                                    'notes' => ltrim($order->notes . "\n[SISTEM] Pembayaran lunas tapi stok tidak mencukupi: " . $e->getMessage())
                                ]);
                            }
                        }

                        if ($oldStatus !== $order->status || $oldPaymentStatus !== $order->payment_status) {
                            \App\Models\TrackingHistory::create([
                                'order_id' => $order->id,
                                'admin_id' => null,
                                'status'   => $order->fresh()->status,
                                'notes'    => 'Pembayaran diverifikasi oleh customer via API.',
                                'payment_method' => $paymentType,
                                'metadata' => ['api_sync' => true],
                            ]);
                        }

                        return ['status' => 'success', 'stock_error' => $stockError, 'order' => $order];
                    } elseif (in_array($transactionStatus, ['deny', 'cancel', 'expire', 'failure', 'refund'])) {
                        $allowedStatuses = [Order::STATUS_PENDING, Order::STATUS_WAITING_CONFIRMATION, Order::STATUS_PERLU_DIPROSES, Order::STATUS_PROCESSING, Order::STATUS_SHIPPING];
                        if ($transactionStatus === 'refund') {
                            $allowedStatuses[] = Order::STATUS_COMPLETED;
                        }

                        if (in_array($order->status, $allowedStatuses)) {
                            if ($transactionStatus === 'refund') {
                                $orderUpdates['status'] = Order::STATUS_REFUNDED;
                            } else {
                                $orderUpdates['status'] = Order::STATUS_CANCELLED;
                            }
                            $orderUpdates['cancel_reason'] = 'Pembayaran ' . $transactionStatus . ' via API Sync.';
                            
                            $order->update($orderUpdates);

                            // Restore stock safely using OrderService
                            if ($order->is_stock_deducted) {
                                app(\App\Services\OrderService::class)->restoreOrderStock($order, $transactionStatus);
                            }
                        } else {
                            $order->update($orderUpdates);
                        }

                        if ($oldStatus !== $order->status || $oldPaymentStatus !== $order->payment_status) {
                            \App\Models\TrackingHistory::create([
                                'order_id' => $order->id,
                                'admin_id' => null,
                                'status'   => $order->fresh()->status,
                                'notes'    => 'Pembayaran ' . $transactionStatus . ' via API Sync.',
                                'refund_method' => in_array($transactionStatus, ['deny', 'cancel', 'expire', 'failure', 'refund']) ? 'api_sync' : null,
                                'refund_reason' => in_array($transactionStatus, ['deny', 'cancel', 'expire', 'failure', 'refund']) ? 'Payment ' . $transactionStatus . ' via API Sync' : null,
                                'payment_method' => $paymentType,
                                'metadata' => ['sync_status' => $transactionStatus],
                            ]);
                        }

                        return ['status' => 'error', 'order' => $order];
                    }

                    $order->update($orderUpdates);
                    return ['status' => 'info', 'order' => $order];
                });

                // Send notifications after transaction succeeds
                if (isset($result['order'])) {
                    $admins = \App\Models\User::where('role', 'administrator')->get();

                    if ($result['status'] === 'success') {
                        \Illuminate\Support\Facades\Notification::send($admins, new \App\Notifications\GeneralOrderNotification([
                            'order_id' => $result['order']->id,
                            'title'    => 'Pembayaran Lunas: ' . ($result['order']->midtrans_order_id ?? $result['order']->order_number),
                            'message'  => "Customer memverifikasi pembayaran pesanan " . ($result['order']->midtrans_order_id ?? $result['order']->order_number) . " secara mandiri.",
                            'type'     => 'payment',
                        ]));

                        return response()->json([
                            'status' => 'success',
                            'message' => 'Pembayaran berhasil diverifikasi!' . ($result['stock_error'] ? ' (Stok tidak mencukupi, admin akan review)' : ''),
                            'order_status' => $result['order']->fresh()->status
                        ]);
                    } elseif ($result['status'] === 'error') {
                        \Illuminate\Support\Facades\Notification::send($admins, new \App\Notifications\GeneralOrderNotification([
                            'order_id' => $result['order']->id,
                            'title'    => 'Pesanan Dibatalkan: ' . ($result['order']->midtrans_order_id ?? $result['order']->order_number),
                            'message'  => "Pesanan " . ($result['order']->midtrans_order_id ?? $result['order']->order_number) . " dibatalkan karena pembayaran {$transactionStatus}.",
                            'type'     => 'cancel',
                        ]));

                        return response()->json([
                            'status' => 'error',
                            'message' => "Pembayaran dibatalkan atau gagal: {$transactionStatus}",
                            'order_status' => $result['order']->status
                        ]);
                    }
                }

                return response()->json([
                    'status' => 'info',
                    'message' => 'Status pembayaran: ' . $transactionStatus,
                    'midtrans_status' => $transactionStatus
                ]);
            }

            if (str_contains($midtransOrderId, '/') && ($response->status() === 404 || $response->status() === 400)) {
                return response()->json([
                    'status' => 'info',
                    'message' => 'Pesanan ini menggunakan format ID lama. Status tidak dapat disinkronisasi otomatis. Harap hubungi Administrator jika Anda telah melakukan pembayaran.',
                    'order_status' => $order->status
                ]);
            }

            return response()->json([
                'status' => 'error',
                'message' => 'Gagal menghubungi Midtrans. (' . $response->status() . ')'
            ], 500);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Pesanan tidak ditemukan atau Anda tidak memiliki akses.',
            ], 404);
        } catch (\Exception $e) {
            Log::error('[API checkStatus] Exception: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan saat memeriksa status pembayaran.',
            ], 500);
        }
    }

    /**
     * V2: Generate snap token for an Invoice
     */
    public function getSnapTokenV2(Request $request)
    {
        try {
            $invoice = \App\Models\Invoice::where('user_id', auth()->id())->findOrFail($request->invoice_id);

            $paymentService = app(\App\Services\PaymentService::class);
            $snapToken = $paymentService->generateSnapTokenForInvoice($invoice);

            if (!$snapToken) {
                return response()->json(['status' => 'error', 'message' => 'Gagal menghasilkan token pembayaran Midtrans.'], 400);
            }

            DB::transaction(function () use ($invoice, $snapToken) {
                $invoice = \App\Models\Invoice::lockForUpdate()->find($invoice->id);
                $invoice->update(['snap_token' => $snapToken]);
            });

            return response()->json([
                'status' => 'success',
                'message' => 'Token pembayaran berhasil dibuat.',
                'snap_token' => $snapToken
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['status' => 'error', 'message' => 'Invoice tidak ditemukan atau Anda tidak memiliki akses.'], 404);
        } catch (\Exception $e) {
            Log::error('PaymentController V2 Exception:', ['message' => $e->getMessage()]);
            return response()->json(['status' => 'error', 'message' => 'Terjadi kesalahan pada server.'], 500);
        }
    }

    /**
     * V2: Check status for an Invoice
     */
    public function checkStatusV2($id)
    {
        try {
            $invoice = \App\Models\Invoice::where('user_id', auth()->id())->findOrFail($id);
            $midtransOrderId = $invoice->midtrans_order_id ?? $invoice->invoice_number;

            $response = Http::timeout(15)->withHeaders([
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
                'Authorization' => 'Basic ' . base64_encode($this->serverKey . ':'),
            ])->get(($this->isProduction ? 'https://api.midtrans.com/v2/' : 'https://api.sandbox.midtrans.com/v2/') . $midtransOrderId . "/status");

            if ($response->successful()) {
                $data = $response->json();
                $transactionStatus = $data['transaction_status'] ?? null;
                $paymentType = $data['payment_type'] ?? $invoice->payment_type;
                $transactionId = $data['transaction_id'] ?? null;

                $txStatus = \App\Services\StatusService::midtransToTransactionStatus($transactionStatus);

                $result = DB::transaction(function () use ($invoice, $transactionStatus, $txStatus, $transactionId, $paymentType, $data) {
                    $stockError = null;
                    $invoice = \App\Models\Invoice::lockForUpdate()->find($invoice->id);

                    if (in_array($invoice->payment_status, ['settlement', 'capture']) && !in_array($transactionStatus, ['settlement', 'capture', 'refund'])) {
                        return ['status' => 'info', 'invoice' => $invoice]; // Already paid
                    }

                    // Create/Update Transaction
                    $transaction = \App\Models\Transaction::firstOrNew(['invoice_id' => $invoice->id, 'order_id' => null]);
                    if (!$transaction->transaction_id) {
                        $transaction->transaction_id = $transactionId ?? ('SYNC-' . strtoupper(uniqid()));
                    }
                    $transaction->payment_method  = $paymentType;
                    $transaction->payment_details = $data;
                    $transaction->amount          = $invoice->grand_total;
                    $transaction->status          = $txStatus;
                    $transaction->notes           = 'Auto-sync Midtrans V2: ' . $transactionStatus;

                    if (in_array($transactionStatus, ['settlement', 'capture'])) {
                        $transaction->payment_date = isset($data['transaction_time'])
                            ? \Carbon\Carbon::parse($data['transaction_time'], 'Asia/Jakarta')
                            : now();
                    } elseif (in_array($txStatus, ['failed', 'refund'])) {
                        $transaction->refunded_at = now();
                    }
                    $transaction->save();

                    // Update Invoice
                    $invoiceUpdates = ['payment_status' => $transactionStatus];
                    if (empty($invoice->payment_type) && $paymentType) {
                        $invoiceUpdates['payment_type'] = $paymentType;
                    }
                    $invoice->update($invoiceUpdates);

                    // Cascade to Child Orders
                    $orders = $invoice->orders()->get();
                    foreach ($orders as $order) {
                        $orderUpdates = ['payment_status' => $transactionStatus];
                        if (empty($order->payment_type) && $paymentType) {
                            $orderUpdates['payment_type'] = $paymentType;
                        }

                        if (in_array($transactionStatus, ['settlement', 'capture'])) {
                            if (in_array($order->status, [\App\Models\Order::STATUS_PENDING, \App\Models\Order::STATUS_CANCELLED])) {
                                $orderUpdates['status'] = \App\Models\Order::STATUS_WAITING_CONFIRMATION;
                            }
                            $order->update($orderUpdates);

                            if (! $order->is_stock_deducted) {
                                try {
                                    app(\App\Services\OrderService::class)->processOrderStock($order);
                                } catch (\App\Exceptions\InsufficientStockException $e) {
                                    $stockError = $e->getMessage();
                                    $order->update(['notes' => ltrim($order->notes . "\n[SISTEM] Pembayaran lunas tapi stok tidak mencukupi: " . $e->getMessage())]);
                                }
                            }

                            // 1. Payment Confirmed Record
                            $freshStatus = $order->fresh()->status;
                            if ($freshStatus === \App\Models\Order::STATUS_WAITING_CONFIRMATION && !$order->trackingHistories()->where('notes', 'like', '%Pembayaran berhasil diverifikasi%')->exists()) {
                                \App\Models\TrackingHistory::create([
                                    'order_id' => $order->id,
                                    'admin_id' => null,
                                    'status'   => $freshStatus,
                                    'notes'    => 'Pembayaran berhasil diverifikasi oleh sistem (V2 Sync).',
                                    'payment_method' => $paymentType,
                                    'metadata' => ['api_sync' => true],
                                ]);
                                
                                // 2. Ready for Processing Record
                                \App\Models\TrackingHistory::create([
                                    'order_id' => $order->id,
                                    'admin_id' => null,
                                    'status'   => \App\Models\Order::STATUS_WAITING_CONFIRMATION,
                                    'notes'    => 'Pesanan siap diproses oleh penjual.',
                                ]);
                            }
                        } elseif (in_array($transactionStatus, ['deny', 'cancel', 'expire', 'failure', 'refund'])) {
                            $allowedStatuses = [\App\Models\Order::STATUS_PENDING, \App\Models\Order::STATUS_WAITING_CONFIRMATION, \App\Models\Order::STATUS_WAITING_CONFIRMATION, \App\Models\Order::STATUS_PROCESSING, \App\Models\Order::STATUS_SHIPPING];
                            if ($transactionStatus === 'refund') $allowedStatuses[] = \App\Models\Order::STATUS_COMPLETED;

                            if (in_array($order->status, $allowedStatuses)) {
                                $orderUpdates['status'] = ($transactionStatus === 'refund') ? \App\Models\Order::STATUS_REFUNDED : \App\Models\Order::STATUS_CANCELLED;
                                $orderUpdates['cancel_reason'] = 'Pembayaran ' . $transactionStatus . ' via API V2 Sync.';
                                
                                $order->update($orderUpdates);

                                if ($order->is_stock_deducted) {
                                    app(\App\Services\OrderService::class)->restoreOrderStock($order, $transactionStatus);
                                }
                            } else {
                                $order->update($orderUpdates);
                            }
                        } else {
                            $order->update($orderUpdates);
                        }
                    }
                    
                    if (in_array($transactionStatus, ['settlement', 'capture'])) {
                        return ['status' => 'success', 'stock_error' => $stockError ?? null, 'invoice' => $invoice];
                    } elseif (in_array($transactionStatus, ['deny', 'cancel', 'expire', 'failure', 'refund'])) {
                        return ['status' => 'error', 'invoice' => $invoice];
                    }
                    
                    return ['status' => 'info', 'invoice' => $invoice];
                });

                if (isset($result['invoice'])) {
                    $firstOrder = $result['invoice']->orders()->first();
                    $admins = \App\Models\User::where('role', 'administrator')->get();

                    if ($result['status'] === 'success') {
                        \Illuminate\Support\Facades\Notification::send($admins, new \App\Notifications\GeneralOrderNotification([
                            'order_id' => $firstOrder->id ?? 0,
                            'title'    => 'Midtrans: Settlement (' . $result['invoice']->midtrans_order_id . ')',
                            'message'  => "Customer memverifikasi pembayaran pesanan " . $result['invoice']->midtrans_order_id . " secara mandiri." . ($result['stock_error'] ? ' (Stock Error)' : ''),
                            'type'     => 'payment',
                        ]));

                        if ($firstOrder && $firstOrder->customer) {
                            $firstOrder->customer->notify(new \App\Notifications\GeneralOrderNotification([
                                'order_id' => $firstOrder->id,
                                'title'    => 'Update Pembayaran: Lunas',
                                'message'  => "Pembayaran untuk pesanan Anda ({$result['invoice']->midtrans_order_id}) kini berstatus: settlement." . ($result['stock_error'] ? ' (Stock akan di-review oleh administrator)' : ''),
                                'type'     => 'payment',
                            ]));
                        }

                        return response()->json([
                            'status' => 'success',
                            'message' => 'Status pembayaran berhasil disinkronisasi.' . ($result['stock_error'] ? ' (Stok tidak mencukupi, admin akan review)' : ''),
                            'data' => $data
                        ]);
                    } elseif ($result['status'] === 'error') {
                        \Illuminate\Support\Facades\Notification::send($admins, new \App\Notifications\GeneralOrderNotification([
                            'order_id' => $firstOrder->id ?? 0,
                            'title'    => 'Midtrans: ' . ucfirst($transactionStatus) . ' (' . $result['invoice']->midtrans_order_id . ')',
                            'message'  => "Pesanan " . $result['invoice']->midtrans_order_id . " dibatalkan karena pembayaran {$transactionStatus}.",
                            'type'     => 'cancel',
                        ]));

                        if ($firstOrder && $firstOrder->customer) {
                            $firstOrder->customer->notify(new \App\Notifications\GeneralOrderNotification([
                                'order_id' => $firstOrder->id,
                                'title'    => 'Update Pembayaran: ' . ucfirst($transactionStatus),
                                'message'  => "Pembayaran untuk pesanan Anda ({$result['invoice']->midtrans_order_id}) dibatalkan atau kadaluarsa.",
                                'type'     => 'cancel',
                            ]));
                        }

                        return response()->json([
                            'status' => 'error',
                            'message' => "Pembayaran dibatalkan atau gagal: {$transactionStatus}",
                            'data' => $data
                        ]);
                    }
                }

                return response()->json([
                    'status' => 'success',
                    'message' => 'Status pembayaran berhasil disinkronisasi.',
                    'data' => $data
                ]);
            }

            if (str_contains($midtransOrderId, '/') && ($response->status() === 404 || $response->status() === 400)) {
                return response()->json([
                    'status' => 'info',
                    'message' => 'Pesanan ini menggunakan format ID lama. Status tidak dapat disinkronisasi otomatis. Harap hubungi Administrator jika Anda telah melakukan pembayaran.',
                ]);
            }

            return response()->json(['status' => 'error', 'message' => 'Gagal menghubungi Midtrans. (' . $response->status() . ')'], 500);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['status' => 'error', 'message' => 'Invoice tidak ditemukan atau Anda tidak memiliki akses.'], 404);
        } catch (\Exception $e) {
            Log::error('[API checkStatusV2] Exception: ' . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => 'Terjadi kesalahan saat memeriksa status pembayaran.'], 500);
        }
    }
}
