<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Store;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    /**
     * INDEX — Daftar pelanggan + filter + stats
     */
    public function index(Request $request)
    {
        $query = User::withCount('orders')
            ->where('role', 'customer');

        // 🔍 Search (name, email, phone)
        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        // 🔘 Filter status (active / inactive)
        if ($status = $request->get('status')) {
            $query->where('is_active', $status === 'active' ? 1 : 0);
        }

        // 📅 Filter tanggal
        if ($request->filled('date_from') && $request->filled('date_to')) {
            $query->whereBetween('created_at', [
                $request->date_from . ' 00:00:00',
                $request->date_to . ' 23:59:59'
            ]);
        }

        // 🏪 Filter berdasarkan toko
        if ($storeId = $request->get('store_id')) {
            $query->whereHas('orders', function ($q) use ($storeId) {
                $q->where('store_id', $storeId);
            });
        }

        // 🔢 Filter minimum jumlah pesanan
        if ($request->filled('min_orders') && is_numeric($request->min_orders) && $request->min_orders >= 0) {
            $query->having('orders_count', '>=', (int) $request->min_orders);
        }

        // ↕️ Sorting
        $sortBy = $request->get('sort_by', 'newest');
        match ($sortBy) {
            'oldest'  => $query->oldest(),
            'name_az' => $query->orderBy('name', 'asc'),
            'name_za' => $query->orderBy('name', 'desc'),
            'orders'  => $query->orderByDesc('orders_count'),
            default   => $query->latest(),
        };

        $customers = $query->get();

        // AJAX: return only the table rows partial
        if ($request->ajax()) {
            return view('customers._table_rows', compact('customers'))->render();
        }

        // 🏪 Data toko (untuk filter dropdown)
        $stores = Store::orderBy('name')->get();

        // 📊 Stats (opsional dashboard)
        $stats = [
            'total'       => User::where('role', 'customer')->count(),
            'active'      => User::where('role', 'customer')->where('is_active', 1)->count(),
            'inactive'    => User::where('role', 'customer')->where('is_active', 0)->count(),
            'total_spent' => \App\Models\Order::whereIn('payment_status', ['settlement', 'capture', 'paid'])->sum('total_amount'),
        ];

        return view('customers.index', compact('customers', 'stores', 'stats'));
    }

    /**
     * SHOW — Detail customer + riwayat pesanan
     */
    public function show($id)
    {
        $customer = User::where('role', 'customer')->findOrFail($id);

        $orders = $customer->orders()
            ->with('store')
            ->latest()
            ->paginate(10);

        $totalOrders = $customer->orders()->count();

        $totalSpent = $customer->orders()
            ->whereIn('payment_status', ['settlement', 'capture', 'paid'])
            ->sum('total_amount');

        return view('customers.show', compact(
            'customer',
            'orders',
            'totalOrders',
            'totalSpent'
        ));
    }

    /**
     * UPDATE STATUS — Aktif / Nonaktif (Blokir)
     */
    public function updateStatus(\App\Http\Requests\CustomerStatusRequest $request, $id)
    {
        try {
            $customer = User::where('role', 'customer')->find($id);

            $customer->update([
                'is_active' => $request->is_active
            ]);

            // 🎯 Pesan profesional & jelas
            if ($request->is_active == 1) {
                return back()->with(
                    'success',
                    "Akun customer berhasil diaktifkan kembali. Customer kini dapat mengakses sistem seperti biasa."
                );
            }

            return back()->with(
                'success',
                "Akun customer berhasil dinonaktifkan. Customer tidak dapat login sampai diaktifkan kembali."
            );

        } catch (\Exception $e) {
            return back()->with(
                'error',
                'Terjadi kesalahan saat memperbarui status akun. Gagal menonaktifkan akun customer.'
            );
        }
    }

    /**
     * DESTROY — Hapus customer permanen
     */
    public function destroy(\App\Http\Requests\CustomerDeleteRequest $request, $id)
    {
        try {
            $customer = User::where('role', 'customer')->find($id);
            $name = $customer->name;

            $customer->delete();

            return redirect()
                ->route('customers.index')
                ->with(
                    'success',
                    "Akun customer berhasil dihapus secara permanen dari sistem."
                );

        } catch (\Exception $e) {

            return back()->with(
                'error',
                'Gagal menghapus akun customer. Tidak dapat menghapus akun customer.'
            );
        }
    }
}
