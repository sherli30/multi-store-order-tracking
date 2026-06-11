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
            ['id' => 11, 'name' => 'Jawa Timur'],
            ['id' => 31, 'name' => 'DKI Jakarta'],
        ];

        foreach ($provinces as $prov) {
            Province::updateOrCreate(['id' => $prov['id']], $prov);
        }

        // 2. Create Cities
        $cities = [
            ['id' => 177, 'province_id' => 11, 'name' => 'Kediri', 'type' => 'Kota', 'postal_code' => '64100'],
            ['id' => 178, 'province_id' => 11, 'name' => 'Kediri', 'type' => 'Kabupaten', 'postal_code' => '64184'],
            ['id' => 152, 'province_id' => 31, 'name' => 'Jakarta Pusat', 'type' => 'Kota', 'postal_code' => '10110'],
            ['id' => 153, 'province_id' => 31, 'name' => 'Jakarta Selatan', 'type' => 'Kota', 'postal_code' => '12110'],
        ];

        foreach ($cities as $city) {
            City::updateOrCreate(['id' => $city['id']], $city);
        }

        // Delete any provinces or cities not in the list to keep data clean
        $provinceIds = collect($provinces)->pluck('id');
        Province::whereNotIn('id', $provinceIds)->delete();
        
        $cityIds = collect($cities)->pluck('id');
        City::whereNotIn('id', $cityIds)->delete();
    }
}
