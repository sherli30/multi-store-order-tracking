<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShippingRate extends Model
{
    protected $fillable = [
        'shipping_service_id',
        'origin_province_id',
        'origin_city_id',
        'destination_province_id',
        'destination_city_id',
        'min_weight',
        'max_weight',
        'cost_per_kg',
        'etd_min',
        'etd_max',
        'is_active',
    ];

    protected $casts = [
        'cost_per_kg' => 'decimal:2',
        'min_weight' => 'decimal:2',
        'max_weight' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function service()
    {
        return $this->belongsTo(ShippingService::class, 'shipping_service_id');
    }

    public function originProvince()
    {
        return $this->belongsTo(Province::class, 'origin_province_id');
    }

    public function destinationProvince()
    {
        return $this->belongsTo(Province::class, 'destination_province_id');
    }

    public function originCity()
    {
        return $this->belongsTo(City::class, 'origin_city_id');
    }

    public function destinationCity()
    {
        return $this->belongsTo(City::class, 'destination_city_id');
    }
}
