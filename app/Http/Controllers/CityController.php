<?php

namespace App\Http\Controllers;

use App\Http\Requests\CityRequest;
use App\Http\Requests\CityDeleteRequest;
use App\Http\Requests\CityStatusRequest;
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

        return redirect()->route('cities.index')->with('success', [
            'title' => 'Kota Ditambahkan',
            'list' => [
                'Kota "<strong>' . $request->name . '</strong>" berhasil ditambahkan.'
            ]
        ]);
    }

    public function show(City $city)
    {
        $city->load('province');
        $city->loadCount('stores');
        return view('regions.cities.show', compact('city'));
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

        return redirect()->route('cities.index')->with('success', [
            'title' => 'Kota Diperbarui',
            'list' => [
                $msg
            ]
        ]);
    }

    public function updateStatus(CityStatusRequest $request, City $city)
    {
        $statusLabel = $request->is_active ? 'diaktifkan' : 'dinonaktifkan';

        try {
            $city->update([
                'is_active' => $request->is_active
            ]);

            return redirect()->back()->with('success', [
                'title' => 'Status Diperbarui',
                'list' => [
                    "Kota \"<strong>{$city->name}</strong>\" berhasil {$statusLabel}."
                ]
            ]);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', [
                'title' => 'Gagal Memperbarui Status',
                'list' => [
                    "Gagal mengubah status kota \"<strong>{$city->name}</strong>\"."
                ]
            ]);
        }
    }

    public function destroy(CityDeleteRequest $request, City $city)
    {
        $cityName = $city->name;

        try {
            $city->delete();

            return redirect()
                ->route('cities.index')
                ->with('success', [
                    'title' => 'Kota Dihapus',
                    'list' => [
                        'Kota "<strong>' . $cityName . '</strong>" telah berhasil dihapus secara permanen dari sistem.'
                    ]
                ]);
        } catch (\Exception $e) {
            return back()->with('error', [
                'title' => 'Gagal Menghapus Kota',
                'list' => [
                    "Gagal menghapus kota <strong>{$cityName}</strong>.",
                    'Data sedang digunakan oleh toko atau riwayat pesanan aktif.'
                ]
            ]);
        }
    }
}
