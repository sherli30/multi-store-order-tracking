<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Order>
 */
class OrderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $shippingType = fake()->randomElement(['reguler', 'cargo']);
        $status = fake()->randomElement(['pending', 'processing', 'shipping', 'completed', 'cancelled']);

        return [
            'store_id'         => \App\Models\Store::inRandomOrder()->first()->id ?? \App\Models\Store::factory(),
            'customer_name'    => fake()->name(),
            'customer_email'   => fake()->unique()->safeEmail(),
            'customer_phone'   => fake()->phoneNumber(),
            'midtrans_order_id' => 'ORD-' . strtoupper(substr(uniqid(), -8)) . '-' . time(),
            'shipping_address' => fake()->address(),
            'shipping_type'    => $shippingType,
            'status'           => $status,
            'total_amount'     => 0, // Will be calculated after adding items
            'cancel_reason'    => $status === 'cancelled' ? 'Dibatalkan oleh sistem (Simulasi)' : null,
            'created_at'       => fake()->dateTimeBetween('-2 months', 'now'),
        ];
    }
}
