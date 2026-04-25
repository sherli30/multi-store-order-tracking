<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreRequest;
use Illuminate\Support\Facades\Storage;
use App\Models\Store;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class StoreController extends Controller
{
    /**
     * Display a listing of the stores with filtering.
     */
    public function index(Request $request)
    {
        $query = Store::withCount(['products', 'productCategories']);

        // ── Filter Search ──
        $query->when($request->filled('search'), function ($q) use ($request) {
            $searchTerm = $request->search;
            $q->where('name', 'like', '%' . $searchTerm . '%');
        });

        // ── Filter Status ──
        $query->when($request->filled('status'), function ($q) use ($request) {
            $q->where('is_active', $request->status === 'active');
        });

        // ── Filter Tanggal ──
        $query->when($request->filled('date'), function ($q) use ($request) {
            $q->whereDate('created_at', $request->date);
        });

        // ── Filter Jumlah Produk ──
        $query->when($request->filled('products'), function ($q) use ($request) {
            if ($request->products === 'empty') {
                $q->has('products', '=', 0);
            } elseif ($request->products === 'few') {
                $q->has('products', '>=', 1)->has('products', '<=', 5);
            } elseif ($request->products === 'many') {
                $q->has('products', '>', 5);
            }
        });

        $query->latest();

        $stores = $query->get();
        $statsQuery = Store::query();
        if ($request->filled('search')) {
            $statsQuery->where('name', 'like', '%' . $request->search . '%');
        }

        $stats = [
            'total' => (clone $statsQuery)->count(),
            'active' => (clone $statsQuery)->where('is_active', true)->count(),
            'inactive' => (clone $statsQuery)->where('is_active', false)->count(),
            'total_products' => Product::whereHas('store', function ($q) use ($request) {
                if ($request->filled('search')) {
                    $q->where('name', 'like', '%' . $request->search . '%');
                }
            })->count(),
        ];

        // JIKA REQUEST ADALAH AJAX, kembalikan partial view
        if ($request->ajax()) {
            return view('stores._table_rows', compact('stores'))->render();
        }

        return view('stores.index', compact('stores', 'stats'));
    }

    public function create(): View
    {
        return view('stores.create');
    }

    /**
     * Store a newly created store in storage.
     */
    public function store(StoreRequest $request): RedirectResponse
    {
        try {
            $data = $request->validated();
            $data['slug'] = $this->uniqueSlug($data['name']);

            $logoUploaded = $request->hasFile('logo');
            if ($logoUploaded) {
                $data['logo'] = $request->file('logo')->store('stores/logos', 'public');
            }

            $store = Store::create($data);

            // ── MULTI NOTIFICATION (TOAST) — store ───────────────────────────
            $messages = [];
            $messages[] = "Sistem telah menyimpan profil toko baru ini.";

            if ($logoUploaded) {
                $messages[] = 'Logo toko berhasil diunggah.';
            }

            $statusLabel = $data['is_active'] ? 'Aktif' : 'Nonaktif';
            $messages[] = "Status toko diset sebagai <strong>{$statusLabel}</strong>.";

            return redirect()
                ->route('stores.index')
                ->with('success', [
                    'title' => "Toko \"{$store->name}\" Berhasil Ditambahkan",
                    'list' => $messages
                ]);
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Terjadi kendala saat menyimpan data toko baru.');
        }
    }

    /**
     * Show the form for editing the specified store.
     */
    public function edit(Store $store): View
    {
        return view('stores.edit', compact('store'));
    }

    /**
     * Update the specified store in storage.
     */
    public function update(StoreRequest $request, Store $store): RedirectResponse
    {
        try {
            $data = $request->validated();

            // Deteksi perubahan SEBELUM update() agar perbandingan akurat
            $nameChanged   = $store->name !== $data['name'];
            $statusChanged = (bool) $store->is_active !== (bool) $data['is_active'];
            $logoChanged   = $request->hasFile('logo');

            $oldName = $store->name;
            $wasActive = $store->is_active;

            if ($data['name'] !== $store->name) {
                $data['slug'] = $this->uniqueSlug($data['name'], $store->id);
            }

            if ($request->hasFile('logo')) {
                // Hapus logo lama jika ada
                if ($store->logo && Storage::disk('public')->exists($store->logo)) {
                    Storage::disk('public')->delete($store->logo);
                }
                $data['logo'] = $request->file('logo')->store('stores/logos', 'public');
            }

            $store->update($data);

            // ── MULTI NOTIFICATION (TOAST) — update ──────────────────────────
            $messages = [];

            if ($nameChanged) {
                $messages[] = "Nama toko berhasil diubah.";
            }

            if ($logoChanged) {
                $messages[] = 'Logo toko berhasil diperbarui.';
            }

            if ($statusChanged) {
                $statusLabel = $data['is_active'] ? 'Aktif' : 'Nonaktif';
                $messages[] = "Status operasional toko berhasil diubah menjadi <strong>{$statusLabel}</strong>.";
            }

            // Fallback jika tidak ada perubahan terdeteksi
            if (empty($messages)) {
                return redirect()
                    ->route('stores.index')
                    ->with('info', 'Data toko sudah sesuai. Tidak ada perubahan yang dilakukan.');
            }

            return redirect()
                ->route('stores.index')
                ->with('success', [
                    'title' => "Toko \"{$store->name}\" Berhasil Diperbarui",
                    'list' => $messages
                ]);
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Terjadi kendala saat menyimpan perubahan data toko.');
        }
    }

    /**
     * Remove the specified store from storage.
     */
    public function destroy(Store $store): RedirectResponse
    {
        $name = $store->name;

        // Cek keterhubungan data sebelum hapus (Proteksi Data)
        $productCount = $store->products()->count();

        if ($productCount > 0) {
            return back()->with('error', "Toko {$name} masih memiliki {$productCount} produk. Kosongkan atau pindahkan produk terlebih dahulu.");
        }

        if ($store->logo && Storage::disk('public')->exists($store->logo)) {
            Storage::disk('public')->delete($store->logo);
        }

        try {
            $store->delete();

            return redirect()
                ->route('stores.index')
                ->with('success', "Toko {$name} telah dihapus secara permanen dari sistem.");
        } catch (\Exception $e) {
            return back()->with('error', 'Sistem gagal menghapus toko karena ada kendala pada database.');
        }
    }

    /**
     * Generate a unique slug for a store.
     */
    private function uniqueSlug(string $name, ?int $exceptId = null): string
    {
        $base = Str::slug($name);
        $slug = $base;
        $count = 1;

        while (
            Store::where('slug', $slug)
                ->when($exceptId, fn($q) => $q->where('id', '!=', $exceptId))
                ->exists()
        ) {
            $slug = $base . '-' . $count++;
        }

        return $slug;
    }
}
