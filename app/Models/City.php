<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class City extends Model
{
    use HasFactory;

    protected $fillable = ['id', 'province_id', 'name', 'type', 'postal_code'];

    public function province()
    {
        return $this->belongsTo(Province::class);
    }

    public function stores()
    {
        return $this->hasMany(Store::class);
    }

    public function getFullNameAttribute()
    {
        return $this->name . ' (' . strtoupper($this->type) . ')';
    }
}
