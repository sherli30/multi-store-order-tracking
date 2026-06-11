<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Services\OrderService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function __construct(private readonly OrderService $orderService) {}

    /**
     * Get list of orders for a user
     */
    public function index(Request $request)
    {
        try {
            $query = Order::with(['store', 'orderItems.product.images']);

            // Fix IDOR: Users should only see their own orders unless they are admins.
            if (auth()->check() && auth()->user()->role !== 'admin') {
                $query->where('user_id', auth()->id());
            } elseif ($request->has('user_id')) {
                // Keep for admin or specific backwards compatibility if needed, but scoped safely
                if (auth()->check() && auth()->user()->role === 'admin') {
                    $query->where('user_id', $request->user_id);
                }
            } elseif ($request->has('customer_phone') && auth()->user()->role === 'admin') {
                $query->where('customer_phone', $request->customer_phone);
            }

            $orders = $query->orderBy('created_at', 'desc')->get()->map(function ($order) {
                $order->orderItems->each(function ($item) {
                    if ($item->product && $item->product->images) {
                        $item->product->images->transform(function ($image) {
                            $image->image_url = url('storage/' . $image->image_path);
                            return $image;
                        });
                    }
                });
                return $order;
            });

            return response()->json([
                'status'  => 'success',
                'message' => 'Data pesanan berhasil diambil.',
                'data'    => $orders
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Terjadi kesalahan saat mengambil data pesanan.',
            ], 500);
        }
    }

    /**
     * Submit a new order from the mobile app
     * Uses idempotency_key for deduplication if provided
     */
    public function store(\App\Http\Requests\Api\StoreOrderRequest $request)
    {
        try {
            $idempotencyKey = $request->header('Idempotency-Key') ?? null;

            // Check for duplicate order creation if idempotency key provided
            if ($idempotencyKey) {
                $existingOrder = Order::where('user_id', auth()->id())
                    ->where('idempotency_key', $idempotencyKey)
                    ->orderBy('created_at', 'desc')
                    ->first();

                if ($existingOrder) {
                    return response()->json([
                        'status'  => 'success',
                        'message' => 'Pesanan ini sudah dibuat sebelumnya.',
                        'data'    => $existingOrder->load('orderItems'),
                        'is_duplicate' => true
                    ], 201);
                }
            }

            DB::beginTransaction();

            $totalAmount    = 0;
            $totalGrams     = 0;
            $packingCost    = 0;
            $orderItemsData = [];

            // Pre-flight validation: lock and check all products first before creating order
            $productLocks = [];
            $sortedItems = collect($request->items)->sortBy('product_id')->values()->all();
            foreach ($sortedItems as $item) {
                $product = \App\Models\Product::where('id', $item['product_id'])
                    ->available()
                    ->lockForUpdate()
                    ->firstOrFail();

                // Verify the product belongs to the requested store
                if ($product->store_id != $request->store_id) {
                    DB::rollBack();
                    return response()->json([
                        'status'  => 'error',
                        'message' => 'Produk "' . $product->name . '" tidak berasal dari toko yang dipilih.',
                    ], 400);
                }

                if ($product->stock < $item['quantity']) {
                    DB::rollBack();
                    return response()->json([
                        'status'  => 'error',
                        'message' => 'Stok produk "' . $product->name . '" tidak mencukupi.',
                    ], 400);
                }

                $subtotal     = $product->price * $item['quantity'];
                $totalAmount += $subtotal;
                $totalGrams += ($product->weight ?? 1000) * $item['quantity'];

                $orderItemsData[] = [
                    'product_id' => $product->id,
                    'quantity'   => $item['quantity'],
                    'price'      => $product->price,
                    'packing'    => null,
                ];

                $productLocks[$product->id] = $product;
            }

            // ── Shipping Cost Calculation (Modernized) ──────────────────
            $totalKg      = ceil($totalGrams / 1000);
            $store        = \App\Models\Store::findOrFail($request->store_id);
            $originCityId = $store->city_id;
            $destCityId   = $request->destination_city_id; // Frontend must send this ID now

            // Penentuan shipping_type otomatis berdasarkan berat total
            $shippingType = ($totalKg >= 10) ? 'cargo' : 'reguler';

            $rate = \App\Models\ShippingRate::where('origin_city_id', $originCityId)
                ->where('destination_city_id', $destCityId)
                ->whereHas('service', function ($q) use ($shippingType) {
                    $q->where('service_name', 'like', '%' . $shippingType . '%')
                        ->where('is_active', true)
                        ->whereHas('courier', function ($qc) {
                            $qc->where('is_active', true);
                        });
                })
                ->with('service.courier')
                ->first();

            if ($rate) {
                $shippingCost = (int)($totalKg * $rate->cost_per_kg);
                $shippingCourier = $rate->service->courier->name;
            } else {
                DB::rollBack();
                return response()->json([
                    'status'  => 'error',
                    'message' => 'Layanan pengiriman (' . ucfirst($shippingType) . ') ke lokasi tujuan belum tersedia.',
                ], 400);
            }

            $finalTotal = $totalAmount + $shippingCost + $packingCost;

            // Create order
            $order = Order::create([
                'store_id'          => $request->store_id,
                'user_id'           => auth()->id(),
                'customer_name'     => $request->customer_name,
                'customer_email'    => auth()->user()?->email ?? $request->customer_email,
                'customer_phone'    => auth()->user()?->phone ?? $request->customer_phone,
                'shipping_address'  => $request->shipping_address,
                'province'          => $request->province,
                'city'              => $request->city,
                'postal_code'       => $request->postal_code,
                'shipping_type'     => $shippingType,
                'shipping_courier'  => $shippingCourier,
                'shipping_cost'     => $shippingCost,
                'packing_cost'      => 0,
                'notes'             => $request->notes,
                'status'            => Order::STATUS_PENDING,
                'total_amount'      => $finalTotal,
                'midtrans_order_id' => 'ORD-' . strtoupper(substr(uniqid(), -8)) . '-' . time(),
                'idempotency_key'   => $idempotencyKey,
            ]);

            // Create order items
            foreach ($orderItemsData as $itemData) {
                $itemData['order_id'] = $order->id;
                OrderItem::create($itemData);
            }

            // Deduct stock for all items (wrapped in processOrderStock transaction)
            // This is done AFTER order/items are created but BEFORE commit
            // If stock fails, entire transaction rolls back
            try {
                $this->orderService->processOrderStock($order);
            } catch (\App\Exceptions\InsufficientStockException $e) {
                DB::rollBack();
                return response()->json([
                    'status'  => 'error',
                    'message' => 'Stok produk tidak mencukupi (race condition): ' . $e->getMessage(),
                ], 400);
            }

            \App\Models\TrackingHistory::create([
                'order_id' => $order->id,
                'admin_id' => null,
                'status'   => Order::STATUS_PENDING,
                'notes'    => 'Pesanan dibuat melalui Aplikasi Pelanggan.',
            ]);

            DB::commit();

            // Notify Admins (outside transaction to avoid blocking order creation on notification failure)
            try {
                $admins = \App\Models\User::where('role', 'admin')->get();
                \Illuminate\Support\Facades\Notification::send($admins, new \App\Notifications\GeneralOrderNotification([
                    'order_id' => $order->id,
                    'title'    => 'Pesanan Baru: ' . $order->midtrans_order_id,
                    'message'  => 'Pelanggan ' . $order->customer_name . ' membuat pesanan baru senilai Rp ' . number_format($order->total_amount, 0, ',', '.') . '.',
                    'type'     => 'new_order',
                ]));
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::warning('[Order Notification] Failed to notify admins for order #' . $order->id . ': ' . $e->getMessage());
            }

            // Notify Customer (outside transaction)
            try {
                if ($order->customer) {
                    $order->customer->notify(new \App\Notifications\GeneralOrderNotification([
                        'order_id' => $order->id,
                        'title'    => 'Pesanan Berhasil Dibuat',
                        'message'  => 'Pesanan Anda (' . $order->midtrans_order_id . ') berhasil dibuat. Silakan lakukan pembayaran.',
                        'type'     => 'new_order',
                    ]));
                }
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::warning('[Order Notification] Failed to notify customer for order #' . $order->id . ': ' . $e->getMessage());
            }

            return response()->json([
                'status'  => 'success',
                'message' => 'Pesanan ' . $order->midtrans_order_id . ' berhasil dibuat! Silakan segera lakukan pembayaran.',
                'data'    => $order->load('orderItems')
            ], 201);
        } catch (\App\Exceptions\InsufficientStockException $e) {
            DB::rollBack();
            return response()->json([
                'status'  => 'error',
                'message' => $e->getMessage(),
            ], 400);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            DB::rollBack();
            return response()->json([
                'status'  => 'error',
                'message' => 'Produk atau toko tidak ditemukan, mungkin sudah dihapus atau tidak aktif.',
            ], 404);
        } catch (\Exception $e) {
            DB::rollBack();
            \Illuminate\Support\Facades\Log::error('[Order Creation] Exception: ' . $e->getMessage());
            return response()->json([
                'status'  => 'error',
                'message' => 'Gagal memproses pesanan: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Mark order as completed by user
     */
    public function complete(Request $request, $id)
    {
        try {
            $result = DB::transaction(function () use ($id) {
                // Re-fetch with lock for atomicity
                $order = Order::lockForUpdate()
                    ->where('id', $id)
                    ->where('user_id', auth()->id())
                    ->firstOrFail();

                if ($order->status !== Order::STATUS_SHIPPING) {
                    return ['error' => 'Pesanan hanya dapat diselesaikan jika sedang dalam pengiriman.'];
                }

                $order->update(['status' => Order::STATUS_COMPLETED]);

                \App\Models\TrackingHistory::create([
                    'order_id' => $order->id,
                    'status'   => Order::STATUS_COMPLETED,
                    'notes'    => 'Pesanan telah diterima oleh pelanggan.',
                ]);

                return ['order' => $order];
            });

            if (isset($result['error'])) {
                return response()->json([
                    'status' => 'error',
                    'message' => $result['error'],
                ], 400);
            }

            $order = $result['order'];

            // Notify Admins
            try {
                $admins = \App\Models\User::where('role', 'admin')->get();
                \Illuminate\Support\Facades\Notification::send($admins, new \App\Notifications\GeneralOrderNotification([
                    'order_id' => $order->id,
                    'title'    => 'Pesanan Selesai: ' . $order->midtrans_order_id,
                    'message'  => 'Pelanggan telah menerima pesanan dan menyelesaikannya.',
                    'type'     => 'status_update',
                ]));
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::warning('[Order Complete Notification] Failed to notify admins: ' . $e->getMessage());
            }

            // Notify Customer
            try {
                if ($order->customer) {
                    $order->customer->notify(new \App\Notifications\GeneralOrderNotification([
                        'order_id' => $order->id,
                        'title'    => 'Pesanan Selesai',
                        'message'  => "Terima kasih! Pesanan Anda ({$order->midtrans_order_id}) telah selesai. Semoga Anda puas dengan layanan kami.",
                        'type'     => 'status_update',
                    ]));
                }
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::warning('[Order Complete Notification] Failed to notify customer: ' . $e->getMessage());
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Terima kasih! Pesanan ' . $order->midtrans_order_id . ' telah selesai.',
                'data' => $order
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Pesanan tidak ditemukan atau Anda tidak memiliki akses.',
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal menyelesaikan pesanan: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Mobile Admin Scanner Barcode for order lookup.
     */
    public function scan(Request $request, $identifier)
    {
        try {
            $order = Order::with(['trackingHistories.admin', 'store', 'orderItems.product.images', 'transaction'])
                ->where(function ($q) use ($identifier) {
                    $q->where('midtrans_order_id', $identifier)
                        ->orWhere('tracking_number', $identifier);
                })
                ->first();

            if (!$order) {
                return response()->json([
                    'status'  => 'error',
                    'message' => "Pesanan atau resi '{$identifier}' tidak ditemukan.",
                ], 404);
            }

            // Apply image URL transformation for frontend compatibility
            $order->orderItems->each(function ($item) {
                if ($item->product && $item->product->images) {
                    $item->product->images->transform(function ($image) {
                        $image->image_url = url('storage/' . $image->image_path);
                        return $image;
                    });
                }
            });

            return response()->json([
                'status'  => 'success',
                'message' => 'Pesanan berhasil ditemukan.',
                'data'    => $order
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Terjadi kesalahan saat mencari pesanan.',
            ], 500);
        }
    }
}
