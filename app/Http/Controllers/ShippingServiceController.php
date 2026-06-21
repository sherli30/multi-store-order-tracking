<?php

namespace App\Http\Controllers;

use App\Models\Courier;
use App\Models\ShippingService;
use App\Http\Requests\ShippingServiceRequest;
use App\Http\Requests\ShippingServiceDeleteRequest;
use App\Http\Requests\ShippingServiceStatusRequest;
use Illuminate\Http\Request;

class ShippingServiceController extends Controller
{
    public function index(Request $request)
    {
        $query = ShippingService::with('courier');

        $query->when($request->filled('courier_id'), fn($q) => $q->where('courier_id', $request->courier_id));
        $query->when($request->filled('status'), fn($q) => $q->where('is_active', $request->status === 'active'));
        $query->when($request->filled('type'), function($q) use ($request) {
            if ($request->type === 'cargo') $q->where('min_weight', '>=', 10);
            elseif ($request->type === 'reguler') $q->where('min_weight', '<', 10);
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

        return redirect()->route('shipping-services.index')->with('success', [
            'title' => 'Layanan Berhasil Ditambahkan',
            'list' => [
                'Layanan "<strong>' . $service->service_name . '</strong>" untuk kurir "<strong>' . $service->courier->name . '</strong>" berhasil ditambahkan.'
            ]
        ]);
    }

    public function show(ShippingService $shippingService)
    {
        $shippingService->load('courier');
        $shippingService->loadCount('rates');
        
        return view('shipping-services.show', compact('shippingService'));
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

        return redirect()->route('shipping-services.index')->with('success', [
            'title' => 'Layanan Berhasil Diperbarui',
            'list' => [
                $msg
            ]
        ]);
    }

    public function updateStatus(ShippingServiceStatusRequest $request, ShippingService $shippingService)
    {
        $statusLabel = $request->is_active ? 'diaktifkan' : 'dinonaktifkan';

        try {
            $shippingService->update([
                'is_active' => $request->is_active
            ]);

            return redirect()->back()->with('success', [
                'title' => 'Status Diperbarui',
                'list' => [
                    "Layanan pengiriman \"<strong>{$shippingService->service_name}</strong>\" berhasil {$statusLabel}."
                ]
            ]);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', [
                'title' => 'Gagal Memperbarui Status',
                'list' => [
                    "Gagal mengubah status layanan pengiriman \"<strong>{$shippingService->service_name}</strong>\".",
                    "Terjadi kesalahan saat memperbarui status layanan."
                ]
            ]);
        }
    }

    public function destroy(ShippingServiceDeleteRequest $request, ShippingService $shippingService)
    {
        $serviceName = $shippingService->service_name;
        $courierName = $shippingService->courier ? $shippingService->courier->name : 'N/A';

        try {
            $shippingService->delete();

            return redirect()
                ->route('shipping-services.index')
                ->with('success', [
                    'title' => 'Layanan Dihapus',
                    'list' => [
                        'Layanan pengiriman "<strong>' . $serviceName . '</strong>" untuk kurir "<strong>' . $courierName . '</strong>" telah berhasil dihapus secara permanen.'
                    ]
                ]);
        } catch (\Exception $e) {
            return back()->with('error', [
                'title' => 'Gagal Menghapus Layanan',
                'list' => [
                    "Gagal menghapus layanan pengiriman <strong>{$serviceName}</strong> untuk kurir <strong>{$courierName}</strong>.",
                    'Data sedang digunakan oleh transaksi atau pengiriman aktif.'
                ]
            ]);
        }
    }
}
