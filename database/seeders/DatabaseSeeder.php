<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * Order matters: stores → categories → (other seeders)
     */
    public function run(): void
    {
        $this->call([
            CustomerSeeder::class,
            RegionSeeder::class,
            StoreSeeder::class,
            ProductCategorySeeder::class,
            CourierSeeder::class,
            ShippingRateSeeder::class,
            DummyOrderSeeder::class,
        ]);
    }
}
