<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Models\Transaction;
use App\Models\StockMovement;
use App\Models\Store;
use App\Models\ProductCategory;
use App\Models\Courier;
use App\Models\ShippingService;
use App\Models\Province;
use App\Models\ShippingRate;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // 1. Summary Cards
        $totalRevenue = Order::whereIn('payment_status', ['settlement', 'capture', 'paid'])->sum('total_amount');
        $totalOrders = Order::whereIn('payment_status', ['settlement', 'capture', 'paid'])->count();
        $totalCustomers = User::where('role', 'customer')->count();
        $totalProducts = Product::count();

        // 2. Sales Chart Data (Last 7 days revenue)
        $last7DaysLabels = [];
        $salesDataArr = [];

        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            $last7DaysLabels[] = $date->translatedFormat('d M');
            $salesDataArr[$date->format('Y-m-d')] = 0;
        }

        $sales = Order::whereIn('payment_status', ['settlement', 'capture', 'paid'])
            ->where('created_at', '>=', Carbon::today()->subDays(6))
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('SUM(total_amount) as total'))
            ->groupBy('date')
            ->get();

        foreach ($sales as $sale) {
            $salesDataArr[$sale->date] = (float) $sale->total;
        }
        $salesChartData = array_values($salesDataArr);

        // 3. Donut/Pie Charts Data (Orders by Status)
        $ordersByStatus = Order::select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->pluck('count', 'status')->toArray();

        // 4. Recent Orders — eager-load customer (user_id) for avatar display (mirrors Customers module)
        $recentOrders = Order::with('customer')->orderBy('created_at', 'desc')->take(5)->get();

        // 5. Transaction Summaries
        $transactionSummaries = [
            'pending' => Transaction::where('status', 'pending')->count(),
            'success' => Transaction::where('status', 'paid')->count(),
            'failed'  => Transaction::where('status', 'failed')->count(),
            'refund'  => Transaction::where('status', 'refund')->count(),
        ];

        // 6. Top Couriers
        $topCouriers = Order::whereNotNull('shipping_courier')
            ->select('shipping_courier', DB::raw('count(*) as count'))
            ->groupBy('shipping_courier')
            ->orderBy('count', 'desc')
            ->take(5)
            ->get();

        // 7. Customer Statistics
        $newCustomers = User::where('role', 'customer')->where('created_at', '>=', Carbon::now()->subMonth())->count();

        // 8. Top-selling products
        $topProducts = DB::table('order_items')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->whereIn('orders.payment_status', ['settlement', 'capture', 'paid'])
            ->select('products.name', DB::raw('SUM(order_items.quantity) as total_sold'))
            ->groupBy('products.id', 'products.name')
            ->orderBy('total_sold', 'desc')
            ->take(5)
            ->get();

        // 9. Recent activities
        $recentActivities = collect();
        foreach ($recentOrders->take(3) as $order) {
            $recentActivities->push([
                'type'        => 'order',
                'description' => "Pesanan baru dari {$order->customer_name} (Rp " . number_format($order->total_amount, 0, ',', '.') . ")",
                'time'        => $order->created_at->diffForHumans(),
            ]);
        }
        $recentStocks = StockMovement::with('product')->orderBy('created_at', 'desc')->take(3)->get();
        foreach ($recentStocks as $stock) {
            $typeLabel = $stock->type === 'in' ? 'Masuk' : 'Keluar';
            $recentActivities->push([
                'type'        => 'stock',
                'description' => "Stok {$typeLabel} untuk produk {$stock->product->name} ({$stock->quantity} qty)",
                'time'        => $stock->created_at->diffForHumans(),
            ]);
        }

        // 10. Master Data Counts
        $totalStores           = Store::where('is_active', true)->count();
        $totalCategories       = ProductCategory::count();
        $totalCouriers         = Courier::count();
        $totalShippingServices = ShippingService::count();
        $totalProvinces        = Province::count();
        $totalShippingRates    = ShippingRate::count();

        // 11. Delivery Summary (berdasarkan status order)
        $deliverySummary = [
            'pending'   => Order::whereIn('status', ['menunggu_konfirmasi_admin', 'perlu_diproses', 'processing'])->count(),
            'shipping'  => Order::where('status', 'shipping')->count(),
            'delivered' => Order::where('status', 'completed')->count(),
        ];

        // 12. Report Snapshot (bulan berjalan)
        $monthStart = Carbon::now()->startOfMonth();
        $monthEnd   = Carbon::now()->endOfMonth();

        $reportSnapshot = [
            'completed'  => Order::where('status', 'completed')
                                ->whereBetween('created_at', [$monthStart, $monthEnd])
                                ->count(),
            'processing' => Order::whereIn('status', ['menunggu_konfirmasi_admin', 'perlu_diproses', 'processing', 'shipping'])
                                ->whereBetween('created_at', [$monthStart, $monthEnd])
                                ->count(),
            'cancelled'  => Order::where('status', 'cancelled')
                                ->whereBetween('created_at', [$monthStart, $monthEnd])
                                ->count(),
            'refunded'   => Order::where('status', 'refunded')
                                ->whereBetween('created_at', [$monthStart, $monthEnd])
                                ->count(),
            'revenue'    => Order::whereIn('payment_status', ['settlement', 'capture', 'paid'])
                                ->whereBetween('created_at', [$monthStart, $monthEnd])
                                ->sum('total_amount'),
        ];

        return view('dashboard', compact(
            // Summary Cards
            'totalRevenue',
            'totalOrders',
            'totalCustomers',
            'totalProducts',
            // Charts
            'last7DaysLabels',
            'salesChartData',
            'ordersByStatus',
            // Tables & Lists
            'recentOrders',
            'transactionSummaries',
            'topCouriers',
            'newCustomers',
            'topProducts',
            'recentActivities',
            // Master Data Counts
            'totalStores',
            'totalCategories',
            'totalCouriers',
            'totalShippingServices',
            'totalProvinces',
            'totalShippingRates',
            // Delivery & Report
            'deliverySummary',
            'reportSnapshot',
        ));
    }
}
