<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Store;
use App\Models\ShippingRate;
use App\Models\ShippingService;
use App\Models\City;
use Illuminate\Http\Request;

class ShippingController extends Controller
{
    /**
     * Menghitung ongkir profesional berdasarkan Master Data & Kategori (Reguler/Cargo)
     */
    public function calculate(Request $request)
    {
        try {
            // 1. Validasi Input
            $request->validate([
                'store_id' => 'nullable',
                'destination_city' => 'required|string',
                'items' => 'required|array',
                'items.*.product_id' => 'required|exists:products,id',
                'items.*.quantity' => 'required|integer|min:1',
            ], [
                'destination_city.required' => 'Kota tujuan wajib dipilih.',
                'items.required'               => 'Daftar produk tidak boleh kosong.',
                'items.array'                  => 'Format daftar produk tidak valid.',
                'items.*.product_id.required'  => 'Produk wajib dipilih.',
                'items.*.product_id.exists'    => 'Produk tidak ditemukan.',
                'items.*.quantity.required'    => 'Jumlah produk wajib diisi.',
                'items.*.quantity.integer'     => 'Jumlah produk harus berupa angka.',
                'items.*.quantity.min'         => 'Jumlah produk minimal 1.',
            ]);

            $destCity = \App\Models\City::get()->first(function ($city) use ($request) {
                return strtolower($city->name) === strtolower($request->destination_city) || 
                       strtolower($city->full_name) === strtolower($request->destination_city);
            });

            if (!$destCity) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Layanan pengiriman belum tersedia untuk kota ini.',
                ], 404);
            }

            $store = Store::with(['city', 'province'])->find($request->store_id);
            $originCityId = $store ? $store->city_id : 178; // Fallback to 178 if store not provided, though it should be required.
            $destCityId = $destCity->id;

            // 2. Hitung Total Berat (KG)
            $totalKg = 0;
            foreach ($request->items as $item) {
                $product = Product::withTrashed()->find($item['product_id']);
                $totalKg += ($product->weight ?? 1.0) * $item['quantity'];
            }
            $totalKg = ceil($totalKg); // Standard Logistik: Pembulatan ke atas

            $rates = ShippingRate::with(['service.courier'])
                ->where('origin_city_id', $originCityId)
                ->where('destination_city_id', $destCityId)
                ->whereHas('service', function($q) {
                    $q->where('is_active', true)
                      ->whereHas('courier', function($qc) {
                          $qc->where('is_active', true);
                      });
                })
                ->get();

            // Filter rates based on total weight rules
            $filteredRates = $rates->filter(function ($rate) use ($totalKg) {
                $isCargo = ($rate->service->min_weight >= 10);
                
                if ($totalKg >= 10) {
                    // >= 10kg: ONLY Cargo
                    return $isCargo;
                } else {
                    // < 10kg: ONLY Reguler (hide Cargo)
                    return !$isCargo;
                }
            });

            // 4. Jika data rate tidak ada di database, kembalikan error informatif
            if ($filteredRates->isEmpty()) {
                return response()->json([
                    'status'  => 'error',
                    'message' => 'Maaf, belum ada tarif ongkos kirim yang tersedia untuk berat pesanan Anda pada rute ini.',
                ], 404);
            }

            // 5. Filter & Format Layanan untuk Flutter
            $availableServices = $filteredRates->map(function($rate) use ($totalKg) {
                $service = $rate->service;
                $courier = $service->courier;
                
                // For Cargo under 10kg, charge the minimum required weight (10kg).
                $chargeableKg = max($totalKg, $service->min_weight);

                return [
                    'courier_name' => $courier->name,
                    'courier_code' => $courier->code,
                    'service_name' => $service->service_name,
                    'type'         => ($service->min_weight >= 10) ? 'Cargo' : 'Reguler',
                    'cost'         => (int)($chargeableKg * $rate->cost_per_kg),
                    'is_available' => true,
                    'min_weight_kg' => $service->min_weight,
                    'etd'          => "{$rate->etd_min}-{$rate->etd_max} Hari"
                ];
            })->unique(function ($item) {
                return $item['courier_name'] . $item['service_name'];
            })->values();

            return response()->json([
                'status' => 'success',
                'data' => [
                    'origin' => $store ? ($store->city->name . ', ' . $store->province->name) : 'Kediri, East Java',
                    'total_weight_kg' => $totalKg,
                    'services' => $availableServices
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => 'Gagal memuat tarif ongkos kirim. Pastikan koneksi internet stabil atau hubungi layanan pelanggan.'], 500);
        }
    }

    /**
     * V2: Hitung ongkir untuk multi-store checkout
     */
    public function calculateMulti(Request $request)
    {
        try {
            $request->validate([
                'destination_city' => 'required|string',
                'store_orders' => 'required|array',
                'store_orders.*.store_id' => 'required|exists:stores,id',
                'store_orders.*.items' => 'required|array',
                'store_orders.*.items.*.product_id' => 'required|exists:products,id',
                'store_orders.*.items.*.quantity' => 'required|integer|min:1',
            ]);

            $destCity = City::get()->first(function ($city) use ($request) {
                return strtolower($city->name) === strtolower($request->destination_city) || 
                       strtolower($city->full_name) === strtolower($request->destination_city);
            });

            if (!$destCity) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Layanan pengiriman belum tersedia untuk kota ini.',
                ], 404);
            }

            $destCityId = $destCity->id;
            $results = [];

            foreach ($request->store_orders as $group) {
                $storeId = $group['store_id'];
                $store = Store::with(['city', 'province'])->find($storeId);
                $originCityId = $store ? $store->city_id : 178;

                $totalKg = 0;
                foreach ($group['items'] as $item) {
                    $product = Product::withTrashed()->find($item['product_id']);
                    $totalKg += ($product->weight ?? 1.0) * $item['quantity'];
                }
                $totalKg = ceil($totalKg);

                $rates = ShippingRate::with(['service.courier'])
                    ->where('origin_city_id', $originCityId)
                    ->where('destination_city_id', $destCityId)
                    ->whereHas('service', function($q) {
                        $q->where('is_active', true)
                          ->whereHas('courier', function($qc) {
                              $qc->where('is_active', true);
                          });
                    })
                    ->get();

                $filteredRates = $rates->filter(function ($rate) use ($totalKg) {
                    $isCargo = ($rate->service->min_weight >= 10);
                    return $totalKg >= 10 ? $isCargo : !$isCargo;
                });

                if ($filteredRates->isEmpty()) {
                    $results[] = [
                        'store_id' => $storeId,
                        'status' => 'error',
                        'message' => 'Tidak ada layanan untuk rute ini.',
                        'services' => []
                    ];
                    continue;
                }

                $availableServices = $filteredRates->map(function($rate) use ($totalKg) {
                    $service = $rate->service;
                    $courier = $service->courier;
                    $chargeableKg = max($totalKg, $service->min_weight);

                    return [
                        'courier_name' => $courier->name,
                        'courier_code' => $courier->code,
                        'service_name' => $service->service_name,
                        'type'         => ($service->min_weight >= 10) ? 'Cargo' : 'Reguler',
                        'cost'         => (int)($chargeableKg * $rate->cost_per_kg),
                        'is_available' => true,
                        'min_weight_kg' => $service->min_weight,
                        'etd'          => "{$rate->etd_min}-{$rate->etd_max} Hari"
                    ];
                })->unique(function ($item) {
                    return $item['courier_name'] . $item['service_name'];
                })->values();

                $results[] = [
                    'store_id' => $storeId,
                    'status' => 'success',
                    'origin' => $store ? ($store->city->name . ', ' . $store->province->name) : 'Kediri, East Java',
                    'total_weight_kg' => $totalKg,
                    'services' => $availableServices
                ];
            }

            return response()->json([
                'status' => 'success',
                'data' => $results
            ]);

        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => 'Gagal memuat tarif ongkos kirim multi-store. ' . $e->getMessage()], 500);
        }
    }

    public function getProvinces() { return response()->json(['status' => 'success', 'data' => \App\Models\Province::all()]); }
    public function getCities($provinceId) { 
        $cities = \App\Models\City::where('province_id', $provinceId)->get()->map(function($c) {
            return ['id' => $c->id, 'name' => $c->full_name];
        });
        return response()->json(['status' => 'success', 'data' => $cities]); 
    }
}
