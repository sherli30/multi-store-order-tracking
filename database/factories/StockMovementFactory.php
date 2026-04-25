<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\StockMovement;
use Illuminate\Database\Eloquent\Factories\Factory;

class StockMovementFactory extends Factory
{
    protected $model = StockMovement::class;

    public function definition(): array
    {
        return [
            'product_id'   => Product::factory(),
            'type'         => $this->faker->randomElement(['in', 'out']),
            'quantity'     => $this->faker->numberBetween(1, 100),
            'source'       => $this->faker->randomElement([
                'initial_stock', 'purchase', 'cancellation', 'refund', 'manual_adjustment',
            ]),
            'reference_id' => null,
        ];
    }

    /**
     * State: stock in movement.
     */
    public function stockIn(): static
    {
        return $this->state(['type' => 'in']);
    }

    /**
     * State: stock out movement.
     */
    public function stockOut(): static
    {
        return $this->state(['type' => 'out']);
    }

    /**
     * State: from a purchase (order reference).
     */
    public function fromPurchase(int $orderId): static
    {
        return $this->state([
            'type'         => 'out',
            'source'       => 'purchase',
            'reference_id' => $orderId,
        ]);
    }
}
