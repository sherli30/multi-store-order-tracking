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
        $order->loadMissing('orderItems.product');

        DB::transaction(function () use ($order, $source) {
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
                    'source'       => $source,
                    'reference_id' => $lockedOrder->id,
                ]);
            }

            $lockedOrder->update(['is_stock_deducted' => false]);
        });
    }
}
