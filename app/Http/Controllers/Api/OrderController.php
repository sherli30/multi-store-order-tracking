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
     * Submit a new order from the mobile app
     */
    public function store(\App\Http\Requests\Api\StoreOrderRequest $request)
    {
        // Data otomatis tervalidasi oleh StoreOrderRequest

        try {
            DB::beginTransaction();

            // Calculate total amount & prepare items
            $totalAmount    = 0;
            $orderItemsData = [];

            foreach ($request->items as $item) {
                $variant = ProductVariant::lockForUpdate()->findOrFail($item['variant_id']);

                // Cek stok tidak mencukupi — rollback dulu sebelum return
                if ($variant->stock < $item['quantity']) {
                    DB::rollBack();
                    return response()->json([
                        'status'  => 'error',
                        'message' => 'Stok produk "' . $variant->name . '" tidak mencukupi. Tersedia ' . $variant->stock . ' unit, Anda memesan ' . $item['quantity'] . ' unit.',
                    ], 400);
                }

                $subtotal     = $variant->price * $item['quantity'];
                $totalAmount += $subtotal;

                $orderItemsData[] = [
                    'product_id'         => $variant->product_id,
                    'product_variant_id' => $variant->id,
                    'quantity'           => $item['quantity'],
                    'unit_price'         => $variant->price,
                    'subtotal'           => $subtotal,
                ];

                // Deduct stock
                $variant->decrement('stock', $item['quantity']);
            }

            // Create Order
            $order = Order::create([
                'store_id'         => $request->store_id,
                'customer_name'    => $request->customer_name,
                'customer_phone'   => $request->customer_phone,
                'shipping_address' => $request->shipping_address,
                'shipping_type'    => $request->shipping_type,
                'status'           => 'pending',
                'total_amount'     => $totalAmount,
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
                'message' => 'Pesanan Anda berhasil dibuat dengan nomor ' . $order->order_number . '. Silakan lakukan pembayaran untuk memproses pesanan.',
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
                'message' => 'Terjadi kesalahan pada server saat memproses pesanan. Silakan coba beberapa saat lagi.',
            ], 500);
        }
    }
}
