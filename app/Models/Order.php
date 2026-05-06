<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\ProductVariant;
use App\Models\StockMovement;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'store_id',
        'user_id',
        'customer_name',
        'customer_email',
        'customer_phone',
        'order_number',
        'shipping_address',
        'province',
        'city',
        'postal_code',
        'shipping_type',
        'shipping_cost',
        'packing_cost',
        'shipping_courier',
        'tracking_number',
        'status',
        'notes',
        'is_stock_deducted',
        'total_amount',
        'snap_token',
        'midtrans_order_id',
        'payment_status',
        'cancel_reason',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'total_amount' => 'decimal:2',
        ];
    }

    /**
     * Get the store that owns the order.
     */
    public function store()
    {
        return $this->belongsTo(Store::class);
    }

    /**
     * Get the items for the order.
     */
    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Get the transaction associated with the order.
     */
    public function transaction()
    {
        return $this->hasOne(Transaction::class);
    }

    /**
     * Get the tracking histories for the order.
     */
    public function trackingHistories()
    {
        return $this->hasMany(TrackingHistory::class);
    }

    /**
     * Get the customer (user) that owns the order.
     */
    public function customer()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Deduct stock for all order items and record outgoing stock movements.
     */
    public function orderItemsStockOut()
    {
        \Illuminate\Support\Facades\DB::transaction(function () {
            if ($this->is_stock_deducted) {
                return;
            }

            foreach ($this->orderItems as $item) {
                $variant = $item->productVariant;
                if ($variant) {
                    // Lock for update
                    $lockedVariant = ProductVariant::lockForUpdate()->find($variant->id);
                    if ($lockedVariant) {
                        $lockedVariant->decrement('stock', $item->quantity);

                        StockMovement::create([
                            'product_variant_id' => $lockedVariant->id,
                            'type'               => 'out',
                            'quantity'           => $item->quantity,
                            'source'             => 'purchase',
                            'reference_id'       => $this->id,
                        ]);
                    }
                }
            }

            $this->update(['is_stock_deducted' => true]);
        });
    }

    /**
     * Restore stock for all order items and record incoming stock movements.
     */
    public function orderItemsStockIn(string $source)
    {
        \Illuminate\Support\Facades\DB::transaction(function () use ($source) {
            if (!$this->is_stock_deducted) {
                return;
            }

            foreach ($this->orderItems as $item) {
                $variant = $item->productVariant;
                if ($variant) {
                    $lockedVariant = ProductVariant::lockForUpdate()->find($variant->id);
                    if ($lockedVariant) {
                        $lockedVariant->increment('stock', $item->quantity);

                        StockMovement::create([
                            'product_variant_id' => $lockedVariant->id,
                            'type'               => 'in',
                            'quantity'           => $item->quantity,
                            'source'             => $source,
                            'reference_id'       => $this->id,
                        ]);
                    }
                }
            }

            $this->update(['is_stock_deducted' => false]);
        });
    }
}
