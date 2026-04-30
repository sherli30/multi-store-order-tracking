<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * Get list of products for the mobile app
     */
    public function index(Request $request)
    {
        try {
            $query = Product::with(['store', 'category', 'variants', 'images'])
                ->where('is_active', true);

            // Filter by store
            if ($request->has('store_id')) {
                $query->where('store_id', $request->store_id);
            }

            // Filter by category
            if ($request->has('category_id')) {
                $query->where('category_id', $request->category_id);
            }

            $products = $query->get()->map(function ($product) {
                // Append full image URL so Flutter can load it easily
                $product->images->transform(function ($image) {
                    $image->image_url = url('storage/' . $image->image_path);
                    return $image;
                });
                return $product;
            });

            // Tidak ada produk yang ditemukan
            if ($products->isEmpty()) {
                return response()->json([
                    'status'  => 'info',
                    'message' => 'Belum ada produk yang tersedia saat ini. Silakan cek kembali nanti.',
                    'data'    => []
                ], 200);
            }

            return response()->json([
                'status'  => 'success',
                'message' => 'Data produk berhasil dimuat. Ditemukan ' . $products->count() . ' produk.',
                'data'    => $products
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Terjadi kesalahan pada server saat mengambil data produk. Silakan coba beberapa saat lagi.',
            ], 500);
        }
    }
}
