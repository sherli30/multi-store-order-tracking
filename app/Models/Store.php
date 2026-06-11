<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Store extends Model
{
    protected $fillable = [
        'name',
        'logo',
        'address',
        'province_id',
        'city_id',
        'phone',
        'operational_hours',
        'description',
        'is_active',
    ];

    public function province()
    {
        return $this->belongsTo(Province::class);
    }

    public function city()
    {
        return $this->belongsTo(City::class);
    }

    protected $casts = [
        'is_active' => 'boolean',
    ];

    protected $appends = ['logo_url'];

    /**
     * Mengembalikan URL lengkap untuk logo toko.
     */
    public function getLogoUrlAttribute(): string
    {
        if ($this->logo) {
            return asset("storage/" . $this->logo);
        }
        // Fallback jika logo kosong
        return "https://ui-avatars.com/api/?name=" . urlencode($this->name) . "&background=random&size=128";
    }

    // ─────────────────────────────────────────────
    // Local Scopes
    // ─────────────────────────────────────────────

    /**
     * Scope: only stores where is_active = true.
     *
     * Usage: Store::active()->get()
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }


    /**
     * Get all categories that belong to this store.
     * Each category is exclusively owned by one store.
     */
    public function productCategories(): HasMany
    {
        return $this->hasMany(ProductCategory::class);
    }

    /**
     * Get only active categories for this store.
     */
    public function activeCategories(): HasMany
    {
        return $this->hasMany(ProductCategory::class)->where('is_active', true);
    }

    /**
     * Get all products that belong to this store (directly, via store_id on products).
     * Canonical path is Store → Category → Product, but this is a convenience relation.
     */
    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    /**
     * Get only active products for this store.
     */
    public function activeProducts(): HasMany
    {
        return $this->hasMany(Product::class)->where('is_active', true);
    }

    /**
     * Get the orders associated with the store.
     */
    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    /**
     * Otomatis hapus file logo saat toko dihapus.
     */
    protected static function booted()
    {
        static::deleting(function ($store) {
            if ($store->logo && \Illuminate\Support\Facades\Storage::disk('public')->exists($store->logo)) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($store->logo);
            }
        });
    }
}
