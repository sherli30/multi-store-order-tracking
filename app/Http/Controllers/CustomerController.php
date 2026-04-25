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

        // 🔘 Filter status (active / non-active)
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

        // ↕️ Sorting
        $sortBy = $request->get('sort_by', 'newest');
        match ($sortBy) {
            'oldest' => $query->oldest(),
            'name_az' => $query->orderBy('name', 'asc'),
            'name_za' => $query->orderBy('name', 'desc'),
            'orders' => $query->orderByDesc('orders_count'),
            default => $query->latest(),
        };

        $customers = $query->get();

        // 🏪 Data toko (untuk filter dropdown)
        $stores = Store::orderBy('name')->get();

        // 📊 Stats (opsional dashboard)
        $stats = [
            'total' => User::where('role', 'customer')->count(),
            'active' => User::where('role', 'customer')->where('is_active', 1)->count(),
            'blocked' => User::where('role', 'customer')->where('is_active', 0)->count(),
            'total_spent' => \App\Models\Order::whereIn('status', ['processing', 'shipping', 'completed'])->sum('total_amount'),
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
            ->whereIn('status', ['processing', 'shipping', 'completed'])
            ->sum('total_amount');

        return view('customers.show', compact(
            'customer',
            'orders',
            'totalOrders',
            'totalSpent'
        ));
    }

    /**
     * UPDATE STATUS — Aktif / Nonaktif
     */
    /**
     * UPDATE STATUS — Aktif / Nonaktif (Blokir)
     */
    public function updateStatus(Request $request, $id)
    {
        try {
            $customer = User::where('role', 'customer')->findOrFail($id);

            $request->validate([
                'is_active' => 'required|in:0,1'
            ], [
                'is_active.required' => 'Status tidak boleh kosong. Sistem membutuhkan nilai untuk melanjutkan proses.',
                'is_active.in' => 'Status tidak valid. Hanya diperbolehkan nilai aktif atau nonaktif.',
            ]);

            $customer->update([
                'is_active' => $request->is_active
            ]);

            // 🎯 Pesan profesional & jelas
            if ($request->is_active == 1) {
                return back()->with(
                    'success',
                    "Akun pelanggan \"{$customer->name}\" berhasil diaktifkan kembali. Pelanggan kini dapat mengakses sistem seperti biasa."
                );
            }

            return back()->with(
                'success',
                "Akun pelanggan \"{$customer->name}\" berhasil dinonaktifkan. Pelanggan tidak dapat login sampai diaktifkan kembali."
            );

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {

            return back()->with(
                'error',
                'Data pelanggan tidak ditemukan. Kemungkinan data sudah dihapus atau terjadi kesalahan sistem.'
            );

        } catch (\Exception $e) {

            return back()->with(
                'error',
                'Terjadi kesalahan saat memperbarui status pelanggan. Silakan coba beberapa saat lagi atau hubungi administrator.'
            );
        }
    }

    /**
     * DESTROY — Hapus customer permanen
     */
    public function destroy($id)
    {
        try {
            $customer = User::where('role', 'customer')->findOrFail($id);
            $name = $customer->name;

            $customer->delete();

            return redirect()
                ->route('customers.index')
                ->with(
                    'success',
                    "Data pelanggan \"{$name}\" berhasil dihapus secara permanen dari sistem. Tindakan ini tidak dapat dibatalkan."
                );

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {

            return back()->with(
                'error',
                'Data pelanggan tidak ditemukan. Kemungkinan sudah dihapus sebelumnya.'
            );

        } catch (\Exception $e) {

            return back()->with(
                'error',
                'Gagal menghapus pelanggan. Pastikan tidak ada data terkait seperti pesanan atau transaksi yang masih terhubung.'
            );
        }
    }
}
