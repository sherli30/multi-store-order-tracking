<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'store_id',
        'category_id',
        'name',
        'slug',
        'is_active',
        'is_featured',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_featured' => 'boolean',
    ];

    /**
     * The accessors to append to the model's array form.
     */
    protected $appends = [
        'price',
        'stock',
        'sold_count',
    ];

    /**
     * Get the total quantity sold for this product.
     */
    public function getSoldCountAttribute(): int
    {
        return (int) \App\Models\OrderItem::whereHas('productVariant', function ($q) {
            $q->where('product_id', $this->id);
        })->sum('quantity');
    }

    // ─────────────────────────────────────────────
    // Local Scopes
    // ─────────────────────────────────────────────

    /**
     * Scope: only products where is_active = true.
     *
     * Usage: Product::active()->get()
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope: products that are FULLY AVAILABLE to customers.
     * Checks the full Store → Category → Product chain:
     *   product.is_active = true
     *   AND category.is_active = true
     *   AND store.is_active = true
     *
     * Uses whereHas to avoid cascade-modifying child records when parent is inactive.
     * This is the RECOMMENDED filter for any customer-facing or storefront queries.
     *
     * Usage: Product::available()->get()
     */
    public function scopeAvailable(Builder $query): Builder
    {
        return $query
            ->where('is_active', true)
            ->whereHas('category', function (Builder $q) {
                $q->where('is_active', true)
                  ->whereHas('store', fn (Builder $sq) => $sq->where('is_active', true));
            });
    }

    /**
     * Scope: products with low stock (at least one active variant has 1-10 stock).
     */
    public function scopeLowStock(Builder $query): Builder
    {
        return $query->whereHas('variants', function (Builder $q) {
            $q->where('is_active', true)
              ->where('stock', '>', 0)
              ->where('stock', '<=', 10);
        });
    }

    /**
     * Scope: products that are out of stock (all active variants have 0 stock).
     */
    public function scopeOutOfStock(Builder $query): Builder
    {
        return $query->whereDoesntHave('variants', function (Builder $q) {
            $q->where('is_active', true)
              ->where('stock', '>', 0);
        });
    }

    /**
     * Scope: products with available stock (at least one active variant has > 10 stock).
     */
    public function scopeAvailableStock(Builder $query): Builder
    {
        return $query->whereHas('variants', function (Builder $q) {
            $q->where('is_active', true)
              ->where('stock', '>', 10);
        });
    }

    // ─────────────────────────────────────────────
    // Relationships
    // ─────────────────────────────────────────────

    /**
     * Get the store that owns this product.
     */
    public function store(): BelongsTo
    {
        return $this->belongsTo(Store::class);
    }

    /**
     * Get the category this product belongs to.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(ProductCategory::class, 'category_id');
    }

    /**
     * Get the variants for this product.
     */
    public function variants(): HasMany
    {
        return $this->hasMany(ProductVariant::class);
    }

    /**
     * Get the images for this product.
     */
    public function images(): HasMany
    {
        return $this->hasMany(ProductImage::class)->orderBy('sort_order');
    }

    /**
     * Get the structured descriptions for this product.
     */
    public function descriptions(): HasMany
    {
        return $this->hasMany(ProductDescription::class)->orderBy('sort_order');
    }

    /**
     * Get the specifications for this product.
     */
    public function specifications(): HasMany
    {
        return $this->hasMany(ProductSpecification::class)->orderBy('sort_order');
    }

    /**
     * Get the packing options for this product.
     */
    public function packingOptions(): HasMany
    {
        return $this->hasMany(PackingOption::class);
    }

    // ─────────────────────────────────────────────
    // Accessors
    // ─────────────────────────────────────────────

    /**
     * Get the primary image of the product.
     */
    public function getPrimaryImageAttribute()
    {
        return $this->images->where('is_primary', true)->first() 
            ?? $this->images->first();
    }

    /**
     * Get the image path for compatibility with old views.
     */
    public function getImageAttribute()
    {
        return $this->primary_image?->image_path;
    }

    /**
     * Get the total stock of all active variants.
     */
    public function getStockAttribute(): int
    {
        return (int) $this->variants()->where('is_active', true)->sum('stock');
    }

    /**
     * Get the base price or price range string.
     */
    public function getFormattedPriceAttribute(): string
    {
        $activeVariants = $this->variants()->where('is_active', true)->get();
        if ($activeVariants->isEmpty()) {
            return 'Rp 0';
        }

        if ($activeVariants->count() === 1) {
            return 'Rp ' . number_format($activeVariants->first()->price, 0, ',', '.');
        }

        $minPrice = $activeVariants->min('price');
        $maxPrice = $activeVariants->max('price');

        if ($minPrice === $maxPrice) {
            return 'Rp ' . number_format($minPrice, 0, ',', '.');
        }

        return 'Rp ' . number_format($minPrice, 0, ',', '.') . ' - ' . number_format($maxPrice, 0, ',', '.');
    }

    /**
     * Get the raw base price.
     */
    public function getPriceAttribute()
    {
        return $this->variants()->where('is_active', true)->min('price') ?? 0;
    }

    /**
     * Otomatis hapus semua foto produk saat produk dihapus secara permanen.
     */
    protected static function booted()
    {
        static::forceDeleted(function ($product) {
            foreach ($product->images as $image) {
                // Memicu hook deleting pada model ProductImage
                $image->delete();
            }
        });
    }
}
