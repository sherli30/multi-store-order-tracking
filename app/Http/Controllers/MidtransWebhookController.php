<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Transaction;
use App\Models\TrackingHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Services\StatusService;

class MidtransWebhookController extends Controller
{
    /**
     * Handle incoming Midtrans payment notification (webhook/callback).
     *
     * Midtrans sends a POST request to this endpoint after any payment event.
     * We verify the signature key, then update the order and transaction
     * accordingly.
     *
     * Route: POST /midtrans/callback  (no auth middleware, exempt from CSRF)
     */
    public function handle(Request $request)
    {
        $payload = $request->all();
        Log::info('[Midtrans Webhook] Received payload', $payload);

        $orderId           = $payload['order_id']           ?? null;
        $statusCode        = $payload['status_code']        ?? null;
        $grossAmount       = $payload['gross_amount']       ?? null;
        $transactionStatus = $payload['transaction_status'] ?? null;
        $transactionId     = $payload['transaction_id']     ?? null;
        $paymentType       = $payload['payment_type']       ?? null;
        $signatureKey      = $payload['signature_key']      ?? null;

        if (! $orderId || ! $signatureKey) {
            Log::warning('[Midtrans Webhook] Missing order_id or signature_key');
            return response()->json(['message' => 'Invalid payload'], 400);
        }

        $serverKey = config('midtrans.server_key');
        $expectedSignature = hash('sha512', $orderId . $statusCode . $grossAmount . $serverKey);

        if ($signatureKey !== $expectedSignature) {
            Log::warning('[Midtrans Webhook] Signature mismatch for order: ' . $orderId);
            return response()->json(['message' => 'Invalid signature'], 403);
        }

        $txStatus = StatusService::midtransToTransactionStatus($transactionStatus);

        // --- TRY TO RESOLVE AS V2 INVOICE ---
        $invoice = \App\Models\Invoice::where('midtrans_order_id', $orderId)->first();
        if (!$invoice) {
            // Fallback for ID matching (e.g. INV/2026...)
            if (str_starts_with($orderId, 'INV/')) {
                $invoice = \App\Models\Invoice::where('invoice_number', $orderId)->first();
            }
        }

        if ($invoice) {
            return $this->processInvoiceWebhook($invoice, $transactionStatus, $txStatus, $transactionId, $paymentType, $payload);
        }

        // --- FALLBACK TO V1 ORDER ---
        $order = Order::where('midtrans_order_id', $orderId)->first();
        if (! $order) {
            $order = Order::where('id', ltrim($orderId, 'ORDER-'))->first();
        }

        if (! $order) {
            Log::warning('[Midtrans Webhook] Order/Invoice not found for midtrans_order_id: ' . $orderId);
            return response()->json(['message' => 'Order/Invoice not found'], 404);
        }

        return $this->processOrderWebhook($order, $transactionStatus, $txStatus, $transactionId, $paymentType, $payload);
    }

    /**
     * V2: Process webhook for an Invoice (Multi-Store)
     */
    private function processInvoiceWebhook($invoice, $transactionStatus, $txStatus, $transactionId, $paymentType, $payload)
    {
        if (in_array($invoice->payment_status, ['settlement', 'capture']) && in_array($transactionStatus, ['settlement', 'capture', 'pending'])) {
            Log::info("[Midtrans Webhook V2] Ignoring '{$transactionStatus}' because invoice #{$invoice->id} is already settled.");
            return response()->json(['message' => 'Ignored. Invoice already settled.'], 200);
        }

        $webhookResult = DB::transaction(function () use ($invoice, $transactionStatus, $txStatus, $transactionId, $paymentType, $payload) {
            $invoice = \App\Models\Invoice::lockForUpdate()->find($invoice->id);

            if (in_array($invoice->payment_status, ['settlement', 'capture']) && in_array($transactionStatus, ['settlement', 'capture', 'pending'])) {
                return ['status' => 'ignored', 'invoice' => $invoice];
            }

            // Create 1 transaction for the entire invoice
            $transaction = Transaction::firstOrNew(['invoice_id' => $invoice->id, 'order_id' => null]);
            if (!$transaction->transaction_id) {
                $transaction->transaction_id = $transactionId ?? ('WEBHOOK-' . strtoupper(uniqid()));
            }
            $transaction->payment_method  = $paymentType;
            $transaction->payment_details = $payload;
            $transaction->amount          = $invoice->grand_total;
            $transaction->status          = $txStatus;
            $transaction->notes           = 'Auto via Midtrans Webhook: ' . $transactionStatus;

            if (in_array($transactionStatus, ['settlement', 'capture'])) {
                $transaction->payment_date = isset($payload['transaction_time'])
                    ? \Carbon\Carbon::parse($payload['transaction_time'], 'Asia/Jakarta')
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

            $webhookStatus = 'processed';
            $stockError = null;

            // Cascade to Child Orders
            $orders = $invoice->orders()->get();
            foreach ($orders as $order) {
                $orderUpdates = ['payment_status' => $transactionStatus];
                if (empty($order->payment_type) && $paymentType) {
                    $orderUpdates['payment_type'] = $paymentType;
                }

                if (in_array($transactionStatus, ['settlement', 'capture'])) {
                    // Change order status based on payment success
                    // Wait! Only change to waiting confirmation if it is currently pending
                    if ($order->status === Order::STATUS_PENDING) {
                        $orderUpdates['status'] = Order::STATUS_WAITING_CONFIRMATION;
                    }
                    $order->update($orderUpdates);

                    // Note: In V2, stock is already deducted at checkout.
                    // This block serves as a fallback.
                    if (! $order->is_stock_deducted) {
                        try {
                            app(\App\Services\OrderService::class)->processOrderStock($order);
                        } catch (\App\Exceptions\InsufficientStockException $e) {
                            $stockError = $e->getMessage();
                            $webhookStatus = 'partial_failure';
                            $order->update(['notes' => ltrim($order->notes . "\n[SISTEM] Pembayaran lunas tapi stok tidak mencukupi: " . $e->getMessage())]);
                        }
                    }

                    $freshStatus = $order->fresh()->status;
                    
                    // 1. Payment Confirmed Record
                    TrackingHistory::create([
                        'order_id' => $order->id,
                        'admin_id' => null,
                        'status'   => $freshStatus,
                        'notes'    => 'Pembayaran berhasil dikonfirmasi via Midtrans Webhook (' . $transactionStatus . ').',
                        'payment_method' => $paymentType,
                        'metadata' => ['webhook_status' => $transactionStatus],
                    ]);

                    // 2. Ready for Processing Record
                    if (isset($orderUpdates['status']) && $orderUpdates['status'] === Order::STATUS_WAITING_CONFIRMATION) {
                        if (!$order->trackingHistories()->where('status', Order::STATUS_WAITING_CONFIRMATION)->exists()) {
                            TrackingHistory::create([
                                'order_id' => $order->id,
                                'admin_id' => null,
                                'status'   => Order::STATUS_WAITING_CONFIRMATION,
                                'notes'    => 'Pembayaran telah berhasil (Settlement). Menunggu konfirmasi admin.',
                            ]);
                        }
                    }

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
                        $orderUpdates['cancel_reason'] = 'Pembayaran ' . $transactionStatus . ' via Midtrans.';
                        $order->update($orderUpdates);

                        // Restore Stock Safely
                        if ($order->is_stock_deducted) {
                            try {
                                app(\App\Services\OrderService::class)->restoreOrderStock($order, $transactionStatus);
                            } catch (\Exception $e) {
                                $webhookStatus = 'partial_failure';
                                $stockError = $e->getMessage();
                            }
                        }
                    } else {
                        $order->update($orderUpdates);
                    }

                    TrackingHistory::create([
                        'order_id' => $order->id,
                        'admin_id' => null,
                        'status'   => $order->fresh()->status,
                        'notes'    => 'Pembayaran ' . $transactionStatus . ' via Midtrans Webhook.',
                        'refund_method' => in_array($transactionStatus, ['deny', 'cancel', 'expire', 'failure', 'refund']) ? 'webhook' : null,
                        'refund_reason' => in_array($transactionStatus, ['deny', 'cancel', 'expire', 'failure', 'refund']) ? 'Payment ' . $transactionStatus : null,
                        'payment_method' => $paymentType,
                        'metadata' => ['webhook_status' => $transactionStatus],
                    ]);
                } else {
                    $order->update($orderUpdates);
                }
            }

            return [
                'status' => $webhookStatus,
                'invoice' => $invoice,
                'stock_error' => $stockError,
                'transaction_status' => $transactionStatus
            ];
        });

        try {
            DB::transaction(function () use ($webhookResult) {
                if ($webhookResult['status'] === 'ignored') return;

                $invoice = $webhookResult['invoice'];
                $transactionStatus = $webhookResult['transaction_status'];
                $firstOrder = $invoice->orders()->first();

                $admins = \App\Models\User::where('role', 'administrator')->get();
                \Illuminate\Support\Facades\Notification::send($admins, new \App\Notifications\GeneralOrderNotification([
                    'order_id' => $firstOrder->id ?? 0,
                    'title'    => 'Midtrans: ' . ucfirst($transactionStatus) . ' (' . $invoice->midtrans_order_id . ')',
                    'message'  => 'Status pembayaran pesanan ' . $invoice->midtrans_order_id . ' menjadi ' . $transactionStatus . ($webhookResult['stock_error'] ? ' (Stock Error)' : '') . '.',
                    'type'     => in_array($transactionStatus, ['deny', 'cancel', 'expire', 'failure', 'refund']) ? 'cancel' : 'payment',
                ]));

                if ($firstOrder && $firstOrder->customer) {
                    $msgType = in_array($transactionStatus, ['deny', 'cancel', 'expire', 'failure', 'refund']) ? 'cancel' : 'payment';
                    $firstOrder->customer->notify(new \App\Notifications\GeneralOrderNotification([
                        'order_id' => $firstOrder->id,
                        'title'    => 'Update Pembayaran: ' . ucfirst($transactionStatus),
                        'message'  => "Pembayaran untuk pesanan Anda ({$invoice->midtrans_order_id}) kini berstatus: {$transactionStatus}." . ($webhookResult['stock_error'] ? ' (Stock akan di-review oleh administrator)' : ''),
                        'type'     => $msgType,
                    ]));
                }
            });
        } catch (\Exception $e) {
            Log::error('[Midtrans Webhook] Failed to send notifications for invoice #' . $webhookResult['invoice']->id . ': ' . $e->getMessage());
        }

        return response()->json(['message' => 'OK'], 200);
    }

    /**
     * V1: Legacy single-order webhook processing
     */
    private function processOrderWebhook($order, $transactionStatus, $txStatus, $transactionId, $paymentType, $payload)
    {
        if (in_array($order->payment_status, ['settlement', 'capture']) && in_array($transactionStatus, ['settlement', 'capture', 'pending'])) {
            Log::info("[Midtrans Webhook] Ignoring '{$transactionStatus}' webhook because order #{$order->id} is already settled.");
            return response()->json(['message' => 'Ignored. Order already settled.'], 200);
        }

        $webhookResult = DB::transaction(function () use ($order, $transactionStatus, $txStatus, $transactionId, $paymentType, $payload) {
            $order = Order::lockForUpdate()->find($order->id);

            $order->update([
                'webhook_attempts' => $order->webhook_attempts + 1,
                'last_webhook_attempt' => now(),
            ]);

            if (in_array($order->payment_status, ['settlement', 'capture']) && in_array($transactionStatus, ['settlement', 'capture', 'pending'])) {
                return ['status' => 'ignored', 'order' => $order];
            }

            $transaction = Transaction::firstOrNew(['order_id' => $order->id, 'invoice_id' => null]);

            if (!$transaction->transaction_id) {
                $transaction->transaction_id = $transactionId ?? ('WEBHOOK-' . strtoupper(uniqid()));
            }

            $transaction->payment_method  = $paymentType;
            $transaction->payment_details = $payload;
            $transaction->amount          = $order->total_amount;
            $transaction->status          = $txStatus;
            $transaction->notes           = 'Auto via Midtrans Webhook: ' . $transactionStatus;

            if (in_array($transactionStatus, ['settlement', 'capture'])) {
                $transaction->payment_date = isset($payload['transaction_time'])
                    ? \Carbon\Carbon::parse($payload['transaction_time'], 'Asia/Jakarta')
                    : now();
            } elseif (in_array($txStatus, ['failed', 'refund'])) {
                $transaction->refunded_at = now();
            }

            $transaction->save();

            $orderUpdates = ['payment_status' => $transactionStatus];
            if (empty($order->payment_type) && $paymentType) {
                $orderUpdates['payment_type'] = $paymentType;
            }

            $webhookStatus = 'processed';
            $stockError = null;

            if (in_array($transactionStatus, ['settlement', 'capture'])) {
                $orderUpdates['payment_status'] = 'settlement';

                if ($order->status === Order::STATUS_PENDING) {
                    $orderUpdates['status'] = Order::STATUS_WAITING_CONFIRMATION;
                }

                $order->update($orderUpdates);

                if (! $order->is_stock_deducted) {
                    try {
                        app(\App\Services\OrderService::class)->processOrderStock($order);
                    } catch (\App\Exceptions\InsufficientStockException $e) {
                        $stockError = $e->getMessage();
                        $webhookStatus = 'partial_failure';
                        $order->update([
                            'notes' => ltrim($order->notes . "\n[SISTEM] Pembayaran lunas tapi stok tidak mencukupi: " . $e->getMessage())
                        ]);
                    }
                }

                $freshStatus = $order->fresh()->status;
                
                // 1. Payment Confirmed Record
                TrackingHistory::create([
                    'order_id' => $order->id,
                    'admin_id' => null,
                    'status'   => $freshStatus,
                    'notes'    => 'Pembayaran berhasil dikonfirmasi via Midtrans Webhook (' . $transactionStatus . ').',
                    'payment_method' => $paymentType,
                    'metadata' => ['webhook_status' => $transactionStatus],
                ]);

                // 2. Ready for Processing Record
                if (isset($orderUpdates['status']) && $orderUpdates['status'] === Order::STATUS_WAITING_CONFIRMATION) {
                    if (!$order->trackingHistories()->where('status', Order::STATUS_WAITING_CONFIRMATION)->exists()) {
                        TrackingHistory::create([
                            'order_id' => $order->id,
                            'admin_id' => null,
                            'status'   => Order::STATUS_WAITING_CONFIRMATION,
                            'notes'    => 'Pembayaran telah berhasil (Settlement). Menunggu konfirmasi admin.',
                        ]);
                    }
                }

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
                    $orderUpdates['cancel_reason'] = 'Pembayaran ' . $transactionStatus . ' via Midtrans.';
                    
                    $order->update($orderUpdates);

                    if ($order->is_stock_deducted) {
                        try {
                            app(\App\Services\OrderService::class)->restoreOrderStock($order, $transactionStatus);
                        } catch (\Exception $e) {
                            $webhookStatus = 'partial_failure';
                            $stockError = $e->getMessage();
                        }
                    }
                } else {
                    $order->update($orderUpdates);
                }

                TrackingHistory::create([
                    'order_id' => $order->id,
                    'admin_id' => null,
                    'status'   => $order->fresh()->status,
                    'notes'    => 'Pembayaran ' . $transactionStatus . ' via Midtrans Webhook.',
                    'refund_method' => in_array($transactionStatus, ['deny', 'cancel', 'expire', 'failure', 'refund']) ? 'webhook' : null,
                    'refund_reason' => in_array($transactionStatus, ['deny', 'cancel', 'expire', 'failure', 'refund']) ? 'Payment ' . $transactionStatus : null,
                    'payment_method' => $paymentType,
                    'metadata' => ['webhook_status' => $transactionStatus],
                ]);

            } else {
                $order->update($orderUpdates);
            }

            return [
                'status' => $webhookStatus,
                'order' => $order,
                'stock_error' => $stockError,
                'transaction_status' => $transactionStatus
            ];
        });

        try {
            DB::transaction(function () use ($webhookResult) {
                if ($webhookResult['status'] === 'ignored') return;

                $order = $webhookResult['order'];
                $transactionStatus = $webhookResult['transaction_status'];

                $admins = \App\Models\User::where('role', 'administrator')->get();
                \Illuminate\Support\Facades\Notification::send($admins, new \App\Notifications\GeneralOrderNotification([
                    'order_id' => $order->id,
                    'title'    => 'Midtrans: ' . ucfirst($transactionStatus) . ' (' . $order->midtrans_order_id . ')',
                    'message'  => 'Status pembayaran pesanan ' . $order->midtrans_order_id . ' menjadi ' . $transactionStatus . ($webhookResult['stock_error'] ? ' (Stock Error)' : '') . '.',
                    'type'     => in_array($transactionStatus, ['deny', 'cancel', 'expire', 'failure', 'refund']) ? 'cancel' : 'payment',
                ]));

                if ($order->customer) {
                    $msgType = in_array($transactionStatus, ['deny', 'cancel', 'expire', 'failure', 'refund']) ? 'cancel' : 'payment';
                    $order->customer->notify(new \App\Notifications\GeneralOrderNotification([
                        'order_id' => $order->id,
                        'title'    => 'Update Pembayaran: ' . ucfirst($transactionStatus),
                        'message'  => "Pembayaran untuk pesanan Anda ({$order->midtrans_order_id}) kini berstatus: {$transactionStatus}." . ($webhookResult['stock_error'] ? ' (Stock akan di-review oleh administrator)' : ''),
                        'type'     => $msgType,
                    ]));
                }
            });
        } catch (\Exception $e) {
            Log::error('[Midtrans Webhook] Failed to send notifications for order #' . ($webhookResult['order']->id ?? 0) . ': ' . $e->getMessage());
        }

        return response()->json(['message' => 'OK'], 200);
    }
}
