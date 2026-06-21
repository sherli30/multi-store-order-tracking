<?php

namespace App\Http\Controllers;

use App\Models\Courier;
use Illuminate\Http\Request;
use App\Http\Requests\CourierRequest;
use App\Http\Requests\CourierDeleteRequest;
use App\Http\Requests\CourierStatusRequest;

class CourierController extends Controller
{
    public function index(Request $request)
    {
        $query = Courier::withCount('services');

        $query->when($request->filled('is_active'), function ($q) use ($request) {
            $q->where('is_active', $request->is_active == '1');
        });

        $query->when($request->filled('has_services'), function ($q) use ($request) {
            if ($request->has_services == 'yes') {
                $q->having('services_count', '>', 0);
            } elseif ($request->has_services == 'no') {
                $q->having('services_count', 0);
            }
        });

        $couriers = $query->orderBy('name')->get();

        if ($request->ajax()) {
            return view('couriers._table_rows', compact('couriers'))->render();
        }

        return view('couriers.index', compact('couriers'));
    }

    public function create()
    {
        return view('couriers.create');
    }

    public function store(CourierRequest $request)
    {
        Courier::create($request->all());

        return redirect()->route('couriers.index')->with('success', [
            'title' => 'Kurir Ditambahkan',
            'list' => [
                'Kurir "<strong>' . $request->name . '</strong>" berhasil ditambahkan.'
            ]
        ]);
    }

    public function show(Courier $courier)
    {
        $courier->loadCount('services');
        return view('couriers.show', compact('courier'));
    }

    public function edit(Courier $courier)
    {
        return view('couriers.edit', compact('courier'));
    }

    public function update(CourierRequest $request, Courier $courier)
    {
        $courier->fill($request->all());
        $dirty = $courier->getDirty();

        $changes = [];
        if (isset($dirty['name'])) {
            $changes[] = 'nama';
        }
        if (isset($dirty['code'])) {
            $changes[] = 'kode';
        }
        if (isset($dirty['is_active'])) {
            $changes[] = 'status operasional';
        }

        $courier->save();

        if (count($changes) > 0) {
            if (count($changes) === 1) {
                $changeStr = $changes[0];
            } elseif (count($changes) === 2) {
                $changeStr = implode(' dan ', $changes);
            } else {
                $last = array_pop($changes);
                $changeStr = implode(', ', $changes) . ', dan ' . $last;
            }
            $msg = 'Detail ' . $changeStr . ' kurir "' . $courier->name . '" berhasil diperbarui.';
        } else {
            $msg = 'Informasi kurir "' . $courier->name . '" berhasil diperbarui tanpa perubahan.';
        }

        return redirect()->route('couriers.index')->with('success', [
            'title' => 'Kurir Diperbarui',
            'list' => [
                $msg
            ]
        ]);
    }

    public function updateStatus(CourierStatusRequest $request, Courier $courier)
    {
        $statusLabel = $request->is_active ? 'diaktifkan' : 'dinonaktifkan';

        try {
            $courier->update([
                'is_active' => $request->is_active
            ]);

            return redirect()->back()->with('success', [
                'title' => 'Status Diperbarui',
                'list' => [
                    "Kurir \"<strong>{$courier->name}</strong>\" berhasil {$statusLabel}."
                ]
            ]);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', [
                'title' => 'Gagal Memperbarui Status',
                'list' => [
                    "Gagal mengubah status kurir \"<strong>{$courier->name}</strong>\".",
                    "Terjadi kesalahan saat memperbarui status kurir."
                ]
            ]);
        }
    }

    public function destroy(CourierDeleteRequest $request, Courier $courier)
    {
        $courierName = $courier->name;

        try {
            $courier->delete();

            return redirect()
                ->route('couriers.index')
                ->with('success', [
                    'title' => 'Kurir Dihapus',
                    'list' => [
                        'Kurir "<strong>' . $courierName . '</strong>" telah berhasil dihapus secara permanen dari sistem.'
                    ]
                ]);
        } catch (\Exception $e) {
            return back()->with('error', [
                'title' => 'Gagal Menghapus Kurir',
                'list' => [
                    "Gagal menghapus kurir <strong>{$courierName}</strong>.",
                    'Data kurir sedang terkait dengan transaksi atau layanan pengiriman aktif.'
                ]
            ]);
        }
    }
}
