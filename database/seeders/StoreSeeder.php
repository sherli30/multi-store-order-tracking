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
                'description' => 'Toko spesialis ayam dan bebek segar berkualitas tinggi. Melayani pemesanan dalam jumlah kecil maupun besar.',
                'phone'       => '082313485212',
                'province_id' => 11, // Jawa Timur
                'city_id'     => 178, // Kediri
                'address'     => 'Kavling Griya Asri, Jalan Galuh Candrakirana No. 01, RT 09/RW 03, Tugurejo, Kecamatan Ngasem, Kabupaten Kediri, Jawa Timur 64184',
                'is_active'   => true,
            ],
            [
                'name'        => 'pakanayam.com',
                'description' => 'Toko pakan ayam terlengkap. Menyediakan berbagai jenis pakan berkualitas untuk kebutuhan peternakan ayam Anda.',
                'phone'       => '082313485212',
                'province_id' => 11, // Jawa Timur
                'city_id'     => 444, // Surabaya
                'address'     => 'Tunjungan Plaza, Jl. Jenderal Basuki Rachmat No.8-12, Kedungdoro, Kec. Tegalsari, Kota SBY, Jawa Timur 60261',
                'is_active'   => true,
            ],
            [
                'name'        => 'pakankucing.com',
                'description' => 'Toko pakan dan aksesori kucing pilihan. Produk pilihan untuk nutrisi dan kesehatan kucing kesayangan Anda.',
                'phone'       => '082313485212',
                'province_id' => 9, // Jawa Barat
                'city_id'     => 23, // Bandung (Kota)
                'address'     => 'Jl. Asia Afrika No.123, Braga, Sumur Bandung, Kota Bandung, Jawa Barat 40111',
                'is_active'   => true, 
            ],
        ];

        foreach ($stores as $store) {
            Store::updateOrCreate(
                ['name' => $store['name']],
                $store
            );
        }
    }
}
