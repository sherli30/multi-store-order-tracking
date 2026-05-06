<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\TrackingHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DeliveryController extends Controller
{
    /**
     * Tampilkan daftar pesanan yang memerlukan tindakan pengiriman.
     */
    public function index(Request $request)
    {
        $query = Order::with('store', 'trackingHistories')
            ->whereIn('status', ['perlu_diproses', 'processing', 'shipping', 'completed', 'cancelled'])
            ->latest();

        $query->when($request->filled('search'), function ($q) use ($request) {
            $q->where('order_number', 'like', "%{$request->search}%")
              ->orWhere('tracking_number', 'like', "%{$request->search}%")
              ->orWhere('customer_name', 'like', "%{$request->search}%");
        });

        $query->when($request->filled('status'), function ($q) use ($request) {
            $q->where('status', $request->status);
        });

        $orders = $query->paginate(10)->appends($request->query());

        return view('deliveries.index', compact('orders'));
    }

    /**
     * Halaman UI Scanner Barcode / Update Manual.
     */
    public function scan()
    {
        return view('deliveries.scan');
    }

    /**
     * Proses pemindaian barcode / input resi & update ke tracking.
     */
    public function updateTracking(Request $request)
    {
        $request->validate([
            'identifier'       => 'required|string', // order_number atau tracking_number
            'status'           => 'required|in:processing,shipping,completed',
            'shipping_courier' => 'nullable|string',
            'tracking_number'  => 'nullable|string',
            'notes'            => 'nullable|string',
        ]);

        $order = Order::where('order_number', $request->identifier)
                      ->orWhere('tracking_number', $request->identifier)
                      ->first();

        if (!$order) {
            return back()->with('error', "Pesanan / Resi '{$request->identifier}' tidak ditemukan.")->withInput();
        }

        // Kalau status berubah ke shipping, bisa sekalian save courier & resi
        if ($request->status === 'shipping') {
            if ($request->filled('shipping_courier')) {
                $order->shipping_courier = $request->shipping_courier;
            }
            if ($request->filled('tracking_number')) {
                // cegah duplicate di order lain
                $exists = Order::where('tracking_number', $request->tracking_number)
                               ->where('id', '!=', $order->id)
                               ->exists();
                if($exists) {
                    return back()->with('error', "Nomor Resi '{$request->tracking_number}' sudah digunakan pesanan lain.")->withInput();
                }
                $order->tracking_number = $request->tracking_number;
            }
        }

        $order->status = $request->status;
        
        DB::transaction(function () use ($order, $request) {
            $order->save();

            // Insert into history
            TrackingHistory::create([
                'order_id' => $order->id,
                'admin_id' => auth()->id(),
                'status'   => $request->status,
                'notes'    => $request->notes,
            ]);
        });

        $statusLabel = [
            'processing' => 'Dikemas',
            'shipping'   => 'Dikirim',
            'completed'  => 'Selesai',
        ][$request->status] ?? $request->status;

        return back()->with('success', "Status pesanan {$order->order_number} berhasil diperbarui menjadi {$statusLabel}!");
    }

    /**
     * Cetak Label Pengiriman
     */
    public function printLabel(Order $order)
    {
        $order->load('store', 'orderItems.product');
        return view('deliveries.label', compact('order'));
    }

    /**
     * Riwayat Tracking & Cari
     */
    public function history(Request $request)
    {
        $query = TrackingHistory::with(['order', 'admin'])->latest();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('order', function ($q) use ($search) {
                $q->where('order_number', 'like', "%{$search}%")
                  ->orWhere('tracking_number', 'like', "%{$search}%");
            });
        }

        $histories = $query->paginate(20)->appends($request->query());

        return view('deliveries.history', compact('histories'));
    }
}
