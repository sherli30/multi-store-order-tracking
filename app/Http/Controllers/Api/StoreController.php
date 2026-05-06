<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Store;
use Illuminate\Http\Request;

class StoreController extends Controller
{
    /**
     * Get list of active stores for mobile app
     */
    public function index()
    {
        try {
            $stores = Store::where('is_active', true)->get();

            if ($stores->isEmpty()) {
                return response()->json([
                    'status'  => 'info',
                    'message' => 'Belum ada toko yang tersedia.',
                    'data'    => []
                ], 200);
            }

            return response()->json([
                'status'  => 'success',
                'message' => 'Daftar toko berhasil dimuat. Ditemukan ' . $stores->count() . ' toko aktif.',
                'data'    => $stores
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Terjadi kendala teknis saat mengambil data toko. Silakan coba beberapa saat lagi.',
            ], 500);
        }
    }
}
