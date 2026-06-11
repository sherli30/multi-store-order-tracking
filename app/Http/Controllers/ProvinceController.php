<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProvinceRequest;
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

        return redirect()->route('provinces.index')->with('success', 'Provinsi "' . $request->name . '" berhasil ditambahkan.');
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

        return redirect()->route('provinces.index')->with('success', $msg);
    }

    public function destroy(Province $province)
    {
        $provinceName = $province->name;

        try {
            if ($province->cities()->count() > 0) {
                return back()->with('error', 'Gagal menghapus! Provinsi "' . $provinceName . '" masih memiliki data kota/kabupaten terkait di sistem.');
            }

            $province->delete();

            return redirect()
                ->route('provinces.index')
                ->with('success', 'Provinsi "' . $provinceName . '" telah berhasil dihapus secara permanen dari sistem.');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menghapus provinsi "' . $provinceName . '". Terjadi kesalahan pada sistem, silakan coba lagi.');
        }
    }
}
