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
        $query = Transaction::with(['order.store']);

        // 1. Filter by status tab
        $tab = $request->input('tab', 'all');
        if ($tab !== 'all' && in_array($tab, ['pending', 'paid', 'failed', 'refund'])) {
            $query->where('status', $tab);
        }

        // 2. Filter by store (via the related order)
        if ($storeId = $request->input('store_id')) {
            $query->whereHas('order', fn($q) => $q->where('store_id', $storeId));
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
            $baseCountQuery->whereHas('order', fn($q) => $q->where('store_id', $storeId));
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

        // Active stores that have at least one transaction, for the Store filter dropdown
        $stores = Store::where('is_active', true)
            ->whereIn('id', Order::select('store_id')
                ->whereIn('id', Transaction::select('order_id')->distinct())
                ->distinct())
            ->orderBy('name')
            ->get();

        return view('transactions.index', compact('transactions', 'tab', 'tabCounts', 'stats', 'stores'));
    }

    /**
     * Display the specified transaction.
     */
    public function show(Transaction $transaction): View
    {
        $transaction->load(['order.store', 'order.orderItems.product']);

        return view('transactions.show', compact('transaction'));
    }

    /**
     * Update the generic status of the transaction manually.
     */
    public function updateStatus(\App\Http\Requests\TransactionStatusRequest $request, Transaction $transaction): RedirectResponse
    {
        $result = \Illuminate\Support\Facades\DB::transaction(function () use ($request, $transaction) {
            $order = $transaction->order;

            // Lock order if exists to prevent race with webhooks
            if ($order) {
                $order = Order::lockForUpdate()->find($order->id);
            }

            $updates = [
                'status' => $request->status,
                'notes'  => $request->notes,
            ];

            if ($request->status === 'paid') {
                $updates['payment_date'] = now();
            }

            if (in_array($request->status, ['failed', 'refund'])) {
                $updates['refunded_at'] = now();
            }

            $transaction->update($updates);

            // Auto-sync: If paid, advance the associated Order status
            if ($request->status === 'paid') {
                if ($order && in_array($order->status, [Order::STATUS_PENDING, Order::STATUS_CANCELLED])) {
                    $order->update([
                        'status'         => Order::STATUS_PERLU_DIPROSES,
                        'payment_status' => 'settlement',
                    ]);

                    $order->load('orderItems.product');
                    if (! $order->is_stock_deducted) {
                        try {
                            $this->orderService->processOrderStock($order);
                        } catch (InsufficientStockException $e) {
                            $transaction->update(['status' => 'pending', 'payment_date' => null]);
                            $order->update(['status' => Order::STATUS_PENDING, 'payment_status' => 'pending']);

                            return back()->withErrors(['stock' => $e->getMessage()]);
                        }
                    }
                } elseif ($order) {
                    $order->update(['payment_status' => 'settlement']);
                }

                if ($order) {
                    TrackingHistory::create([
                        'order_id'       => $order->id,
                        'admin_id'       => auth()->id(),
                        'status'         => $order->fresh()->status,
                        'notes'          => 'Status pembayaran diperbarui menjadi Lunas oleh Admin.',
                        'payment_method' => $transaction->payment_method,
                    ]);

                    AuditService::logOrderRefund(
                        auth()->id(),
                        $order->id,
                        'manual_payment_confirmation',
                        $request->notes ?? 'Manual payment status update'
                    );
                }
            }

            if (in_array($request->status, ['failed', 'refund'])) {
                if ($order && in_array($order->status, [
                    Order::STATUS_PENDING,
                    Order::STATUS_PERLU_DIPROSES,
                    Order::STATUS_PROCESSING,
                    Order::STATUS_SHIPPING,
                ])) {
                    $reason = $request->notes ?? "Pesanan dibatalkan karena pembayaran {$request->status}.";
                    $order->update([
                        'status'         => Order::STATUS_CANCELLED,
                        'payment_status' => $request->status,
                        'cancel_reason'  => $reason,
                    ]);

                    $order->load('orderItems.product');
                    if ($order->is_stock_deducted) {
                        $this->orderService->restoreOrderStock($order, $request->status);
                    }

                    TrackingHistory::create([
                        'order_id'       => $order->id,
                        'admin_id'       => auth()->id(),
                        'status'         => Order::STATUS_CANCELLED,
                        'notes'          => $reason,
                        'refund_method'  => 'manual',
                        'refund_reason'  => $request->notes,
                        'payment_method' => $transaction->payment_method,
                    ]);

                    AuditService::logOrderRefund(
                        auth()->id(),
                        $order->id,
                        'manual_refund',
                        $request->notes ?? 'Manual refund trigger'
                    );
                }
            }

            $statusMsg = \App\Services\StatusService::getTransactionLabel($request->status);

            if ($order) {
                $admins = \App\Models\User::where('role', 'admin')->get();
                \Illuminate\Support\Facades\Notification::send($admins, new \App\Notifications\GeneralOrderNotification([
                    'order_id' => $order->id,
                    'title'    => 'Pembayaran ' . $statusMsg . ': ' . $order->midtrans_order_id,
                    'message'  => "Status pembayaran untuk pesanan {$order->midtrans_order_id} telah diubah menjadi {$statusMsg}.",
                    'type'     => in_array($request->status, ['failed', 'refund']) ? 'cancel' : 'payment',
                ]));

                if ($order->customer) {
                    $order->customer->notify(new \App\Notifications\GeneralOrderNotification([
                        'order_id' => $order->id,
                        'title'    => 'Status Pembayaran: ' . $statusMsg,
                        'message'  => "Status pembayaran untuk pesanan Anda ({$order->midtrans_order_id}) telah diperbarui menjadi {$statusMsg}.",
                        'type'     => in_array($request->status, ['failed', 'refund']) ? 'cancel' : 'payment',
                    ]));
                }
            }

            return back()->with('success', "Data pembayaran berhasil diproses. Status transaksi {$transaction->transaction_id} telah diubah menjadi '{$statusMsg}'.");
        });

        return $result;
    }
}
