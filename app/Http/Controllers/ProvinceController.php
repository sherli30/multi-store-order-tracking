<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProvinceRequest;
use App\Http\Requests\ProvinceDeleteRequest;
use App\Http\Requests\ProvinceStatusRequest;
use App\Models\Province;
use Illuminate\Http\Request;

class ProvinceController extends Controller
{
    public function index(Request $request)
    {
        $query = Province::withCount('cities');

        $query->when($request->filled('has_city'), function ($q) use ($request) {
            if ($request->has_city == 'yes') {
                $q->having('cities_count', '>', 0);
            } elseif ($request->has_city == 'no') {
                $q->having('cities_count', 0);
            }
        });

        $query->when($request->filled('city_count'), function ($q) use ($request) {
            $q->having('cities_count', '>=', $request->city_count);
        });

        $provinces = $query->orderBy('name')->get();

        if ($request->ajax()) {
            return view('regions.provinces._table_rows', compact('provinces'))->render();
        }

        return view('regions.provinces.index', compact('provinces'));
    }

    public function store(ProvinceRequest $request)
    {
        Province::create($request->validated());

        return redirect()->route('provinces.index')->with('success', [
            'title' => 'Provinsi Ditambahkan',
            'list' => [
                'Provinsi "<strong>' . $request->name . '</strong>" berhasil ditambahkan.'
            ]
        ]);
    }

    public function show(Province $province)
    {
        $province->loadCount('cities');
        return view('regions.provinces.show', compact('province'));
    }

    public function update(ProvinceRequest $request, Province $province)
    {
        $province->fill($request->validated());
        $dirty = $province->getDirty();

        $province->save();

        if (isset($dirty['name'])) {
            $msg = 'Nama provinsi berhasil diperbarui menjadi "' . $province->name . '".';
        } else {
            $msg = 'Informasi provinsi "' . $province->name . '" berhasil diperbarui tanpa perubahan.';
        }

        return redirect()->route('provinces.index')->with('success', [
            'title' => 'Provinsi Diperbarui',
            'list' => [
                $msg
            ]
        ]);
    }

    public function updateStatus(ProvinceStatusRequest $request, Province $province)
    {
        $statusLabel = $request->is_active ? 'diaktifkan' : 'dinonaktifkan';

        try {
            $province->update([
                'is_active' => $request->is_active
            ]);

            return redirect()->back()->with('success', [
                'title' => 'Status Diperbarui',
                'list' => [
                    "Provinsi \"<strong>{$province->name}</strong>\" berhasil {$statusLabel}."
                ]
            ]);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', [
                'title' => 'Gagal Memperbarui Status',
                'list' => [
                    "Gagal mengubah status provinsi \"<strong>{$province->name}</strong>\"."
                ]
            ]);
        }
    }

    public function destroy(ProvinceDeleteRequest $request, Province $province)
    {
        $provinceName = $province->name;

        try {
            $province->delete();

            return redirect()
                ->route('provinces.index')
                ->with('success', [
                    'title' => 'Provinsi Dihapus',
                    'list' => [
                        'Provinsi "<strong>' . $provinceName . '</strong>" telah berhasil dihapus secara permanen dari sistem.'
                    ]
                ]);
        } catch (\Exception $e) {
            return back()->with('error', [
                'title' => 'Gagal Menghapus Provinsi',
                'list' => [
                    "Gagal menghapus provinsi <strong>{$provinceName}</strong>.",
                    'Data sedang digunakan oleh entitas lain (contoh: toko atau pesanan).'
                ]
            ]);
        }
    }
}
