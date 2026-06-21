<?php

namespace App\Services;

use App\Exceptions\InsufficientStockException;
use App\Models\Order;
use App\Models\Product;
use App\Models\StockMovement;
use Illuminate\Support\Facades\DB;

/**
 * OrderService — Handles order-related stock operations.
 *
 * Wraps the existing Order::orderItemsStockOut() / orderItemsStockIn() logic
 * with:
 *   1. Explicit DB transactions (already present in Order model, kept here for safety)
 *   2. lockForUpdate() on each product for race-condition-safe concurrent orders
 *   3. Pre-flight stock availability check before any deduction begins
 *
 * Backward compatibility:
 *   - The Order model's orderItemsStockOut() / orderItemsStockIn() methods remain
 *     untouched. Controllers can call those OR this service — both work.
 *   - This service is the RECOMMENDED path for new code and the order creation flow.
 */
class OrderService
{
    // ─────────────────────────────────────────────
    // Public API
    // ─────────────────────────────────────────────

    /**
     * Deduct stock for all items in the given order.
     * Atomically checks is_stock_deducted under lock to prevent double-deduction.
     */
    public function processOrderStock(Order $order): void
    {
        $order->loadMissing('orderItems.product');

        DB::transaction(function () use ($order) {
            // Re-fetch with lock FIRST, then check is_stock_deducted atomically
            $lockedOrder = Order::lockForUpdate()->find($order->id);

            if ($lockedOrder->is_stock_deducted) {
                return;
            }

            $sortedItems = $lockedOrder->orderItems->sortBy('product_id');
            foreach ($sortedItems as $item) {
                $product = $item->product;
                if (!$product) continue;

                $lockedProduct = Product::lockForUpdate()->withTrashed()->find($product->id);

                if (!$lockedProduct || $lockedProduct->stock < $item->quantity) {
                    throw new InsufficientStockException(
                        $lockedProduct->name ?? 'Unknown',
                        $item->quantity,
                        $lockedProduct->stock ?? 0,
                    );
                }

                $lockedProduct->decrement('stock', $item->quantity);

                StockMovement::create([
                    'product_id'   => $lockedProduct->id,
                    'type'         => 'out',
                    'quantity'     => $item->quantity,
                    'source'       => 'purchase',
                    'reference_id' => $lockedOrder->id,
                ]);
            }

            $lockedOrder->update(['is_stock_deducted' => true]);
        });
    }

    /**
     * Restore stock for all items in an order (cancellation / refund).
     * Atomically checks is_stock_deducted under lock to prevent double-restoration.
     */
    public function restoreOrderStock(Order $order, string $source = 'cancellation'): void
    {
        // Standardize source terminology for inventory ledger consistency
        $normalizedSource = match ($source) {
            'expire', 'deny', 'failure', 'cancel', 'cancellation' => 'cancellation',
            'refund' => 'refund',
            default  => 'cancellation', // Fallback to cancellation for any unknown failed status
        };

        $order->loadMissing('orderItems.product');

        DB::transaction(function () use ($order, $normalizedSource) {
            // Re-fetch with lock FIRST, then check is_stock_deducted atomically
            $lockedOrder = Order::lockForUpdate()->find($order->id);

            if (!$lockedOrder->is_stock_deducted) {
                return;
            }

            $sortedItems = $lockedOrder->orderItems->sortBy('product_id');
            foreach ($sortedItems as $item) {
                $product = $item->product;
                if (!$product) continue;

                $lockedProduct = Product::lockForUpdate()->withTrashed()->find($product->id);
                if (!$lockedProduct) continue;

                $lockedProduct->increment('stock', $item->quantity);

                StockMovement::create([
                    'product_id'   => $lockedProduct->id,
                    'type'         => 'in',
                    'quantity'     => $item->quantity,
                    'source'       => $normalizedSource,
                    'reference_id' => $lockedOrder->id,
                ]);
            }

            $lockedOrder->update(['is_stock_deducted' => false]);
        });
    }

    /**
     * Atomically cancel an order, update transaction, restore stock, and add history.
     * Centralized logic for both Web Admin and Mobile App.
     */
    public function cancelOrder(Order $order, string $reason, ?int $adminId = null): Order
    {
        return DB::transaction(function () use ($order, $reason, $adminId) {
            // 1. Lock the order row first to prevent race conditions
            $lockedOrder = Order::lockForUpdate()->find($order->id);

            // Guard: check status under lock
            if (in_array($lockedOrder->status, [Order::STATUS_CANCELLED, Order::STATUS_REFUNDED])) {
                throw new \Exception("Pesanan {$lockedOrder->order_number} sudah dibatalkan atau dikembalikan sebelumnya.");
            }
            if ($lockedOrder->status === Order::STATUS_COMPLETED) {
                throw new \Exception("Pesanan {$lockedOrder->order_number} sudah selesai dan tidak dapat dibatalkan.");
            }

            // 2. Update Order Status & sync payment_status for revenue exclusion
            $orderUpdate = [
                'status'        => Order::STATUS_CANCELLED,
                'cancel_reason' => $reason,
            ];

            // If the order was already paid, update payment_status so it is
            // immediately excluded from all revenue queries (Dashboard KPIs,
            // Reports, Charts, Exports) which filter by payment_status IN
            // ('settlement', 'capture', 'paid').
            if (in_array($lockedOrder->payment_status, ['settlement', 'capture', 'paid'])) {
                $orderUpdate['payment_status'] = 'cancelled';
            }

            $lockedOrder->update($orderUpdate);

            // 3. Sync with Transaction table
            $transaction = $lockedOrder->transaction()->lockForUpdate()->first();
            if ($transaction) {
                if ($transaction->status === 'pending') {
                    $transaction->update([
                        'status' => 'failed',
                        'notes'  => "Dibatalkan manual. Alasan: " . $reason,
                    ]);
                } elseif ($transaction->status === 'paid') {
                    $transaction->update([
                        'status' => 'refund',
                        'refunded_at' => now(),
                        'notes'  => "Dibatalkan manual setelah dibayar. Alasan: " . $reason,
                    ]);
                }
            }

            // 4. Add History
            \App\Models\TrackingHistory::create([
                'order_id' => $lockedOrder->id,
                'admin_id' => $adminId,
                'status'   => Order::STATUS_CANCELLED,
                'notes'    => "Pesanan dibatalkan. Alasan: " . $reason,
            ]);

            // 5. Restore stock using the centralized method
            if ($lockedOrder->is_stock_deducted) {
                $this->restoreOrderStock($lockedOrder, 'cancellation');
            }

            return $lockedOrder;
        });
    }
}
