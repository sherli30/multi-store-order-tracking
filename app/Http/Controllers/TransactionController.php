<?php

namespace App\Http\Controllers;

use App\Exceptions\InsufficientStockException;
use App\Models\Order;
use App\Models\Store;
use App\Models\Transaction;
use App\Models\TrackingHistory;
use App\Services\AuditService;
use App\Services\OrderService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TransactionController extends Controller
{
    public function __construct(private readonly OrderService $orderService) {}

    /**
     * Display a listing of transactions.
     *
     * Active filters (all map 1-to-1 to visible table columns):
     *   tab         → Status column  (handled by the tab bar, not a filter dropdown)
     *   store_id    → Pelanggan / Toko column
     *   date        → ID Transaksi column (date portion)
     *   sort        → sorts by created_at  (Terbaru / Terlama)
     *   amount_sort → sorts by amount      (Tertinggi / Terendah)
     *
     * Note: when both `sort` and `amount_sort` are present, amount_sort takes
     * precedence because it is the more specific intent. If only `sort` is set,
     * rows are ordered by date as before.
     */
    public function index(Request $request)
    {
        $query = Transaction::with(['order.store', 'invoice.orders.store', 'invoice.user']);

        // 1. Filter by status tab
        $tab = $request->input('tab', 'all');
        if ($tab !== 'all' && in_array($tab, ['pending', 'paid', 'failed', 'refund'])) {
            $query->where('status', $tab);
        }

        // 2. Filter by store (via the related order or invoice orders)
        if ($storeId = $request->input('store_id')) {
            $query->where(function ($q) use ($storeId) {
                $q->whereHas('order', fn($q2) => $q2->where('store_id', $storeId))
                  ->orWhereHas('invoice.orders', fn($q2) => $q2->where('store_id', $storeId));
            });
        }

        // 3. Filter by specific date
        if ($date = $request->input('date')) {
            $query->whereDate('created_at', $date);
        }

        // 4. Sorting — amount_sort takes precedence over date sort when both are set.
        //    This keeps the UI simple (two independent dropdowns) without confusing
        //    multi-column sort ordering.
        $amountSort = $request->input('amount_sort'); // 'asc' | 'desc' | ''
        $dateSort   = $request->input('sort', 'desc'); // 'asc' | 'desc'

        if (in_array($amountSort, ['asc', 'desc'])) {
            // Primary: amount; secondary: date descending as a stable tiebreaker
            $query->orderBy('amount', $amountSort)
                  ->orderBy('created_at', 'desc');
        } else {
            $query->orderBy('created_at', $dateSort === 'asc' ? 'asc' : 'desc');
        }

        // 5. Build a scoped base query for tab counts & stats.
        //    Respects store and date filters — but NOT the status/tab filter so all
        //    tab counts reflect the same data scope.
        $baseCountQuery = Transaction::query();

        if ($storeId) {
            $baseCountQuery->where(function ($q) use ($storeId) {
                $q->whereHas('order', fn($q2) => $q2->where('store_id', $storeId))
                  ->orWhereHas('invoice.orders', fn($q2) => $q2->where('store_id', $storeId));
            });
        }
        if ($date) {
            $baseCountQuery->whereDate('created_at', $date);
        }

        $tabCounts = [
            'all'     => (clone $baseCountQuery)->count(),
            'pending' => (clone $baseCountQuery)->where('status', 'pending')->count(),
            'paid'    => (clone $baseCountQuery)->where('status', 'paid')->count(),
            'failed'  => (clone $baseCountQuery)->where('status', 'failed')->count(),
            'refund'  => (clone $baseCountQuery)->where('status', 'refund')->count(),
        ];

        $stats = [
            'total_revenue' => (clone $baseCountQuery)->where('status', 'paid')->sum('amount'),
            'pending_count' => $tabCounts['pending'],
            'paid_count'    => $tabCounts['paid'],
            'failed_count'  => $tabCounts['failed'],
            'refund_count'  => $tabCounts['refund'],
        ];

        if ($request->ajax()) {
            $transactions = $query->get();
            return view('transactions.partials._table_rows', compact('transactions'));
        }

        $transactions = $query->get();

        // Active stores that have at least one transaction
        $stores = Store::where('is_active', true)
            ->where(function ($q) {
                $q->whereIn('id', Order::select('store_id')
                        ->whereIn('id', Transaction::select('order_id')->whereNotNull('order_id')->distinct())
                        ->distinct()
                    )
                  ->orWhereIn('id', Order::select('store_id')
                        ->whereIn('invoice_id', Transaction::select('invoice_id')->whereNotNull('invoice_id')->distinct())
                        ->distinct()
                    );
            })
            ->orderBy('name')
            ->get();

        return view('transactions.index', compact('transactions', 'tab', 'tabCounts', 'stats', 'stores'));
    }

    /**
     * Display the specified transaction.
     */
    public function show(Transaction $transaction): View
    {
        $transaction->load(['order.store', 'order.orderItems.product', 'invoice.orders.store', 'invoice.orders.orderItems.product']);

        return view('transactions.show', compact('transaction'));
    }

}
