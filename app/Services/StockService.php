<?php

namespace App\Services;

use App\Exceptions\InsufficientStockException;
use App\Models\ProductVariant;
use App\Models\StockMovement;
use Illuminate\Support\Facades\DB;

/**
 * StockService — Centralized, race-condition-safe stock management.
 *
 * ALL stock mutations (add or deduct) should go through this service.
 * Every operation:
 *   1. Wraps in a DB transaction for atomicity.
 *   2. Uses lockForUpdate() to prevent race conditions under concurrent requests.
 *   3. Records a StockMovement log entry.
 *
 * Backward compatibility:
 *   - Existing Order::orderItemsStockOut() / orderItemsStockIn() methods still work.
 *   - This service is the NEW, recommended path for any fresh stock logic.
 *
 * Example (inject via constructor):
 *   public function __construct(private StockService $stockService) {}
 *
 * Example (resolve from IoC):
 *   $service = app(StockService::class);
 */
class StockService
{
    // ─────────────────────────────────────────────
    // Public API
    // ─────────────────────────────────────────────

    /**
     * Add stock to a product variant (type: in).
     *
     * @param  ProductVariant  $variant      The variant to add stock to.
     * @param  int             $qty          Amount to add. Must be > 0.
     * @param  string          $source       Movement source label (e.g. 'manual_adjustment', 'initial_stock').
     * @param  int|null        $referenceId  Optional Order ID or other reference.
     *
     * @return StockMovement                 The created stock movement log.
     */
    public function addStock(
        ProductVariant $variant,
        int            $qty,
        string         $source      = 'manual_adjustment',
        ?int           $referenceId = null,
    ): StockMovement {
        if ($qty <= 0) {
            throw new \InvalidArgumentException("Qty harus lebih dari 0, diterima: {$qty}.");
        }

        return DB::transaction(function () use ($variant, $qty, $source, $referenceId) {
            // Re-fetch with a write lock to prevent race condition
            $locked = ProductVariant::lockForUpdate()->findOrFail($variant->id);

            $locked->increment('stock', $qty);

            return StockMovement::create([
                'product_variant_id' => $locked->id,
                'type'               => 'in',
                'quantity'           => $qty,
                'source'             => $source,
                'reference_id'       => $referenceId,
            ]);
        });
    }

    /**
     * Deduct stock from a product variant (type: out).
     * Throws InsufficientStockException if stock would go negative.
     *
     * @param  ProductVariant  $variant      The variant to deduct from.
     * @param  int             $qty          Amount to deduct. Must be > 0.
     * @param  string          $source       Movement source label.
     * @param  int|null        $referenceId  Optional Order ID or other reference.
     *
     * @return StockMovement                 The created stock movement log.
     *
     * @throws InsufficientStockException
     */
    public function deductStock(
        ProductVariant $variant,
        int            $qty,
        string         $source      = 'manual_adjustment',
        ?int           $referenceId = null,
    ): StockMovement {
        if ($qty <= 0) {
            throw new \InvalidArgumentException("Qty harus lebih dari 0, diterima: {$qty}.");
        }

        return DB::transaction(function () use ($variant, $qty, $source, $referenceId) {
            // Re-fetch with a write lock to prevent race conditions
            $locked = ProductVariant::lockForUpdate()->findOrFail($variant->id);

            if ($locked->stock < $qty) {
                // Determine name combining product and variant names
                $productName = $locked->product->name . ' - ' . $locked->name;
                throw new InsufficientStockException($productName, $qty, $locked->stock);
            }

            $locked->decrement('stock', $qty);

            return StockMovement::create([
                'product_variant_id' => $locked->id,
                'type'               => 'out',
                'quantity'           => $qty,
                'source'             => $source,
                'reference_id'       => $referenceId,
            ]);
        });
    }

    /**
     * Get the total net stock based on movement history.
     * Useful for auditing — should match variant.stock.
     *
     * @param  ProductVariant $variant
     * @return int
     */
    public function computeStockFromLog(ProductVariant $variant): int
    {
        $in  = StockMovement::where('product_variant_id', $variant->id)
                            ->where('type', 'in')
                            ->sum('quantity');

        $out = StockMovement::where('product_variant_id', $variant->id)
                            ->where('type', 'out')
                            ->sum('quantity');

        return (int) ($in - $out);
    }
}
