<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ProductCategory;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    /**
     * Get list of categories for mobile app
     */
    public function index(Request $request)
    {
        try {
            $query = ProductCategory::where('is_active', true);

            // Filter by store if provided
            if ($request->has('store_id')) {
                $query->where('store_id', $request->store_id);
            }

            $categories = $query->get();

            if ($categories->isEmpty()) {
                return response()->json([
                    'status'  => 'info',
                    'message' => 'Belum ada kategori yang tersedia.',
                    'data'    => []
                ], 200);
            }

            return response()->json([
                'status'  => 'success',
                'message' => 'Daftar kategori berhasil dimuat. Ditemukan ' . $categories->count() . ' kategori aktif.',
                'data'    => $categories
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Terjadi kendala teknis saat mengambil data kategori. Silakan coba beberapa saat lagi.',
            ], 500);
        }
    }
}
