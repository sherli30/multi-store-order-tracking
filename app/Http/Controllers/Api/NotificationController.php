<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index()
    {
        try {
            $notifications = auth()->user()->notifications()->paginate(20);
            $unreadCount   = auth()->user()->unreadNotifications()->count();

            return response()->json([
                'status'       => 'success',
                'unread_count' => $unreadCount,
                'data'         => $notifications,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Terjadi kesalahan saat memuat notifikasi.',
            ], 500);
        }
    }

    public function markAsRead($id)
    {
        try {
            $notification = auth()->user()->notifications()->find($id);

            if (!$notification) {
                return response()->json([
                    'status'  => 'error',
                    'message' => 'Notifikasi tidak ditemukan.',
                ], 404);
            }

            $notification->markAsRead();

            return response()->json([
                'status'  => 'success',
                'message' => 'Notifikasi ditandai sudah dibaca.',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Gagal mengubah status notifikasi.',
            ], 500);
        }
    }

    public function markAllAsRead()
    {
        try {
            auth()->user()->unreadNotifications->markAsRead();

            return response()->json([
                'status'  => 'success',
                'message' => 'Semua notifikasi ditandai sudah dibaca.',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Gagal mengubah status notifikasi.',
            ], 500);
        }
    }

    public function saveFcmToken(Request $request)
    {
        $request->validate([
            'token' => 'required|string',
            'device_type' => 'nullable|string'
        ]);

        \App\Models\FcmToken::updateOrCreate(
            ['token' => $request->token],
            [
                'user_id' => auth()->id(),
                'device_type' => $request->device_type ?? 'android'
            ]
        );

        return response()->json(['status' => 'success']);
    }

    public function stream(Request $request)
    {
        session_write_close();

        return response()->stream(function () use ($request) {
            $user = auth()->user();
            if (!$user) return;

            $lastChecked = now();

            while (true) {
                if (connection_aborted()) {
                    break;
                }

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

                sleep(5);
            }
        }, 200, [
            'Content-Type' => 'text/event-stream',
            'Cache-Control' => 'no-cache',
            'Connection' => 'keep-alive',
            'X-Accel-Buffering' => 'no'
        ]);
    }
}
