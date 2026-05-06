<?php

namespace Database\Seeders;

use App\Models\ProductCategory;
use App\Models\Store;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProductCategorySeeder extends Seeder
{
    /**
     * Seed a structured set of categories tied to the correct stores.
     * Uses firstOrCreate to be idempotent (safe to re-run).
     */
    public function run(): void
    {
        // Map of store slug → array of category names for that store
        $categoryMap = [
            'ayambebek-com' => [
                'Ayam Segar',
                'Bebek Segar',
                'Ayam Beku',
                'Bebek Beku',
                'Produk Olahan',
            ],
            'pakanayam-com' => [
                'Pakan Starter',
                'Pakan Grower',
                'Pakan Finisher',
                'Suplemen & Vitamin',
                'Peralatan Kandang',
            ],
            'pakankucing-com' => [
                'Makanan Kering',
                'Makanan Basah',
                'Camilan Kucing',
                'Suplemen Kucing',
                'Aksesori Kucing',
            ],
        ];

        foreach ($categoryMap as $storeSlug => $names) {
            $store = Store::where('slug', $storeSlug)->first();

            if (!$store) {
                continue; // Store not seeded yet — skip gracefully
            }

            foreach ($names as $name) {
                ProductCategory::firstOrCreate(
                    [
                        'store_id' => $store->id,
                        'slug' => $store->id . '-' . Str::slug($name),
                    ],
                    [
                        'name' => $name,
                        'is_active' => true,
                    ]
                );
            }
        }
    }
}
