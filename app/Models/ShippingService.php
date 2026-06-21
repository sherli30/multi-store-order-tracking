<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShippingService extends Model
{
    protected $fillable = ['courier_id', 'service_name', 'service_code', 'min_weight', 'description', 'estimated_delivery', 'is_active'];

    protected $casts = [
        'is_active' => 'boolean',
        'min_weight' => 'decimal:2',
    ];

    public function courier()
    {
        return $this->belongsTo(Courier::class);
    }

    public function rates()
    {
        return $this->hasMany(ShippingRate::class);
    }
}
