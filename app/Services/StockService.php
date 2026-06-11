<?php

namespace App\Services;

use App\Exceptions\InsufficientStockException;
use App\Models\Product;
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
     * Add stock to a product (type: in).
     *
     * @param  Product  $product      The product to add stock to.
     * @param  int      $qty          Amount to add. Must be > 0.
     * @param  string   $source       Movement source label (e.g. 'manual_adjustment', 'initial_stock').
     * @param  int|null $referenceId  Optional Order ID or other reference.
     *
     * @return StockMovement          The created stock movement log.
     */
    public function addStock(
        Product $product,
        int     $qty,
        string  $source      = 'manual_adjustment',
        ?int    $referenceId = null,
        ?string $note        = null,
    ): StockMovement {
        if ($qty <= 0) {
            throw new \InvalidArgumentException("Qty harus lebih dari 0, diterima: {$qty}.");
        }

        return DB::transaction(function () use ($product, $qty, $source, $referenceId, $note) {
            // Re-fetch with a write lock to prevent race condition
            $locked = Product::lockForUpdate()->findOrFail($product->id);

            $locked->increment('stock', $qty);

            return StockMovement::create([
                'product_id'   => $locked->id,
                'type'         => 'in',
                'quantity'     => $qty,
                'source'       => $source,
                'reference_id' => $referenceId,
                'note'         => $note,
            ]);
        });
    }

    /**
     * Deduct stock from a product (type: out).
     * Throws InsufficientStockException if stock would go negative.
     *
     * @param  Product     $product      The product to deduct from.
     * @param  int         $qty          Amount to deduct. Must be > 0.
     * @param  string      $source       Movement source label.
     * @param  int|null    $referenceId  Optional Order ID or other reference.
     * @param  string|null $note         Optional note for the movement.
     *
     * @return StockMovement             The created stock movement log.
     *
     * @throws InsufficientStockException
     */
    public function deductStock(
        Product $product,
        int     $qty,
        string  $source      = 'manual_adjustment',
        ?int    $referenceId = null,
        ?string $note        = null,
    ): StockMovement {
        if ($qty <= 0) {
            throw new \InvalidArgumentException("Qty harus lebih dari 0, diterima: {$qty}.");
        }

        return DB::transaction(function () use ($product, $qty, $source, $referenceId, $note) {
            // Re-fetch with a write lock to prevent race conditions
            $locked = Product::lockForUpdate()->findOrFail($product->id);

            if ($locked->stock < $qty) {
                throw new InsufficientStockException($locked->name, $qty, $locked->stock);
            }

            $locked->decrement('stock', $qty);

            return StockMovement::create([
                'product_id'   => $locked->id,
                'type'         => 'out',
                'quantity'     => $qty,
                'source'       => $source,
                'reference_id' => $referenceId,
                'note'         => $note,
            ]);
        });
    }

    /**
     * Get the total net stock based on movement history.
     * Useful for auditing — should match product.stock.
     *
     * @param  Product $product
     * @return int
     */
    public function computeStockFromLog(Product $product): int
    {
        $in  = StockMovement::where('product_id', $product->id)
                            ->where('type', 'in')
                            ->sum('quantity');

        $out = StockMovement::where('product_id', $product->id)
                            ->where('type', 'out')
                            ->sum('quantity');

        return (int) ($in - $out);
    }
}
