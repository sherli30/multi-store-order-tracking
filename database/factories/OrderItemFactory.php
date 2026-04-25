<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\OrderItem>
 */
class OrderItemFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $product = \App\Models\Product::inRandomOrder()->first() ?? \App\Models\Product::factory()->create();

        return [
            'order_id'   => \App\Models\Order::factory(),
            'product_id' => $product->id,
            'quantity'   => fake()->numberBetween(1, 5),
            'price'      => $product->price,
        ];
    }
}
