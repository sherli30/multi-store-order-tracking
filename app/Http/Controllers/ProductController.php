<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductRequest;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\Store;
use App\Services\StockService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function __construct(private readonly StockService $stockService)
    {
    }

    /**
     * Display a listing of all products.
     * Products are always queried with their store and category eager-loaded.
     * All filters respect the Store → Category → Product hierarchy.
     */
    public function show(Product $product)
    {
        return redirect()->route('products.index');
    }

    public function index(Request $request)
    {
        // Only load products if both their category and store are active.
        $query = Product::with(['store', 'category', 'variants'])
            ->whereHas('store', fn($q) => $q->active())
            ->whereHas('category', fn($q) => $q->active())
            ->latest();

        // ── Filter: store — primary scope ─────────────────────────────────
        $query->when($request->filled('store_id'), function ($q) use ($request) {
            $q->where('store_id', $request->store_id);
        });

        // ── Filter: category — secondary scope (scoped to the store above) ──
        $query->when($request->filled('category_id'), function ($q) use ($request) {
            $q->where('category_id', $request->category_id);
        });

        // ── Filter: stock ─────────────────────────────────────────────────
        $query->when($request->filled('stock'), function ($q) use ($request) {
            if ($request->stock == 'empty')
                $q->outOfStock();
            elseif ($request->stock == 'low')
                $q->lowStock();
            elseif ($request->stock == 'available')
                $q->availableStock();
        });

        // ── Filter: price ─────────────────────────────────────────────────
        $query->when($request->filled('price'), function ($q) use ($request) {
            $q->whereHas('variants', function ($v) use ($request) {
                $v->where('is_active', true);
                if ($request->price == 'low')
                    $v->where('price', '<', 50000);
                elseif ($request->price == 'medium')
                    $v->whereBetween('price', [50000, 200000]);
                elseif ($request->price == 'high')
                    $v->where('price', '>', 200000);
            });
        });

        // ── Filter: status ────────────────────────────────────────────────
        $query->when($request->filled('status'), function ($q) use ($request) {
            $q->where('is_active', $request->status === 'active');
        });

        $products = $query->get();

        // AJAX: return only the table rows partial
        if ($request->ajax()) {
            return view('products._table_rows', compact('products'))->render();
        }

        // For the filter dropdowns: active stores, active categories scoped to store
        $stores = Store::active()->orderBy('name')->get();

        $categoriesQuery = ProductCategory::available()->orderBy('name');
        if ($request->filled('store_id')) {
            $categoriesQuery->where('store_id', $request->store_id);
        }
        $categories = $categoriesQuery->get();

        // Base query for stats (applying the active parent rule)
        $statsQuery = Product::query()
            ->whereHas('store', fn($q) => $q->active())
            ->whereHas('category', fn($q) => $q->active());

        $stats = [
            'total' => (clone $statsQuery)->count(),
            'active' => (clone $statsQuery)->active()->count(),
            'inactive' => (clone $statsQuery)->where('is_active', false)->count(),
            'low_stock' => (clone $statsQuery)->lowStock()->count(),
        ];

        return view('products.index', compact('stores', 'categories', 'products', 'stats'));
    }

    /**
     * Show the form for creating a new product.
     */
    public function create(): View
    {
        $stores = Store::active()->orderBy('name')->get();

        return view('products.create', compact('stores'));
    }

    /**
     * Store a newly created product.
     * ProductRequest already validates that category_id belongs to store_id.
     */
    public function store(ProductRequest $request): RedirectResponse
    {
        try {
            DB::beginTransaction();

            $data = $request->validated();
            $data['slug'] = $this->uniqueSlug($data['store_id'], $data['name']);

            $product = Product::create([
                'store_id' => $data['store_id'],
                'category_id' => $data['category_id'],
                'name' => $data['name'],
                'slug' => $data['slug'],
                'is_active' => $data['is_active'],
            ]);

            // Save Variants
            if ($request->has_variants) {
                foreach ($data['variants'] as $variantData) {
                    $variant = $product->variants()->create([
                        'name' => $variantData['name'],
                        'sku' => $variantData['sku'] ?? null,
                        'price' => $variantData['price'],
                        'stock' => 0, // Stock will be added via StockService
                        'weight' => $variantData['weight'],
                        'is_active' => true,
                    ]);

                    if ($variantData['stock'] > 0) {
                        $this->stockService->addStock(
                            variant: $variant,
                            qty: (int) $variantData['stock'],
                            source: 'initial_stock'
                        );
                    }
                }
            } else {
                $variant = $product->variants()->create([
                    'name' => 'Default',
                    'price' => $data['price'],
                    'stock' => 0,
                    'weight' => $data['weight'],
                    'is_active' => true,
                ]);

                if ($data['stock'] > 0) {
                    $this->stockService->addStock(
                        variant: $variant,
                        qty: (int) $data['stock'],
                        source: 'initial_stock'
                    );
                }
            }

            // Save Images
            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $index => $file) {
                    $path = $file->store('products', 'public');
                    $product->images()->create([
                        'image_path' => $path,
                        'is_primary' => ($index == ($data['primary_image_index'] ?? 0)),
                        'sort_order' => $index,
                    ]);
                }
            }

            // Save Descriptions
            if (!empty($data['descriptions'])) {
                foreach ($data['descriptions'] as $index => $desc) {
                    $product->descriptions()->create([
                        'title' => $desc['title'],
                        'content' => $desc['content'],
                        'sort_order' => $index,
                    ]);
                }
            }

            // Save Specifications
            if (!empty($data['specifications'])) {
                foreach ($data['specifications'] as $index => $spec) {
                    $product->specifications()->create([
                        'name' => $spec['name'],
                        'value' => $spec['value'],
                        'sort_order' => $index,
                    ]);
                }
            }

            // Save Packing Options
            if (!empty($data['packing_options'])) {
                foreach ($data['packing_options'] as $po) {
                    $product->packingOptions()->create([
                        'name' => $po['name'],
                        'extra_price' => $po['extra_price'],
                        'is_active' => true,
                    ]);
                }
            }

            DB::commit();

            // ── MULTI NOTIFICATION (TOAST) — store ───────────────────────────
            $messages = [];

            if ($request->has_variants) {
                $variantCount = count($data['variants']);
                $messages[] = "{$variantCount} varian produk berhasil disimpan.";
            } else {
                $messages[] = 'Varian default berhasil disimpan.';
            }

            if ($request->hasFile('images')) {
                $imageCount = count($request->file('images'));
                $messages[] = "{$imageCount} foto produk berhasil diunggah.";
            }

            if (!empty($data['descriptions'])) {
                $messages[] = count($data['descriptions']) . ' deskripsi produk berhasil disimpan.';
            }

            if (!empty($data['specifications'])) {
                $messages[] = count($data['specifications']) . ' spesifikasi produk berhasil disimpan.';
            }

            if (!empty($data['packing_options'])) {
                $messages[] = count($data['packing_options']) . ' opsi packing berhasil disimpan.';
            }

            return redirect()
                ->route('products.index')
                ->with('success', [
                    'title' => "Produk \"{$product->name}\" Berhasil Ditambahkan",
                    'list' => $messages
                ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Terjadi kesalahan teknis saat menyimpan data produk: ' . $e->getMessage());
        }
    }

    /**
     * Show the form for editing an existing product.
     */
    public function edit(Product $product): View
    {
        $product->load(['variants', 'images', 'descriptions', 'specifications', 'packingOptions']);

        $stores = Store::active()->orderBy('name')->get();

        $categories = ProductCategory::where('store_id', $product->store_id)
            ->active()
            ->orderBy('name')
            ->get();

        return view('products.edit', compact('stores', 'categories', 'product'));
    }

    /**
     * Update an existing product.
     */
    public function update(ProductRequest $request, Product $product): RedirectResponse
    {
        try {
            DB::beginTransaction();

            $data = $request->validated();

            // Deteksi perubahan SEBELUM update() agar perbandingan akurat
            $nameChanged       = $product->name !== $data['name'];
            $storeChanged      = (int) $product->store_id !== (int) $data['store_id'];
            $categoryChanged   = (int) $product->category_id !== (int) $data['category_id'];
            $statusChanged     = (bool) $product->is_active !== (bool) $data['is_active'];
            $imagesDeleted     = !empty($data['deleted_images']);
            $imagesReplaced    = $request->hasFile('replace_images');
            $imagesAdded       = $request->hasFile('images');

            $currentPrimary    = $product->images()->where('is_primary', true)->first();
            $primaryChanged    = isset($data['primary_image_id']) && (!$currentPrimary || (int)$currentPrimary->id !== (int)$data['primary_image_id']);

            // Compare Variants / Single Price & Weight
            $singlePriceChanged  = false;
            $singleWeightChanged = false;
            $variantsChanged     = false;

            if (!$request->has_variants) {
                $defaultVariant = $product->variants()->first();
                if ($defaultVariant) {
                    $singlePriceChanged  = (int) $defaultVariant->price !== (int) $data['price'];
                    $singleWeightChanged = (int) $defaultVariant->weight !== (int) $data['weight'];
                } else {
                    $singlePriceChanged = true;
                }
            } else {
                $oldVariants = $product->variants->where('name', '!=', 'Default')->map(fn($v) => ['name' => $v->name, 'sku' => $v->sku, 'price' => (int) $v->price, 'weight' => (int) $v->weight])->values()->toArray();
                $newVariants = collect($data['variants'] ?? [])->map(fn($v) => ['name' => $v['name'], 'sku' => $v['sku'] ?? null, 'price' => (int) $v['price'], 'weight' => (int) $v['weight']])->values()->toArray();
                $variantsChanged = json_encode($oldVariants) !== json_encode($newVariants);
            }

            // Compare Descriptions
            $oldDesc = $product->descriptions->map(fn($d) => ['title' => $d->title, 'content' => $d->content])->values()->toArray();
            $newDesc = collect($data['descriptions'] ?? [])->map(fn($d) => ['title' => $d['title'], 'content' => $d['content']])->values()->toArray();
            $descriptionsChanged = json_encode($oldDesc) !== json_encode($newDesc);

            // Compare Specifications
            $oldSpec = $product->specifications->map(fn($s) => ['name' => $s->name, 'value' => $s->value])->values()->toArray();
            $newSpec = collect($data['specifications'] ?? [])->map(fn($s) => ['name' => $s['name'], 'value' => $s['value']])->values()->toArray();
            $specificationsChanged = json_encode($oldSpec) !== json_encode($newSpec);

            // Compare Packing Options
            $oldPack = $product->packingOptions->map(fn($p) => ['name' => $p->name, 'extra_price' => (int) $p->extra_price])->values()->toArray();
            $newPack = collect($data['packing_options'] ?? [])->map(fn($p) => ['name' => $p['name'], 'extra_price' => (int) $p['extra_price']])->values()->toArray();
            $packingChanged = json_encode($oldPack) !== json_encode($newPack);

            if ($data['name'] !== $product->name || $data['store_id'] != $product->store_id) {
                $data['slug'] = $this->uniqueSlug($data['store_id'], $data['name'], $product->id);
            }

            $product->update([
                'store_id' => $data['store_id'],
                'category_id' => $data['category_id'],
                'name' => $data['name'],
                'slug' => $data['slug'] ?? $product->slug,
                'is_active' => $data['is_active'],
            ]);

            // Sync Variants
            // For simplicity in update: delete omitted variants or update existing, create new
            $existingVariantIds = $product->variants()->pluck('id')->toArray();
            $keptVariantIds = [];

            if ($request->has_variants) {
                foreach ($data['variants'] as $variantData) {
                    if (!empty($variantData['id']) && in_array($variantData['id'], $existingVariantIds)) {
                        // Update existing
                        $variant = $product->variants()->find($variantData['id']);
                        $variant->update([
                            'name' => $variantData['name'],
                            'sku' => $variantData['sku'] ?? null,
                            'price' => $variantData['price'],
                            'weight' => $variantData['weight'],
                        ]);
                        $keptVariantIds[] = $variant->id;
                    } else {
                        // Create new
                        $variant = $product->variants()->create([
                            'name' => $variantData['name'],
                            'sku' => $variantData['sku'] ?? null,
                            'price' => $variantData['price'],
                            'stock' => 0,
                            'weight' => $variantData['weight'],
                            'is_active' => true,
                        ]);
                        $keptVariantIds[] = $variant->id;
                    }
                }
            } else {
                // Single default variant
                $defaultVariant = $product->variants()->first() ?? $product->variants()->create(['name' => 'Default', 'stock' => 0]);
                $defaultVariant->update([
                    'name' => 'Default',
                    'price' => $data['price'],
                    'weight' => $data['weight'],
                ]);
                $keptVariantIds[] = $defaultVariant->id;
            }

            // Remove deleted variants (if safe)
            // Ideally, we shouldn't delete variants if they have order_items. We should just deactivate them.
            $product->variants()->whereNotIn('id', $keptVariantIds)->update(['is_active' => false]);

            // Sync Images: Delete requested images
            if (!empty($data['deleted_images'])) {
                foreach ($data['deleted_images'] as $imgId) {
                    $img = $product->images()->find($imgId);
                    if ($img) {
                        Storage::disk('public')->delete($img->image_path);
                        $img->delete();
                    }
                }
            }

            // Replace specific images
            if ($request->hasFile('replace_images')) {
                foreach ($request->file('replace_images') as $imgId => $file) {
                    $img = $product->images()->find($imgId);
                    if ($img) {
                        Storage::disk('public')->delete($img->image_path);
                        $path = $file->store('products', 'public');
                        $img->update(['image_path' => $path]);
                    }
                }
            }

            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $index => $file) {
                    $path = $file->store('products', 'public');
                    $product->images()->create([
                        'image_path' => $path,
                        'is_primary' => false,
                        'sort_order' => 99, // push to end
                    ]);
                }
            }

            // Update primary image
            if (isset($data['primary_image_id'])) {
                $product->images()->update(['is_primary' => false]);
                $product->images()->where('id', $data['primary_image_id'])->update(['is_primary' => true]);
            }

            // Sync Descriptions (Delete all and recreate for simplicity)
            $product->descriptions()->delete();
            if (!empty($data['descriptions'])) {
                foreach ($data['descriptions'] as $index => $desc) {
                    $product->descriptions()->create([
                        'title' => $desc['title'],
                        'content' => $desc['content'],
                        'sort_order' => $index,
                    ]);
                }
            }

            // Sync Specifications
            $product->specifications()->delete();
            if (!empty($data['specifications'])) {
                foreach ($data['specifications'] as $index => $spec) {
                    $product->specifications()->create([
                        'name' => $spec['name'],
                        'value' => $spec['value'],
                        'sort_order' => $index,
                    ]);
                }
            }

            // Sync Packing Options
            $product->packingOptions()->delete();
            if (!empty($data['packing_options'])) {
                foreach ($data['packing_options'] as $po) {
                    $product->packingOptions()->create([
                        'name' => $po['name'],
                        'extra_price' => $po['extra_price'],
                        'is_active' => true,
                    ]);
                }
            }

            DB::commit();

            // ── MULTI NOTIFICATION (TOAST) — update ──────────────────────────
            $messages = [];

            if ($nameChanged) {
                $messages[] = "Nama produk berhasil diubah.";
            }

            if ($storeChanged) {
                $messages[] = 'Toko produk berhasil diperbarui.';
            }

            if ($categoryChanged) {
                $messages[] = 'Kategori produk berhasil diperbarui.';
            }

            if ($statusChanged) {
                $statusLabel = $data['is_active'] ? 'Aktif' : 'Nonaktif';
                $messages[] = "Status produk berhasil diubah menjadi <strong>{$statusLabel}</strong>.";
            }

            if ($singlePriceChanged) {
                $messages[] = "Harga produk berhasil diperbarui menjadi <strong>Rp " . number_format($data['price'], 0, ',', '.') . "</strong>.";
            }

            if ($singleWeightChanged) {
                $messages[] = "Berat produk berhasil diperbarui menjadi <strong>{$data['weight']} gram</strong>.";
            }

            if ($variantsChanged) {
                $messages[] = 'Data varian produk berhasil diperbarui.';
            }

            if ($imagesDeleted) {
                $messages[] = count($data['deleted_images']) . ' foto produk berhasil dihapus.';
            }

            if ($imagesReplaced) {
                $messages[] = 'Foto produk berhasil diganti.';
            }

            if ($imagesAdded) {
                $imageCount = count($request->file('images'));
                $messages[] = "{$imageCount} foto baru berhasil diunggah.";
            }

            if ($primaryChanged) {
                $messages[] = 'Foto utama produk berhasil diperbarui.';
            }

            if ($descriptionsChanged) {
                $messages[] = 'Deskripsi produk berhasil diperbarui.';
            }

            if ($specificationsChanged) {
                $messages[] = 'Spesifikasi produk berhasil diperbarui.';
            }

            if ($packingChanged) {
                $messages[] = 'Opsi packing produk berhasil diperbarui.';
            }

            // Fallback jika tidak ada perubahan terdeteksi
            if (empty($messages)) {
                return redirect()
                    ->route('products.index')
                    ->with('info', 'Data produk sudah sesuai. Tidak ada perubahan yang dilakukan.');
            }

            return redirect()
                ->route('products.index')
                ->with('success', [
                    'title' => "Produk \"{$product->name}\" Berhasil Diperbarui",
                    'list' => $messages
                ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Terjadi kesalahan saat update data: ' . $e->getMessage());
        }
    }

    /**
     * Soft-delete a product (leaves it in DB, never hard-deletes).
     * Image files are also cleaned up.
     */
    public function destroy(Product $product): RedirectResponse
    {
        $name = $product->name;

        try {
            // Delete images
            foreach ($product->images as $image) {
                Storage::disk('public')->delete($image->image_path);
                $image->delete();
            }

            $product->delete();

            return redirect()
                ->route('products.index')
                ->with('success', "Produk \"{$name}\" berhasil dihapus secara permanen dari sistem.");
        } catch (\Exception $e) {
            return back()->with('error', "Gagal menghapus produk \"{$name}\". Terjadi kesalahan pada sistem, silakan coba lagi.");
        }
    }

    public function byStore(Store $store): JsonResponse
    {
        $categories = $store->productCategories()
            ->active()
            ->orderBy('name')
            ->get(['id', 'name']);

        return response()->json($categories);
    }

    /**
     * Generate a slug that is unique within the given store.
     */
    private function uniqueSlug(int $storeId, string $name, ?int $exceptId = null): string
    {
        $base = Str::slug($name);
        $slug = $base;
        $count = 1;

        while (
            Product::where('store_id', $storeId)
                ->where('slug', $slug)
                ->when($exceptId, fn($q) => $q->where('id', '!=', $exceptId))
                ->exists()
        ) {
            $slug = $base . '-' . $count++;
        }

        return $slug;
    }

    /**
     * Remove a product image permanently.
     */
    public function destroyImage(\App\Models\ProductImage $image): RedirectResponse
    {
        try {
            if (Storage::disk('public')->exists($image->image_path)) {
                Storage::disk('public')->delete($image->image_path);
            }
            $image->delete();
            return back()->with('success', 'Foto produk berhasil dihapus dari sistem.');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menghapus foto produk. Silakan coba lagi.');
        }
    }
}
