<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\ProductVariant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    /**
     * Get list of orders for a user
     */
    public function index(Request $request)
    {
        try {
            $query = Order::with(['store', 'orderItems.product.images', 'orderItems.productVariant']);

            if ($request->has('user_id')) {
                $query->where('user_id', $request->user_id);
            } elseif ($request->has('customer_phone')) {
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
     */
    public function store(\App\Http\Requests\Api\StoreOrderRequest $request)
    {
        // Data otomatis tervalidasi oleh StoreOrderRequest

        try {
            DB::beginTransaction();

            $totalAmount    = 0;
            $totalGrams     = 0;
            $packingCost    = 0;
            $orderItemsData = [];

            foreach ($request->items as $item) {
                // Handle product_id or variant_id
                if (isset($item['variant_id'])) {
                    $variant = ProductVariant::lockForUpdate()->findOrFail($item['variant_id']);
                } else {
                    // Find first active variant for the product
                    $variant = ProductVariant::where('product_id', $item['product_id'])
                        ->where('is_active', true)
                        ->lockForUpdate()
                        ->firstOrFail();
                }

                if ($variant->stock < $item['quantity']) {
                    DB::rollBack();
                    return response()->json([
                        'status'  => 'error',
                        'message' => 'Stok produk "' . $variant->product->name . '" tidak mencukupi.',
                    ], 400);
                }

                $subtotal     = $variant->price * $item['quantity'];
                $totalAmount += $subtotal;

                // Hitung Berat untuk Ongkir
                $totalGrams += ($variant->product->weight ?? 1000) * $item['quantity'];

                // Re-calculate packing cost at server side for security
                if (isset($item['packing']) && str_contains(strtolower($item['packing']), 'extra')) {
                    $packingCost += (2000 * $item['quantity']);
                }

                $orderItemsData[] = [
                    'product_variant_id' => $variant->id,
                    'quantity'           => $item['quantity'],
                    'price'              => $variant->price,
                    'packing'            => $item['packing'] ?? null,
                ];

                $variant->decrement('stock', $item['quantity']);
            }

            // ── Re-calculate Shipping Cost (Security Check) ──────────────────
            $totalKg  = $totalGrams / 1000;
            $province = strtolower($request->province ?? '');
            $city     = strtolower($request->city ?? '');

            $regulerRate = 25000; // Default
            $cargoRate   = 12000; 

            if (str_contains($province, 'timur') || str_contains($city, 'malang') || str_contains($city, 'surabaya')) {
                $regulerRate = 8000; 
                $cargoRate   = 3500;
                if (str_contains($city, 'malang')) {
                    $regulerRate = 5000;
                    $cargoRate   = 2000;
                }
            } elseif (str_contains($province, 'tengah') || str_contains($province, 'barat') || str_contains($province, 'jakarta') || str_contains($province, 'jogja') || str_contains($province, 'yogyakarta')) {
                $regulerRate = 15000; 
                $cargoRate   = 7000;
            } elseif (str_contains($province, 'bali')) {
                $regulerRate = 20000; 
                $cargoRate   = 9000;
            }

            $threshold = config('shipping.cargo_threshold', 10);
            $type = $totalKg <= $threshold ? 'reguler' : 'cargo';
            $ratePerKg = ($type == 'reguler') ? $regulerRate : $cargoRate;
            $shippingCost = (int) round($totalKg * $ratePerKg);

            // Final Total = Products + Shipping + Packing
            $finalTotal = $totalAmount + $shippingCost + $packingCost;

            $order = Order::create([
                'store_id'         => $request->store_id,
                'user_id'          => auth()->id(),
                'customer_name'    => $request->customer_name,
                'customer_phone'   => $request->customer_phone,
                'shipping_address' => $request->shipping_address,
                'province'         => $request->province,
                'city'             => $request->city,
                'postal_code'      => $request->postal_code,
                'shipping_type'    => $request->shipping_type,
                'shipping_cost'    => $shippingCost,
                'packing_cost'     => $packingCost,
                'notes'            => $request->notes,
                'status'           => 'pending',
                'total_amount'     => $finalTotal,
                'order_number'     => 'ORD-' . strtoupper(uniqid()),
            ]);

            // Insert Order Items
            foreach ($orderItemsData as $itemData) {
                $itemData['order_id'] = $order->id;
                OrderItem::create($itemData);
            }

            DB::commit();

            return response()->json([
                'status'  => 'success',
                'message' => 'Pesanan ' . $order->order_number . ' berhasil dibuat! Silakan segera lakukan pembayaran melalui Snap Midtrans untuk memproses pengiriman.',
                'data'    => $order->load('orderItems')
            ], 201);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            DB::rollBack();
            return response()->json([
                'status'  => 'error',
                'message' => 'Salah satu produk yang Anda pesan tidak ditemukan. Silakan periksa kembali daftar pesanan Anda.',
            ], 404);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status'  => 'error',
                'message' => 'Gagal memproses pesanan: ' . $e->getMessage(),
            ], 500);
        }
    }
}
