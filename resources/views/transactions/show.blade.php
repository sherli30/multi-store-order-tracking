@extends('layouts.app')

@section('title', 'Detail Transaksi — ' . $transaction->transaction_code)

@section('styles')
/* =============================================
   TRANSACTION SHOW — Detail Page
   ============================================= */

/* ── Breadcrumb ──────────────────────────────── */
.breadcrumb { display: flex; align-items: center; gap: 8px; margin-bottom: 20px; font-size: 12.5px; color: var(--text-3); }
.breadcrumb a { color: var(--text-3); text-decoration: none; transition: color 0.15s; }
.breadcrumb a:hover { color: var(--accent); }
.breadcrumb svg { width: 12px; height: 12px; color: var(--text-4); }
.breadcrumb .current { color: var(--text-1); font-weight: 600; }

/* ── Page Header ─────────────────────────────── */
.detail-header {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    margin-bottom: 28px;
    flex-wrap: wrap;
    gap: 16px;
}
.detail-header-left h1 {
    font-size: 20px;
    font-weight: 800;
    color: var(--text-1);
    letter-spacing: -0.03em;
    display: flex;
    align-items: center;
    gap: 10px;
    margin-bottom: 6px;
}
.detail-header-left h1 code {
    font-family: 'Courier New', monospace;
    font-size: 18px;
    background: var(--surface-2);
    padding: 2px 10px;
    border-radius: 7px;
    color: var(--text-1);
}
.detail-header-left p { font-size: 13px; color: var(--text-3); }
.detail-header-actions { display: flex; gap: 10px; align-items: center; }

/* ── Status Badge (large) ────────────────────── */
.status-large {
    display: inline-flex;
    align-items: center;
    gap: 7px;
    font-size: 13px;
    font-weight: 700;
    padding: 7px 16px;
    border-radius: 24px;
}
.status-large::before { content: ''; width: 8px; height: 8px; border-radius: 50%; flex-shrink: 0; }
.status-large.pending { background: var(--amber-dim); color: var(--amber); border: 1.5px solid rgba(245, 158, 11, 0.3); }
.status-large.pending::before { background: var(--amber); animation: pulse-dot 1.5s ease-in-out infinite; }
.status-large.paid { background: var(--green-dim); color: var(--green); border: 1.5px solid rgba(22, 163, 74, 0.3); }
.status-large.paid::before { background: var(--green); }
.status-large.failed { background: var(--surface-2); color: var(--text-2); border: 1.5px solid var(--border); }
.status-large.failed::before { background: var(--text-4); }
.status-large.refund { background: var(--red-dim); color: var(--red); border: 1.5px solid rgba(220, 38, 38, 0.3); }
.status-large.refund::before { background: var(--red); }

@keyframes pulse-dot {
    0%, 100% { box-shadow: 0 0 0 2px rgba(245,158,11,0.2); }
    50% { box-shadow: 0 0 0 5px rgba(245,158,11,0.08); }
}

/* ── Layout Grid ─────────────────────────────── */
.detail-grid {
    display: grid;
    grid-template-columns: 1fr 340px;
    gap: 20px;
    align-items: start;
}
@media (max-width: 960px) { .detail-grid { grid-template-columns: 1fr; } }

/* ── Card Base ───────────────────────────────── */
.detail-card {
    background: var(--panel);
    border: 1px solid var(--border);
    border-radius: 14px;
    overflow: hidden;
    box-shadow: var(--shadow-sm);
    animation: rise 0.3s ease both;
}
.detail-card + .detail-card { margin-top: 20px; }

.card-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 16px 22px;
    border-bottom: 1px solid var(--border);
    background: var(--surface);
}
.card-header-left { display: flex; align-items: center; gap: 10px; }
.card-header-icon { width: 32px; height: 32px; border-radius: 8px; display: flex; align-items: center; justify-content: center; }
.card-header-icon svg { width: 15px; height: 15px; }
.card-header-icon.blue   { background: rgba(59, 130, 246, 0.1); color: #3b82f6; }
.card-header-icon.green  { background: var(--green-dim); color: var(--green); }
.card-header-icon.amber  { background: var(--amber-dim); color: var(--amber); }
.card-header-icon.purple { background: rgba(139, 92, 246, 0.1); color: #8b5cf6; }
.card-header-icon.gray   { background: var(--surface-2); color: var(--text-3); }
.card-title { font-size: 13.5px; font-weight: 700; color: var(--text-1); }
.card-subtitle { font-size: 11.5px; color: var(--text-3); margin-top: 1px; }
.card-body { padding: 20px 22px; }

/* ── Info Grid (label-value pairs) ──────────────── */
.info-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px 28px; }
.info-grid.cols-1 { grid-template-columns: 1fr; }
.info-item {}
.info-label { font-size: 11px; font-weight: 700; color: var(--text-3); text-transform: uppercase; letter-spacing: 0.07em; margin-bottom: 5px; }
.info-value { font-size: 13.5px; font-weight: 600; color: var(--text-1); }
.info-value.mono { font-family: 'Courier New', monospace; font-size: 13px; }
.info-value.muted { color: var(--text-3); font-weight: 500; font-style: italic; }
.info-value.large { font-size: 20px; font-weight: 800; letter-spacing: -0.03em; }
.info-value.green { color: var(--green); }
.info-divider { height: 1px; background: var(--border); margin: 18px 0; }

/* ── Timeline ───────────────────────────────── */
.timeline { display: flex; flex-direction: column; gap: 0; }
.timeline-item { display: flex; gap: 14px; position: relative; }
.timeline-item:not(:last-child)::after {
    content: '';
    position: absolute;
    left: 15px;
    top: 32px;
    bottom: -8px;
    width: 2px;
    background: var(--border);
}
.timeline-dot {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    margin-top: 2px;
    z-index: 1;
}
.timeline-dot svg { width: 14px; height: 14px; }
.timeline-dot.done  { background: var(--green-dim); color: var(--green); border: 2px solid rgba(22,163,74,0.2); }
.timeline-dot.active { background: var(--amber-dim); color: var(--amber); border: 2px solid rgba(245,158,11,0.25); }
.timeline-dot.wait  { background: var(--surface-2); color: var(--text-4); border: 2px solid var(--border); }
.timeline-dot.fail  { background: var(--red-dim); color: var(--red); border: 2px solid rgba(220,38,38,0.2); }
.timeline-content { flex: 1; padding-bottom: 20px; }
.timeline-title { font-size: 13px; font-weight: 700; color: var(--text-1); margin-bottom: 3px; }
.timeline-date  { font-size: 11.5px; color: var(--text-3); margin-bottom: 4px; }
.timeline-note  { font-size: 12px; color: var(--text-2); background: var(--surface-2); border-radius: 7px; padding: 7px 10px; margin-top: 5px; }
.timeline-item:last-child .timeline-content { padding-bottom: 0; }

/* ── Action Panel (sidebar) ──────────────────── */
.action-panel { display: flex; flex-direction: column; gap: 12px; }
.action-btn {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    width: 100%;
    padding: 11px 18px;
    border-radius: 10px;
    font-family: var(--font);
    font-size: 13.5px;
    font-weight: 700;
    cursor: pointer;
    transition: all 0.15s;
    border: none;
    text-decoration: none;
    text-align: center;
}
.action-btn svg { width: 16px; height: 16px; }
.action-btn.primary { background: var(--accent); color: #fff; box-shadow: 0 2px 10px color-mix(in srgb, var(--accent) 30%, transparent); }
.action-btn.primary:hover { opacity: 0.9; transform: translateY(-1px); }
.action-btn.danger { background: var(--red); color: #fff; box-shadow: 0 2px 10px rgba(220,38,38,0.25); }
.action-btn.danger:hover { opacity: 0.9; transform: translateY(-1px); }
.action-btn.outline { background: transparent; color: var(--text-2); border: 1.5px solid var(--border); }
.action-btn.outline:hover { border-color: var(--border-2); color: var(--text-1); background: var(--surface); }
.action-section-title { font-size: 11px; font-weight: 700; color: var(--text-3); text-transform: uppercase; letter-spacing: 0.07em; margin-bottom: 4px; margin-top: 4px; }
.action-divider { height: 1px; background: var(--border); margin: 4px 0; }

/* ── Notes Box ───────────────────────────────── */
.notes-box { background: var(--surface-2); border-radius: 9px; padding: 13px 15px; font-size: 13px; color: var(--text-2); line-height: 1.6; border-left: 3px solid var(--border-2); }

/* ── Alert Flash ─────────────────────────────── */
.alert-success {
    display: flex; align-items: center; gap: 11px;
    background: var(--green-dim);
    border: 1px solid rgba(22, 163, 74, 0.25);
    border-radius: 11px;
    padding: 13px 18px;
    font-size: 13px; color: var(--green); font-weight: 600;
    margin-bottom: 22px;
    animation: rise 0.3s ease both;
}
.alert-success svg { width: 18px; height: 18px; flex-shrink: 0; }

/* ── Btn Back ────────────────────────────────── */
.btn-back { display: inline-flex; align-items: center; gap: 7px; padding: 8px 16px; border: 1px solid var(--border); border-radius: 9px; font-family: var(--font); font-size: 13px; font-weight: 600; color: var(--text-2); background: var(--panel); cursor: pointer; text-decoration: none; transition: all 0.15s; }
.btn-back:hover { border-color: var(--border-2); color: var(--text-1); }
.btn-back svg { width: 14px; height: 14px; }

/* ── Modal (same as index) ───────────────────── */
.modal-overlay { position: fixed; inset: 0; background: rgba(15, 23, 42, 0.5); backdrop-filter: blur(6px); z-index: 300; display: flex; align-items: center; justify-content: center; opacity: 0; pointer-events: none; transition: opacity 0.2s; }
.modal-overlay.open { opacity: 1; pointer-events: auto; }
.modal-box { background: var(--panel); border-radius: 18px; width: 440px; max-width: 92vw; box-shadow: 0 25px 60px rgba(0,0,0,0.2), 0 0 0 1px var(--border); transform: scale(0.94) translateY(8px); transition: transform 0.22s cubic-bezier(0.34,1.56,0.64,1), opacity 0.2s; opacity: 0; overflow: hidden; }
.modal-overlay.open .modal-box { transform: scale(1) translateY(0); opacity: 1; }
.modal-header { padding: 22px 24px 0; }
.modal-icon { width: 42px; height: 42px; border-radius: 11px; display: flex; align-items: center; justify-content: center; margin-bottom: 14px; }
.modal-icon svg { width: 20px; height: 20px; }
.modal-icon.success { background: var(--green-dim); color: var(--green); }
.modal-icon.danger  { background: var(--red-dim); color: var(--red); }
.modal-icon.warning { background: var(--amber-dim); color: var(--amber); }
.modal-title { font-size: 16px; font-weight: 800; color: var(--text-1); margin-bottom: 6px; }
.modal-desc  { font-size: 13px; color: var(--text-2); line-height: 1.6; }
.modal-alert { display: flex; align-items: flex-start; gap: 10px; border-radius: 9px; padding: 11px 14px; margin-top: 14px; font-size: 12.5px; font-weight: 600; line-height: 1.5; }
.modal-alert.info { background: color-mix(in srgb, var(--accent) 8%, transparent); color: var(--accent); border: 1px solid color-mix(in srgb, var(--accent) 20%, transparent); }
.modal-alert.warn { background: var(--red-dim); color: var(--red); border: 1px solid rgba(220,38,38,0.2); }
.modal-alert svg  { width: 14px; height: 14px; flex-shrink: 0; margin-top: 1px; }
.modal-body  { padding: 18px 24px; }
.form-label  { font-size: 12px; font-weight: 700; color: var(--text-2); margin-bottom: 7px; display: block; text-transform: uppercase; letter-spacing: 0.04em; }
.form-textarea { width: 100%; padding: 10px 13px; border: 1px solid var(--border); border-radius: 9px; font-family: var(--font); font-size: 13px; color: var(--text-1); background: var(--surface); outline: none; resize: vertical; min-height: 80px; transition: border-color 0.15s, box-shadow 0.15s; box-sizing: border-box; }
.form-textarea:focus { border-color: var(--accent); box-shadow: 0 0 0 3px var(--accent-glow); }
.form-textarea::placeholder { color: var(--text-4); }
.modal-footer { padding: 0 24px 22px; display: flex; gap: 10px; justify-content: flex-end; }
.btn-modal { display: inline-flex; align-items: center; gap: 7px; border: none; border-radius: 9px; padding: 9px 18px; font-family: var(--font); font-weight: 700; font-size: 13px; cursor: pointer; transition: all 0.15s; }
.btn-modal svg { width: 14px; height: 14px; }
.btn-modal.outline    { background: transparent; color: var(--text-2); border: 1px solid var(--border); }
.btn-modal.outline:hover { border-color: var(--border-2); color: var(--text-1); }
.btn-modal.primary    { background: var(--accent); color: #fff; box-shadow: 0 2px 8px color-mix(in srgb, var(--accent) 35%, transparent); }
.btn-modal.primary:hover { opacity: 0.9; transform: translateY(-1px); }
.btn-modal.danger-btn { background: var(--red); color: #fff; box-shadow: 0 2px 8px rgba(220,38,38,0.3); }
.btn-modal.danger-btn:hover { opacity: 0.9; transform: translateY(-1px); }
.modal-divider { height: 1px; background: var(--border); margin: 0 24px; }
@endsection

@section('content')

{{-- Breadcrumb --}}
<div class="breadcrumb">
    <a href="{{ route('dashboard') }}">Dashboard</a>
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"/></svg>
    <a href="{{ route('transactions.index') }}">Manajemen Transaksi</a>
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"/></svg>
    <span class="current">{{ $transaction->transaction_code }}</span>
</div>

{{-- Flash --}}
@if(session('success'))
    <div class="alert-success" id="flash-success">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
            <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/>
        </svg>
        {{ session('success') }}
    </div>
@endif

{{-- Detail Header --}}
<div class="detail-header">
    <div class="detail-header-left">
        <h1>
            <code>{{ $transaction->transaction_code }}</code>
        </h1>
        <p>
            Dibuat {{ $transaction->created_at->diffForHumans() }} · {{ $transaction->created_at->format('d M Y, H:i') }} WIB
        </p>
    </div>
    <div class="detail-header-actions">
        @php
            $lbls = ['pending' => 'Menunggu Konfirmasi', 'paid' => 'Lunas', 'failed' => 'Gagal', 'refund' => 'Dikembalikan (Refund)'];
        @endphp
        <span class="status-large {{ $transaction->status }}">
            {{ $lbls[$transaction->status] ?? $transaction->status }}
        </span>
        <a href="{{ route('transactions.index') }}" class="btn-back">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/>
            </svg>
            Kembali
        </a>
    </div>
</div>

{{-- Main Grid --}}
<div class="detail-grid">

    {{-- ===== LEFT COLUMN ===== --}}
    <div>

        {{-- Card: Informasi Transaksi --}}
        <div class="detail-card">
            <div class="card-header">
                <div class="card-header-left">
                    <div class="card-header-icon blue">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="1" y="4" width="22" height="16" rx="2" ry="2"/><line x1="1" y1="10" x2="23" y2="10"/>
                        </svg>
                    </div>
                    <div>
                        <div class="card-title">Informasi Transaksi</div>
                        <div class="card-subtitle">Data pembayaran lengkap</div>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="info-grid">
                    <div class="info-item">
                        <div class="info-label">ID Transaksi</div>
                        <div class="info-value mono">{{ $transaction->transaction_code }}</div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Metode Pembayaran</div>
                        <div class="info-value">{{ $transaction->payment_method ?? '—' }}</div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Total Pembayaran</div>
                        <div class="info-value large green">Rp {{ number_format($transaction->amount, 0, ',', '.') }}</div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Status</div>
                        <div class="info-value">
                            <span class="status-large {{ $transaction->status }}" style="font-size:12px; padding:4px 12px;">
                                {{ $lbls[$transaction->status] ?? $transaction->status }}
                            </span>
                        </div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Tanggal Transaksi Dibuat</div>
                        <div class="info-value">{{ $transaction->created_at->format('d M Y, H:i') }} WIB</div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Tanggal Dikonfirmasi</div>
                        <div class="info-value">
                            @if($transaction->payment_date)
                                {{ $transaction->payment_date->format('d M Y, H:i') }} WIB
                            @else
                                <span class="muted">Belum dikonfirmasi</span>
                            @endif
                        </div>
                    </div>
                </div>

                @if($transaction->notes)
                    <div class="info-divider"></div>
                    <div class="info-label" style="margin-bottom:8px;">Catatan</div>
                    <div class="notes-box">{{ $transaction->notes }}</div>
                @endif
            </div>
        </div>

        {{-- Card: Informasi Pesanan --}}
        @if($transaction->order)
        <div class="detail-card">
            <div class="card-header">
                <div class="card-header-left">
                    <div class="card-header-icon purple">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/><path d="M16 10a4 4 0 0 1-8 0"/>
                        </svg>
                    </div>
                    <div>
                        <div class="card-title">Pesanan Terkait</div>
                        <div class="card-subtitle">Detail order yang dihubungkan ke transaksi ini</div>
                    </div>
                </div>
                <a href="{{ route('orders.show', $transaction->order) }}" class="btn-back" style="font-size:12px; padding:6px 12px;">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width:12px;height:12px;">
                        <path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/><polyline points="15 3 21 3 21 9"/><line x1="10" y1="14" x2="21" y2="3"/>
                    </svg>
                    Buka Pesanan
                </a>
            </div>
            <div class="card-body">
                <div class="info-grid">
                    <div class="info-item">
                        <div class="info-label">Nomor Pesanan</div>
                        <div class="info-value mono">{{ $transaction->order->order_number }}</div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Status Pesanan</div>
                        <div class="info-value">{{ ucfirst($transaction->order->status ?? '—') }}</div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Nama Customer</div>
                        <div class="info-value">{{ $transaction->order->customer_name ?? '—' }}</div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">No. Telepon</div>
                        <div class="info-value">{{ $transaction->order->customer_phone ?? '—' }}</div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Toko</div>
                        <div class="info-value">{{ $transaction->order->store->name ?? '—' }}</div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Tanggal Pesanan</div>
                        <div class="info-value">{{ $transaction->order->created_at->format('d M Y, H:i') }}</div>
                    </div>
                </div>

                @if($transaction->order->shipping_address)
                    <div class="info-divider"></div>
                    <div class="info-label" style="margin-bottom:8px;">Alamat Pengiriman</div>
                    <div class="notes-box">{{ $transaction->order->shipping_address }}</div>
                @endif
            </div>
        </div>
        @else
        <div class="detail-card">
            <div class="card-body" style="text-align:center; padding: 40px 22px;">
                <div style="font-size:30px; margin-bottom:10px;">📦</div>
                <div style="font-size:14px; font-weight:700; color:var(--text-2); margin-bottom:5px;">Pesanan Tidak Ditemukan</div>
                <div style="font-size:12.5px; color:var(--text-3);">Pesanan yang dihubungkan ke transaksi ini telah dihapus dari sistem.</div>
            </div>
        </div>
        @endif

        {{-- Card: Timeline Riwayat --}}
        <div class="detail-card">
            <div class="card-header">
                <div class="card-header-left">
                    <div class="card-header-icon gray">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/>
                        </svg>
                    </div>
                    <div>
                        <div class="card-title">Riwayat & Timeline</div>
                        <div class="card-subtitle">Jejak perubahan status transaksi</div>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="timeline">
                    {{-- Step 1: Transaksi Dibuat --}}
                    <div class="timeline-item">
                        <div class="timeline-dot done">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                <polyline points="20 6 9 17 4 12"/>
                            </svg>
                        </div>
                        <div class="timeline-content">
                            <div class="timeline-title">Transaksi Dibuat</div>
                            <div class="timeline-date">{{ $transaction->created_at->format('d M Y, H:i') }} WIB</div>
                            <div class="timeline-note">Pelanggan mengajukan pembayaran via {{ $transaction->payment_method ?? 'N/A' }}.</div>
                        </div>
                    </div>

                    {{-- Step 2: Menunggu / Konfirmasi --}}
                    @if(in_array($transaction->status, ['pending', 'paid', 'failed', 'refund']))
                    <div class="timeline-item">
                        <div class="timeline-dot {{ $transaction->status === 'pending' ? 'active' : 'done' }}">
                            @if($transaction->status === 'pending')
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/>
                                </svg>
                            @else
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                    <polyline points="20 6 9 17 4 12"/>
                                </svg>
                            @endif
                        </div>
                        <div class="timeline-content">
                            <div class="timeline-title">
                                @if($transaction->status === 'pending') Menunggu Konfirmasi Admin
                                @else Dikonfirmasi Admin
                                @endif
                            </div>
                            <div class="timeline-date">
                                @if($transaction->payment_date)
                                    {{ $transaction->payment_date->format('d M Y, H:i') }} WIB
                                @else
                                    Menunggu verifikasi...
                                @endif
                            </div>
                        </div>
                    </div>
                    @endif

                    {{-- Step 3: Hasil Akhir --}}
                    @if($transaction->status === 'paid')
                    <div class="timeline-item">
                        <div class="timeline-dot done">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/>
                            </svg>
                        </div>
                        <div class="timeline-content">
                            <div class="timeline-title">Pembayaran Lunas</div>
                            <div class="timeline-date">{{ $transaction->payment_date?->format('d M Y, H:i') }} WIB</div>
                            <div class="timeline-note">Dana diterima. Pesanan otomatis masuk ke status <strong>Dalam Pengemasan</strong>.</div>
                        </div>
                    </div>
                    @elseif($transaction->status === 'failed')
                    <div class="timeline-item">
                        <div class="timeline-dot fail">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                <line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/>
                            </svg>
                        </div>
                        <div class="timeline-content">
                            <div class="timeline-title">Pembayaran Ditolak / Gagal</div>
                            <div class="timeline-date">{{ $transaction->updated_at->format('d M Y, H:i') }} WIB</div>
                            @if($transaction->notes)
                                <div class="timeline-note">{{ $transaction->notes }}</div>
                            @endif
                        </div>
                    </div>
                    @elseif($transaction->status === 'refund')
                    <div class="timeline-item">
                        <div class="timeline-dot fail">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <polyline points="1 4 1 10 7 10"/><path d="M3.51 15a9 9 0 1 0 .49-4"/>
                            </svg>
                        </div>
                        <div class="timeline-content">
                            <div class="timeline-title">Refund Diproses</div>
                            <div class="timeline-date">{{ $transaction->updated_at->format('d M Y, H:i') }} WIB</div>
                            @if($transaction->notes)
                                <div class="timeline-note">{{ $transaction->notes }}</div>
                            @endif
                        </div>
                    </div>
                    @elseif($transaction->status === 'pending')
                    <div class="timeline-item">
                        <div class="timeline-dot wait">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/>
                            </svg>
                        </div>
                        <div class="timeline-content">
                            <div class="timeline-title" style="color:var(--text-3);">Menunggu Keputusan Admin</div>
                            <div class="timeline-date">—</div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>

    </div>
    {{-- ===== END LEFT COLUMN ===== --}}

    {{-- ===== RIGHT COLUMN (Sidebar) ===== --}}
    <div>

        {{-- Aksi Konfirmasi --}}
        <div class="detail-card">
            <div class="card-header">
                <div class="card-header-left">
                    <div class="card-header-icon {{ $transaction->status === 'pending' ? 'amber' : 'green' }}">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                        </svg>
                    </div>
                    <div>
                        <div class="card-title">Aksi Konfirmasi</div>
                        <div class="card-subtitle">Ubah status transaksi</div>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="action-panel">
                    @if($transaction->status === 'pending')
                        <div class="action-section-title">Konfirmasi Pembayaran</div>
                        <button class="action-btn primary"
                            onclick="openAction('paid', {{ $transaction->id }}, '{{ $transaction->transaction_code }}')">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                <polyline points="20 6 9 17 4 12"/>
                            </svg>
                            Tandai Lunas
                        </button>
                        <div class="action-divider"></div>
                        <div class="action-section-title">Tolak Pembayaran</div>
                        <button class="action-btn danger"
                            onclick="openAction('failed', {{ $transaction->id }}, '{{ $transaction->transaction_code }}')">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                <line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/>
                            </svg>
                            Tolak Pembayaran
                        </button>

                    @elseif($transaction->status === 'paid' && $transaction->order && !in_array($transaction->order->status, ['shipping', 'completed']))
                        <div class="action-section-title">Proses Refund</div>
                        <button class="action-btn danger"
                            onclick="openAction('refund', {{ $transaction->id }}, '{{ $transaction->transaction_code }}')">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <polyline points="1 4 1 10 7 10"/><path d="M3.51 15a9 9 0 1 0 .49-4"/>
                            </svg>
                            Proses Refund
                        </button>

                    @else
                        <div style="text-align:center; padding: 12px 0;">
                            <div style="font-size:28px; margin-bottom:8px;">
                                @if($transaction->status === 'paid') ✅
                                @elseif($transaction->status === 'failed') ❌
                                @else 🔄
                                @endif
                            </div>
                            <div style="font-size:13px; font-weight:700; color:var(--text-1); margin-bottom:4px;">
                                Transaksi Telah Diproses
                            </div>
                            <div style="font-size:12px; color:var(--text-3);">
                                Status: <strong>{{ $lbls[$transaction->status] ?? $transaction->status }}</strong>
                            </div>
                        </div>
                    @endif

                    <div class="action-divider" style="margin-top:8px;"></div>
                    <a href="{{ route('transactions.index') }}" class="action-btn outline">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/>
                        </svg>
                        Kembali ke Daftar
                    </a>
                </div>
            </div>
        </div>

        {{-- Card: Ringkasan Pembayaran --}}
        <div class="detail-card">
            <div class="card-header">
                <div class="card-header-left">
                    <div class="card-header-icon green">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/>
                        </svg>
                    </div>
                    <div>
                        <div class="card-title">Ringkasan Pembayaran</div>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="info-grid cols-1" style="gap:14px;">
                    <div class="info-item" style="display:flex; justify-content:space-between; align-items:center;">
                        <span style="font-size:13px; color:var(--text-2);">Subtotal</span>
                        <span style="font-size:13px; font-weight:600; color:var(--text-1);">Rp {{ number_format($transaction->amount, 0, ',', '.') }}</span>
                    </div>
                    <div style="height:1px; background:var(--border);"></div>
                    <div class="info-item" style="display:flex; justify-content:space-between; align-items:center;">
                        <span style="font-size:14px; font-weight:700; color:var(--text-1);">Total Dibayar</span>
                        <span style="font-size:16px; font-weight:800; color:var(--green); letter-spacing:-0.02em;">Rp {{ number_format($transaction->amount, 0, ',', '.') }}</span>
                    </div>
                    <div style="height:1px; background:var(--border);"></div>
                    <div class="info-item">
                        <span style="font-size:11.5px; color:var(--text-3); display:block; text-align:center;">Metode: {{ $transaction->payment_method ?? 'N/A' }}</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Card: Meta Info --}}
        <div class="detail-card">
            <div class="card-header">
                <div class="card-header-left">
                    <div class="card-header-icon gray">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/>
                        </svg>
                    </div>
                    <div>
                        <div class="card-title">Informasi Sistem</div>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="info-grid cols-1" style="gap:14px;">
                    <div class="info-item">
                        <div class="info-label">ID Rekam</div>
                        <div class="info-value mono" style="font-size:12px;">#{{ $transaction->id }}</div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Dibuat Pada</div>
                        <div class="info-value" style="font-size:12.5px;">{{ $transaction->created_at->format('d/m/Y H:i:s') }}</div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Diperbarui Pada</div>
                        <div class="info-value" style="font-size:12.5px;">{{ $transaction->updated_at->format('d/m/Y H:i:s') }}</div>
                    </div>
                </div>
            </div>
        </div>

    </div>
    {{-- ===== END RIGHT COLUMN ===== --}}

</div>

{{-- ========== Action Confirmation Modal ========== --}}
<div class="modal-overlay" id="actionModal">
    <div class="modal-box">
        <div class="modal-header">
            <div class="modal-icon" id="modalIcon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/>
                </svg>
            </div>
            <div class="modal-title" id="actionTitle">Konfirmasi Transaksi</div>
            <div class="modal-desc" id="actionDesc"></div>
            <div class="modal-alert" id="modalAlert" style="display:none;">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/>
                </svg>
                <span id="modalAlertText"></span>
            </div>
        </div>

        <form id="actionForm" method="POST" action="">
            @csrf
            @method('PATCH')
            <input type="hidden" name="status" id="actionStatus">
            <input type="hidden" name="redirect" value="show">

            <div id="notesContainer" class="modal-body" style="display:none; padding-top:16px;">
                <div style="height:1px; background:var(--border); margin-bottom:16px;"></div>
                <label class="form-label">Catatan (Opsional)</label>
                <textarea name="notes" class="form-textarea"
                    placeholder="Tulis alasan penolakan atau catatan refund..."></textarea>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn-modal outline" onclick="closeActionModal()">Batalkan</button>
                <button type="submit" class="btn-modal primary" id="actionSubmitBtn">Lanjutkan</button>
            </div>
        </form>
    </div>
</div>

@endsection

@push('scripts')
<script>
    // Flash auto-hide
    setTimeout(() => {
        const flash = document.getElementById('flash-success');
        if (flash) {
            flash.style.transition = 'opacity 0.5s';
            flash.style.opacity = '0';
            setTimeout(() => flash.remove(), 500);
        }
    }, 4500);

    function openAction(type, trxId, trxCode) {
        const form      = document.getElementById('actionForm');
        const icon      = document.getElementById('modalIcon');
        const title     = document.getElementById('actionTitle');
        const desc      = document.getElementById('actionDesc');
        const alert     = document.getElementById('modalAlert');
        const alertText = document.getElementById('modalAlertText');
        const notes     = document.getElementById('notesContainer');
        const btn       = document.getElementById('actionSubmitBtn');

        form.action = `/transactions/${trxId}/status`;
        document.getElementById('actionStatus').value = type;
        notes.style.display = (type === 'failed' || type === 'refund') ? 'block' : 'none';

        if (type === 'paid') {
            icon.className      = 'modal-icon success';
            title.innerText     = 'Konfirmasi Pembayaran Lunas';
            desc.innerHTML      = `ID Transaksi: <strong>${trxCode}</strong><br>Tandai pembayaran ini sebagai <strong>lunas</strong>.`;
            alert.className     = 'modal-alert info';
            alert.style.display = 'flex';
            alertText.innerHTML = 'Auto-Sync: Status pesanan terkait akan diperbarui otomatis menjadi <strong>Dalam Pengemasan (Processing)</strong>.';
            btn.className       = 'btn-modal primary';
            btn.innerHTML       = `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="width:14px;height:14px;"><polyline points="20 6 9 17 4 12"/></svg> Tandai Lunas`;
        } else if (type === 'failed') {
            icon.className      = 'modal-icon danger';
            title.innerText     = 'Tolak Transaksi';
            desc.innerHTML      = `ID Transaksi: <strong>${trxCode}</strong><br>Tandai pembayaran ini sebagai <strong>gagal/tidak valid</strong>.`;
            alert.className     = 'modal-alert warn';
            alert.style.display = 'flex';
            alertText.innerHTML = 'Auto-Sync: Pesanan terkait akan otomatis <strong>dibatalkan (Cancelled)</strong>.';
            btn.className       = 'btn-modal danger-btn';
            btn.innerHTML       = `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="width:14px;height:14px;"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg> Tolak Pembayaran`;
        } else if (type === 'refund') {
            icon.className      = 'modal-icon warning';
            title.innerText     = 'Proses Refund';
            desc.innerHTML      = `ID Transaksi: <strong>${trxCode}</strong><br>Ubah status menjadi <strong>Refund</strong>. Aksi ini tidak melakukan transfer dana secara nyata.`;
            alert.className     = 'modal-alert warn';
            alert.style.display = 'flex';
            alertText.innerHTML = 'Auto-Sync: Pesanan terkait akan otomatis <strong>dibatalkan</strong>.';
            btn.className       = 'btn-modal danger-btn';
            btn.innerHTML       = `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width:14px;height:14px;"><polyline points="1 4 1 10 7 10"/><path d="M3.51 15a9 9 0 1 0 .49-4"/></svg> Konfirmasi Refund`;
        }

        document.getElementById('actionModal').classList.add('open');
    }

    function closeActionModal() {
        document.getElementById('actionModal').classList.remove('open');
    }

    document.getElementById('actionModal').addEventListener('click', function(e) {
        if (e.target === this) closeActionModal();
    });
    document.addEventListener('keydown', e => { if (e.key === 'Escape') closeActionModal(); });
</script>
@endpush
