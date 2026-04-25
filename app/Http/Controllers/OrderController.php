<?php

namespace App\Http\Controllers;

use App\Exceptions\InsufficientStockException;
use App\Models\Order;
use App\Models\Store;
use App\Models\TrackingHistory;
use App\Services\OrderService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OrderController extends Controller
{
    public function __construct(private readonly OrderService $orderService) {}

    /**
     * Display a listing of orders with correct tab counts and eager loading.
     */
    public function index(Request $request): View|\Illuminate\Http\Response|string
    {
        // ── Tab counts (single query via groupBy for efficiency) ──────────
        $countQuery = Order::query();

        // Apply non-status filters so tab counts respect active store/search/date filters
        if ($storeId = $request->input('store_id')) {
            $countQuery->where('store_id', $storeId);
        }
        if ($search = $request->input('search')) {
            $countQuery->where(function ($q) use ($search) {
                $q->where('order_number', 'like', "%{$search}%")
                  ->orWhere('customer_name', 'like', "%{$search}%");
            });
        }
        if ($date = $request->input('date')) {
            $countQuery->whereDate('created_at', $date);
        }

        $statusCounts = (clone $countQuery)
            ->selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status')
            ->toArray();

        $tabCounts = [
            'all'        => array_sum($statusCounts),
            'pending'    => $statusCounts['pending']    ?? 0,
            'processing' => $statusCounts['processing'] ?? 0,
            'shipping'   => $statusCounts['shipping']   ?? 0,
            'completed'  => $statusCounts['completed']  ?? 0,
            'cancelled'  => $statusCounts['cancelled']  ?? 0,
        ];

        // ── Main query — only load what the table needs ───────────────────
        $query = Order::with(['store', 'orderItems']);

        // Sorting
        $sort = $request->input('sort', 'created_at');
        $dir  = $request->input('dir', 'desc');
        $allowedSorts = ['created_at', 'status', 'total_amount'];
        if (in_array($sort, $allowedSorts)) {
            $query->orderBy($sort, $dir === 'asc' ? 'asc' : 'desc');
        } else {
            $query->latest();
        }

        // Filter by tab/status
        $tab = $request->input('tab', 'all');
        if ($tab !== 'all' && in_array($tab, ['pending', 'processing', 'shipping', 'completed', 'cancelled'])) {
            $query->where('status', $tab);
        }

        // Filter by store
        if ($storeId) {
            $query->where('store_id', $storeId);
        }

        // Filter by explicit status param (sidebar links)
        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        // Search
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('order_number', 'like', "%{$search}%")
                  ->orWhere('customer_name', 'like', "%{$search}%")
                  ->orWhereHas('orderItems.productVariant.product', function ($q2) use ($search) {
                      $q2->where('name', 'like', "%{$search}%");
                  });
            });
        }

        // Filter by date
        if ($date) {
            $query->whereDate('created_at', $date);
        }

        $perPage = (int) $request->input('per_page', 10);
        $orders  = $query->paginate($perPage)->appends($request->query());

        if ($request->ajax()) {
            return view('orders.partials.table', compact('orders'))->render();
        }

        $stores = Store::orderBy('name')->get();

        return view('orders.index', compact('orders', 'stores', 'tab', 'tabCounts'));
    }

    /**
     * Display the specified order — fully loaded for the detail page.
     */
    public function show(Order $order): View
    {
        $order->load([
            'store',
            'orderItems.productVariant.product.images',
            'trackingHistories' => fn ($q) => $q->with('admin')->latest(),
        ]);

        return view('orders.show', compact('order'));
    }

    /**
     * Update the generic status of the order.
     * When changing to 'processing', deducts stock via OrderService.
     */
    public function updateStatus(Request $request, Order $order): RedirectResponse
    {
        $request->validate([
            'status' => 'required|in:pending,processing,shipping,completed',
        ]);

        $newStatus = $request->status;

        // Deduct stock when order moves to 'processing' (if not already done)
        if ($newStatus === 'processing' && !$order->is_stock_deducted) {
            try {
                $this->orderService->processOrderStock($order);
            } catch (InsufficientStockException $e) {
                return back()->withErrors(['stock' => $e->getMessage()]);
            }
        }

        $order->update(['status' => $newStatus]);

        TrackingHistory::create([
            'order_id' => $order->id,
            'admin_id' => auth()->id(),
            'status'   => $newStatus,
            'notes'    => 'Diperbarui melalui panel Manajemen Pesanan.',
        ]);

        $statusMsg = match ($newStatus) {
            'processing' => 'Sedang Dikemas',
            'shipping'   => 'Sedang Dikirim',
            'completed'  => 'Selesai',
            default      => ucfirst($newStatus),
        };

        return back()->with('success', "Status pesanan {$order->order_number} berhasil diubah menjadi {$statusMsg}.");
    }

    /**
     * Cancel the order with a mandatory reason.
     * Restores stock via OrderService if previously deducted.
     */
    public function cancel(Request $request, Order $order): RedirectResponse
    {
        $request->validate([
            'cancel_reason' => 'required|string|max:1000',
        ]);

        $order->update([
            'status'        => 'cancelled',
            'cancel_reason' => $request->cancel_reason,
        ]);

        TrackingHistory::create([
            'order_id' => $order->id,
            'admin_id' => auth()->id(),
            'status'   => 'cancelled',
            'notes'    => "Pesanan dibatalkan. Alasan: " . $request->cancel_reason,
        ]);

        // Restore stock via OrderService (race-condition safe & logged)
        $this->orderService->restoreOrderStock($order, 'cancellation');

        return back()->with('success', "Pesanan {$order->order_number} berhasil dibatalkan.");
    }

    /**
     * Update the shipping type (reguler/cargo).
     */
    public function updateShipping(Request $request, Order $order): RedirectResponse
    {
        $request->validate([
            'shipping_type' => 'required|in:reguler,cargo',
        ]);

        $order->update(['shipping_type' => $request->shipping_type]);

        return back()->with('success', "Jenis pengiriman pesanan {$order->order_number} berhasil diperbarui ke " . ucfirst($request->shipping_type) . ".");
    }
}
