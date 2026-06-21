<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index()
    {
        $notifications = auth()->user()->notifications()->paginate(15);
        return view('notifications.index', compact('notifications'));
    }

    public function markAsRead($id)
    {
        $notification = auth()->user()->notifications()->findOrFail($id);
        $notification->markAsRead();
        return back()->with('success', [
            'title' => 'Notifikasi Dibaca',
            'list' => [
                'Notifikasi berhasil ditandai sudah dibaca.'
            ]
        ]);
    }

    public function show($id)
    {
        $notification = auth()->user()->notifications()->findOrFail($id);
        $notification->markAsRead();
        return view('notifications.show', compact('notification'));
    }

    public function redirect($id)
    {
        $notification = auth()->user()->notifications()->findOrFail($id);
        $notification->markAsRead();

        $data = $notification->data;
        
        // If order_id exists, redirect to Order Detail or Delivery Tracking based on type
        if (isset($data['order_id'])) {
            $order = \App\Models\Order::find($data['order_id']);
            
            if (!$order) {
                return redirect()->route('notifications.show', $id)
                    ->with('error', 'Entitas yang terkait dengan notifikasi ini sudah tidak ditemukan atau telah dihapus.');
            }

            // Redirect logic based on type
            $type = $data['type'] ?? 'info';
            
            if (in_array($type, ['payment'])) {
                $transaction = \App\Models\Transaction::where('order_id', $order->id)->first();
                if ($transaction) {
                    return redirect()->route('transactions.show', $transaction->id);
                }
                return redirect()->route('orders.show', $order->id);
            }

            if (in_array($type, ['shipping', 'delivered'])) {
                // If it's a shipping related notification, redirect to orders.show which contains tracking
                return redirect()->route('orders.show', $order->id);
            }
            
            if (in_array($type, ['return_requested', 'return_approved', 'return_rejected'])) {
                // Redirect to orders show where return details exist
                return redirect()->route('orders.show', $order->id);
            }

            return redirect()->route('orders.show', $order->id);
        }

        // Fallback
        return redirect()->route('notifications.show', $id);
    }

    public function markAllAsRead()
    {
        auth()->user()->unreadNotifications->markAsRead();
        return back()->with('success', [
            'title' => 'Semua Notifikasi Dibaca',
            'list' => [
                'Seluruh pesan notifikasi telah ditandai sudah dibaca.'
            ]
        ]);
    }

    public function stream(Request $request)
    {
        // Tutup session write agar tidak nge-lock request lain
        session_write_close();

        return response()->stream(function () use ($request) {
            $user = auth()->user();
            if (!$user) return;

            // Optional: baca parameter "since" atau gunakan now()
            // Kita akan stream notifikasi yang baru dibuat.
            $lastChecked = now();

            while (true) {
                if (connection_aborted()) {
                    break;
                }

                // Ambil notifikasi unread yang baru saja masuk
                $newNotifications = $user->unreadNotifications()
                    ->where('created_at', '>', $lastChecked)
                    ->get();

                if ($newNotifications->isNotEmpty()) {
                    $unreadCount = $user->unreadNotifications()->count();
                    
                    $data = [
                        'unread_count' => $unreadCount,
                        'notifications' => $newNotifications->map(function ($n) {
                            return [
                                'id' => $n->id,
                                'data' => $n->data,
                                'created_at_human' => $n->created_at->diffForHumans(),
                                'created_at' => $n->created_at->toIso8601String()
                            ];
                        })->toArray()
                    ];

                    echo "event: message\n";
                    echo "data: " . json_encode($data) . "\n\n";
                    
                    ob_flush();
                    flush();

                    $lastChecked = now();
                }

                sleep(5); // Polling setiap 5 detik
            }
        }, 200, [
            'Content-Type' => 'text/event-stream',
            'Cache-Control' => 'no-cache',
            'Connection' => 'keep-alive',
            'X-Accel-Buffering' => 'no' // Untuk Nginx agar tidak mem-buffer
        ]);
    }
}
