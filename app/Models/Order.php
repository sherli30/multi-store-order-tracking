<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Product;
use App\Models\StockMovement;
use App\Services\StatusService;

class Order extends Model
{
    use HasFactory;

    // ─── Order Status Constants (All Indonesian labels) ──────────────────
    const STATUS_PENDING = StatusService::ORDER_PENDING;
    const STATUS_PERLU_DIPROSES = StatusService::ORDER_PROCESSING_NEEDED;
    const STATUS_PROCESSING = StatusService::ORDER_PROCESSING;
    const STATUS_SHIPPING = StatusService::ORDER_SHIPPING;
    const STATUS_COMPLETED = StatusService::ORDER_COMPLETED;
    const STATUS_CANCELLED = StatusService::ORDER_CANCELLED;

    protected $fillable = [
        'store_id',
        'user_id',
        'customer_name',
        'customer_email',
        'customer_phone',
        'midtrans_order_id',
        'payment_type',
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
        'payment_status',
        'cancel_reason',
        'idempotency_key',
        'webhook_attempts',
        'last_webhook_attempt',
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
            'shipping_cost' => 'decimal:2',
            'packing_cost' => 'decimal:2',
            'is_stock_deducted' => 'boolean',
            'last_webhook_attempt' => 'datetime',
        ];
    }

    /**
     * Get a human-readable order reference number.
     * Uses midtrans_order_id when available, falls back to "#ID".
     */
    public function getOrderNumberAttribute(): string
    {
        return $this->midtrans_order_id ?? ('#' . $this->id);
    }

    /**
     * Get human-readable status label (Indonesian).
     */
    public function getStatusLabelAttribute(): string
    {
        return StatusService::getOrderLabel($this->status);
    }

    /**
     * Get badge CSS class for this order status.
     */
    public function getStatusBadgeClassAttribute(): string
    {
        return StatusService::getOrderBadgeClass($this->status);
    }

    /**
     * Get the total weight of the order in grams.
     */
    public function getTotalWeightAttribute(): int
    {
        return (int) $this->orderItems->sum(function ($item) {
            // Mengalikan jumlah beli dengan berat masing-masing produk
            return $item->quantity * ($item->product->weight ?? 0);
        });
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
        return $this->belongsTo(User::class, 'user_id')->withTrashed();
    }

    /**
     * Get webhook failures for this order.
     */
    public function webhookFailures()
    {
        return $this->hasMany(WebhookFailure::class);
    }

    /**
     * Deduct stock for all order items and record outgoing stock movements.
     * Atomically checks is_stock_deducted under lock to prevent double-deduction.
     */
    public function orderItemsStockOut()
    {
        \Illuminate\Support\Facades\DB::transaction(function () {
            // Re-fetch with lock FIRST, then check is_stock_deducted atomically
            $lockedOrder = Order::lockForUpdate()->find($this->id);

            if ($lockedOrder->is_stock_deducted) {
                return;
            }

            foreach ($lockedOrder->orderItems as $item) {
                $product = $item->product;
                if ($product) {
                    $lockedProduct = Product::lockForUpdate()->find($product->id);
                    if ($lockedProduct) {
                        if ($lockedProduct->stock < $item->quantity) {
                            throw new \App\Exceptions\InsufficientStockException(
                                $lockedProduct->name,
                                $item->quantity,
                                $lockedProduct->stock,
                            );
                        }

                        $lockedProduct->decrement('stock', $item->quantity);

                        StockMovement::create([
                            'product_id'   => $lockedProduct->id,
                            'type'         => 'out',
                            'quantity'     => $item->quantity,
                            'source'       => 'purchase',
                            'reference_id' => $lockedOrder->id,
                        ]);
                    }
                }
            }

            $lockedOrder->update(['is_stock_deducted' => true]);
        });
    }

    /**
     * Restore stock for all order items and record incoming stock movements.
     * Atomically checks is_stock_deducted under lock to prevent double-restoration.
     */
    public function orderItemsStockIn(string $source)
    {
        \Illuminate\Support\Facades\DB::transaction(function () use ($source) {
            // Re-fetch with lock FIRST, then check is_stock_deducted atomically
            $lockedOrder = Order::lockForUpdate()->find($this->id);

            if (!$lockedOrder->is_stock_deducted) {
                return;
            }

            foreach ($lockedOrder->orderItems as $item) {
                $product = $item->product;
                if ($product) {
                    $lockedProduct = Product::lockForUpdate()->find($product->id);
                    if ($lockedProduct) {
                        $lockedProduct->increment('stock', $item->quantity);

                        StockMovement::create([
                            'product_id'   => $lockedProduct->id,
                            'type'         => 'in',
                            'quantity'     => $item->quantity,
                            'source'       => $source,
                            'reference_id' => $lockedOrder->id,
                        ]);
                    }
                }
            }

            $lockedOrder->update(['is_stock_deducted' => false]);
        });
    }
}
