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
        // Map of store name → array of category names for that store
        $categoryMap = [
            'ayambebek.com' => [
                [
                    'name' => 'Ayam Segar',
                    'description' => 'Daging ayam pilihan yang diproses secara higienis dan dijamin kesegarannya.'
                ],
                [
                    'name' => 'Bebek Segar',
                    'description' => 'Daging bebek berkualitas tinggi dengan tekstur empuk dan rendah lemak.'
                ],
            ],
            'pakanayam.com' => [
                [
                    'name' => 'Pakan Utama',
                    'description' => 'Pakan harian bernutrisi lengkap untuk mendukung pertumbuhan ternak yang sehat.'
                ],
                [
                    'name' => 'Suplemen Ternak',
                    'description' => 'Produk pelengkap untuk meningkatkan daya tahan tubuh dan kesehatan hewan ternak.'
                ],
            ],
            'pakankucing.com' => [
                [
                    'name' => 'Makanan Kering',
                    'description' => 'Nutrisi harian praktis untuk mendukung kesehatan gigi dan pencernaan kucing kesayangan.'
                ],
                [
                    'name' => 'Makanan Basah',
                    'description' => 'Makanan lezat dengan kadar air tinggi untuk menjaga hidrasi dan nafsu makan kucing.'
                ],
            ],
        ];

        foreach ($categoryMap as $storeName => $categories) {
            $store = Store::where('name', $storeName)->first();

            if (!$store) {
                continue;
            }

            // 1. Buat/Update Kategori Baru & Simpan ID-nya
            $newCategoryIds = [];
            foreach ($categories as $cat) {
                $category = ProductCategory::updateOrCreate(
                    [
                        'store_id' => $store->id,
                        'name' => $cat['name']
                    ],
                    [

                        'description' => $cat['description'],
                        'is_active' => true,
                    ]
                );
                $newCategoryIds[] = $category->id;
            }

            // 2. Identifikasi Kategori Lama yang tidak ada di seeder terbaru
            $oldCategories = ProductCategory::where('store_id', $store->id)
                ->whereNotIn('id', $newCategoryIds)
                ->get();

            // Gunakan kategori pertama sebagai penampung (fallback) jika ada pemindahan produk
            $fallbackCategoryId = $newCategoryIds[0];

            foreach ($oldCategories as $oldCat) {
                // 3. Migrasi Produk: Pindahkan produk dari kategori lama ke kategori baru (fallback)
                \App\Models\Product::where('category_id', $oldCat->id)
                    ->update(['category_id' => $fallbackCategoryId]);

                // 4. Hapus Kategori Lama setelah produk diamankan
                $oldCat->delete();
            }
        }
    }
}
