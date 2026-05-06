<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductImage extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'image_path',
        'is_primary',
        'sort_order',
    ];

    protected $casts = [
        'is_primary' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Otomatis hapus file foto dari storage saat record ProductImage dihapus.
     */
    protected static function booted()
    {
        static::deleting(function ($image) {
            if ($image->image_path && \Illuminate\Support\Facades\Storage::disk('public')->exists($image->image_path)) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($image->image_path);
            }
        });
    }
}
