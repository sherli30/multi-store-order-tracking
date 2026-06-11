<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\WebhookFailure;
use App\Services\WebhookRecoveryService;
use App\Services\PaymentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class WebhookRecoveryController extends Controller
{
    public function __construct(
        private WebhookRecoveryService $recoveryService,
        private PaymentService $paymentService,
    ) {}

    /**
     * Get list of orders with unresolved webhook failures
     */
    public function failedOrders(Request $request)
    {
        try {
            $page = $request->query('page', 1);
            $perPage = $request->query('per_page', 50);

            $failures = WebhookFailure::unresolved()
                ->with(['order' => function ($q) {
                    $q->select('id', 'midtrans_order_id', 'customer_name', 'status', 'payment_status', 'is_stock_deducted', 'total_amount');
                }])
                ->paginate($perPage, ['*'], 'page', $page);

            return response()->json([
                'status' => 'success',
                'message' => 'List pesanan dengan kegagalan webhook',
                'data' => $failures->items(),
                'pagination' => [
                    'current_page' => $failures->currentPage(),
                    'per_page' => $failures->perPage(),
                    'total' => $failures->total(),
                    'last_page' => $failures->lastPage(),
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('[WebhookRecovery] Exception listing failed orders: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal mengambil data pesanan dengan kegagalan',
            ], 500);
        }
    }

    /**
     * Audit single order for consistency issues
     */
    public function auditOrder($orderId)
    {
        try {
            $order = Order::with(['transaction', 'orderItems', 'webhookFailures'])
                ->findOrFail($orderId);

            $issues = $this->recoveryService->auditOrder($order);
            $webhookFailures = $order->webhookFailures()->where('resolved', false)->get();

            return response()->json([
                'status' => 'success',
                'data' => [
                    'order_id' => $order->id,
                    'midtrans_order_id' => $order->midtrans_order_id,
                    'payment_status' => $order->payment_status,
                    'is_stock_deducted' => $order->is_stock_deducted,
                    'order_status' => $order->status,
                    'consistency_issues' => $issues,
                    'webhook_failures' => $webhookFailures,
                    'action_required' => count($issues) > 0 || count($webhookFailures) > 0,
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('[WebhookRecovery] Exception auditing order #' . $orderId . ': ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Pesanan tidak ditemukan atau gagal mengaudit',
            ], 404);
        }
    }

    /**
     * Manually recover order: deduct stock for settled payment
     */
    public function recoverPaymentStock($orderId)
    {
        try {
            $order = Order::findOrFail($orderId);

            $recovered = $this->recoveryService->recoverPartialPayment($order);

            if ($recovered) {
                return response()->json([
                    'status' => 'success',
                    'message' => 'Stok pesanan berhasil dikurangi setelah pembayaran',
                    'data' => [
                        'order_id' => $order->id,
                        'is_stock_deducted' => true,
                    ]
                ]);
            } else {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Gagal mengurangi stok: stok tidak cukup atau pesanan belum lunas',
                ], 400);
            }
        } catch (\Exception $e) {
            Log::error('[WebhookRecovery] Exception recovering payment stock for order #' . $orderId . ': ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan saat memulihkan pesanan',
            ], 500);
        }
    }

    /**
     * Regenerate snap token for order stuck without payment
     */
    public function regenerateSnapToken($orderId)
    {
        try {
            $order = Order::findOrFail($orderId);

            if ($order->payment_status && in_array($order->payment_status, ['settlement', 'capture'])) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Pesanan sudah lunas, tidak perlu regenerasi token',
                ], 400);
            }

            $token = $this->recoveryService->recoverSnapTokenGeneration($order, $this->paymentService);

            if ($token) {
                return response()->json([
                    'status' => 'success',
                    'message' => 'Token pembayaran berhasil dibuat ulang',
                    'data' => [
                        'order_id' => $order->id,
                        'snap_token' => $token,
                    ]
                ]);
            } else {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Gagal membuat ulang token pembayaran, silakan periksa data alamat pelanggan',
                ], 400);
            }
        } catch (\Exception $e) {
            Log::error('[WebhookRecovery] Exception regenerating snap token for order #' . $orderId . ': ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan saat membuat ulang token',
            ], 500);
        }
    }

    /**
     * Mark webhook failure as resolved (manual override)
     */
    public function markFailureResolved($failureId)
    {
        try {
            $failure = WebhookFailure::findOrFail($failureId);
            $failure->markResolved();

            return response()->json([
                'status' => 'success',
                'message' => 'Kegagalan webhook ditandai sebagai terselesaikan',
                'data' => [
                    'failure_id' => $failure->id,
                    'resolved' => true,
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('[WebhookRecovery] Exception marking failure as resolved: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal menandai kegagalan sebagai terselesaikan',
            ], 500);
        }
    }

    /**
     * Get audit report of all orders for consistency check
     */
    public function auditAllOrders()
    {
        try {
            $ordersWithIssues = [];
            $orders = Order::with(['transaction', 'webhookFailures'])->get();

            foreach ($orders as $order) {
                $issues = $this->recoveryService->auditOrder($order);
                if (count($issues) > 0) {
                    $ordersWithIssues[] = [
                        'order_id' => $order->id,
                        'midtrans_order_id' => $order->midtrans_order_id,
                        'customer_name' => $order->customer_name,
                        'status' => $order->status,
                        'issues' => $issues,
                    ];
                }
            }

            $failureCount = WebhookFailure::where('resolved', false)->count();

            return response()->json([
                'status' => 'success',
                'data' => [
                    'total_orders_checked' => $orders->count(),
                    'orders_with_consistency_issues' => count($ordersWithIssues),
                    'unresolved_webhook_failures' => $failureCount,
                    'affected_orders' => $ordersWithIssues,
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('[WebhookRecovery] Exception auditing all orders: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal melakukan audit semua pesanan',
            ], 500);
        }
    }
}
