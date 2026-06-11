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

        // ── 1. Basic payload validation ───────────────────────────────────
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

        // ── 2. Verify signature key ───────────────────────────────────────
        $serverKey = config('midtrans.server_key');
        $expectedSignature = hash('sha512', $orderId . $statusCode . $grossAmount . $serverKey);

        if ($signatureKey !== $expectedSignature) {
            Log::warning('[Midtrans Webhook] Signature mismatch for order: ' . $orderId);
            return response()->json(['message' => 'Invalid signature'], 403);
        }

        // ── 3. Find the order ─────────────────────────────────────────────
        // Midtrans sends `order_id` which maps to our `midtrans_order_id`.
        $order = Order::where('midtrans_order_id', $orderId)->first();

        if (! $order) {
            // Fallback: search by local order number pattern if used
            $order = Order::where('id', ltrim($orderId, 'ORDER-'))->first();
        }

        if (! $order) {
            Log::warning('[Midtrans Webhook] Order not found for midtrans_order_id: ' . $orderId);
            return response()->json(['message' => 'Order not found'], 404);
        }

        // ── 3.5. Prevent downgrading already paid orders ──────────────────
        // If an old unused token expires, Midtrans might send an 'expire' webhook.
        // We must ignore it if the order is already paid to prevent accidental cancellation.
        if (in_array($order->payment_status, ['settlement', 'capture']) && !in_array($transactionStatus, ['settlement', 'capture', 'refund'])) {
            Log::info("[Midtrans Webhook] Ignoring '{$transactionStatus}' webhook because order #{$order->id} is already settled.");
            return response()->json(['message' => 'Ignored. Order already settled.'], 200);
        }

        // ── 4. Wrap all state changes in a transaction with pessimistic lock ──
        $txStatus = StatusService::midtransToTransactionStatus($transactionStatus);

        $webhookResult = DB::transaction(function () use ($order, $orderId, $transactionStatus, $transactionId, $paymentType, $payload, $txStatus) {
            // Re-fetch with lock to prevent race with concurrent webhooks or checkStatus
            $order = Order::lockForUpdate()->find($order->id);

            // Track webhook attempt
            $order->update([
                'webhook_attempts' => $order->webhook_attempts + 1,
                'last_webhook_attempt' => now(),
            ]);

            // Re-check: Prevent downgrading already paid orders under the lock
            if (in_array($order->payment_status, ['settlement', 'capture']) && !in_array($transactionStatus, ['settlement', 'capture', 'refund'])) {
                Log::info("[Midtrans Webhook] Ignoring '{$transactionStatus}' under lock — order #{$order->id} already settled.");
                return ['status' => 'ignored', 'order' => $order];
            }

            $transaction = Transaction::firstOrNew(['order_id' => $order->id]);

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

            // ── 5. Sync order payment_status & midtrans_order_id ─────────────
            $orderUpdates = ['payment_status' => $transactionStatus];

            if (empty($order->midtrans_order_id)) {
                $orderUpdates['midtrans_order_id'] = $orderId;
            }
            if (empty($order->payment_type) && $paymentType) {
                $orderUpdates['payment_type'] = $paymentType;
            }

            $webhookStatus = 'processed';
            $stockError = null;

            // ── 6. Advance order status on successful payment ─────────────────
            if (in_array($transactionStatus, ['settlement', 'capture'])) {
                $orderUpdates['payment_status'] = 'settlement';

                if (in_array($order->status, [Order::STATUS_PENDING, Order::STATUS_CANCELLED])) {
                    $orderUpdates['status'] = Order::STATUS_PERLU_DIPROSES;
                }

                $order->update($orderUpdates);

                // Deduct stock safely to prevent negative inventory
                if (! $order->is_stock_deducted) {
                    try {
                        app(\App\Services\OrderService::class)->processOrderStock($order);
                    } catch (\App\Exceptions\InsufficientStockException $e) {
                        Log::error('[Midtrans Webhook] Insufficient stock for paid order #' . $order->id . ': ' . $e->getMessage());
                        $stockError = $e->getMessage();
                        $webhookStatus = 'partial_failure';
                        $order->update([
                            'notes' => ltrim($order->notes . "\n[SISTEM] Pembayaran lunas tapi stok tidak mencukupi: " . $e->getMessage())
                        ]);
                    }
                }

                TrackingHistory::create([
                    'order_id' => $order->id,
                    'admin_id' => null,
                    'status'   => $order->status,
                    'notes'    => 'Pembayaran berhasil dikonfirmasi via Midtrans Webhook (' . $transactionStatus . ').',
                    'payment_method' => $paymentType,
                    'metadata' => ['webhook_status' => $transactionStatus],
                ]);

                Log::info('[Midtrans Webhook] Payment settled for order #' . $order->id);

            } elseif (in_array($transactionStatus, ['deny', 'cancel', 'expire', 'failure', 'refund'])) {
                if (in_array($order->status, [Order::STATUS_PENDING, Order::STATUS_PERLU_DIPROSES, Order::STATUS_PROCESSING, Order::STATUS_SHIPPING])) {
                    $orderUpdates['status']        = Order::STATUS_CANCELLED;
                    $orderUpdates['cancel_reason'] = 'Pembayaran ' . $transactionStatus . ' via Midtrans.';
                }

                $order->update($orderUpdates);

                if ($order->is_stock_deducted) {
                    try {
                        app(\App\Services\OrderService::class)->restoreOrderStock($order, $transactionStatus);
                    } catch (\Exception $e) {
                        Log::error('[Midtrans Webhook] Failed to restore stock for order #' . $order->id . ': ' . $e->getMessage());
                        $webhookStatus = 'partial_failure';
                        $stockError = $e->getMessage();
                    }
                }

                TrackingHistory::create([
                    'order_id' => $order->id,
                    'admin_id' => null,
                    'status'   => $order->status,
                    'notes'    => 'Pembayaran ' . $transactionStatus . ' via Midtrans Webhook.',
                    'refund_method' => in_array($transactionStatus, ['deny', 'cancel', 'expire', 'failure', 'refund']) ? 'webhook' : null,
                    'refund_reason' => in_array($transactionStatus, ['deny', 'cancel', 'expire', 'failure', 'refund']) ? 'Payment ' . $transactionStatus : null,
                    'payment_method' => $paymentType,
                    'metadata' => ['webhook_status' => $transactionStatus],
                ]);

                Log::info('[Midtrans Webhook] Payment ' . $transactionStatus . ' for order #' . $order->id);

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

        // ── 7. Send notifications INSIDE a new transaction after webhook processing ─────
        // This ensures notifications are only sent after order state is confirmed
        try {
            DB::transaction(function () use ($webhookResult) {
                $order = $webhookResult['order'];
                $transactionStatus = $webhookResult['transaction_status'];

                $admins = \App\Models\User::where('role', 'admin')->get();
                \Illuminate\Support\Facades\Notification::send($admins, new \App\Notifications\GeneralOrderNotification([
                    'order_id' => $order->id,
                    'title'    => 'Midtrans: ' . ucfirst($transactionStatus) . ' (' . $order->midtrans_order_id . ')',
                    'message'  => 'Status pembayaran pesanan ' . $order->midtrans_order_id . ' menjadi ' . $transactionStatus . ($webhookResult['stock_error'] ? ' (Stock Error)' : '') . '.',
                    'type'     => in_array($transactionStatus, ['deny', 'cancel', 'expire', 'failure', 'refund']) ? 'cancel' : 'payment',
                ]));

                // Notify Customer
                if ($order->customer) {
                    $msgType = in_array($transactionStatus, ['deny', 'cancel', 'expire', 'failure', 'refund']) ? 'cancel' : 'payment';
                    $order->customer->notify(new \App\Notifications\GeneralOrderNotification([
                        'order_id' => $order->id,
                        'title'    => 'Update Pembayaran: ' . ucfirst($transactionStatus),
                        'message'  => "Pembayaran untuk pesanan Anda ({$order->midtrans_order_id}) kini berstatus: {$transactionStatus}." . ($webhookResult['stock_error'] ? ' (Stock akan di-review oleh admin)' : ''),
                        'type'     => $msgType,
                    ]));
                }
            });
        } catch (\Exception $e) {
            Log::error('[Midtrans Webhook] Failed to send notifications for order #' . $webhookResult['order']->id . ': ' . $e->getMessage());
        }

        // Return 200 OK only if webhook was fully processed (even partial failures return 200 for idempotency)
        return response()->json(['message' => 'OK'], 200);
    }
}
