<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShippingService extends Model
{
    protected $fillable = ['courier_id', 'service_name', 'min_weight', 'description', 'is_active'];

    protected $casts = [
        'is_active' => 'boolean',
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
