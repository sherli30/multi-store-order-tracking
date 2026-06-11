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
                'store_id' => 'required|exists:stores,id',
                'destination_city_id' => 'required|exists:cities,id',
                'items' => 'required|array',
                'items.*.product_id' => 'required|exists:products,id',
                'items.*.quantity' => 'required|integer|min:1',
            ], [
                'store_id.required'            => 'Toko pengirim wajib dipilih.',
                'store_id.exists'              => 'Toko pengirim tidak valid.',
                'destination_city_id.required' => 'Kota tujuan wajib dipilih.',
                'destination_city_id.exists'   => 'Kota tujuan tidak valid.',
                'items.required'               => 'Daftar produk tidak boleh kosong.',
                'items.array'                  => 'Format daftar produk tidak valid.',
                'items.*.product_id.required'  => 'Produk wajib dipilih.',
                'items.*.product_id.exists'    => 'Produk tidak ditemukan.',
                'items.*.quantity.required'    => 'Jumlah produk wajib diisi.',
                'items.*.quantity.integer'     => 'Jumlah produk harus berupa angka.',
                'items.*.quantity.min'         => 'Jumlah produk minimal 1.',
            ]);

            $store = Store::findOrFail($request->store_id);
            $originCityId = $store->city_id;
            $destCityId = $request->destination_city_id;

            // 2. Hitung Total Berat (Grams)
            $totalGrams = 0;
            foreach ($request->items as $item) {
                $product = Product::withTrashed()->find($item['product_id']);
                $totalGrams += ($product->weight ?? 1000) * $item['quantity'];
            }
            $totalKg = ceil($totalGrams / 1000); // Standard Logistik: Pembulatan ke atas

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

            // Filter rates based on total weight: >= 10kg only gets Cargo, < 10kg only gets Reguler
            $filteredRates = $rates->filter(function ($rate) use ($totalKg) {
                $isCargoService = $rate->service->min_weight >= 10000;
                if ($totalKg >= 10) {
                    return $isCargoService;
                } else {
                    return !$isCargoService;
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
                
                // Logika Penentuan Ketersediaan berdasarkan Berat (Cargo vs Reguler)
                $isAvailable = $totalKg >= ($service->min_weight / 1000);
                
                return [
                    'courier_name' => $courier->name,
                    'courier_code' => $courier->code,
                    'service_name' => $service->service_name,
                    'type'         => ($service->min_weight >= 10000) ? 'Cargo' : 'Reguler',
                    'cost'         => (int)($totalKg * $rate->cost_per_kg),
                    'is_available' => true,
                    'min_weight_kg' => $service->min_weight / 1000,
                    'etd'          => "{$rate->etd_min}-{$rate->etd_max} Hari"
                ];
            })->values();

            return response()->json([
                'status' => 'success',
                'data' => [
                    'origin' => $store->city->name ?? 'Unknown',
                    'total_weight_kg' => $totalKg,
                    'services' => $availableServices
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
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
