<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Services\StatusService;

class Transaction extends Model
{
    use HasFactory;

    // ─── Transaction Status Constants (All normalized, not raw Midtrans) ─
    const STATUS_PENDING = StatusService::TRANSACTION_PENDING;  // 'pending'
    const STATUS_PAID = StatusService::TRANSACTION_PAID;        // 'paid'
    const STATUS_FAILED = StatusService::TRANSACTION_FAILED;    // 'failed'
    const STATUS_REFUND = StatusService::TRANSACTION_REFUND;    // 'refund'

    protected $fillable = [
        'invoice_id',
        'order_id',
        'transaction_id',
        'payment_method',
        'payment_details',
        'amount',
        'status',
        'payment_date',
        'refunded_at',
        'notes',
    ];

    protected $casts = [
        'amount'          => 'decimal:2',
        'payment_date'    => 'datetime',
        'refunded_at'     => 'datetime',
        'payment_details' => 'array',
    ];

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Backward-compatible accessor: the DB column was renamed from
     * 'transaction_code' to 'transaction_id'. This alias ensures views
     * that reference $transaction->transaction_code still resolve correctly.
     */
    public function getTransactionCodeAttribute(): ?string
    {
        return $this->transaction_id;
    }

    /**
     * Get Indonesian status label
     */
    public function getStatusLabelAttribute(): string
    {
        return StatusService::getTransactionLabel($this->status);
    }

    /**
     * Get badge CSS class for this transaction status
     */
    public function getStatusBadgeClassAttribute(): string
    {
        return StatusService::getTransactionBadgeClass($this->status);
    }
}

