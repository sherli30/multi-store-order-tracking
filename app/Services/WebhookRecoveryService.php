<?php

namespace App\Services;

use App\Models\Order;
use App\Models\WebhookFailure;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * WebhookRecoveryService — Handles recovery from failed webhooks and transaction inconsistencies.
 *
 * Provides methods to:
 * - Track webhook failures
 * - Recover orders from partial payment/stock failures
 * - Audit and reconcile order states
 */
class WebhookRecoveryService
{
    /**
     * Record a webhook failure for later manual review/retry
     */
    public function recordFailure(Order $order, string $failureReason, string $type = 'midtrans_payment', ?array $payload = null): WebhookFailure
    {
        $existing = WebhookFailure::where('order_id', $order->id)
            ->where('webhook_type', $type)
            ->where('resolved', false)
            ->first();

        if ($existing) {
            $existing->recordAttempt($failureReason);
            return $existing;
        }

        return WebhookFailure::create([
            'order_id' => $order->id,
            'webhook_type' => $type,
            'failure_reason' => $failureReason,
            'payload' => $payload,
            'attempt_count' => 1,
            'first_failed_at' => now(),
            'last_failed_at' => now(),
        ]);
    }

    /**
     * Recover order: reconcile payment/stock state
     * Called when payment is successful but stock deduction failed
     */
    public function recoverPartialPayment(Order $order): bool
    {
        try {
            return DB::transaction(function () use ($order) {
                $order = Order::lockForUpdate()->find($order->id);

                // Verify payment is settled
                if (!in_array($order->payment_status, ['settlement', 'capture'])) {
                    Log::warning('[Recovery] Order #' . $order->id . ' not settled, skipping stock deduction');
                    return false;
                }

                // Check if stock already deducted
                if ($order->is_stock_deducted) {
                    Log::info('[Recovery] Order #' . $order->id . ' stock already deducted');
                    $this->markFailureResolved($order, 'midtrans_payment');
                    return true;
                }

                // Attempt stock deduction
                try {
                    app(OrderService::class)->processOrderStock($order);
                    Log::info('[Recovery] Successfully deducted stock for order #' . $order->id);
                    $this->markFailureResolved($order, 'midtrans_payment');
                    return true;
                } catch (\App\Exceptions\InsufficientStockException $e) {
                    Log::error('[Recovery] Insufficient stock for order #' . $order->id . ': ' . $e->getMessage());
                    $this->recordFailure($order, 'Stock insufficient: ' . $e->getMessage(), 'stock_deduction');
                    return false;
                }
            });
        } catch (\Exception $e) {
            Log::error('[Recovery] Exception while recovering partial payment for order #' . $order->id . ': ' . $e->getMessage());
            $this->recordFailure($order, 'Recovery exception: ' . $e->getMessage(), 'midtrans_payment');
            return false;
        }
    }

    /**
     * Recover order: retry snap token generation
     * Called when order created but payment token generation failed
     */
    public function recoverSnapTokenGeneration(Order $order, \App\Services\PaymentService $paymentService): ?string
    {
        try {
            if ($order->snap_token && $order->payment_status === null) {
                Log::info('[Recovery] Order #' . $order->id . ' already has snap token');
                return $order->snap_token;
            }

            Log::info('[Recovery] Attempting to regenerate snap token for order #' . $order->id);

            // Call payment service to regenerate token
            $token = $paymentService->generateSnapToken($order);

            if ($token) {
                $order->update(['snap_token' => $token]);
                $this->markFailureResolved($order, 'snap_token_generation');
                Log::info('[Recovery] Successfully regenerated snap token for order #' . $order->id);
                return $token;
            }

            $this->recordFailure($order, 'Snap token generation failed', 'snap_token_generation');
            return null;

        } catch (\Exception $e) {
            Log::error('[Recovery] Exception while recovering snap token for order #' . $order->id . ': ' . $e->getMessage());
            $this->recordFailure($order, 'Exception: ' . $e->getMessage(), 'snap_token_generation');
            return null;
        }
    }

    /**
     * Get all orders with unresolved webhook failures
     */
    public function getFailedOrders($limit = 50)
    {
        return WebhookFailure::unresolved()
            ->with('order.customer')
            ->paginate($limit);
    }

    /**
     * Mark a webhook failure as resolved
     */
    private function markFailureResolved(Order $order, string $type = 'midtrans_payment'): void
    {
        $failure = WebhookFailure::where('order_id', $order->id)
            ->where('webhook_type', $type)
            ->where('resolved', false)
            ->first();

        if ($failure) {
            $failure->markResolved();
        }
    }

    /**
     * Audit order consistency: verify payment/stock state alignment
     */
    public function auditOrder(Order $order): array
    {
        $issues = [];

        // Check: Payment settled but stock not deducted
        if (in_array($order->payment_status, ['settlement', 'capture']) && !$order->is_stock_deducted) {
            $issues[] = 'Payment settled but stock not deducted';
        }

        // Check: Stock deducted but payment not settled
        if ($order->is_stock_deducted && !in_array($order->payment_status, ['settlement', 'capture'])) {
            $issues[] = 'Stock deducted but payment not settled (may indicate cancellation failure)';
        }

        // Check: Cancelled but stock not restored
        if ($order->status === Order::STATUS_CANCELLED && $order->is_stock_deducted) {
            $issues[] = 'Order cancelled but stock not restored';
        }

        // Check: Transaction record inconsistencies
        if ($order->payment_status && !$order->transaction) {
            $issues[] = 'No transaction record found despite payment status set';
        }

        return $issues;
    }
}
