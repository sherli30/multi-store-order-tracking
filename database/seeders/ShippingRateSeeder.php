<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ShippingService;
use App\Models\ShippingRate;
use App\Models\City;

class ShippingRateSeeder extends Seeder
{
    public function run(): void
    {
        $services = ShippingService::with('courier')->get();
        $cities = City::whereIn('id', [152, 153, 177, 178])->get();

        foreach ($services as $service) {
            foreach ($cities as $origin) {
                foreach ($cities as $dest) {
                    $isSameCity = ($origin->id === $dest->id);
                    $isCargo = ($service->min_weight >= 10000);
                    
                    if ($isSameCity) {
                        $baseCost = $isCargo ? 1500 : 5000;
                        $etdMin = 1;
                        $etdMax = 2;
                    } else {
                        $baseCost = $isCargo ? 3000 : 12000;
                        $etdMin = rand(1, 2);
                        $etdMax = rand(3, 5);
                    }
                    
                    ShippingRate::updateOrCreate(
                        [
                            'shipping_service_id' => $service->id,
                            'origin_city_id' => $origin->id,
                            'destination_city_id' => $dest->id,
                        ],
                        [
                            'cost_per_kg' => $baseCost + ($isSameCity ? rand(0, 1000) : rand(0, 5000)),
                            'etd_min' => $etdMin,
                            'etd_max' => $etdMax,
                        ]
                    );
                }
            }
        }
    }
}
