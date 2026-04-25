<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class OrderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $stores = \App\Models\Store::all();

        if ($stores->isEmpty()) {
            return;
        }

        // Loop over each store and create random orders
        foreach ($stores as $store) {
            $products = $store->products;

            if ($products->isEmpty()) continue;

            $orders = \App\Models\Order::factory()
                ->count(random_int(10, 20))
                ->create(['store_id' => $store->id]);

            foreach ($orders as $order) {
                // Attach 1 to 4 unique random products to this order
                $orderProducts = $products->random(random_int(1, min(4, $products->count())));
                $totalAmount = 0;

                foreach ($orderProducts as $product) {
                    $quantity = random_int(1, 3);
                    $price = $product->price;

                    \App\Models\OrderItem::factory()->create([
                        'order_id'   => $order->id,
                        'product_id' => $product->id,
                        'quantity'   => $quantity,
                        'price'      => $price,
                    ]);

                    $totalAmount += ($price * $quantity);
                }

                // Add fake shipping cost (e.g., Reguler 15k, Cargo 50k)
                $shippingCost = $order->shipping_type === 'cargo' ? 50000 : 15000;

                $order->update([
                    'total_amount' => $totalAmount + $shippingCost
                ]);
            }
        }
    }
}
