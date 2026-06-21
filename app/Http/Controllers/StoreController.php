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

        // ── Filter Wilayah ──
        $query->when($request->filled('province_id'), function ($q) use ($request) {
            $q->where('province_id', $request->province_id);
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
        // Hapus logika search untuk mematuhi aturan "jangan tambahkan cari"

        $stats = [
            'total' => (clone $statsQuery)->count(),
            'active' => (clone $statsQuery)->where('is_active', true)->count(),
            'inactive' => (clone $statsQuery)->where('is_active', false)->count(),
            'total_products' => \App\Models\Product::count(),
        ];

        // JIKA REQUEST ADALAH AJAX, kembalikan partial view
        if ($request->ajax()) {
            return view('stores._table_rows', compact('stores'))->render();
        }

        $provinces = \App\Models\Province::orderBy('name')->get();

        return view('stores.index', compact('stores', 'stats', 'provinces'));
    }

    public function create(): View
    {
        $provinces = \App\Models\Province::orderBy('name')->get();
        return view('stores.create', compact('provinces'));
    }

    /**
     * Store a newly created store in storage.
     */
    public function store(StoreRequest $request): RedirectResponse
    {
        try {
            $data = $request->validated();

            $logoUploaded = $request->hasFile('logo');
            if ($logoUploaded) {
                $data['logo'] = $request->file('logo')->store('avatars', 'public');
            }

            $store = Store::create($data);

            // ── MULTI NOTIFICATION (TOAST) — store ───────────────────────────
            $messages = [];
            $messages[] = "Profil toko <strong>{$store->name}</strong> telah berhasil didaftarkan ke sistem.";
            $messages[] = "Alamat toko telah diset di {$store->city->name}, {$store->province->name}.";
            $messages[] = "Informasi kontak dan jam operasional telah disimpan.";

            if ($logoUploaded) {
                $messages[] = 'Logo identitas toko berhasil diunggah dan dikaitkan.';
            }

            $statusLabel = $data['is_active'] ? 'Aktif' : 'Nonaktif';
            $messages[] = "Toko saat ini dalam status operasional <strong>{$statusLabel}</strong>.";

            return redirect()
                ->route('stores.index')
                ->with('success', [
                    'title' => "Pendaftaran Toko Berhasil",
                    'list' => $messages
                ]);
        } catch (\Exception $e) {
            return back()->withInput()->with('error', [
                'title' => 'Gagal Menyimpan Toko',
                'list' => [
                    'Gagal menyimpan data toko baru.',
                    'Pastikan koneksi stabil atau hubungi administrator jika masalah berlanjut.'
                ]
            ]);
        }
    }

    /**
     * Display the specified store.
     */
    public function show(Store $store): View
    {
        $store->loadCount(['products', 'productCategories', 'orders']);
        $store->load(['province', 'city']);
        return view('stores.show', compact('store'));
    }

    /**
     * Show the form for editing the specified store.
     */
    public function edit(Store $store): View
    {
        $provinces = \App\Models\Province::orderBy('name')->get();
        $cities = \App\Models\City::where('province_id', $store->province_id)->orderBy('name')->get();
        return view('stores.edit', compact('store', 'provinces', 'cities'));
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
            $locationChanged = $store->province_id != $data['province_id'] || $store->city_id != $data['city_id'] || $store->address !== $data['address'];
            $phoneChanged = $store->phone !== $data['phone'];
            $hoursChanged = $store->operational_hours !== $data['operational_hours'];
            $descChanged = $store->description !== $data['description'];

            $oldName = $store->name;
            $wasActive = $store->is_active;

            if ($request->hasFile('logo')) {
                // Hapus logo lama jika ada
                if ($store->logo && Storage::disk('public')->exists($store->logo)) {
                    Storage::disk('public')->delete($store->logo);
                }
                $data['logo'] = $request->file('logo')->store('avatars', 'public');
            }

            $store->update($data);

            // ── MULTI NOTIFICATION (TOAST) — update ──────────────────────────
            $messages = [];

            if ($nameChanged) {
                $messages[] = "Nama toko berhasil diubah menjadi <strong>{$data['name']}</strong>.";
            }



            if ($locationChanged) {
                $store->load(['city', 'province']);
                $messages[] = "Lokasi operasional toko telah dipindahkan ke <strong>{$store->city->name}, {$store->province->name}</strong>.";
                $messages[] = "Ongkos kirim (Reguler & Cargo) untuk pesanan baru akan dihitung otomatis dari lokasi baru ini.";
            }

            if ($phoneChanged) {
                $messages[] = "Nomor telepon toko telah diperbarui.";
            }

            if ($hoursChanged) {
                $messages[] = "Jam operasional toko telah diperbarui.";
            }

            if ($descChanged) {
                $messages[] = "Deskripsi toko telah diperbarui.";
            }

            if ($logoChanged) {
                $messages[] = 'Logo toko berhasil diperbarui dengan gambar baru.';
            }

            if ($statusChanged) {
                $statusLabel = $data['is_active'] ? 'Aktif' : 'Nonaktif';
                $messages[] = "Status operasional toko berhasil diubah menjadi <strong>{$statusLabel}</strong>.";
                
                if (!$data['is_active']) {
                    $messages[] = "Semua kategori dan produk di toko ini akan tidak tersedia bagi customer.";
                } else {
                    $messages[] = "Semua kategori dan produk aktif di toko ini kembali tersedia bagi customer.";
                }
            }

            // Fallback jika tidak ada perubahan terdeteksi
            if (empty($messages)) {
                return redirect()
                    ->route('stores.index')
                    ->with('info', [
                        'title' => 'Data Sudah Sesuai',
                        'list' => [
                            'Data toko sudah sesuai. Tidak ada perubahan yang dilakukan.'
                        ]
                    ]);
            }

            return redirect()
                ->route('stores.index')
                ->with('success', [
                    'title' => "Toko \"{$store->name}\" Berhasil Diperbarui",
                    'list' => $messages
                ]);
        } catch (\Exception $e) {
            return back()->withInput()->with('error', [
                'title' => 'Gagal Menyimpan Perubahan',
                'list' => [
                    'Gagal menyimpan perubahan data toko.',
                    'Muat ulang halaman dan coba kembali, atau hubungi administrator jika masalah berlanjut.'
                ]
            ]);
        }
    }

    /**
     * Remove the specified store from storage.
     */
    public function destroy(\App\Http\Requests\StoreDeleteRequest $request, Store $store): RedirectResponse
    {
        $name = $store->name;

        if ($store->logo && Storage::disk('public')->exists($store->logo)) {
            Storage::disk('public')->delete($store->logo);
        }

        try {
            $store->delete();

            return redirect()
                ->route('stores.index')
                ->with('success', [
                    'title' => 'Toko Dihapus',
                    'list' => [
                        "Toko <strong>{$name}</strong> beserta seluruh profilnya telah dihapus secara permanen dari sistem."
                    ]
                ]);
        } catch (\Exception $e) {
            return back()->with('error', [
                'title' => 'Toko Gagal Dihapus',
                'list' => [
                    'Gagal menghapus toko karena data sedang digunakan oleh sistem lain.',
                    'Muat ulang halaman dan coba kembali.'
                ]
            ]);
        }
    }

    /**
     * Remove the store's logo permanently.
     */
    public function destroyLogo(Store $store): RedirectResponse
    {
        if ($store->logo && Storage::disk('public')->exists($store->logo)) {
            Storage::disk('public')->delete($store->logo);
            $store->update(['logo' => null]);
            return back()->with('success', [
                'title' => 'Logo Toko Dihapus',
                'list' => [
                    'File logo lama berhasil dihapus dari penyimpanan.'
                ]
            ]);
        }
        return back()->with('error', [
            'title' => 'Logo Gagal Dihapus',
            'list' => [
                'Logo tidak ditemukan atau sistem telah menghapusnya sebelumnya.'
            ]
        ]);
    }

    /**
     * Update the store's active status.
     */
    public function updateStatus(\App\Http\Requests\StoreStatusRequest $request, Store $store): RedirectResponse
    {
        try {
            $store->update(['is_active' => $request->is_active]);

            $message = $request->is_active ? 'Toko berhasil diaktifkan.' : 'Toko berhasil dinonaktifkan.';

            return back()->with('success', [
                'title' => 'Status Berhasil Diubah',
                'list' => [
                    $message
                ]
            ]);
        } catch (\Exception $e) {
            return back()->with('error', [
                'title' => 'Gagal mengubah status toko',
                'list' => [
                    'Terjadi kesalahan saat memperbarui status toko.'
                ]
            ]);
        }
    }
}
