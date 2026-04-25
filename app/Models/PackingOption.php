<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PackingOption extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'name',
        'extra_price',
        'is_active',
    ];

    protected $casts = [
        'extra_price' => 'decimal:2',
        'is_active'   => 'boolean',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
