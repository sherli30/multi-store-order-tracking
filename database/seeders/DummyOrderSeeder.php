<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Store;
use App\Models\TrackingHistory;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DummyOrderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $store = Store::first();
        $admin = User::first();
        $product1 = Product::first();
        $product2 = Product::skip(1)->first() ?? $product1;

        if (!$store || !$admin || !$product1) {
            $this->command->warn('Store, User, or Product not found. Run basic seeders first.');
            return;
        }

        // --- 1. Order Completed ---
        $order1 = Order::create([
            'store_id' => $store->id,
            'user_id' => null,
            'customer_name' => 'Budi Santoso',
            'customer_email' => 'budi@example.com',
            'customer_phone' => '081234567890',
            'midtrans_order_id' => 'ORD-' . strtoupper(Str::random(8)),
            'shipping_address' => 'Jl. Merdeka No. 45, RT 01/RW 02',
            'province' => 'DKI Jakarta',
            'city' => 'Jakarta Selatan',
            'postal_code' => '12345',
            'shipping_type' => 'reguler',
            'shipping_cost' => 15000,
            'packing_cost' => 2000,
            'shipping_courier' => 'JNE',
            'tracking_number' => 'JNE' . rand(1000000000, 9999999999),
            'status' => 'completed',
            'is_stock_deducted' => true,
            'total_amount' => ($product1->price * 2) + 15000 + 2000,
            'payment_status' => 'settlement',
            'created_at' => now()->subDays(3),
            'updated_at' => now()->subDays(1),
        ]);

        OrderItem::create([
            'order_id' => $order1->id,
            'product_id' => $product1->id,
            'quantity' => 2,
            'price' => $product1->price,
        ]);

        Transaction::create([
            'order_id' => $order1->id,
            'transaction_id' => Str::uuid()->toString(),
            'payment_method' => 'bank_transfer',
            'payment_details' => ['bank' => 'bca', 'va_number' => '123456789012'],
            'amount' => $order1->total_amount,
            'status' => 'paid',
            'payment_date' => now()->subDays(3),
            'notes' => 'Pembayaran lunas via BCA VA',
        ]);

        $statuses1 = ['pending', 'perlu_diproses', 'processing', 'shipping', 'completed'];
        foreach ($statuses1 as $index => $status) {
            TrackingHistory::create([
                'order_id' => $order1->id,
                'admin_id' => $status === 'pending' ? null : $admin->id,
                'status' => $status,
                'notes' => $status === 'shipping' ? "Pesanan dikirim dengan resi {$order1->tracking_number}" : "Status pesanan diubah ke {$status}",
                'created_at' => now()->subDays(3)->addHours($index * 5),
            ]);
        }


        // --- 2. Order Shipping ---
        $order2 = Order::create([
            'store_id' => $store->id,
            'user_id' => null,
            'customer_name' => 'Siti Aminah',
            'customer_email' => 'siti@example.com',
            'customer_phone' => '089876543210',
            'midtrans_order_id' => 'ORD-' . strtoupper(Str::random(8)),
            'shipping_address' => 'Jl. Pahlawan Blok C No 12',
            'province' => 'Jawa Barat',
            'city' => 'Bandung',
            'postal_code' => '40123',
            'shipping_type' => 'cargo',
            'shipping_cost' => 25000,
            'packing_cost' => 0,
            'shipping_courier' => 'JNT',
            'tracking_number' => 'JP' . rand(1000000000, 9999999999),
            'status' => 'shipping',
            'is_stock_deducted' => true,
            'total_amount' => $product2->price + 25000,
            'payment_status' => 'settlement',
            'created_at' => now()->subDays(1),
            'updated_at' => now()->subHours(2),
        ]);

        OrderItem::create([
            'order_id' => $order2->id,
            'product_id' => $product2->id,
            'quantity' => 1,
            'price' => $product2->price,
        ]);

        Transaction::create([
            'order_id' => $order2->id,
            'transaction_id' => Str::uuid()->toString(),
            'payment_method' => 'qris',
            'payment_details' => ['issuer' => 'gopay'],
            'amount' => $order2->total_amount,
            'status' => 'paid',
            'payment_date' => now()->subDays(1),
            'notes' => 'Pembayaran lunas via QRIS',
        ]);

        $statuses2 = ['pending', 'perlu_diproses', 'processing', 'shipping'];
        foreach ($statuses2 as $index => $status) {
            TrackingHistory::create([
                'order_id' => $order2->id,
                'admin_id' => $status === 'pending' ? null : $admin->id,
                'status' => $status,
                'notes' => $status === 'shipping' ? "Diserahkan ke kurir pengiriman" : "Status diperbarui",
                'created_at' => now()->subDays(1)->addHours($index * 2),
            ]);
        }


        // --- 3. Order Pending ---
        $order3 = Order::create([
            'store_id' => $store->id,
            'user_id' => null,
            'customer_name' => 'Agus Pratama',
            'customer_email' => 'agus@example.com',
            'customer_phone' => '085612345678',
            'midtrans_order_id' => 'ORD-' . strtoupper(Str::random(8)),
            'shipping_address' => 'Perumahan Indah Asri Kav 8',
            'province' => 'Jawa Timur',
            'city' => 'Surabaya',
            'postal_code' => '60123',
            'shipping_type' => 'reguler',
            'shipping_cost' => 20000,
            'packing_cost' => 0,
            'shipping_courier' => null,
            'tracking_number' => null,
            'status' => 'pending',
            'is_stock_deducted' => false,
            'total_amount' => $product1->price + $product2->price + 20000,
            'payment_status' => 'pending',
            'created_at' => now()->subHours(5),
            'updated_at' => now()->subHours(5),
        ]);

        OrderItem::create([
            'order_id' => $order3->id,
            'product_id' => $product1->id,
            'quantity' => 1,
            'price' => $product1->price,
        ]);

        OrderItem::create([
            'order_id' => $order3->id,
            'product_id' => $product2->id,
            'quantity' => 1,
            'price' => $product2->price,
        ]);

        Transaction::create([
            'order_id' => $order3->id,
            'transaction_id' => Str::uuid()->toString(),
            'payment_method' => 'bank_transfer',
            'payment_details' => ['bank' => 'mandiri', 'va_number' => '890123456789'],
            'amount' => $order3->total_amount,
            'status' => 'pending',
            'payment_date' => null,
            'notes' => 'Menunggu verifikasi pembayaran',
        ]);

        TrackingHistory::create([
            'order_id' => $order3->id,
            'admin_id' => null,
            'status' => 'pending',
            'notes' => 'Menunggu pembayaran dari pelanggan',
            'created_at' => now()->subHours(5),
        ]);
    }
}
