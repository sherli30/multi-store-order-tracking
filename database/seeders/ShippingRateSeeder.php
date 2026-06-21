<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ShippingService;
use App\Models\ShippingRate;
use App\Models\City;
use Illuminate\Support\Facades\DB;

class ShippingRateSeeder extends Seeder
{
    public function run(): void
    {
        $services = ShippingService::with('courier')->get();
        $cities = City::with('province')->get();

        if ($services->isEmpty() || $cities->isEmpty()) {
            $this->command->info('No services or cities found. Skipping ShippingRateSeeder.');
            return;
        }

        // Clear old, inconsistent, or outdated shipping rates first
        DB::table('shipping_rates')->truncate();

        $rates = [];
        $now = now();

        // Zone Mapping based on typical Indonesian province names
        $getZone = function ($provinceName) {
            $name = strtolower($provinceName);
            if (str_contains($name, 'jawa') || str_contains($name, 'dki') || str_contains($name, 'banten') || str_contains($name, 'yogyakarta') || str_contains($name, 'bali') || str_contains($name, 'nusa tenggara')) {
                return 1; // Zone 1: Java, Bali & Nusa Tenggara
            } elseif (str_contains($name, 'sumatera') || str_contains($name, 'aceh') || str_contains($name, 'riau') || str_contains($name, 'jambi') || str_contains($name, 'bengkulu') || str_contains($name, 'lampung') || str_contains($name, 'bangka')) {
                return 2; // Zone 2: Sumatra
            } elseif (str_contains($name, 'kalimantan')) {
                return 3; // Zone 3: Kalimantan
            } elseif (str_contains($name, 'sulawesi') || str_contains($name, 'gorontalo')) {
                return 4; // Zone 4: Sulawesi
            } elseif (str_contains($name, 'maluku') || str_contains($name, 'papua')) {
                return 5; // Zone 5: Maluku & Papua
            }
            return 3; // Default intermediate zone
        };

        foreach ($services as $service) {
            $isCargo = ($service->min_weight >= 10); // Cargo services
            
            // 1. Courier Multiplier
            $courierName = strtolower($service->courier->name ?? '');
            $courierMultiplier = 1.0;
            if (str_contains($courierName, 'jne')) {
                $courierMultiplier = 1.05; // Premium
            } elseif (str_contains($courierName, 'j&t') || str_contains($courierName, 'jnt')) {
                $courierMultiplier = 0.98; // Competitive
            } elseif (str_contains($courierName, 'pos')) {
                $courierMultiplier = 0.85; // Economy
            } elseif (str_contains($courierName, 'sicepat')) {
                $courierMultiplier = 1.00; // Standard
            }

            // 2. Service Level Multiplier
            $serviceName = strtolower($service->service_name);
            $serviceMultiplier = 1.0;
            $etdBaseMin = 2;
            $etdBaseMax = 4;

            if (str_contains($serviceName, 'express') || str_contains($serviceName, 'next') || str_contains($serviceName, 'yes')) {
                $serviceMultiplier = 1.5;
                $etdBaseMin = 1;
                $etdBaseMax = 2;
            } elseif (str_contains($serviceName, 'eco') || str_contains($serviceName, 'hemat') || str_contains($serviceName, 'oke')) {
                $serviceMultiplier = 0.8;
                $etdBaseMin = 4;
                $etdBaseMax = 7;
            }

            if ($isCargo) {
                $serviceMultiplier *= 0.3; // Cargo per-kg is much cheaper
                $etdBaseMin += 3; // Cargo is slower
                $etdBaseMax += 5;
            }

            // Fetch unique city IDs where stores are located to act as origins
            $originCityIds = \App\Models\Store::pluck('city_id')->unique()->toArray();
            
            if (empty($originCityIds)) {
                $originCityIds = [178]; // Kediri fallback
            }

            foreach ($originCityIds as $originCityId) {
                $origin = $cities->firstWhere('id', $originCityId);
                if (!$origin) continue;
                $originZone = $getZone($origin->province->name ?? '');

                foreach ($cities as $dest) {
                    $destZone = $getZone($dest->province->name ?? '');
                    
                    $isSameCity = ($origin->id === $dest->id);
                    $isSameProvince = ($origin->province_id === $dest->province_id);
                    $isSameZone = ($originZone === $destZone);

                    // 3. Distance / Zone Base Cost
                    if ($isSameCity) {
                        $baseCost = 8000;
                        $distanceEtdAdd = 0;
                    } elseif ($isSameProvince) {
                        $baseCost = 12000;
                        $distanceEtdAdd = 0;
                    } elseif ($isSameZone) {
                        $baseCost = 18000;
                        $distanceEtdAdd = 1;
                    } else {
                        // Cross Zone
                        $zoneDiff = abs($originZone - $destZone);
                        if ($destZone == 5 || $originZone == 5) { // Papua/Maluku
                            $baseCost = 65000;
                            $distanceEtdAdd = 4;
                        } elseif ($zoneDiff == 1) { // Adjacent zones
                            $baseCost = 25000;
                            $distanceEtdAdd = 2;
                        } elseif ($zoneDiff == 2) {
                            $baseCost = 30000;
                            $distanceEtdAdd = 2;
                        } elseif ($zoneDiff == 3) {
                            $baseCost = 40000;
                            $distanceEtdAdd = 3;
                        } else {
                            $baseCost = 45000;
                            $distanceEtdAdd = 3;
                        }
                    }

                    // 4. Deterministic City Variance (Rp 0 - Rp 4,500)
                    // Avoid exactly identical costs for every city in the same province
                    $variance = ($origin->id ^ $dest->id) % 10 * 500;

                    // Calculate Final Cost
                    $rawCost = ($baseCost + $variance) * $courierMultiplier * $serviceMultiplier;
                    
                    // Cargo Minimum floor protection
                    // Cargo's min weight is 10kg, so cost_per_kg of 5000 ensures a minimum charge of Rp50.000
                    $minKgCost = 5000; 
                    if ($rawCost < $minKgCost) {
                        $rawCost = $minKgCost;
                    }

                    $finalCost = round($rawCost / 100) * 100; // Round to nearest 100 to preserve multiplier differences

                    $rates[] = [
                        'shipping_service_id' => $service->id,
                        'origin_city_id' => $origin->id,
                        'destination_city_id' => $dest->id,
                        'cost_per_kg' => $finalCost,
                        'etd_min' => $etdBaseMin + $distanceEtdAdd,
                        'etd_max' => $etdBaseMax + $distanceEtdAdd,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ];
                }
            }
        }

        // To avoid duplicates and handle bulk efficiently, we chunk and use upsert
        $chunks = array_chunk($rates, 1000);
        foreach ($chunks as $chunk) {
            ShippingRate::upsert(
                $chunk,
                ['shipping_service_id', 'origin_city_id', 'destination_city_id'],
                ['cost_per_kg', 'etd_min', 'etd_max', 'updated_at']
            );
        }

        $this->command->info('Shipping rates seeded successfully with deterministic zone-based matrix.');
    }
}
