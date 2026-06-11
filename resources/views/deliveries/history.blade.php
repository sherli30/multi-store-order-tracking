@extends('layouts.app')

@section('title', 'Riwayat Tracking Pengiriman')

@section('styles')
/* ── Page Header ─────────────────────────────── */
.page-header {
display: flex; align-items: flex-start; justify-content: space-between;
margin-bottom: 28px; flex-wrap: wrap; gap: 16px;
}
.page-header-left h1 {
font-size: 22px; font-weight: 800; letter-spacing: -0.04em; color: var(--text-1);
margin-bottom: 5px; display: flex; align-items: center; gap: 10px;
}
.page-icon {
width: 36px; height: 36px;
background: linear-gradient(135deg, var(--accent), color-mix(in srgb, var(--accent) 70%, #7c3aed));
border-radius: 10px; display: flex; align-items: center; justify-content: center; flex-shrink: 0;
}
.page-icon svg { width: 18px; height: 18px; color: #fff; }
.page-header-left p { font-size: 13px; color: var(--text-3); margin-left: 46px; }

/* ── Search Box ──────────────────────────────── */
.search-card {
background: var(--panel); border: 1px solid var(--border); border-radius: 14px;
padding: 20px 22px; box-shadow: var(--shadow-sm); margin-bottom: 20px;
}
.search-row {
display: flex; gap: 10px; align-items: center;
}
.search-input {
flex: 1; padding: 10px 14px; border: 1px solid var(--border); border-radius: 9px;
font-family: var(--font); font-size: 13px; color: var(--text-1); background: var(--surface);
outline: none; transition: border-color 0.15s, box-shadow 0.15s; box-sizing: border-box;
}
.search-input:focus { border-color: var(--accent); box-shadow: 0 0 0 3px var(--accent-glow); }
.search-input::placeholder { color: var(--text-4); }

.btn-primary {
display: inline-flex; align-items: center; gap: 7px;
background: var(--accent); color: #fff; border: none;
padding: 10px 18px; border-radius: 9px; font-family: var(--font);
font-weight: 700; font-size: 13px; cursor: pointer; text-decoration: none;
transition: all 0.15s; white-space: nowrap;
box-shadow: 0 2px 8px color-mix(in srgb, var(--accent) 30%, transparent);
}
.btn-primary:hover { opacity: 0.9; transform: translateY(-1px); }
.btn-primary svg { width: 14px; height: 14px; }

.btn-reset {
display: inline-flex; align-items: center; gap: 7px;
background: var(--red-dim); color: var(--red);
border: 1px solid rgba(220,38,38,0.2);
padding: 10px 16px; border-radius: 9px; font-family: var(--font);
font-weight: 700; font-size: 13px; cursor: pointer; text-decoration: none;
transition: all 0.15s; white-space: nowrap;
}
.btn-reset:hover { border-color: rgba(220,38,38,0.4); background: color-mix(in srgb, var(--red-dim) 80%, var(--red)); }
.btn-reset svg { width: 13px; height: 13px; }

/* ── Timeline Card ───────────────────────────── */
.timeline-card {
background: var(--panel); border: 1px solid var(--border); border-radius: 14px;
box-shadow: var(--shadow-sm); overflow: hidden;
}
.timeline-card-header {
padding: 16px 22px; border-bottom: 1px solid var(--border);
background: var(--surface); display: flex; align-items: center; gap: 8px;
}
.timeline-card-title { font-size: 13px; font-weight: 700; color: var(--text-1); }
.timeline-card-body { padding: 24px 22px; }

/* ── Timeline ────────────────────────────────── */
.timeline-wrap { position: relative; padding-left: 44px; }
.timeline-line {
position: absolute; left: 20px; top: 10px; bottom: 10px;
width: 2px; background: var(--border); z-index: 1;
}
.timeline-item { position: relative; margin-bottom: 24px; }
.timeline-item:last-child { margin-bottom: 0; }

.timeline-dot {
position: absolute; left: -34px; top: 3px;
width: 22px; height: 22px; border-radius: 50%;
background: var(--panel); border: 2px solid var(--border-2);
z-index: 2; display: flex; align-items: center; justify-content: center;
box-shadow: 0 0 0 3px var(--panel);
}
.timeline-dot.latest { border-color: var(--accent); }
.timeline-dot-inner { width: 8px; height: 8px; border-radius: 50%; background: var(--border-2); }
.timeline-dot.latest .timeline-dot-inner { background: var(--accent); }

.timeline-content {
background: var(--surface); border: 1px solid var(--border);
border-radius: 12px; padding: 14px 16px;
}

.timeline-meta {
display: flex; align-items: flex-start; justify-content: space-between;
gap: 10px; flex-wrap: wrap; margin-bottom: 8px;
}
.timeline-status { font-size: 13px; font-weight: 700; color: var(--text-1); }
.timeline-date {
font-size: 11px; font-weight: 600; color: var(--text-4);
background: var(--surface-2); padding: 2px 8px; border-radius: 5px; white-space: nowrap; flex-shrink: 0;
}
.timeline-order {
font-size: 12.5px; color: var(--text-2); line-height: 1.6; margin-bottom: 8px;
}
.timeline-notes {
margin: 8px 0; padding: 10px 12px;
background: color-mix(in srgb, var(--amber) 6%, var(--panel));
border-left: 3px solid var(--amber); border-radius: 0 8px 8px 0;
font-size: 12px; color: var(--text-2); font-style: italic; line-height: 1.5;
}
.timeline-resi {
display: inline-flex; align-items: center; gap: 6px;
font-family: var(--mono); font-size: 11.5px; font-weight: 700;
color: var(--accent); background: var(--accent-dim);
padding: 3px 9px; border-radius: 5px;
border: 1px solid color-mix(in srgb, var(--accent) 20%, transparent);
margin-bottom: 8px;
}
.timeline-actor {
display: inline-flex; align-items: center; gap: 6px;
font-size: 11px; color: var(--text-4); font-weight: 600;
}
.timeline-actor-avatar {
width: 18px; height: 18px; border-radius: 50%;
background: var(--surface-2); border: 1px solid var(--border);
display: flex; align-items: center; justify-content: center;
font-size: 9px; font-weight: 800; color: var(--text-3); flex-shrink: 0;
}

/* ── Empty State ─────────────────────────────── */
.empty-state {
text-align: center; padding: 60px 20px; display: flex;
flex-direction: column; align-items: center; justify-content: center;
}
.empty-icon {
width: 72px; height: 72px;
background: color-mix(in srgb, var(--accent) 8%, transparent);
border-radius: 20px; display: flex; align-items: center; justify-content: center;
border: 1px solid color-mix(in srgb, var(--accent) 12%, transparent);
margin-bottom: 16px;
}
.empty-title { font-size: 16px; font-weight: 800; color: var(--text-1); margin-bottom: 6px; }
.empty-desc { font-size: 13px; color: var(--text-3); }

/* ── Pagination ──────────────────────────────── */
.pagination-wrap { padding: 16px 22px; border-top: 1px solid var(--border); }
@endsection

@section('content')

<div class="page-header">
    <div class="page-header-left">
        <h1>
            <span class="page-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="10" />
                    <polyline points="12 6 12 12 16 14" />
                </svg>
            </span>
            Riwayat Tracking
        </h1>
        <p>Cari detail pergerakan paket berdasarkan nomor resi atau nomor pesanan.</p>
    </div>
</div>

{{-- Search --}}
<div class="search-card">
    <form method="GET" action="{{ route('deliveries.history') }}">
        <div class="search-row">
            <input
                type="text"
                name="search"
                class="search-input"
                placeholder="Ketik nomor resi (JNE123...) atau nomor pesanan (#ORD-123...)"
                value="{{ request('search') }}"
                autofocus>
            <button type="submit" class="btn-primary">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="11" cy="11" r="8" />
                    <line x1="21" y1="21" x2="16.65" y2="16.65" />
                </svg>
                Lacak Paket
            </button>
            @if(request('search'))
            <a href="{{ route('deliveries.history') }}" class="btn-reset">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="18" y1="6" x2="6" y2="18" />
                    <line x1="6" y1="6" x2="18" y2="18" />
                </svg>
                Reset
            </a>
            @endif
        </div>
    </form>
</div>

{{-- Timeline Card --}}
<div class="timeline-card">
    <div class="timeline-card-header">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="width:15px;height:15px;color:var(--text-3);">
            <circle cx="12" cy="12" r="10" />
            <polyline points="12 6 12 12 16 14" />
        </svg>
        <span class="timeline-card-title">
            Riwayat Aktivitas Logistik
            @if(request('search'))
            &mdash; <span style="color:var(--accent);font-weight:800;">{{ request('search') }}</span>
            @endif
        </span>
    </div>

    <div class="timeline-card-body">
        @if($histories->isNotEmpty())
        <div class="timeline-wrap">
            <div class="timeline-line"></div>

            @foreach($histories as $index => $h)
            @php $isLatest = $index === 0; @endphp
            <div class="timeline-item">
                <div class="timeline-dot {{ $isLatest ? 'latest' : '' }}">
                    <div class="timeline-dot-inner"></div>
                </div>

                <div class="timeline-content">
                    <div class="timeline-meta">
                        <div class="timeline-status">
                            {{ \App\Services\StatusService::getOrderLabel($h->status ?? '') }}
                        </div>
                        <div class="timeline-date">
                            {{ $h->created_at->translatedFormat('d F Y, H:i:s') }}
                        </div>
                    </div>

                    <div class="timeline-order">
                        Pesanan <strong>{{ $h->order->order_number }}</strong>
                        ({{ $h->order->customer_name }}) mengalami perubahan status logistik.
                    </div>

                    @if($h->order->tracking_number)
                    <div class="timeline-resi">
                        <svg viewBox="0 0 24 24" width="11" fill="none" stroke="currentColor" stroke-width="2.5">
                            <rect x="1" y="3" width="15" height="13" />
                            <polygon points="16 8 20 8 23 11 23 16 16 16 16 8" />
                            <circle cx="5.5" cy="18.5" r="2.5" />
                            <circle cx="18.5" cy="18.5" r="2.5" />
                        </svg>
                        {{ $h->order->shipping_courier ? strtoupper($h->order->shipping_courier) . ' : ' : '' }}{{ $h->order->tracking_number }}
                    </div>
                    @endif

                    @if($h->notes)
                    <div class="timeline-notes">"{{ $h->notes }}"</div>
                    @endif

                    <div class="timeline-actor">
                        <div class="timeline-actor-avatar">{{ substr($h->admin->name ?? 'S', 0, 1) }}</div>
                        {{ $h->admin ? $h->admin->name : 'Sistem Otomatis' }}
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        @else
        <div class="empty-state">
            <div class="empty-icon">
                <svg viewBox="0 0 24 24" width="32" height="32" fill="none" stroke="currentColor" stroke-width="1.5" style="color: var(--accent); opacity: 0.8;">
                    <circle cx="12" cy="12" r="10" />
                    <polyline points="12 6 12 12 16 14" />
                </svg>
            </div>
            <div class="empty-title">Tidak Ada Riwayat Ditemukan</div>
            <div class="empty-desc">
                @if(request('search'))
                Tidak ada riwayat yang cocok dengan pencarian <strong>"{{ request('search') }}"</strong>.
                @else
                Belum ada riwayat pergerakan logistik yang terekam di sistem.
                @endif
            </div>
        </div>
        @endif
    </div>

    @if($histories->hasPages())
    <div class="pagination-wrap">
        {{ $histories->links('vendor.pagination.custom') }}
    </div>
    @endif
</div>

@endsection
