<?php

namespace App\Http\Controllers;

use App\Models\Courier;
use App\Models\ShippingService;
use App\Http\Requests\ShippingServiceRequest;
use Illuminate\Http\Request;

class ShippingServiceController extends Controller
{
    public function index(Request $request)
    {
        $query = ShippingService::with('courier');

        $query->when($request->filled('courier_id'), fn($q) => $q->where('courier_id', $request->courier_id));
        $query->when($request->filled('status'), fn($q) => $q->where('is_active', $request->status === 'active'));
        $query->when($request->filled('type'), function($q) use ($request) {
            if ($request->type === 'cargo') $q->where('min_weight', '>=', 10000);
            elseif ($request->type === 'reguler') $q->where('min_weight', '<', 10000);
        });

        $services = $query->orderBy('courier_id')->get();

        if ($request->ajax()) {
            return view('shipping-services._table_rows', compact('services'))->render();
        }

        // Active couriers only for the filter dropdown.
        $couriers = Courier::where('is_active', true)->orderBy('name')->get();

        return view('shipping-services.index', compact('services', 'couriers'));
    }

    public function store(ShippingServiceRequest $request)
    {
        $service = ShippingService::create($request->validated());
        $service->load('courier');

        return redirect()->route('shipping-services.index')->with('success', 'Layanan "' . $service->service_name . '" untuk kurir "' . $service->courier->name . '" berhasil ditambahkan.');
    }

    public function update(ShippingServiceRequest $request, ShippingService $shippingService)
    {
        $shippingService->fill($request->validated());
        $dirty = $shippingService->getDirty();

        $changes = [];
        if (isset($dirty['courier_id'])) {
            $changes[] = 'kurir induk';
        }
        if (isset($dirty['service_name'])) {
            $changes[] = 'nama layanan';
        }
        if (isset($dirty['min_weight'])) {
            $changes[] = 'minimal berat';
        }
        if (isset($dirty['is_active'])) {
            $changes[] = 'status operasional';
        }

        $shippingService->save();
        $shippingService->load('courier');

        if (count($changes) > 0) {
            if (count($changes) === 1) {
                $changeStr = $changes[0];
            } elseif (count($changes) === 2) {
                $changeStr = implode(' dan ', $changes);
            } else {
                $last = array_pop($changes);
                $changeStr = implode(', ', $changes) . ', dan ' . $last;
            }
            $msg = 'Detail ' . $changeStr . ' layanan "' . $shippingService->service_name . '" untuk kurir "' . $shippingService->courier->name . '" berhasil diperbarui.';
        } else {
            $msg = 'Informasi layanan "' . $shippingService->service_name . '" untuk kurir "' . $shippingService->courier->name . '" berhasil diperbarui tanpa perubahan.';
        }

        return redirect()->route('shipping-services.index')->with('success', $msg);
    }

    public function destroy(ShippingService $shippingService)
    {
        $serviceName = $shippingService->service_name;
        $courierName = $shippingService->courier ? $shippingService->courier->name : 'N/A';

        try {
            if ($shippingService->rates()->count() > 0) {
                return back()->with('error', 'Gagal menghapus! Layanan "' . $serviceName . '" untuk kurir "' . $courierName . '" masih memiliki data tarif ongkir terkait di sistem.');
            }

            $shippingService->delete();

            return redirect()
                ->route('shipping-services.index')
                ->with('success', 'Layanan "' . $serviceName . '" untuk kurir "' . $courierName . '" telah berhasil dihapus secara permanen dari database sistem.');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menghapus layanan "' . $serviceName . '" untuk kurir "' . $courierName . '". Terjadi kesalahan pada sistem, silakan coba lagi.');
        }
    }
}
