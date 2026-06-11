<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShippingRate extends Model
{
    protected $fillable = ['shipping_service_id', 'origin_city_id', 'destination_city_id', 'cost_per_kg', 'etd_min', 'etd_max'];

    protected $casts = [
        'cost_per_kg' => 'decimal:2',
    ];

    public function service()
    {
        return $this->belongsTo(ShippingService::class, 'shipping_service_id');
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
