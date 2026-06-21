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
            $stores = Store::with(['city', 'province'])->where('is_active', true)->get();

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

    /**
     * Get detail of a specific store
     */
    public function show($id)
    {
        try {
            $store = Store::with(['city', 'province'])->find($id);

            if (!$store) {
                return response()->json([
                    'status'  => 'error',
                    'message' => 'Toko tidak ditemukan.',
                    'data'    => null
                ], 404);
            }

            return response()->json([
                'status'  => 'success',
                'message' => 'Detail toko berhasil dimuat.',
                'data'    => $store
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Terjadi kesalahan pada server saat mengambil detail toko.',
            ], 500);
        }
    }
}
