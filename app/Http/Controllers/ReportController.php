<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Store;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ReportController extends Controller
{
    /**
     * Unified Sales Reports Page
     */
    public function index(\App\Http\Requests\ReportFilterRequest $request)
    {
        // ── Section 1: Advanced Report Filters ──────────────────────────────────
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date', Carbon::now()->toDateString());
        $storeId = $request->input('store_id');
        $customerId = $request->input('customer_id');
        $orderStatus = $request->input('order_status');
        $paymentStatus = $request->input('payment_status');
        $invoiceNumber = $request->input('invoice_number');
        $productId = $request->input('product_id');

        // Base Order Query
        $query = Order::with(['store', 'invoice.user', 'orderItems.product'])
            ->whereBetween('orders.created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);

        if ($storeId) $query->where('orders.store_id', $storeId);
        if ($orderStatus) $query->where('orders.status', $orderStatus);
        if ($paymentStatus) $query->where('orders.payment_status', $paymentStatus);

        if ($invoiceNumber || $customerId) {
            $query->whereHas('invoice', function ($q) use ($invoiceNumber, $customerId) {
                if ($invoiceNumber) {
                    $q->where(function($sub) use ($invoiceNumber) {
                        $sub->where('invoice_number', 'like', "%{$invoiceNumber}%")
                            ->orWhere('midtrans_order_id', 'like', "%{$invoiceNumber}%");
                    });
                }
                if ($customerId) {
                    $q->where('user_id', $customerId);
                }
            });
        }

        if ($productId) {
            $query->whereHas('orderItems', function ($q) use ($productId) {
                $q->where('product_id', $productId);
            });
        }

        // Section 3: Main Sales Transaction Report (Paginated)
        $orders = clone $query;
        $orders = $orders->latest('orders.created_at')->paginate(25)->withQueryString();

        // ── Section 2: Executive Summary ──────────────────────────────────
        $baseQueryForAggregates = clone $query;

        $totalTransactions = (clone $baseQueryForAggregates)->count();

        // Total revenue (only completed/paid orders)
        $totalRevenue = (clone $baseQueryForAggregates)
            ->whereIn('orders.payment_status', ['settlement', 'capture', 'paid'])
            ->sum('orders.total_amount');

        $averageOrderValue = $totalTransactions > 0 ? $totalRevenue / $totalTransactions : 0;

        $totalProductsSold = OrderItem::whereIn('order_id', (clone $baseQueryForAggregates)->pluck('orders.id'))
            ->sum('quantity');

        $totalCustomers = DB::table('orders')
            ->join('invoices', 'orders.invoice_id', '=', 'invoices.id')
            ->whereIn('orders.id', (clone $baseQueryForAggregates)->pluck('orders.id'))
            ->distinct('invoices.user_id')
            ->count('invoices.user_id');

        // ── Section 4: Product Sales Detail Report ────────────────────────
        $orderItems = OrderItem::with(['order.invoice', 'order.store', 'product'])
            ->whereIn('order_id', (clone $baseQueryForAggregates)->pluck('orders.id'))
            ->latest('created_at')
            ->paginate(25, ['*'], 'product_page')
            ->withQueryString();

        // ── Section 5 & 6: Store Performance & Consolidated Report ─────────
        $storesList = Store::where('is_active', true)->get();
        $storeStats = (clone $baseQueryForAggregates)->reorder()->select(
            'orders.store_id',
            DB::raw('COUNT(*) as total_orders'),
            DB::raw("SUM(CASE WHEN orders.status = 'completed' THEN 1 ELSE 0 END) as completed_orders"),
            DB::raw("SUM(CASE WHEN orders.status = 'cancelled' THEN 1 ELSE 0 END) as cancelled_orders"),
            DB::raw("SUM(CASE WHEN orders.status = 'refunded' THEN 1 ELSE 0 END) as refunded_orders"),
            DB::raw("SUM(CASE WHEN orders.payment_status IN ('settlement', 'capture', 'paid') THEN orders.total_amount ELSE 0 END) as total_revenue"),
            DB::raw("SUM(orders.shipping_cost) as shipping_revenue")
        )->groupBy('orders.store_id')->get()->keyBy('store_id');

        $consolidatedReport = $storesList->map(function ($store) use ($storeStats, $baseQueryForAggregates) {
            $stats = $storeStats->get($store->id);

            $productsSold = OrderItem::whereIn('order_id', (clone $baseQueryForAggregates)->where('orders.store_id', $store->id)->pluck('orders.id'))->sum('quantity');

            $uniqueCustomers = DB::table('orders')
                ->join('invoices', 'orders.invoice_id', '=', 'invoices.id')
                ->whereIn('orders.id', (clone $baseQueryForAggregates)->where('orders.store_id', $store->id)->pluck('orders.id'))
                ->distinct('invoices.user_id')
                ->count('invoices.user_id');

            return (object) [
                'store_id' => $store->id,
                'store_name' => $store->name,
                'total_orders' => $stats ? $stats->total_orders : 0,
                'completed_orders' => $stats ? $stats->completed_orders : 0,
                'cancelled_orders' => $stats ? $stats->cancelled_orders : 0,
                'refunded_orders' => $stats ? $stats->refunded_orders : 0,
                'total_revenue' => $stats ? $stats->total_revenue : 0,
                'shipping_revenue' => $stats ? $stats->shipping_revenue : 0,
                'products_sold' => $productsSold,
                'unique_customers' => $uniqueCustomers,
                'success_rate' => ($stats && $stats->total_orders > 0) ? round(($stats->completed_orders / $stats->total_orders) * 100, 1) : 0
            ];
        });

        // Filter dropdown data
        $customers = User::where('role', 'customer')->orderBy('name')->get();
        $products = Product::whereHas('store', function($q) { $q->where('is_active', true); })->orderBy('name')->get();

        return view('reports.index', compact(
            'startDate', 'endDate', 'storeId', 'customerId', 'orderStatus', 'paymentStatus', 'invoiceNumber', 'productId',
            'storesList', 'customers', 'products',
            'orders', 'orderItems',
            'totalTransactions', 'totalRevenue', 'totalProductsSold', 'totalCustomers', 'averageOrderValue',
            'consolidatedReport'
        ));
    }

    /**
     * PDF Export
     */
    public function export(\App\Http\Requests\ReportExportRequest $request)
    {
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        $storeId = $request->input('store_id');
        $customerId = $request->input('customer_id');
        $orderStatus = $request->input('order_status');
        $paymentStatus = $request->input('payment_status');
        $invoiceNumber = $request->input('invoice_number');
        $productId = $request->input('product_id');

        $query = Order::with(['store', 'invoice.user', 'orderItems.product'])
            ->whereBetween('orders.created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);

        if ($storeId) $query->where('orders.store_id', $storeId);
        if ($orderStatus) $query->where('orders.status', $orderStatus);
        if ($paymentStatus) $query->where('orders.payment_status', $paymentStatus);
        if ($invoiceNumber || $customerId) {
            $query->whereHas('invoice', function ($q) use ($invoiceNumber, $customerId) {
                if ($invoiceNumber) {
                    $q->where(function($sub) use ($invoiceNumber) {
                        $sub->where('invoice_number', 'like', "%{$invoiceNumber}%")
                            ->orWhere('midtrans_order_id', 'like', "%{$invoiceNumber}%");
                    });
                }
                if ($customerId) {
                    $q->where('user_id', $customerId);
                }
            });
        }
        if ($productId) {
            $query->whereHas('orderItems', function ($q) use ($productId) {
                $q->where('product_id', $productId);
            });
        }

        $baseQuery = clone $query;
        $orders = (clone $baseQuery)->latest('orders.created_at')->get();
        $orderItems = OrderItem::with(['order.invoice', 'order.store', 'product'])
            ->whereIn('order_id', $orders->pluck('id'))->get();

        $totalTransactions = $orders->count();
        $totalRevenue = $orders->whereIn('payment_status', ['settlement', 'capture', 'paid'])->sum('total_amount');
        $totalProductsSold = $orderItems->sum('quantity');

        // FIX: $storesList sebelumnya selalu mengambil SEMUA toko aktif tanpa
        // memperhatikan filter $storeId dari request. Akibatnya, PDF yang
        // di-generate dari tombol "Download PDF" per toko (section Performa
        // Toko) selalu menampilkan konsolidasi SEMUA toko, identik dengan
        // hasil "Generate & Cetak PDF Laporan" di section Ekspor — padahal
        // seharusnya cuma berisi data toko yang dipilih.
        $storesList = Store::where('is_active', true)
            ->when($storeId, function ($q) use ($storeId) {
                $q->where('id', $storeId);
            })
            ->get();

        $storeStats = (clone $baseQuery)->reorder()->select(
            'orders.store_id',
            DB::raw('COUNT(*) as total_orders'),
            DB::raw("SUM(CASE WHEN orders.status = 'completed' THEN 1 ELSE 0 END) as completed_orders"),
            DB::raw("SUM(CASE WHEN orders.status = 'cancelled' THEN 1 ELSE 0 END) as cancelled_orders"),
            DB::raw("SUM(CASE WHEN orders.status = 'refunded' THEN 1 ELSE 0 END) as refunded_orders"),
            DB::raw("SUM(CASE WHEN orders.payment_status IN ('settlement', 'capture', 'paid') THEN orders.total_amount ELSE 0 END) as total_revenue"),
            DB::raw("SUM(orders.shipping_cost) as shipping_revenue")
        )->groupBy('orders.store_id')->get()->keyBy('store_id');

        $consolidatedReport = $storesList->map(function ($store) use ($storeStats, $orders, $orderItems) {
            $stats = $storeStats->get($store->id);
            $storeOrders = $orders->where('store_id', $store->id);
            $storeOrderIds = $storeOrders->pluck('id');
            $productsSold = $orderItems->whereIn('order_id', $storeOrderIds)->sum('quantity');

            $uniqueCustomers = $storeOrders->pluck('invoice.user_id')->filter()->unique()->count();

            return (object) [
                'store_name' => $store->name,
                'total_orders' => $stats ? $stats->total_orders : 0,
                'completed_orders' => $stats ? $stats->completed_orders : 0,
                'cancelled_orders' => $stats ? $stats->cancelled_orders : 0,
                'refunded_orders' => $stats ? $stats->refunded_orders : 0,
                'total_revenue' => $stats ? $stats->total_revenue : 0,
                'shipping_revenue' => $stats ? $stats->shipping_revenue : 0,
                'products_sold' => $productsSold,
                'unique_customers' => $uniqueCustomers,
                'success_rate' => ($stats && $stats->total_orders > 0) ? round(($stats->completed_orders / $stats->total_orders) * 100, 1) : 0
            ];
        });

        // Judul laporan otomatis menyesuaikan: kalau di-filter ke 1 toko
        // spesifik, judul PDF menunjukkan nama toko itu supaya jelas beda
        // dengan laporan konsolidasi semua toko.
        $month = Carbon::parse($startDate)->format('Y-m');
        $fileName = "Report_{$month}.pdf";
        $title = 'Laporan Penjualan Eksekutif';
        
        if ($storeId) {
            $storeName = $storesList->first()->name ?? null;
            if ($storeName) {
                $cleanStoreName = preg_replace('/[^A-Za-z0-9]/', '', $storeName);
                $fileName = "Store_{$cleanStoreName}_{$month}.pdf";
                $title = 'Laporan Penjualan Toko: ' . $storeName;
            }
        }

        try {
            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('reports.export', compact(
                'title', 'startDate', 'endDate', 'storeId',
                'orders', 'orderItems', 'totalTransactions', 'totalRevenue', 'totalProductsSold',
                'consolidatedReport'
            ))->setPaper('a4', 'landscape');

            return $pdf->download($fileName);
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menghasilkan file PDF. Terjadi kesalahan saat memproses laporan.');
        }
    }
}
