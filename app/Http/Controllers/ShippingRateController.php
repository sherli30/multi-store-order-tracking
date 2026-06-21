<?php

namespace App\Http\Controllers;

use App\Models\City;
use App\Models\Province;
use App\Models\ShippingRate;
use App\Models\ShippingService;
use App\Http\Requests\ShippingRateRequest;
use App\Http\Requests\ShippingRateStatusRequest;
use App\Http\Requests\ShippingRateDeleteRequest;
use Illuminate\Http\Request;

class ShippingRateController extends Controller
{
    public function index(Request $request)
    {
        $query = ShippingRate::with(['service.courier', 'originCity', 'destinationCity']);

        $query->when($request->filled('service_id'), fn($q) => $q->where('shipping_service_id', $request->service_id));
        $query->when($request->filled('origin_city_id'), fn($q) => $q->where('origin_city_id', $request->origin_city_id));
        $query->when($request->filled('destination_city_id'), fn($q) => $q->where('destination_city_id', $request->destination_city_id));

        $rates = $query->latest()->get();

        if ($request->ajax()) {
            return view('shipping-rates._table_rows', compact('rates'))->render();
        }

        // Active services with active couriers only for the filter dropdown.
        $services = ShippingService::with('courier')
            ->where('is_active', true)
            ->whereHas('courier', fn($q) => $q->where('is_active', true))
            ->orderBy('id')
            ->get();

        $cities = City::orderBy('name')->get();
        $provinces = Province::orderBy('name')->get();

        return view('shipping-rates.index', compact('rates', 'services', 'cities', 'provinces'));
    }

    public function show(ShippingRate $shippingRate)
    {
        $shippingRate->load(['service.courier', 'originProvince', 'originCity', 'destinationProvince', 'destinationCity']);
        return view('shipping-rates.show', compact('shippingRate'));
    }

    public function store(ShippingRateRequest $request)
    {
        $service = ShippingService::with('courier')->find($request->shipping_service_id);
        $originCity = City::find($request->origin_city_id);
        $destCity = City::find($request->destination_city_id);

        $serviceName = $service ? ($service->courier->name . ' (' . $service->service_name . ')') : 'N/A';
        $routeName = ($originCity && $destCity) ? ($originCity->name . ' → ' . $destCity->name) : 'N/A';

        try {
            ShippingRate::create($request->all());

            return redirect()->route('shipping-rates.index')
                ->with('success', [
                    'title' => 'Tarif Berhasil Ditambahkan',
                    'list' => [
                        'Tarif ongkos kirim baru untuk layanan "<strong>' . $serviceName . '</strong>" rute "<strong>' . $routeName . '</strong>" sebesar Rp ' . number_format($request->cost_per_kg, 0, ',', '.') . '/kg berhasil ditambahkan.'
                    ]
                ]);
        } catch (\Exception $e) {
            \Log::error('Gagal menambahkan tarif ongkir: ' . $e->getMessage());
            return back()->with('error', [
                'title' => 'Kesalahan Sistem',
                'list' => [
                    "Gagal menambahkan tarif ongkir rute <strong>{$routeName}</strong>.",
                    'Silakan coba lagi atau hubungi administrator jika masalah berlanjut.'
                ]
            ]);
        }
    }

    public function update(ShippingRateRequest $request, ShippingRate $shippingRate)
    {
        $service = ShippingService::with('courier')->find($request->shipping_service_id);
        $originCity = City::find($request->origin_city_id);
        $destCity = City::find($request->destination_city_id);

        $serviceName = $service ? ($service->courier->name . ' (' . $service->service_name . ')') : 'N/A';
        $routeName = ($originCity && $destCity) ? ($originCity->name . ' → ' . $destCity->name) : 'N/A';

        try {
            $shippingRate->fill($request->all());
            $dirty = $shippingRate->getDirty();

            $changes = [];
            if (isset($dirty['shipping_service_id'])) {
                $changes[] = 'layanan kurir';
            }

            if (isset($dirty['destination_city_id'])) {
                $changes[] = 'kota tujuan pengiriman';
            }
            if (isset($dirty['cost_per_kg'])) {
                $changes[] = 'nominal tarif per kg';
            }
            if (isset($dirty['etd_min']) || isset($dirty['etd_max'])) {
                $changes[] = 'estimasi waktu pengiriman (ETD)';
            }

            $shippingRate->save();

            if (count($changes) > 0) {
                if (count($changes) === 1) {
                    $changeStr = $changes[0];
                } elseif (count($changes) === 2) {
                    $changeStr = implode(' dan ', $changes);
                } else {
                    $last = array_pop($changes);
                    $changeStr = implode(', ', $changes) . ', dan ' . $last;
                }
                $msg = 'Detail ' . $changeStr . ' untuk rute "' . $routeName . '" (' . $serviceName . ') telah berhasil diperbarui.';
            } else {
                $msg = 'Data tarif rute "' . $routeName . '" (' . $serviceName . ') diperbarui tanpa ada perubahan data.';
            }

            return redirect()->route('shipping-rates.index')->with('success', [
                'title' => 'Tarif Berhasil Diperbarui',
                'list' => [
                    $msg
                ]
            ]);
        } catch (\Exception $e) {
            \Log::error('Gagal memperbarui tarif ongkir: ' . $e->getMessage());
            return back()->with('error', [
                'title' => 'Kesalahan Sistem',
                'list' => [
                    "Gagal memperbarui data tarif rute <strong>{$routeName}</strong>.",
                    'Silakan coba lagi atau hubungi administrator jika masalah berlanjut.'
                ]
            ]);
        }
    }

    public function updateStatus(ShippingRateStatusRequest $request, ShippingRate $shippingRate)
    {
        try {
            $shippingRate->update([
                'is_active' => $request->is_active
            ]);

            $statusText = $request->is_active ? 'diaktifkan' : 'dinonaktifkan';
            
            return redirect()->route('shipping-rates.index')->with('success', [
                'title' => 'Status Diperbarui',
                'list' => [
                    "Status tarif pengiriman berhasil {$statusText}."
                ]
            ]);
        } catch (\Exception $e) {
            \Log::error('Gagal memperbarui status tarif ongkir: ' . $e->getMessage());
            return back()->with('error', [
                'title' => 'Gagal Memperbarui Status',
                'list' => [
                    'Terjadi kesalahan saat memperbarui status tarif pengiriman.'
                ]
            ]);
        }
    }

    public function destroy(ShippingRateDeleteRequest $request, ShippingRate $shippingRate)
    {
        $serviceName = $shippingRate->service ? ($shippingRate->service->courier->name . ' (' . $shippingRate->service->service_name . ')') : 'N/A';
        $routeName = ($shippingRate->originCity && $shippingRate->destinationCity) ? ($shippingRate->originCity->name . ' → ' . $shippingRate->destinationCity->name) : 'N/A';

        try {
            $shippingRate->delete();

            return redirect()->route('shipping-rates.index')
                ->with('success', [
                    'title' => 'Tarif Dihapus',
                    'list' => [
                        'Tarif pengiriman berhasil dihapus.'
                    ]
                ]);
        } catch (\Exception $e) {
            return back()->with('error', [
                'title' => 'Gagal Menghapus Tarif',
                'list' => [
                    "Tarif pengiriman tidak dapat dihapus karena masih digunakan."
                ]
            ]);
        }
    }
}
