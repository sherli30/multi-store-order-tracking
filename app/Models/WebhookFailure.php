<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WebhookFailure extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'webhook_type',
        'failure_reason',
        'payload',
        'attempt_count',
        'first_failed_at',
        'last_failed_at',
        'resolved',
        'resolved_at',
    ];

    protected $casts = [
        'payload' => 'array',
        'first_failed_at' => 'datetime',
        'last_failed_at' => 'datetime',
        'resolved_at' => 'datetime',
        'resolved' => 'boolean',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Get unresolved webhook failures for manual review
     */
    public static function unresolved()
    {
        return self::where('resolved', false)->orderBy('last_failed_at', 'desc');
    }

    /**
     * Increment attempt count and update last_failed_at
     */
    public function recordAttempt(string $reason): void
    {
        $this->update([
            'attempt_count' => $this->attempt_count + 1,
            'last_failed_at' => now(),
            'failure_reason' => $reason,
        ]);
    }

    /**
     * Mark this webhook failure as resolved
     */
    public function markResolved(): void
    {
        $this->update([
            'resolved' => true,
            'resolved_at' => now(),
        ]);
    }
}
