<?php

namespace App\Http\Controllers;

use App\Http\Requests\CategoryRequest;
use App\Models\ProductCategory;
use App\Models\Product;
use App\Models\Store;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ProductCategoryController extends Controller
{
    /**
     * List all categories (optionally filtered).
     * Categories are always displayed with their parent store.
     */
    public function index(Request $request)
    {
        // 1. Base Query untuk Tabel
        $query = ProductCategory::with('store')
            ->whereHas('store', fn($q) => $q->active())
            ->withCount('products');

        // ── Filter (Tetap gunakan logika asli Anda) ──
        $query->when($request->filled('search'), function ($q) use ($request) {
            $q->where('name', 'like', '%' . $request->search . '%');
        });

        $query->when($request->filled('store_id'), fn($q) => $q->where('store_id', $request->store_id));
        $query->when($request->filled('status'), fn($q) => $q->where('is_active', $request->status === 'active'));

        $query->when($request->filled('products'), function ($q) use ($request) {
            if ($request->products === 'empty')
                $q->has('products', '=', 0);
            elseif ($request->products === 'few')
                $q->has('products', '>=', 1)->has('products', '<=', 5);
            elseif ($request->products === 'many')
                $q->has('products', '>', 5);
        });

        $query->when($request->filled('date'), fn($q) => $q->whereDate('created_at', $request->date));

        $categories = $query->latest()->get();

        // 2. AJAX Response
        if ($request->ajax()) {
            return view('product-categories._table_rows', compact('categories'))->render();
        }

        // 3. Statistik (VERSI FIX: Sinkron dengan Filter Toko)
        $statsQuery = ProductCategory::whereHas('store', fn($q) => $q->active());
        if ($request->filled('store_id')) {
            $statsQuery->where('store_id', $request->store_id);
        }

        $stats = [
            'total' => (clone $statsQuery)->count(),
            'active' => (clone $statsQuery)->where('is_active', true)->count(),
            'inactive' => (clone $statsQuery)->where('is_active', false)->count(),
            'total_products' => \App\Models\Product::whereHas('category', function ($q) use ($request) {
                $q->whereHas('store', fn($s) => $s->active());
                // Tambahkan filter ini agar angka produk sinkron dengan toko yang dipilih
                if ($request->filled('store_id')) {
                    $q->where('store_id', $request->store_id);
                }
            })->count(),
        ];

        $stores = Store::active()->orderBy('name')->get();

        return view('product-categories.index', compact('categories', 'stats', 'stores'));
    }

    /**
     * Show the form for creating a new category.
     * Passes the list of stores so the admin can assign one.
     */
    public function create(): View
    {
        $stores = Store::where('is_active', true)->orderBy('name')->get();

        return view('product-categories.create', compact('stores'));
    }

    /**
     * Persist a new category.
     * Validates that the chosen store exists and that the name is unique within that store.
     */
    public function store(CategoryRequest $request): RedirectResponse
    {
        try {
            $data = $request->validated();
            $store = Store::findOrFail($data['store_id']); // Untuk ambil nama toko

            $data['slug'] = $this->uniqueSlug((int) $data['store_id'], Str::slug($data['name']));
            $data['is_active'] = $request->has('is_active');

            $category = ProductCategory::create($data);

            // ── MULTI NOTIFICATION (TOAST) — store ───────────────────────────
            $messages = [];
            $messages[] = "Kategori ini terdaftar di toko <strong>{$store->name}</strong>.";

            $statusLabel = $data['is_active'] ? 'Aktif' : 'Nonaktif';
            $messages[] = "Status kategori diset sebagai <strong>{$statusLabel}</strong>.";

            return redirect()
                ->route('product-categories.index')
                ->with('success', [
                    'title' => "Kategori \"{$category->name}\" Berhasil Ditambahkan",
                    'list' => $messages
                ]);
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Terjadi kesalahan saat menyimpan data. Silakan coba lagi.');
        }
    }

    /**
     * Show the edit form for a category.
     */
    public function edit(ProductCategory $productCategory): View
    {
        $stores = Store::where('is_active', true)->orderBy('name')->get();

        return view('product-categories.edit', compact('productCategory', 'stores'));
    }

    /**
     * Update an existing category.
     */
    public function update(CategoryRequest $request, ProductCategory $productCategory): RedirectResponse
    {
        try {
            $data = $request->validated();

            // Simpan data lama untuk perbandingan di pesan sukses
            $oldName    = $productCategory->name;
            $oldStoreId = $productCategory->store_id;
            $wasActive  = $productCategory->is_active;

            // Deteksi perubahan SEBELUM update() agar perbandingan akurat
            $nameChanged   = $oldName !== $data['name'];
            $storeChanged  = (int) $oldStoreId !== (int) $data['store_id'];
            $statusChanged = (bool) $wasActive !== (bool) $request->has('is_active');

            // Logika Slug: Hanya re-generate jika nama atau toko berubah
            if ($data['name'] !== $oldName || (int) $data['store_id'] !== $productCategory->store_id) {
                $data['slug'] = $this->uniqueSlug((int) $data['store_id'], Str::slug($data['name']), $productCategory->id);
            }

            $data['is_active'] = $request->has('is_active');
            $productCategory->update($data);

            // ── MULTI NOTIFICATION (TOAST) — update ──────────────────────────
            $messages = [];

            if ($nameChanged) {
                $messages[] = "Nama kategori berhasil diubah.";
            }

            if ($storeChanged) {
                $newStore = Store::find($data['store_id']);
                $messages[] = "Toko kategori berhasil dipindahkan ke <strong>{$newStore->name}</strong>.";
            }

            if ($statusChanged) {
                $statusLabel = $data['is_active'] ? 'Aktif' : 'Nonaktif';
                $messages[] = "Status kategori berhasil diubah menjadi <strong>{$statusLabel}</strong>.";
            }

            // Fallback jika tidak ada perubahan terdeteksi
            if (empty($messages)) {
                return redirect()
                    ->route('product-categories.index')
                    ->with('info', 'Data kategori sudah sesuai. Tidak ada perubahan yang dilakukan.');
            }

            return redirect()
                ->route('product-categories.index')
                ->with('success', [
                    'title' => "Kategori \"{$productCategory->name}\" Berhasil Diperbarui",
                    'list'  => $messages
                ]);

        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Terjadi kesalahan saat menyimpan perubahan data kategori. Silakan coba lagi.');
        }
    }

    /**
     * Delete a category.
     * Refuses deletion when products are still linked.
     */
    public function destroy(ProductCategory $productCategory): RedirectResponse
    {
        // Cek relasi produk
        $productCount = $productCategory->products()->count();

        if ($productCount > 0) {
            return back()->with('error', "Kategori \"{$productCategory->name}\" masih memiliki {$productCount} produk. Hapus atau pindahkan produk tersebut sebelum menghapus kategori ini.");
        }

        try {
            $name = $productCategory->name;
            $productCategory->delete();

            return redirect()
                ->route('product-categories.index')
                ->with('success', "Kategori \"{$name}\" telah dihapus secara permanen dari sistem.");
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menghapus data kategori karena masalah teknis.');
        }
    }

    /**
     * Return active categories for a given store as JSON.
     * Used by the product create/edit forms to dynamically populate the category dropdown
     * after the admin selects a store, ensuring only categories from that store appear.
     */
    public function byStore(Store $store): JsonResponse
    {
        $categories = $store->productCategories()
            ->active()
            ->orderBy('name')
            ->get(['id', 'name']);

        return response()->json($categories);
    }

    // ─────────────────────────────────────────────
    // Helpers
    // ─────────────────────────────────────────────

    /**
     * Generate a slug that is unique within the given store.
     */
    private function uniqueSlug(int $storeId, string $base, ?int $exceptId = null): string
    {
        $base = $base ?: 'category';
        $slug = $base;
        $count = 1;

        while (
            ProductCategory::where('store_id', $storeId)
                ->where('slug', $slug)
                ->when($exceptId, fn($q) => $q->where('id', '!=', $exceptId))
                ->exists()
        ) {
            $slug = $base . '-' . $count++;
        }

        return $slug;
    }
}
