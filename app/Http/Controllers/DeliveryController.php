<?php

namespace App\Http\Controllers;

use App\Models\Courier;
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
        $tab     = $request->get('tab', 'all');
        $storeId = $request->get('store_id');

        $activeStatuses = [
            Order::STATUS_PERLU_DIPROSES,
            Order::STATUS_PROCESSING,
            Order::STATUS_SHIPPING,
            Order::STATUS_COMPLETED,
        ];

        $query = Order::with(['store', 'trackingHistories' => function ($q) {
            $q->with('admin')->latest();
        }])->whereIn('status', $activeStatuses);

        // ── Filter: store (active stores only) ───────────────────────────────
        if ($request->filled('store_id')) {
            $query->where('store_id', $storeId);
        }

        // ── Filter: shipping_courier ──────────────────────────────────────────
        if ($request->filled('courier')) {
            $query->where('shipping_courier', $request->courier);
        }

        // ── Filter: date (based on updated_at / last tracking update) ─────────
        if ($request->filled('date')) {
            $query->whereDate('updated_at', $request->date);
        }

        // ── Filter: status tab ────────────────────────────────────────────────
        if ($tab !== 'all' && in_array($tab, $activeStatuses)) {
            $query->where('status', $tab);
        }

        // ── Search ────────────────────────────────────────────────────────────
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('midtrans_order_id', 'like', "%{$search}%")
                ->orWhere('tracking_number', 'like', "%{$search}%")
                ->orWhere('customer_name', 'like', "%{$search}%");
            });
        }

        $orders = $query->latest('updated_at')->get();

        // ── Stores: active only, for filter dropdown ──────────────────────────
        $stores = \App\Models\Store::where('is_active', true)->orderBy('name')->get();

        // ── Couriers: from master couriers table ──────────────────────────────
        $couriers = Courier::where('is_active', true)->orderBy('name')->get();

        // ── Tab counts (respects store + courier + date filter) ────────────────
        $countQuery = Order::query();
        if ($request->filled('store_id')) {
            $countQuery->where('store_id', $storeId);
        }
        if ($request->filled('courier')) {
            $countQuery->where('shipping_courier', $request->courier);
        }
        if ($request->filled('date')) {
            $countQuery->whereDate('updated_at', $request->date);
        }

        $tabCounts = [
            'all'                        => (clone $countQuery)->whereIn('status', $activeStatuses)->count(),
            Order::STATUS_PERLU_DIPROSES => (clone $countQuery)->where('status', Order::STATUS_PERLU_DIPROSES)->count(),
            Order::STATUS_PROCESSING     => (clone $countQuery)->where('status', Order::STATUS_PROCESSING)->count(),
            Order::STATUS_SHIPPING       => (clone $countQuery)->where('status', Order::STATUS_SHIPPING)->count(),
            Order::STATUS_COMPLETED      => (clone $countQuery)->where('status', Order::STATUS_COMPLETED)->count(),
        ];

        // ── AJAX: return only table rows ──────────────────────────────────────
        if ($request->ajax()) {
            return view('deliveries._table_rows', compact('orders'))->render();
        }

        return view('deliveries.index', compact('orders', 'stores', 'couriers', 'tab', 'tabCounts'));
    }

    /**
     * Halaman UI Scanner Barcode untuk pencarian resi/pesanan.
     */
    public function scan(Request $request)
    {
        $order = null;
        if ($request->filled('identifier')) {
            $order = Order::with(['trackingHistories.admin', 'store', 'orderItems.product', 'transaction'])
                ->where(function ($q) use ($request) {
                    $q->where('midtrans_order_id', $request->identifier)
                      ->orWhere('tracking_number', $request->identifier);
                })
                ->first();

            if (!$order) {
                return back()->with('error', "Pesanan atau resi '{$request->identifier}' tidak ditemukan.");
            }
        }
        return view('deliveries.scan', compact('order'));
    }

    // Method updateTracking removed as per instructions: "use scanning only for order lookup"

    /**
     * Cetak Label Pengiriman
     */
    public function printLabel(Order $order)
    {
        if (in_array($order->status, [Order::STATUS_PENDING, Order::STATUS_CANCELLED])) {
            return back()->with('error', 'Label pengiriman hanya dapat dicetak untuk pesanan yang sudah dibayar (minimal status Perlu Diproses).');
        }

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
                $q->where('midtrans_order_id', 'like', "%{$search}%")
                  ->orWhere('tracking_number', 'like', "%{$search}%")
                  ->orWhere('customer_name', 'like', "%{$search}%");
            });
        }

        $histories = $query->paginate(20)->appends($request->query());

        return view('deliveries.history', compact('histories'));
    }

    /**
     * Get tracking history modal content for AJAX.
     */
    public function getTrackingModal(Order $order)
    {
        $order->load(['trackingHistories.admin', 'store']);
        return view('deliveries.partials.tracking_modal_content', compact('order'));
    }
}
