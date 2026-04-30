<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Store extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'logo',
        'description',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    protected $appends = ['address', 'logo_url'];

    /**
     * Karena semua toko memiliki alamat yang sama, kita kembalikan alamat default.
     */
    public function getAddressAttribute(): string
    {
        return "Jl. Kaliurang No. 45, Sleman, Yogyakarta";
    }

    /**
     * Mengembalikan URL lengkap untuk logo toko.
     */
    public function getLogoUrlAttribute(): string
    {
        if ($this->logo) {
            return "http://192.168.22.39:8000/storage/" . $this->logo;
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

    // ─────────────────────────────────────────────
    // Relationships
    // ─────────────────────────────────────────────

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
}
