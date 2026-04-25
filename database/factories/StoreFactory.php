<?php

namespace Database\Factories;

use App\Models\Store;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class StoreFactory extends Factory
{
    protected $model = Store::class;

    public function definition(): array
    {
        $name = $this->faker->unique()->company();

        return [
            'name'        => $name,
            'slug'        => Str::slug($name),
            'description' => $this->faker->optional()->paragraph(),
            'is_active'   => $this->faker->boolean(80), // 80% chance active
        ];
    }

    /**
     * State: active store.
     */
    public function active(): static
    {
        return $this->state(['is_active' => true]);
    }

    /**
     * State: inactive store.
     */
    public function inactive(): static
    {
        return $this->state(['is_active' => false]);
    }
}
