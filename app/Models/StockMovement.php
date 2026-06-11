<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockMovement extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'type',        // 'in' | 'out'
        'quantity',    // amount moved
        'source',      // 'initial_stock' | 'purchase' | 'cancellation' | 'refund' | 'manual_adjustment'
        'reference_id', // Order ID or null
        'note',         // reason for movement
    ];

    protected $casts = [
        'quantity'     => 'integer',
        'reference_id' => 'integer',
    ];

    // ─────────────────────────────────────────────
    // Relationships
    // ─────────────────────────────────────────────

    /**
     * The product that this stock movement belongs to.
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class)->withTrashed();
    }

    // ─────────────────────────────────────────────
    // Accessors
    // ─────────────────────────────────────────────

    /**
     * Human-readable label for the movement source.
     */
    public function getSourceLabelAttribute(): string
    {
        return match ($this->source) {
            'initial_stock'    => 'Stok Awal',
            'purchase'         => 'Penjualan',
            'cancellation'     => 'Pembatalan Pesanan',
            'refund'           => 'Refund',
            'manual_adjustment'=> 'Penyesuaian Manual',
            'failed'           => 'Pembayaran Gagal',
            'deny'             => 'Pembayaran Ditolak',
            'cancel'           => 'Pembayaran Dibatalkan',
            'expire'           => 'Pembayaran Kedaluwarsa',
            'failure'          => 'Pembayaran Gagal',
            default            => ucfirst(str_replace('_', ' ', $this->source)),
        };
    }

    /**
     * Return the direction label (Masuk / Keluar).
     */
    public function getTypeLabelAttribute(): string
    {
        return $this->type === 'in' ? 'Masuk' : 'Keluar';
    }
}
