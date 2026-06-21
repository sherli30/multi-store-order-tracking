<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Courier extends Model
{
    protected $fillable = ['name', 'code', 'logo', 'description', 'contact_person', 'phone_number', 'email', 'is_active'];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function services()
    {
        return $this->hasMany(ShippingService::class);
    }
}
