<?php

namespace Database\Factories;

use App\Models\ProductCategory;
use App\Models\Store;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ProductCategoryFactory extends Factory
{
    protected $model = ProductCategory::class;

    public function definition(): array
    {
        $name = $this->faker->unique()->words(2, true);

        return [
            'store_id'    => Store::factory(),
            'name'        => ucwords($name),
            'slug'        => Str::slug($name),
            'description' => $this->faker->optional()->sentence(),
            'is_active'   => $this->faker->boolean(85),
        ];
    }

    /**
     * State: active category.
     */
    public function active(): static
    {
        return $this->state(['is_active' => true]);
    }

    /**
     * State: inactive category.
     */
    public function inactive(): static
    {
        return $this->state(['is_active' => false]);
    }

    /**
     * State: belongs to a specific store.
     */
    public function forStore(Store $store): static
    {
        return $this->state(['store_id' => $store->id]);
    }
}
