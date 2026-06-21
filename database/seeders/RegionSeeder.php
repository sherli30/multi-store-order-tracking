<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Province;
use App\Models\City;

class RegionSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Create Provinces
        $provinces = [
            ['id' => 9,  'name' => 'Jawa Barat'],
            ['id' => 11, 'name' => 'Jawa Timur'],
        ];

        foreach ($provinces as $prov) {
            Province::updateOrCreate(['id' => $prov['id']], $prov);
        }

        // 2. Create Cities
        // Real RajaOngkir IDs and postal codes for selected major cities/regencies
        $cities = [
            // --- Jawa Barat (9) ---
            ['id' => 22,  'province_id' => 9,  'name' => 'Bandung', 'type' => 'Kabupaten', 'postal_code' => '40311'],
            ['id' => 23,  'province_id' => 9,  'name' => 'Bandung', 'type' => 'Kota', 'postal_code' => '40111'],
            ['id' => 54,  'province_id' => 9,  'name' => 'Bekasi', 'type' => 'Kabupaten', 'postal_code' => '17530'],
            ['id' => 55,  'province_id' => 9,  'name' => 'Bekasi', 'type' => 'Kota', 'postal_code' => '17121'],
            ['id' => 78,  'province_id' => 9,  'name' => 'Bogor', 'type' => 'Kabupaten', 'postal_code' => '16911'],
            ['id' => 79,  'province_id' => 9,  'name' => 'Bogor', 'type' => 'Kota', 'postal_code' => '16119'],
            ['id' => 104, 'province_id' => 9,  'name' => 'Cirebon', 'type' => 'Kota', 'postal_code' => '45116'],
            ['id' => 115, 'province_id' => 9,  'name' => 'Depok', 'type' => 'Kota', 'postal_code' => '16416'],

            // --- Jawa Timur (11) ---
            ['id' => 177, 'province_id' => 11, 'name' => 'Kediri', 'type' => 'Kota', 'postal_code' => '64125'],
            ['id' => 178, 'province_id' => 11, 'name' => 'Kediri', 'type' => 'Kabupaten', 'postal_code' => '64184'],
            ['id' => 247, 'province_id' => 11, 'name' => 'Madiun', 'type' => 'Kabupaten', 'postal_code' => '63153'],
            ['id' => 248, 'province_id' => 11, 'name' => 'Madiun', 'type' => 'Kota', 'postal_code' => '63122'],
            ['id' => 255, 'province_id' => 11, 'name' => 'Malang', 'type' => 'Kabupaten', 'postal_code' => '65163'],
            ['id' => 256, 'province_id' => 11, 'name' => 'Malang', 'type' => 'Kota', 'postal_code' => '65112'],
            ['id' => 409, 'province_id' => 11, 'name' => 'Sidoarjo', 'type' => 'Kabupaten', 'postal_code' => '61219'],
            ['id' => 444, 'province_id' => 11, 'name' => 'Surabaya', 'type' => 'Kota', 'postal_code' => '60119'],
        ];

        foreach ($cities as $city) {
            City::updateOrCreate(['id' => $city['id']], $city);
        }

        // Clean up data that is no longer in this list to prevent orphaned relations
        $provinceIds = collect($provinces)->pluck('id');
        Province::whereNotIn('id', $provinceIds)->delete();
        
        $cityIds = collect($cities)->pluck('id');
        City::whereNotIn('id', $cityIds)->delete();
    }
}
