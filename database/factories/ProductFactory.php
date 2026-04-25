<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\Store;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition(): array
    {
        $name = $this->faker->unique()->words(3, true);

        // Get store and category from the category (ensures store_id consistency)
        $category = ProductCategory::inRandomOrder()->first()
            ?? ProductCategory::factory()->active()->create();

        return [
            'store_id'    => $category->store_id,
            'category_id' => $category->id,
            'name'        => ucwords($name),
            'slug'        => Str::slug($name),
            'description' => $this->faker->optional()->paragraph(),
            'price'       => $this->faker->randomFloat(2, 5000, 500000),
            'stock'       => $this->faker->numberBetween(0, 200),
            'weight'      => $this->faker->randomFloat(2, 50, 5000),
            'image'       => null,
            'is_active'   => $this->faker->boolean(85),
        ];
    }

    /**
     * State: active product.
     */
    public function active(): static
    {
        return $this->state(['is_active' => true]);
    }

    /**
     * State: inactive product.
     */
    public function inactive(): static
    {
        return $this->state(['is_active' => false]);
    }

    /**
     * State: out of stock.
     */
    public function outOfStock(): static
    {
        return $this->state(['stock' => 0]);
    }

    /**
     * State: low stock (1–10).
     */
    public function lowStock(): static
    {
        return $this->state([
            'stock' => $this->faker->numberBetween(1, 10),
        ]);
    }

    /**
     * State: belongs to a specific category (and auto-sets store_id).
     */
    public function forCategory(ProductCategory $category): static
    {
        return $this->state([
            'category_id' => $category->id,
            'store_id'    => $category->store_id,
        ]);
    }
}
