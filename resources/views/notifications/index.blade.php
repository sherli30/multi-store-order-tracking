@extends('layouts.app')

@section('title', 'Notifikasi')

@section('styles')
    .page-header { margin-bottom: 24px; display: flex; justify-content: space-between; align-items: center; }
    .page-header h1 { font-size: 20px; font-weight: 800; color: var(--text-1); letter-spacing: -0.03em; }
    
    .notif-card {
        background: var(--panel); border: 1px solid var(--border); border-radius: 14px;
        overflow: hidden; box-shadow: var(--shadow-sm); animation: rise 0.4s ease both;
    }
    .notif-item {
        padding: 16px 20px; border-bottom: 1px solid var(--border); display: flex; gap: 16px; transition: background 0.15s;
        text-decoration: none; color: inherit; cursor: pointer;
    }
    .notif-item:last-child { border-bottom: none; }
    .notif-item:hover { background: var(--surface); }
    .notif-item.unread { background: var(--accent-dim); border-left: 3px solid var(--accent); }
    
    .notif-icon {
        width: 40px; height: 40px; border-radius: 10px; display: flex; align-items: center; justify-content: center;
        flex-shrink: 0; background: var(--surface-2); color: var(--text-2);
    }
    .notif-item.unread .notif-icon { background: #fff; color: var(--accent); }
    
    .notif-body { flex: 1; min-width: 0; }
    .notif-title { font-size: 14px; font-weight: 700; color: var(--text-1); }
    .notif-message { font-size: 13px; color: var(--text-2); margin-top: 4px; line-height: 1.5; }
    .notif-time { font-size: 11px; color: var(--text-4); margin-top: 6px; font-weight: 500; display: flex; align-items: center; gap: 4px; }
    
    .notif-actions { display: flex; align-items: center; }
    .btn-mark {
        background: none; border: 1px solid var(--border); padding: 6px 12px; border-radius: 6px;
        font-size: 11px; font-weight: 600; color: var(--text-2); cursor: pointer; transition: all 0.15s;
        z-index: 2; position: relative;
    }
    .btn-mark:hover { background: var(--surface-2); color: var(--text-1); }
@endsection

@section('content')
    <div class="page-header">
        <h1>Semua Notifikasi</h1>
        @if(auth()->user()->unreadNotifications->count() > 0)
            <form method="POST" action="{{ route('notifications.markAllAsRead') }}">
                @csrf
                <button type="submit" class="btn-primary" style="padding: 8px 16px; font-size: 12px; border-radius: 8px; border: none; background: var(--accent); color: #fff; font-weight: 600; cursor: pointer;">
                    Tandai Semua Dibaca
                </button>
            </form>
        @endif
    </div>

    <div class="notif-card">
        @forelse($notifications as $notif)
            <a href="{{ route('notifications.redirect', $notif->id) }}" class="notif-item {{ $notif->read_at ? '' : 'unread' }}">
                <div class="notif-icon">
                    @if(isset($notif->data['type']) && $notif->data['type'] == 'new_order')
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="5" width="20" height="14" rx="2"/><line x1="2" y1="10" x2="22" y2="10"/></svg>
                    @elseif(isset($notif->data['type']) && $notif->data['type'] == 'payment')
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M12 8v4l3 3"/></svg>
                    @elseif(isset($notif->data['type']) && in_array($notif->data['type'], ['cancel', 'return_requested', 'return_approved', 'return_rejected']))
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>
                    @elseif(isset($notif->data['type']) && in_array($notif->data['type'], ['shipping', 'delivered']))
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                    @else
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                    @endif
                </div>
                <div class="notif-body">
                    <div class="notif-title">{{ $notif->data['title'] ?? 'Notifikasi Sistem' }}</div>
                    <div class="notif-message">{{ $notif->data['message'] ?? '' }}</div>
                    <div class="notif-time">
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                        {{ $notif->created_at->diffForHumans() }} &bull; {{ $notif->created_at->format('d M Y, H:i') }} WIB
                    </div>
                </div>
                @if(!$notif->read_at)
                    <div class="notif-actions">
                        <span class="btn-mark" style="color: var(--accent); border-color: transparent; font-weight: 700;">● Baru</span>
                    </div>
                @endif
            </a>
        @empty
            <div style="padding: 60px 20px; text-align: center; color: var(--text-3);">
                <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" style="margin-bottom: 16px; opacity: 0.5;"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/></svg>
                <div style="font-size: 16px; font-weight: 700; color: var(--text-1); margin-bottom: 4px;">Tidak ada notifikasi</div>
                <div style="font-size: 13px;">Belum ada notifikasi apapun saat ini.</div>
            </div>
        @endforelse
    </div>

    @if($notifications->hasPages())
        <div style="margin-top: 20px;">
            {{ $notifications->links() }}
        </div>
    @endif
@endsection
