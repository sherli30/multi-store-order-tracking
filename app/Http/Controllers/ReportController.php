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
     * Sales Dashboard Overview
     */
    public function index(Request $request)
    {
        // Default range: Last 30 days
        $days = $request->input('days', 30);
        $startDate = Carbon::now()->subDays($days);

        // 1. KPI Cards
        $totalRevenue = Transaction::where('status', 'paid')
            ->where('created_at', '>=', $startDate)
            ->sum('amount');
        
        $totalOrders = Order::where('status', '!=', 'cancelled')
            ->where('created_at', '>=', $startDate)
            ->count();
        
        $avgOrderValue = $totalOrders > 0 ? $totalRevenue / $totalOrders : 0;
        
        $activeCustomers = Order::where('created_at', '>=', $startDate)
            ->distinct('customer_email')
            ->count('customer_email');

        // 2. Sales Trend (Daily Revenue)
        $salesTrend = Transaction::where('status', 'paid')
            ->where('created_at', '>=', $startDate)
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('SUM(amount) as revenue'))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // 3. Top Selling Products
        $topProducts = OrderItem::join('product_variants', 'order_items.product_variant_id', '=', 'product_variants.id')
            ->select('product_variants.product_id', DB::raw('SUM(order_items.quantity) as total_qty'))
            ->groupBy('product_variants.product_id')
            ->orderByDesc('total_qty')
            ->limit(5)
            ->get()
            ->map(function($item) {
                $item->product = Product::find($item->product_id);
                return $item;
            });

        // 4. Payment Method Distribution
        $paymentMethods = Transaction::where('status', 'paid')
            ->select('payment_method', DB::raw('COUNT(*) as count'))
            ->groupBy('payment_method')
            ->get();

        return view('reports.index', compact(
            'totalRevenue', 'totalOrders', 'avgOrderValue', 'activeCustomers',
            'salesTrend', 'topProducts', 'paymentMethods', 'days'
        ));
    }

    /**
     * Reports categorized by Store
     */
    public function stores(Request $request)
    {
        $startDate = $request->input('start_date', Carbon::now()->startOfMonth()->toDateString());
        $endDate = $request->input('end_date', Carbon::now()->toDateString());
        $storeId = $request->input('store_id');

        $query = Store::withCount(['orders' => function($q) use ($startDate, $endDate) {
            $q->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);
        }])->with(['orders' => function($q) use ($startDate, $endDate) {
            $q->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);
        }]);

        if ($storeId) {
            $query->where('id', $storeId);
        }

        $stores = $query->get()->map(function($store) {
            $store->revenue = $store->orders->where('status', '!=', 'cancelled')->sum('total_amount');
            return $store;
        });

        $allStores = Store::all();

        return view('reports.stores', compact('stores', 'allStores', 'startDate', 'endDate'));
    }

    /**
     * Combined/Consolidated Report for all stores
     */
    public function consolidated(Request $request)
    {
        $startDate = $request->input('start_date', Carbon::now()->startOfMonth()->toDateString());
        $endDate = $request->input('end_date', Carbon::now()->toDateString());

        $report = Store::all()->map(function($store) use ($startDate, $endDate) {
            $orders = Order::where('store_id', $store->id)
                ->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
                ->get();
            
            return [
                'store_name' => $store->name,
                'total_orders' => $orders->count(),
                'completed_orders' => $orders->where('status', 'completed')->count(),
                'cancelled_orders' => $orders->where('status', 'cancelled')->count(),
                'total_revenue' => $orders->where('status', '!=', 'cancelled')->sum('total_amount'),
            ];
        });

        $totals = [
            'orders' => $report->sum('total_orders'),
            'revenue' => $report->sum('total_revenue'),
        ];

        return view('reports.consolidated', compact('report', 'totals', 'startDate', 'endDate'));
    }

    /**
     * Export functionality (Print-Friendly View)
     */
    public function export(Request $request)
    {
        $type = $request->input('type', 'consolidated'); // consolidated or store
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        if ($type === 'consolidated') {
            $data = Store::all()->map(function($store) use ($startDate, $endDate) {
                $orders = Order::where('store_id', $store->id)
                    ->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
                    ->get();
                return [
                    'name' => $store->name,
                    'count' => $orders->count(),
                    'revenue' => $orders->where('status', '!=', 'cancelled')->sum('total_amount'),
                ];
            });
            $title = "Laporan Konsolidasi Penjualan";
        } else {
            $store = Store::findOrFail($request->store_id);
            $orders = Order::where('store_id', $store->id)
                ->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
                ->with('orderItems.product')
                ->get();
            $data = [
                'store' => $store,
                'orders' => $orders,
                'revenue' => $orders->where('status', '!=', 'cancelled')->sum('total_amount')
            ];
            $title = "Laporan Penjualan - " . $store->name;
        }

        return view('reports.export', compact('data', 'type', 'title', 'startDate', 'endDate'));
    }
}
