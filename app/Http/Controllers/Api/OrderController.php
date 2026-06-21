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
            $query = Order::with(['store', 'orderItems.product.images', 'invoice.orders.store', 'invoice.orders.orderItems.product.images', 'transaction', 'trackingHistories.admin']);

            // Fix IDOR: Users should only see their own orders unless they are admins.
            if (auth()->check() && auth()->user()->role !== 'administrator') {
                $query->where('user_id', auth()->id());
            } elseif ($request->has('user_id')) {
                // Keep for admin or specific backwards compatibility if needed, but scoped safely
                if (auth()->check() && auth()->user()->role === 'administrator') {
                    $query->where('user_id', $request->user_id);
                }
            } elseif ($request->has('customer_phone') && auth()->user()->role === 'administrator') {
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
     * Get a single order for a user
     */
    public function show($id)
    {
        try {
            $order = Order::with(['store', 'orderItems.product.images', 'invoice.orders.store', 'invoice.orders.orderItems.product.images', 'transaction', 'trackingHistories.admin'])
                ->where('user_id', auth()->id())
                ->findOrFail($id);

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
                'message' => 'Data pesanan berhasil diambil.',
                'data'    => $order
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Pesanan tidak ditemukan.',
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Terjadi kesalahan saat mengambil data pesanan.',
            ], 500);
        }
    }

    /**
     * Get a single invoice for a user
     */
    public function showInvoice($id)
    {
        try {
            $invoice = \App\Models\Invoice::with(['orders.store', 'orders.orderItems.product.images', 'orders.transaction', 'orders.trackingHistories.admin'])
                ->where('user_id', auth()->id())
                ->findOrFail($id);

            $invoice->orders->each(function ($order) {
                $order->orderItems->each(function ($item) {
                    if ($item->product && $item->product->images) {
                        $item->product->images->transform(function ($image) {
                            $image->image_url = url('storage/' . $image->image_path);
                            return $image;
                        });
                    }
                });
            });

            return response()->json([
                'status'  => 'success',
                'message' => 'Data invoice berhasil diambil.',
                'data'    => $invoice
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Invoice tidak ditemukan.',
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Terjadi kesalahan saat mengambil data invoice.',
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
                if ((int)$product->store_id !== (int)$request->store_id) {
                    DB::rollBack();
                    return response()->json([
                        'status'  => 'error',
                        'message' => '[V1 API ERROR] Produk "' . $product->name . '" tidak berasal dari toko yang dipilih.',
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
                $admins = \App\Models\User::where('role', 'administrator')->get();
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
                'message' => 'Terjadi kesalahan pada server saat membuat pesanan.',
            ], 500);
        }
    }

    /**
     * Cancel an order.
     */
    public function cancel(Request $request, $id)
    {
        try {
            $order = Order::where('user_id', auth()->id())->findOrFail($id);

            if ($order->status !== Order::STATUS_PENDING) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Pesanan hanya dapat dibatalkan jika statusnya masih Belum Bayar.'
                ], 400);
            }

            $reason = $request->input('reason', 'Dibatalkan oleh pembeli melalui aplikasi.');

            $order = app(\App\Services\OrderService::class)->cancelOrder($order, $reason, null);

            // Notify Admins
            try {
                $admins = \App\Models\User::where('role', 'administrator')->get();
                \Illuminate\Support\Facades\Notification::send($admins, new \App\Notifications\GeneralOrderNotification([
                    'order_id' => $order->id,
                    'title'    => 'Pesanan Dibatalkan: ' . ($order->midtrans_order_id ?? $order->order_number),
                    'message'  => "Pesanan {$order->order_number} dibatalkan oleh pembeli: " . $reason,
                    'type'     => 'cancel',
                ]));
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::warning('[Order Notification] Failed to notify admins: ' . $e->getMessage());
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Pesanan berhasil dibatalkan.',
                'data' => $order
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Pesanan tidak ditemukan atau Anda tidak memiliki akses.'
            ], 404);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('[API Order Cancel] Exception: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan saat membatalkan pesanan.'
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
                $admins = \App\Models\User::where('role', 'administrator')->get();
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

    /**
     * V2: Submit a multi-store checkout
     */
    public function storeMulti(Request $request)
    {
        try {
            $request->validate([
                'customer_name' => 'required|string',
                'customer_phone' => 'required|string',
                'shipping_address' => 'required|string',
                'province' => 'required|string',
                'city' => 'required|string',
                'postal_code' => 'required|string',
                'payment_method' => 'nullable|string',
                'notes' => 'nullable|string',
                'store_orders' => 'required|array',
                'store_orders.*.store_id' => 'required|exists:stores,id',
                'store_orders.*.shipping_type' => 'required|string',
                'store_orders.*.shipping_cost' => 'required|numeric',
                'store_orders.*.items' => 'required|array',
                'store_orders.*.items.*.product_id' => 'required|exists:products,id',
                'store_orders.*.items.*.quantity' => 'required|integer|min:1',
            ]);

            DB::beginTransaction();

            // Prepare Invoice placeholder
            $invoiceNumber = 'INV/' . date('Ymd') . '/' . strtoupper(substr(uniqid(), -8));
            $invoice = \App\Models\Invoice::create([
                'invoice_number' => $invoiceNumber,
                'user_id' => auth()->id(),
                'total_product_amount' => 0,
                'total_shipping_amount' => 0,
                'grand_total' => 0,
                'payment_status' => \App\Models\Order::STATUS_PENDING,
                'payment_type' => $request->payment_method ?? 'midtrans',
                'midtrans_order_id' => str_replace('/', '-', $invoiceNumber),
            ]);

            $invoiceTotalProduct = 0;
            $invoiceTotalShipping = 0;

            foreach ($request->store_orders as $storeOrder) {
                $storeTotalAmount = 0;
                $storeTotalKg = 0;
                $orderItemsData = [];

                foreach ($storeOrder['items'] as $item) {
                    $product = \App\Models\Product::where('id', $item['product_id'])
                        ->available()
                        ->lockForUpdate()
                        ->firstOrFail();

                    if ((int)$product->store_id !== (int)$storeOrder['store_id']) {
                        DB::rollBack();
                        return response()->json(['status' => 'error', 'message' => 'Produk "' . $product->name . '" (Store: '.$product->store_id.') tidak berasal dari toko yang dipilih (Store: '.$storeOrder['store_id'].').'], 400);
                    }

                    if ($product->stock < $item['quantity']) {
                        DB::rollBack();
                        return response()->json(['status' => 'error', 'message' => 'Stok produk "' . $product->name . '" tidak mencukupi.'], 400);
                    }

                    $subtotal = $product->price * $item['quantity'];
                    $storeTotalAmount += $subtotal;
                    $storeTotalKg += ($product->weight ?? 1.0) * $item['quantity'];

                    $orderItemsData[] = [
                        'product_id' => $product->id,
                        'quantity' => $item['quantity'],
                        'price' => $product->price,
                        'packing' => null,
                        'product' => $product
                    ];
                }

                $invoiceTotalProduct += $storeTotalAmount;
                $invoiceTotalShipping += $storeOrder['shipping_cost'];

                $storeModel = \App\Models\Store::find($storeOrder['store_id']);
                $orderNumber = 'ORD/' . date('Ymd') . '/' . strtoupper(substr(uniqid(), -6));

                $order = Order::create([
                    'user_id' => auth()->id(),
                    'store_id' => $storeOrder['store_id'],
                    'invoice_id' => $invoice->id,
                    'order_number' => $orderNumber,
                    'customer_name' => $request->customer_name,
                    'customer_phone' => $request->customer_phone,
                    'shipping_address' => $request->shipping_address,
                    'province' => $request->province,
                    'city' => $request->city,
                    'postal_code' => $request->postal_code,
                    'notes' => $request->notes,
                    'total_amount' => $storeTotalAmount + $storeOrder['shipping_cost'],
                    'status' => Order::STATUS_PENDING,
                    'payment_status' => Order::STATUS_PENDING,
                    'payment_method' => $request->payment_method ?? 'midtrans',
                    'shipping_type' => str_contains(strtolower($storeOrder['shipping_type']), 'cargo') ? 'cargo' : 'reguler',
                    'shipping_cost' => $storeOrder['shipping_cost'],
                    'shipping_courier' => explode(' - ', $storeOrder['shipping_type'])[0] ?? 'unknown',
                    'midtrans_order_id' => str_replace('/', '-', $orderNumber),
                    'is_stock_deducted' => false,
                ]);

                foreach ($orderItemsData as $data) {
                    $order->orderItems()->create([
                        'product_id' => $data['product_id'],
                        'quantity' => $data['quantity'],
                        'price' => $data['price'],
                        'packing' => $data['packing'],
                    ]);
                }
            }

            $invoice->update([
                'total_product_amount' => $invoiceTotalProduct,
                'total_shipping_amount' => $invoiceTotalShipping,
                'grand_total' => $invoiceTotalProduct + $invoiceTotalShipping,
            ]);

            DB::commit();

            // Notify Admins & Customer
            try {
                $admins = \App\Models\User::where('role', 'administrator')->get();
                \Illuminate\Support\Facades\Notification::send($admins, new \App\Notifications\GeneralOrderNotification([
                    'invoice_id' => $invoice->id,
                    'target_type' => 'invoice',
                    'title'    => 'Pesanan Multi-Toko Baru: ' . $invoice->invoice_number,
                    'message'  => 'Pelanggan membuat pesanan multi-toko senilai Rp ' . number_format($invoice->grand_total, 0, ',', '.') . '.',
                    'type'     => 'new_order',
                    'status'   => \App\Models\Order::STATUS_PENDING,
                ]));

                $customer = auth()->user();
                if ($customer) {
                    $customer->notify(new \App\Notifications\GeneralOrderNotification([
                        'invoice_id' => $invoice->id,
                        'target_type' => 'invoice',
                        'title'    => 'Pesanan Berhasil Dibuat',
                        'message'  => 'Pesanan multi-toko Anda (' . $invoice->invoice_number . ') berhasil dibuat. Silakan lakukan pembayaran.',
                        'type'     => 'new_order',
                        'status'   => \App\Models\Order::STATUS_PENDING,
                    ]));
                }
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::warning('[Order Notification] Failed to notify for multi-store: ' . $e->getMessage());
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Pesanan multi-toko berhasil dibuat.',
                'data' => $invoice
            ], 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['status' => 'error', 'message' => $e->errors()], 422);
        } catch (\Exception $e) {
            DB::rollBack();
            \Illuminate\Support\Facades\Log::error("Store Multi Error: " . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => 'Terjadi kesalahan sistem: ' . $e->getMessage()], 500);
        }
    }

    public function requestReturn(Request $request, $id)
    {
        $request->validate([
            'reason' => 'required|string|max:1000'
        ]);

        $order = Order::where('user_id', auth()->id())->findOrFail($id);

        if ($order->status !== Order::STATUS_COMPLETED) {
            return response()->json([
                'status' => 'error',
                'message' => 'Hanya pesanan yang telah selesai yang dapat diajukan pengembalian.',
            ], 400);
        }

        if ($order->return_status) {
            return response()->json([
                'status' => 'error',
                'message' => 'Pengajuan pengembalian untuk pesanan ini sudah ada.',
            ], 400);
        }

        $order->update([
            'return_status' => 'requested',
            'return_reason' => $request->reason,
        ]);

        \App\Models\TrackingHistory::create([
            'order_id' => $order->id,
            'status' => Order::STATUS_COMPLETED,
            'notes' => 'Customer mengajukan pengembalian (Return Request): ' . $request->reason,
        ]);

        // Notify Admins
        try {
            $admins = \App\Models\User::where('role', 'administrator')->get();
            \Illuminate\Support\Facades\Notification::send($admins, new \App\Notifications\GeneralOrderNotification([
                'order_id' => $order->id,
                'title'    => 'Pengajuan Pengembalian: ' . ($order->midtrans_order_id ?? $order->order_number),
                'message'  => "Pelanggan mengajukan pengembalian: " . $request->reason,
                'type'     => 'status_update',
                'status'   => $order->status,
            ]));
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::warning('[Order Notification] Failed to notify admins for return request: ' . $e->getMessage());
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Pengajuan pengembalian berhasil dikirim.',
            'data' => $order->fresh()
        ]);
    }
}
