@extends('layouts.app')

@section('title', 'Riwayat Tracking Pengiriman')

@section('styles')
.page-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 24px;
    flex-wrap: wrap;
    gap: 16px;
}
.page-header-left h1 {
    font-size: 22px;
    font-weight: 800;
    color: var(--text-1);
    letter-spacing: -0.02em;
    margin-bottom: 4px;
}
.page-header-left p {
    font-size: 13.5px;
    color: var(--text-3);
}

.timeline-container {
    background: var(--panel);
    border: 1px solid var(--border);
    border-radius: 14px;
    padding: 24px;
    box-shadow: var(--shadow-sm);
    margin-top: 20px;
}

.search-box {
    display: flex;
    gap: 12px;
    margin-bottom: 24px;
}

.form-input {
    flex: 1;
    padding: 12px 16px;
    border: 1px solid var(--border);
    border-radius: 10px;
    font-size: 14px;
    color: var(--text-1);
    outline: none;
    transition: all 0.2s;
}
.form-input:focus { border-color: var(--accent); box-shadow: 0 0 0 3px var(--accent-glow); }

.btn-primary {
    background: var(--accent);
    color: #fff;
    border: none;
    padding: 12px 24px;
    border-radius: 10px;
    font-weight: 600;
    cursor: pointer;
    font-size: 14px;
    display: flex;
    align-items: center;
    gap: 8px;
}
.btn-primary:hover { background: #4f51e8; }

.timeline-item {
    position: relative;
    padding-left: 30px;
    margin-bottom: 20px;
    border-left: 2px solid var(--border-2);
}

.timeline-item::before {
    content: '';
    position: absolute;
    left: -7px;
    top: 0;
    width: 12px;
    height: 12px;
    border-radius: 50%;
    background: var(--accent);
    border: 3px solid #fff;
    box-shadow: 0 0 0 2px var(--border);
}

.timeline-item:last-child {
    border-left-color: transparent;
    margin-bottom: 0;
}

.timeline-date {
    font-size: 12px;
    color: var(--text-4);
    font-weight: 600;
    margin-bottom: 4px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.timeline-content {
    background: var(--surface);
    padding: 16px;
    border-radius: 12px;
    border: 1px solid var(--border);
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
}

.timeline-content-left h4 {
    margin: 0 0 6px 0;
    color: var(--text-1);
    font-size: 14px;
    font-weight: 700;
}

.timeline-content-left p {
    margin: 0;
    color: var(--text-2);
    font-size: 13px;
    line-height: 1.5;
}

.admin-tag {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    font-size: 11.5px;
    font-weight: 600;
    color: var(--text-3);
    background: var(--surface-2);
    padding: 4px 8px;
    border-radius: 6px;
    margin-top: 8px;
}

.badge-resi {
    background: var(--accent-dim);
    color: var(--accent);
    padding: 4px 10px;
    border-radius: 6px;
    font-size: 12px;
    font-weight: 700;
    font-family: var(--mono);
    border: 1px solid rgba(79, 70, 229, 0.1);
}
@endsection

@section('content')
<div class="page-header">
    <div class="page-header-left">
        <h1>Riwayat Pelacakan Paket</h1>
        <p>Cari detail pergerakan paket berdasarkan Nomor Resi / Nomer Pesanan</p>
    </div>
</div>

<form method="GET" action="{{ route('deliveries.history') }}" class="search-box">
    <input 
        type="text" 
        name="search" 
        class="form-input" 
        placeholder="Ketik Nomor Resi Ex: JNE123... atau #ORD-123..." 
        value="{{ request('search') }}"
        autofocus
    >
    <button type="submit" class="btn-primary">
        <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
        Lacak Paket
    </button>
    @if(request('search'))
        <a href="{{ route('deliveries.history') }}" style="display:flex;align-items:center;background:var(--surface-3);color:var(--text-2);border-radius:10px;padding:12px 24px;text-decoration:none;font-size:14px;font-weight:600;">Reset</a>
    @endif
</form>

<div class="timeline-container">
    @forelse($histories as $h)
        <div class="timeline-item">
            <div class="timeline-date">{{ $h->created_at->translatedFormat('d F Y, H:i:s') }}</div>
            <div class="timeline-content">
                <div class="timeline-content-left">
                    <h4>
                        @if($h->status === 'processing')
                            Dikemas
                        @elseif($h->status === 'shipping')
                            Dikirim
                        @elseif($h->status === 'completed')
                            Selesai
                        @elseif($h->status === 'cancelled')
                            Dibatalkan
                        @else
                            {{ ucfirst($h->status) }}
                        @endif
                    </h4>
                    
                    <p>Pesanan <strong>{{ $h->order->order_number }}</strong> ({{ $h->order->customer_name }}) mengalami perubahan status logistik internal.</p>
                    
                    @if($h->notes)
                        <div style="margin-top:8px; padding:10px; background:rgba(245,158,11,0.05); border-left:3px solid var(--yellow); font-size:12.5px; color:var(--text-2);">
                            <em>"{{ $h->notes }}"</em>
                        </div>
                    @endif

                    <div class="admin-tag">
                        <svg viewBox="0 0 24 24" width="12" height="12" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="7" r="4"/><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/></svg>
                        Diperbarui oleh: {{ $h->admin ? $h->admin->name : 'Sistem Otomatis' }}
                    </div>
                </div>
                
                @if($h->order->tracking_number)
                <div class="timeline-content-right">
                    <span class="badge-resi" title="Penyedia: {{ $h->order->shipping_courier ?: 'Lokal' }}">
                        {{ $h->order->shipping_courier ? $h->order->shipping_courier . ' : ' : '' }}{{ $h->order->tracking_number }}
                    </span>
                </div>
                @endif
            </div>
        </div>
    @empty
        <div style="text-align:center; padding:40px; color:var(--text-3);">
            <svg viewBox="0 0 24 24" width="48" height="48" fill="none" stroke="currentColor" stroke-width="1.5" style="margin-bottom:16px; opacity:0.3;"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
            <p style="font-size:14px;">Belum ada riwayat pergerakan logistik yang terekam sistem.</p>
        </div>
    @endforelse

    @if($histories->hasPages())
        <div style="margin-top:30px;">
            {{ $histories->links('vendor.pagination.custom') }}
        </div>
    @endif
</div>
@endsection
