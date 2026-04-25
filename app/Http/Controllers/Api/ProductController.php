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

        return response()->json([
            'status' => 'success',
            'message' => 'Data produk berhasil diambil',
            'data' => $products
        ]);
    }
}
