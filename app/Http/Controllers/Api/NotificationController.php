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
}
