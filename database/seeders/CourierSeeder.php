<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Courier;
use App\Models\ShippingService;

class CourierSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $couriers = [
            [
                'name' => 'J&T Express',
                'code' => 'jnt',
                'services' => [
                    ['service_name' => 'Reguler', 'min_weight' => 0],
                    ['service_name' => 'Cargo (J&T Cargo)', 'min_weight' => 10],
                ]
            ],
            [
                'name' => 'JNE',
                'code' => 'jne',
                'services' => [
                    ['service_name' => 'REG (Reguler)', 'min_weight' => 0],
                    ['service_name' => 'JTR (JNE Trucking/Cargo)', 'min_weight' => 10],
                ]
            ],
        ];

        foreach ($couriers as $cData) {
            $services = $cData['services'];
            unset($cData['services']);
            
            $courier = Courier::updateOrCreate(
                ['code' => $cData['code']],
                $cData
            );
            
            // Delete old services of this courier that are not in the new configuration
            $newServiceNames = collect($services)->pluck('service_name')->toArray();
            $courier->services()->whereNotIn('service_name', $newServiceNames)->delete();
            
            foreach ($services as $s) {
                $courier->services()->updateOrCreate(
                    ['service_name' => $s['service_name']],
                    $s
                );
            }
        }

        // Delete any courier that is not in the configuration (e.g. sicepat)
        $newCourierCodes = collect($couriers)->pluck('code')->toArray();
        Courier::whereNotIn('code', $newCourierCodes)->delete();
    }
}
