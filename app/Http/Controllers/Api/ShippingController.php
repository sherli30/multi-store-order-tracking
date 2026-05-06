<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

class ShippingController extends Controller
{
    /**
     * Menghitung ongkir berdasarkan berat produk
     */
    public function calculate(Request $request)
    {
        try {
            // Validasi Input
            $request->validate([
                'items' => 'required|array',
                'items.*.product_id' => 'required|integer',
                'items.*.quantity' => 'required|integer|min:1',
                'items.*.packing' => 'nullable|string',
            ]);

            $totalGrams = 0;
            $packingCost = 0;
            $itemsDetails = [];

            foreach ($request->items as $item) {
                $product = Product::find($item['product_id']);
                
                // 1. Hitung Berat
                $weightPerItem = $product ? ($product->weight ?? 1000) : 1000;
                $subtotalGrams = $weightPerItem * $item['quantity'];
                $totalGrams += $subtotalGrams;

                // 2. Hitung Packing (Rp 2.000 per item jika Extra)
                $isExtraPacking = isset($item['packing']) && str_contains(strtolower($item['packing']), 'extra');
                $itemPackingCost = $isExtraPacking ? (2000 * $item['quantity']) : 0;
                $packingCost += $itemPackingCost;

                $itemsDetails[] = [
                    'product_id' => $item['product_id'],
                    'name' => $product ? $product->name : 'Unknown Product',
                    'weight_per_item' => $weightPerItem,
                    'quantity' => $item['quantity'],
                    'packing' => $item['packing'] ?? 'Biasa',
                    'subtotal_grams' => $subtotalGrams,
                    'item_packing_cost' => $itemPackingCost
                ];
            }

            // 3. Hitung Ongkir Berdasarkan Wilayah (Provinsi) & Berat
            $totalKg = $totalGrams / 1000;
            $province = strtolower($request->province ?? '');
            $city = strtolower($request->city ?? ''); 
            
            // Konfigurasi Tarif per Wilayah (Mockup Realistis)
            $regulerRate = 25000; // Default Luar Jawa
            $cargoRate = 12000;   // Default Luar Jawa

            if (str_contains($province, 'timur') || str_contains($city, 'malang') || str_contains($city, 'surabaya')) {
                $regulerRate = 8000; 
                $cargoRate = 3500;
                
                // Bonus Khusus Malang (Lokasi Toko)
                if (str_contains($city, 'malang')) {
                    $regulerRate = 5000;
                    $cargoRate = 2000;
                }
            } elseif (str_contains($province, 'tengah') || str_contains($province, 'barat') || str_contains($province, 'jakarta') || str_contains($province, 'jogja') || str_contains($province, 'yogyakarta')) {
                $regulerRate = 15000; 
                $cargoRate = 7000;
            } elseif (str_contains($province, 'bali')) {
                $regulerRate = 20000; 
                $cargoRate = 9000;
            }

            $threshold = config('shipping.cargo_threshold', 10);
            $type = $totalKg <= $threshold ? 'reguler' : 'cargo';
            $ratePerKg = ($type == 'reguler') ? $regulerRate : $cargoRate;
            
            $shippingCost = (int) round($totalKg * $ratePerKg);

            return response()->json([
                'status' => 'success',
                'message' => 'Biaya pengiriman dan packing berhasil dihitung secara otomatis berdasarkan berat produk dan wilayah Anda.',
                'data' => [
                    'total_grams' => $totalGrams,
                    'total_kg' => $totalKg,
                    'shipping_type' => ucfirst($type),
                    'rate_per_kg' => $ratePerKg,
                    'shipping_cost' => $shippingCost,
                    'packing_cost' => $packingCost,
                    'items_breakdown' => $itemsDetails
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal menghitung ongkir: ' . $e->getMessage()
            ], 500);
        }
    }
}
