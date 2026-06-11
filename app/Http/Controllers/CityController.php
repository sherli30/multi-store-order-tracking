<?php

namespace App\Http\Controllers;

use App\Http\Requests\CityRequest;
use App\Models\City;
use App\Models\Province;
use Illuminate\Http\Request;

class CityController extends Controller
{
    public function index(Request $request)
    {
        $query = City::with('province');

        $query->when($request->filled('province_id'), fn($q) => $q->where('province_id', $request->province_id));
        $query->when($request->filled('type'), fn($q) => $q->where('type', $request->type));
        $query->when($request->filled('postal_code'), fn($q) => $q->where('postal_code', 'like', '%' . $request->postal_code . '%'));

        $cities = $query->orderBy('name')->get();
        $provinces = Province::orderBy('name')->get();

        if ($request->ajax()) {
            return view('regions.cities._table_rows', compact('cities'))->render();
        }

        return view('regions.cities.index', compact('cities', 'provinces'));
    }

    public function store(CityRequest $request)
    {
        City::create($request->validated());

        return redirect()->route('cities.index')->with('success', 'Kota "' . $request->name . '" berhasil ditambahkan.');
    }

    public function update(CityRequest $request, City $city)
    {
        $city->fill($request->validated());
        $dirty = $city->getDirty();

        $changes = [];
        if (isset($dirty['name'])) {
            $changes[] = 'nama';
        }
        if (isset($dirty['province_id'])) {
            $changes[] = 'provinsi';
        }
        if (isset($dirty['type'])) {
            $changes[] = 'tipe';
        }
        if (isset($dirty['postal_code'])) {
            $changes[] = 'kodepos';
        }

        $city->save();

        if (count($changes) > 0) {
            if (count($changes) === 1) {
                $changeStr = $changes[0];
            } elseif (count($changes) === 2) {
                $changeStr = implode(' dan ', $changes);
            } else {
                $last = array_pop($changes);
                $changeStr = implode(', ', $changes) . ', dan ' . $last;
            }
            $msg = 'Detail ' . $changeStr . ' kota "' . $city->name . '" berhasil diperbarui.';
        } else {
            $msg = 'Informasi kota "' . $city->name . '" berhasil diperbarui tanpa perubahan.';
        }

        return redirect()->route('cities.index')->with('success', $msg);
    }

    public function destroy(City $city)
    {
        $cityName = $city->name;

        try {
            $city->delete();

            return redirect()
                ->route('cities.index')
                ->with('success', 'Kota "' . $cityName . '" telah berhasil dihapus secara permanen dari sistem.');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menghapus kota "' . $cityName . '". Terjadi kesalahan pada sistem, silakan coba lagi.');
        }
    }
}
