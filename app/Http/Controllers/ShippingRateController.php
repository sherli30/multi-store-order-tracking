<?php

namespace App\Http\Controllers;

use App\Models\City;
use App\Models\ShippingRate;
use App\Models\ShippingService;
use App\Http\Requests\ShippingRateRequest;
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

        return view('shipping-rates.index', compact('rates', 'services', 'cities'));
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

            return redirect()
                ->route('shipping-rates.index')
                ->with('success', 'Tarif ongkos kirim baru untuk layanan "' . $serviceName . '" rute "' . $routeName . '" sebesar Rp ' . number_format($request->cost_per_kg, 0, ',', '.') . '/kg berhasil ditambahkan ke database.');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menambahkan tarif ongkir rute "' . $routeName . '". Terjadi kesalahan sistem: ' . $e->getMessage());
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
            if (isset($dirty['origin_city_id'])) {
                $changes[] = 'kota asal pengiriman';
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

            return redirect()->route('shipping-rates.index')->with('success', $msg);
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal memperbarui data tarif rute "' . $routeName . '". Terjadi kesalahan sistem: ' . $e->getMessage());
        }
    }

    public function destroy(ShippingRate $shippingRate)
    {
        $serviceName = $shippingRate->service ? ($shippingRate->service->courier->name . ' (' . $shippingRate->service->service_name . ')') : 'N/A';
        $routeName = ($shippingRate->originCity && $shippingRate->destinationCity) ? ($shippingRate->originCity->name . ' → ' . $shippingRate->destinationCity->name) : 'N/A';

        try {
            $shippingRate->delete();

            return redirect()
                ->route('shipping-rates.index')
                ->with('success', 'Tarif pengiriman untuk layanan "' . $serviceName . '" rute "' . $routeName . '" telah berhasil dihapus secara permanen dari sistem.');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menghapus data tarif layanan "' . $serviceName . '" rute "' . $routeName . '". Terjadi kesalahan pada database, silakan coba lagi.');
        }
    }
}
