<?php

namespace Database\Seeders;

use App\Models\Store;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class StoreSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $stores = [
            [
                'name'        => 'ayambebek.com',
                'slug'        => 'ayambebek-com',
                'description' => 'Toko spesialis ayam dan bebek segar berkualitas tinggi. Melayani pemesanan dalam jumlah kecil maupun besar.',
                'is_active'   => true,
            ],
            [
                'name'        => 'pakanayam.com',
                'slug'        => 'pakanayam-com',
                'description' => 'Toko pakan ayam terlengkap. Menyediakan berbagai jenis pakan berkualitas untuk kebutuhan peternakan ayam Anda.',
                'is_active'   => true,
            ],
            [
                'name'        => 'pakankucing.com',
                'slug'        => 'pakankucing-com',
                'description' => 'Toko pakan dan aksesori kucing pilihan. Produk pilihan untuk nutrisi dan kesehatan kucing kesayangan Anda.',
                'is_active'   => true,
            ],
        ];

        foreach ($stores as $store) {
            Store::firstOrCreate(
                ['slug' => $store['slug']],
                $store
            );
        }
    }
}
