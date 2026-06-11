<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Services\StatusService;

class TrackingHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'admin_id',
        'status',
        'notes',
        'refund_method',
        'refund_reason',
        'payment_method',
        'metadata',
    ];

    protected $casts = [
        'metadata' => 'array',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id')->withTrashed();
    }

    /**
     * Get human-readable status label (Indonesian).
     */
    public function getStatusLabelAttribute(): string
    {
        return StatusService::getOrderLabel($this->status);
    }

    /**
     * Get badge CSS class for this status.
     */
    public function getStatusBadgeClassAttribute(): string
    {
        return StatusService::getOrderBadgeClass($this->status);
    }
}

