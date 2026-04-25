<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\TrackingHistory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TransactionController extends Controller
{
    /**
     * Display a listing of the transactions.
     */
    public function index(Request $request): View
    {
        $query = Transaction::with(['order.store'])->latest();

        // 1. Filter by status (tab mapping)
        $tab = $request->input('tab', 'all');
        if ($tab !== 'all' && in_array($tab, ['pending', 'paid', 'failed', 'refund'])) {
            $query->where('status', $tab);
        }

        // 2. Text Search (Transaction Code, Order Number, or Customer Name)
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                // Search transaction code
                $q->where('transaction_code', 'like', "%{$search}%")
                  // Or search related order fields
                  ->orWhereHas('order', function ($orderQuery) use ($search) {
                      $orderQuery->where('order_number', 'like', "%{$search}%")
                                 ->orWhere('customer_name', 'like', "%{$search}%");
                  });
            });
        }

        // 3. Filter by Specific Date (Transaction creation date)
        if ($date = $request->input('date')) {
            $query->whereDate('created_at', $date);
        }

        $transactions = $query->paginate(10)->appends($request->query());

        return view('transactions.index', compact('transactions', 'tab'));
    }

    /**
     * Update the generic status of the transaction manually.
     */
    public function updateStatus(Request $request, Transaction $transaction): RedirectResponse
    {
        $request->validate([
            'status' => 'required|in:paid,failed,refund',
            'notes'  => 'nullable|string|max:1000'
        ]);

        $updates = [
            'status' => $request->status,
            'notes'  => $request->notes,
        ];

        // Set payment timestamp if marked as paid
        if ($request->status === 'paid') {
            $updates['payment_date'] = now();
        }

        $transaction->update($updates);

        // Auto-sync: If paid, change the associated Order's status to processing
        if ($request->status === 'paid') {
            $order = $transaction->order;
            if ($order && $order->status === 'pending') {
                $order->update([
                    'status' => 'processing'
                ]);

                // Update stock when payment is verified
                $order->orderItemsStockOut();

                TrackingHistory::create([
                    'order_id' => $order->id,
                    'admin_id' => auth()->id(),
                    'status'   => 'processing',
                    'notes'    => 'Status pesanan otomatis diproses karena pembayaran Lunas.',
                ]);
            }
        }

        if (in_array($request->status, ['failed', 'refund'])) {
            $order = $transaction->order;
            if ($order && in_array($order->status, ['pending', 'processing'])) {
                // Restore stock when failed or refunded
                $order->orderItemsStockIn($request->status);

                $reason = $request->notes ?? "Pesanan dibatalkan karena pembayaran {$request->status}.";
                $order->update([
                    'status' => 'cancelled',
                    'cancel_reason' => $reason
                ]);

                TrackingHistory::create([
                    'order_id' => $order->id,
                    'admin_id' => auth()->id(),
                    'status'   => 'cancelled',
                    'notes'    => $reason,
                ]);
            }
        }

        $statusMsg = match($request->status) {
            'paid'   => 'Lunas',
            'failed' => 'Gagal',
            'refund' => 'Dikembalikan',
            default  => $request->status
        };

        return back()->with('success', "Transaksi {$transaction->transaction_code} berhasil diperbarui menjadi {$statusMsg}.");
    }
}
