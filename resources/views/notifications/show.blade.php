@extends('layouts.app')

@section('title', 'Detail Notifikasi')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="mb-0 text-gray-800" style="font-weight: 700;">Detail Notifikasi</h4>
            <a href="{{ route('notifications.index') }}" class="btn btn-outline-secondary btn-sm" style="border-radius: 8px;">
                <i class="fas fa-arrow-left me-1"></i> Kembali ke Daftar
            </a>
        </div>

        <div class="card border-0 shadow-sm" style="border-radius: 12px; overflow: hidden;">
            <div class="card-body p-4">
                <div class="d-flex align-items-center mb-3">
                    @php
                        $type = $notification->data['type'] ?? 'info';
                        $icon = 'fa-bell text-primary bg-primary-subtle';
                        
                        if (in_array($type, ['new_order'])) {
                            $icon = 'fa-shopping-bag text-success bg-success-subtle';
                        } elseif (in_array($type, ['payment'])) {
                            $icon = 'fa-money-bill-wave text-warning bg-warning-subtle';
                        } elseif (in_array($type, ['cancel', 'return_requested', 'return_approved', 'return_rejected'])) {
                            $icon = 'fa-exclamation-circle text-danger bg-danger-subtle';
                        } elseif (in_array($type, ['shipping', 'delivered'])) {
                            $icon = 'fa-truck text-info bg-info-subtle';
                        }
                    @endphp
                    <div class="rounded-circle d-flex align-items-center justify-content-center me-3 {{ explode(' ', $icon)[2] ?? '' }}" style="width: 48px; height: 48px;">
                        <i class="fas {{ explode(' ', $icon)[0] }} {{ explode(' ', $icon)[1] ?? '' }} fs-5"></i>
                    </div>
                    <div>
                        <h5 class="mb-1" style="font-weight: 700;">{{ $notification->data['title'] ?? 'Notifikasi Sistem' }}</h5>
                        <p class="text-muted mb-0 small">
                            <i class="far fa-clock me-1"></i> {{ $notification->created_at->translatedFormat('d M Y, H:i') }}
                            ({{ $notification->created_at->diffForHumans() }})
                            <span class="ms-2 badge bg-success-subtle text-success"><i class="fas fa-check-double me-1"></i> Dibaca</span>
                        </p>
                    </div>
                </div>

                <hr class="my-4">

                <div class="notification-content" style="font-size: 1.05rem; line-height: 1.6; color: #4a5568;">
                    {{ $notification->data['message'] ?? 'Tidak ada pesan spesifik untuk notifikasi ini.' }}
                </div>

                @if(isset($notification->data['order_id']))
                    <div class="mt-4 p-3 bg-light rounded border">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-1 fw-bold text-dark">Informasi Terkait</h6>
                                <span class="text-muted small">Terdapat referensi pesanan (ID: {{ $notification->data['order_id'] }})</span>
                            </div>
                            <a href="{{ route('notifications.redirect', $notification->id) }}" class="btn btn-primary btn-sm px-3" style="border-radius: 8px;">
                                Lihat Data <i class="fas fa-arrow-right ms-1"></i>
                            </a>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
