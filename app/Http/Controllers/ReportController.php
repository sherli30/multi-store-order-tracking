<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Store;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ReportController extends Controller
{
    /**
     * Unified Sales Reports Page
     * Loads data for all sections: Dashboard, Per-Store, Consolidated, Export
     */
    public function index(Request $request)
    {
        // ── Section 1: Dashboard KPIs ──────────────────────────────────
        $days      = (int) $request->input('days', 30);
        $dashStart = Carbon::now()->subDays($days)->startOfDay();

        // Monthly revenue (last 12 months)
        $monthlyRevenue = Order::whereIn('payment_status', ['settlement', 'capture', 'paid'])
            ->where('created_at', '>=', Carbon::now()->subMonths(12)->startOfMonth())
            ->select(
                DB::raw("DATE_FORMAT(created_at, '%Y-%m') as month"),
                DB::raw('SUM(total_amount) as revenue'),
                DB::raw('COUNT(*) as count')
            )
            ->groupBy('month')
            ->orderBy('month')
            ->get();



        // ── Section 2: Per-Store ───────────────────────────────────────
        $storeDefaultStart = Carbon::now()->startOfMonth()->toDateString();
        $storeDefaultEnd   = Carbon::now()->toDateString();
        $storeStartDate    = $request->input('store_start_date', $storeDefaultStart);
        $storeEndDate      = $request->input('store_end_date',   $storeDefaultEnd);
        $storeId           = $request->input('store_id');

        $storeBaseQuery = Store::query();
        if ($storeId) {
            $storeBaseQuery->where('id', $storeId);
        }

        $storesList = $storeBaseQuery->get();
        $storesList = $storeBaseQuery->get();
        $storeStats = Order::whereIn('store_id', $storesList->pluck('id'))
            ->whereBetween('created_at', [
                $storeStartDate . ' 00:00:00',
                $storeEndDate   . ' 23:59:59',
            ])
            ->select(
                'store_id',
                DB::raw('COUNT(*) as total_orders'),
                DB::raw("SUM(CASE WHEN payment_status IN ('settlement', 'capture', 'paid') THEN total_amount ELSE 0 END) as total_revenue"),
                DB::raw("SUM(CASE WHEN status = 'cancelled' THEN 1 ELSE 0 END) as total_cancelled")
            )
            ->groupBy('store_id')
            ->get()
            ->keyBy('store_id');

        $stores = $storesList->map(function ($store) use ($storeStats) {
            $stats = $storeStats->get($store->id);

            $store->orders_count    = $stats ? $stats->total_orders : 0;
            $store->cancelled_count = $stats ? $stats->total_cancelled : 0;
            $store->revenue         = $stats ? $stats->total_revenue : 0;
            return $store;
        });

        // ── Section 3: Consolidated ────────────────────────────────────
        $consolidatedDefaultStart = Carbon::now()->startOfMonth()->toDateString();
        $consolidatedDefaultEnd   = Carbon::now()->toDateString();
        $consolidatedStartDate    = $request->input('cons_start_date', $consolidatedDefaultStart);
        $consolidatedEndDate      = $request->input('cons_end_date',   $consolidatedDefaultEnd);

        $allConsolidatedStores = Store::all();
        $allConsolidatedStores = Store::all();
        $consStats = Order::whereIn('store_id', $allConsolidatedStores->pluck('id'))
            ->whereBetween('created_at', [
                $consolidatedStartDate . ' 00:00:00',
                $consolidatedEndDate   . ' 23:59:59',
            ])
            ->select(
                'store_id',
                DB::raw('COUNT(*) as total_orders'),
                DB::raw("SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END) as completed_orders"),
                DB::raw("SUM(CASE WHEN status = 'cancelled' THEN 1 ELSE 0 END) as cancelled_orders"),
                DB::raw("SUM(CASE WHEN payment_status IN ('settlement', 'capture', 'paid') THEN total_amount ELSE 0 END) as total_revenue")
            )
            ->groupBy('store_id')
            ->get()
            ->keyBy('store_id');

        $report = $allConsolidatedStores->map(function ($store) use ($consStats) {
            $stats = $consStats->get($store->id);
            return [
                'store_name'       => $store->name,
                'total_orders'     => $stats ? $stats->total_orders : 0,
                'completed_orders' => $stats ? $stats->completed_orders : 0,
                'cancelled_orders' => $stats ? $stats->cancelled_orders : 0,
                'total_revenue'    => $stats ? $stats->total_revenue : 0,
            ];
        });

        $totals = [
            'orders'  => $report->sum('total_orders'),
            'revenue' => $report->sum('total_revenue'),
        ];

        // ── Section 4: Detailed Product Performance ────────────────────
        $productStats = OrderItem::whereHas('order', function ($q) use ($dashStart) {
            $q->whereIn('payment_status', ['settlement', 'capture', 'paid'])->where('created_at', '>=', $dashStart);
        })
            ->select('product_id', DB::raw('SUM(quantity) as total_sold'), DB::raw('SUM(quantity * price) as total_revenue'))
            ->groupBy('product_id')
            ->get()
            ->keyBy('product_id');

        // FIX: eager-load store on each product so the blade can safely access
        // $p->store->name and $p->image without any further queries or null crashes.
        // The 'image' column is a plain DB field — no accessor required.
        // Only include products from active stores to match report filter statement.
        $allProductsPerformance = Product::whereHas('store', function ($q) {
            $q->where('is_active', true);
        })->with('store')->get()->map(function ($product) use ($productStats) {
            $stat = $productStats->get($product->id);
            $product->total_sold    = $stat ? $stat->total_sold    : 0;
            $product->total_revenue = $stat ? $stat->total_revenue : 0;
            return $product;
        })->sortByDesc('total_sold')->values();

        // ── Section 5: Pending Payments / Transaksi Tertunda ───────────
        $pendingPayments = Transaction::with('order.store')
            ->whereIn('status', ['pending', 'failed'])
            ->orderBy('created_at', 'desc')
            ->take(100)
            ->get();

        // ── Section 6: Cancellation Analysis ───────────────────────────
        $cancellationAnalysis = Order::where('status', 'cancelled')
            ->where('created_at', '>=', $dashStart)
            ->select('cancel_reason', DB::raw('COUNT(*) as count'))
            ->groupBy('cancel_reason')
            ->orderByDesc('count')
            ->get();

        // ── Section 7: Export defaults ─────────────────────────────────
        $exportDefaultStart = Carbon::now()->startOfMonth()->toDateString();
        $exportDefaultEnd   = Carbon::now()->toDateString();

        // Only active stores are surfaced in filter dropdowns and the export selector
        $allStores = Store::where('is_active', true)->get();

        return view('reports.index', compact(
            // Dashboard
            'monthlyRevenue',
            'days',
            // Per-Store
            'stores',
            'allStores',
            'storeStartDate',
            'storeEndDate',
            'storeDefaultStart',
            'storeDefaultEnd',
            // Consolidated
            'report',
            'totals',
            'consolidatedStartDate',
            'consolidatedEndDate',
            'consolidatedDefaultStart',
            'consolidatedDefaultEnd',
            // New Features
            'allProductsPerformance',
            'pendingPayments',
            'cancellationAnalysis',
            // Export
            'exportDefaultStart',
            'exportDefaultEnd'
        ));
    }


    /**
     * Export — print-friendly PDF view (unchanged logic)
     */
    public function export(Request $request)
    {
        $type      = $request->input('type');
        $startDate = $request->input('start_date', Carbon::now()->startOfMonth()->toDateString());
        $endDate   = $request->input('end_date',   Carbon::now()->toDateString());

        if (!$type) {
            return redirect()->route('reports.index')->withFragment('ekspor');
        }

        if ($type === 'consolidated') {
            $stores = Store::where('is_active', true)->get();
            $exportStats = Order::whereIn('store_id', $stores->pluck('id'))
                ->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
                ->whereIn('payment_status', ['settlement', 'capture', 'paid'])
                ->select(
                    'store_id',
                    DB::raw('COUNT(*) as count'),
                    DB::raw('SUM(total_amount) as revenue')
                )
                ->groupBy('store_id')
                ->get()
                ->keyBy('store_id');

            $data = $stores->map(function ($store) use ($exportStats) {
                $stats = $exportStats->get($store->id);
                return [
                    'name'    => $store->name,
                    'count'   => $stats ? $stats->count : 0,
                    'revenue' => $stats ? $stats->revenue : 0,
                ];
            });
            $title = 'Laporan Konsolidasi Penjualan';
        } else {
            $store  = Store::findOrFail($request->store_id);
            $orders = Order::where('store_id', $store->id)
                ->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
                ->whereIn('payment_status', ['settlement', 'capture', 'paid'])
                ->with('orderItems.product')
                ->get();
            $data  = [
                'store'   => $store,
                'orders'  => $orders,
                'revenue' => $orders->sum('total_amount'),
            ];
            $title = 'Laporan Penjualan - ' . $store->name;
        }

        return view('reports.export', compact('data', 'type', 'title', 'startDate', 'endDate'));
    }
}
