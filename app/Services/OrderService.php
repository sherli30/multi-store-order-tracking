<?php

namespace App\Services;

use App\Exceptions\InsufficientStockException;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductVariant;
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
     */
    public function processOrderStock(Order $order): void
    {
        if ($order->is_stock_deducted) {
            return;
        }

        $order->loadMissing('orderItems.productVariant.product');

        foreach ($order->orderItems as $item) {
            $variant = $item->productVariant;
            if (!$variant) continue;

            if ($variant->stock < $item->quantity) {
                throw new InsufficientStockException(
                    $variant->product->name . ' (' . $variant->name . ')',
                    $item->quantity,
                    $variant->stock,
                );
            }
        }

        DB::transaction(function () use ($order) {
            foreach ($order->orderItems as $item) {
                $variant = $item->productVariant;
                if (!$variant) continue;

                $lockedVariant = ProductVariant::lockForUpdate()->find($variant->id);

                if (!$lockedVariant || $lockedVariant->stock < $item->quantity) {
                    throw new InsufficientStockException(
                        $lockedVariant->product->name ?? 'Unknown',
                        $item->quantity,
                        $lockedVariant->stock ?? 0,
                    );
                }

                $lockedVariant->decrement('stock', $item->quantity);

                StockMovement::create([
                    'product_variant_id' => $lockedVariant->id,
                    'type'               => 'out',
                    'quantity'           => $item->quantity,
                    'source'             => 'purchase',
                    'reference_id'       => $order->id,
                ]);
            }

            $order->update(['is_stock_deducted' => true]);
        });
    }

    /**
     * Restore stock for all items in an order (cancellation / refund).
     */
    public function restoreOrderStock(Order $order, string $source = 'cancellation'): void
    {
        if (!$order->is_stock_deducted) {
            return;
        }

        $order->loadMissing('orderItems.productVariant');

        DB::transaction(function () use ($order, $source) {
            foreach ($order->orderItems as $item) {
                $variant = $item->productVariant;
                if (!$variant) continue;

                $lockedVariant = ProductVariant::lockForUpdate()->find($variant->id);
                if (!$lockedVariant) continue;

                $lockedVariant->increment('stock', $item->quantity);

                StockMovement::create([
                    'product_variant_id' => $lockedVariant->id,
                    'type'               => 'in',
                    'quantity'           => $item->quantity,
                    'source'             => $source,
                    'reference_id'       => $order->id,
                ]);
            }

            $order->update(['is_stock_deducted' => false]);
        });
    }
}
