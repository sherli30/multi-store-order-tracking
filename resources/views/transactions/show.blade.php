@extends('layouts.app')

@section('title', 'Detail Transaksi — ' . $transaction->transaction_code)

@section('styles')
<style>
/* =============================================
   TRANSACTION SHOW — Refined Detail Page
   ============================================= */

/* ── Root Extras ── */
:root {
    --card-radius: 18px;
    --section-gap: 24px;
}

/* ── Breadcrumb ── */
.breadcrumb {
    display: flex; align-items: center; gap: 8px;
    margin-bottom: 20px; font-size: 12.5px; color: var(--text-3);
}
.breadcrumb a { color: var(--text-3); text-decoration: none; transition: color 0.15s; }
.breadcrumb a:hover { color: var(--accent); }
.breadcrumb svg { width: 12px; height: 12px; color: var(--text-4); }
.breadcrumb .current { color: var(--text-1); font-weight: 600; }

/* ── Page Header ── */
.page-header {
    display: flex; align-items: flex-start;
    justify-content: space-between;
    margin-bottom: 20px; flex-wrap: wrap; gap: 16px;
}
.page-header-left { display: flex; align-items: center; gap: 14px; }
.page-icon {
    width: 44px; height: 44px; flex-shrink: 0;
    background: linear-gradient(135deg, var(--accent), #0ea5e9);
    border-radius: 12px;
    display: flex; align-items: center; justify-content: center;
    box-shadow: 0 8px 20px rgba(79,70,229,0.2);
}
.page-icon svg { width: 22px; height: 22px; color: #fff; }
.page-header-text h1 {
    font-size: 21px; font-weight: 900; letter-spacing: -0.04em;
    color: var(--text-1); margin: 0 0 4px 0;
    display: flex; align-items: center; gap: 10px;
}
.page-header-text h1 code {
    font-family: 'Courier New', monospace;
    font-size: 16px;
    background: var(--surface-2);
    padding: 2px 10px;
    border-radius: 7px;
    color: var(--text-2);
    font-weight: 700;
}
.page-header-text p { font-size: 12.5px; color: var(--text-3); margin: 0; }
.header-actions { display: flex; gap: 10px; align-items: center; flex-wrap: wrap; }

/* ── Meta Strip ── */
.meta-strip {
    display: flex; gap: 0; align-items: stretch;
    background: var(--panel); border: 1px solid var(--border);
    border-radius: 14px; overflow: hidden;
    margin-bottom: var(--section-gap);
    box-shadow: var(--shadow-sm);
}
.meta-chip {
    flex: 1; padding: 14px 20px;
    border-right: 1px solid var(--border);
    display: flex; flex-direction: column; gap: 4px;
    min-width: 0;
}
.meta-chip:last-child { border-right: none; }
.meta-chip-label {
    font-size: 10px; font-weight: 800; color: var(--text-4);
    text-transform: uppercase; letter-spacing: 0.06em;
}
.meta-chip-value {
    font-size: 13px; font-weight: 800; color: var(--text-1);
    white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
}

/* ── Status Badges ── */
.status-badge {
    display: inline-flex; align-items: center; gap: 6px;
    padding: 4px 12px; border-radius: 20px;
    font-size: 11px; font-weight: 800;
}
.status-badge::before {
    content: ''; width: 6px; height: 6px;
    border-radius: 50%; background: currentColor;
}
.status-pending { background: var(--amber-dim); color: var(--amber); }
.status-paid    { background: var(--green-dim); color: var(--green); }
.status-failed  { background: var(--surface-2); color: var(--text-2); }
.status-refund  { background: var(--red-dim);   color: var(--red); }

/* Animate pending dot */
.status-badge.status-pending::before {
    animation: pulse-dot 1.5s ease-in-out infinite;
}
@keyframes pulse-dot {
    0%, 100% { box-shadow: 0 0 0 2px rgba(245,158,11,0.2); }
    50%       { box-shadow: 0 0 0 5px rgba(245,158,11,0.08); }
}

/* ── Alert Banners ── */
.alert-banner {
    padding: 13px 18px; border-radius: 12px; margin-bottom: var(--section-gap);
    display: flex; align-items: center; gap: 12px;
    font-size: 13px; font-weight: 600;
}
.alert-banner svg { width: 18px; height: 18px; flex-shrink: 0; }
.alert-success { background: var(--green-dim); color: var(--green); border: 1px solid rgba(22,163,74,0.2); }

/* ── Main Layout Grid ── */
.detail-grid {
    display: grid;
    grid-template-columns: 1fr 330px;
    gap: var(--section-gap);
    align-items: start;
}
@media (max-width: 1060px) {
    .detail-grid { grid-template-columns: 1fr; }
    .sidebar-priority { order: -1; }
}
.detail-main { display: flex; flex-direction: column; gap: var(--section-gap); }
.detail-side { display: flex; flex-direction: column; gap: var(--section-gap); }

/* ── Cards ── */
.detail-card {
    background: var(--panel); border: 1px solid var(--border);
    border-radius: var(--card-radius); overflow: hidden;
    box-shadow: var(--shadow-sm);
}
.card-header {
    padding: 16px 22px; border-bottom: 1px solid var(--border);
    display: flex; align-items: center; justify-content: space-between;
    background: linear-gradient(to right, var(--surface), transparent);
}
.card-title {
    font-size: 13.5px; font-weight: 900; color: var(--text-1);
    display: flex; align-items: center; gap: 9px;
}
.card-title svg { width: 16px; height: 16px; color: var(--accent); }
.card-body { padding: 20px 22px; }

/* ── Info Rows (label / value pairs) ── */
.info-row {
    display: flex; justify-content: space-between; align-items: flex-start;
    padding: 9px 0; border-bottom: 1px dashed var(--border);
    gap: 12px;
}
.info-row:last-child { border-bottom: none; }
.info-label { font-size: 11.5px; font-weight: 700; color: var(--text-3); white-space: nowrap; }
.info-value { font-size: 13px; font-weight: 800; color: var(--text-1); text-align: right; }
.info-value.mono { font-family: 'Courier New', monospace; font-size: 12px; color: var(--text-2); }
.info-value.muted { color: var(--text-4); font-style: italic; font-weight: 600; }
.info-value.large { font-size: 20px; font-weight: 900; color: var(--accent); letter-spacing: -0.03em; }

/* ── Payment Summary Grid ── */
.payment-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(160px, 1fr));
    gap: 16px;
}
.payment-cell-label {
    font-size: 10px; font-weight: 800; color: var(--text-4);
    text-transform: uppercase; letter-spacing: 0.06em; margin-bottom: 5px;
}
.payment-cell-value { font-size: 13px; font-weight: 800; color: var(--text-1); }
.payment-cell-value.mono { font-family: 'Courier New', monospace; font-size: 12px; color: var(--text-2); }
.payment-cell-value.accent { color: var(--accent); }
.payment-cell-value.green  { color: var(--green); }
.payment-cell-value.amber  { color: var(--amber); }
.payment-cell-value.red    { color: var(--red); }

/* ── Timeline ── */
.timeline { display: flex; flex-direction: column; }
.timeline-item { display: flex; gap: 14px; position: relative; }
.timeline-item:not(:last-child)::after {
    content: ''; position: absolute;
    left: 15px; top: 32px; bottom: -8px;
    width: 2px; background: var(--border);
}
.timeline-dot {
    width: 32px; height: 32px; border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    flex-shrink: 0; margin-top: 2px; z-index: 1;
}
.timeline-dot svg { width: 14px; height: 14px; }
.timeline-dot.done   { background: var(--green-dim); color: var(--green); border: 2px solid rgba(22,163,74,0.2); }
.timeline-dot.active { background: var(--amber-dim); color: var(--amber); border: 2px solid rgba(245,158,11,0.25); }
.timeline-dot.wait   { background: var(--surface-2); color: var(--text-4); border: 2px solid var(--border); }
.timeline-dot.fail   { background: var(--red-dim);   color: var(--red);   border: 2px solid rgba(220,38,38,0.2); }
.timeline-content { flex: 1; padding-bottom: 22px; }
.timeline-item:last-child .timeline-content { padding-bottom: 0; }
.timeline-title { font-size: 13px; font-weight: 700; color: var(--text-1); margin-bottom: 3px; }
.timeline-date  { font-size: 11.5px; color: var(--text-3); margin-bottom: 4px; }
.timeline-note  {
    font-size: 12px; color: var(--text-2); line-height: 1.55;
    background: var(--surface-2); border-radius: 8px;
    padding: 8px 11px; margin-top: 6px;
    border-left: 3px solid var(--border-2);
}

/* ── Notes Box ── */
.notes-box {
    background: var(--surface); border: 1px solid var(--border);
    border-radius: 10px; padding: 13px 14px;
    font-size: 13px; color: var(--text-2); line-height: 1.65;
}

/* ── Action Panel (sidebar card) ── */
.action-panel { display: flex; flex-direction: column; gap: 10px; }
.action-section-title {
    font-size: 10px; font-weight: 800; color: var(--text-4);
    text-transform: uppercase; letter-spacing: 0.06em;
    margin-bottom: 2px; margin-top: 2px;
}
.action-divider { height: 1px; background: var(--border); margin: 4px 0; }

.action-btn {
    display: flex; align-items: center; justify-content: center;
    gap: 8px; width: 100%;
    padding: 11px 18px; border-radius: 10px;
    font-family: var(--font); font-size: 13.5px; font-weight: 700;
    cursor: pointer; transition: all 0.15s;
    border: none; text-decoration: none; text-align: center;
}
.action-btn svg { width: 16px; height: 16px; }
.action-btn.primary {
    background: var(--accent); color: #fff;
    box-shadow: 0 2px 10px color-mix(in srgb, var(--accent) 30%, transparent);
}
.action-btn.primary:hover { opacity: 0.9; transform: translateY(-1px); }
.action-btn.danger {
    background: var(--red); color: #fff;
    box-shadow: 0 2px 10px rgba(220,38,38,0.25);
}
.action-btn.danger:hover { opacity: 0.9; transform: translateY(-1px); }
.action-btn.outline {
    background: transparent; color: var(--text-2);
    border: 1.5px solid var(--border);
}
.action-btn.outline:hover { border-color: var(--border-2); color: var(--text-1); background: var(--surface); }

/* ── Processed State ── */
.processed-state {
    text-align: center; padding: 18px 0 12px;
}
.processed-state-emoji { font-size: 30px; margin-bottom: 10px; }
.processed-state-title { font-size: 13px; font-weight: 700; color: var(--text-1); margin-bottom: 4px; }
.processed-state-sub { font-size: 12px; color: var(--text-3); }

/* ── Log Table ── */
.log-table { width: 100%; border-collapse: collapse; text-align: left; min-width: 480px; }
.log-table thead tr { background: var(--surface); }
.log-table th {
    padding: 11px 18px; font-size: 10.5px; font-weight: 800;
    color: var(--text-4); text-transform: uppercase; letter-spacing: 0.05em;
    border-bottom: 1px solid var(--border);
}
.log-table td {
    padding: 13px 18px; font-size: 12.5px; font-weight: 600;
    color: var(--text-2); border-bottom: 1px solid var(--border);
    vertical-align: middle;
}
.log-table tbody tr:last-child td { border-bottom: none; }
.log-table tbody tr:hover td { background: var(--surface); }

/* ── Utility Buttons ── */
.btn-back {
    display: inline-flex; align-items: center; gap: 8px;
    padding: 9px 16px; border: 1px solid var(--border);
    border-radius: 10px; background: var(--panel);
    font-family: var(--font); font-size: 13px; font-weight: 700;
    color: var(--text-2); text-decoration: none; cursor: pointer; transition: all 0.2s;
}
.btn-back:hover { border-color: var(--accent); color: var(--accent); transform: translateX(-2px); }
.btn-back svg { width: 14px; height: 14px; }

.btn-open-order {
    display: inline-flex; align-items: center; gap: 7px;
    padding: 7px 14px; border: 1px solid var(--border);
    border-radius: 9px; background: var(--panel);
    font-family: var(--font); font-size: 12px; font-weight: 700;
    color: var(--text-2); text-decoration: none; cursor: pointer; transition: all 0.2s;
}
.btn-open-order:hover { border-color: var(--accent); color: var(--accent); }
.btn-open-order svg { width: 13px; height: 13px; }

/* ── Empty State ── */
.empty-state {
    text-align: center; padding: 44px 22px;
}
.empty-state-emoji { font-size: 32px; margin-bottom: 12px; }
.empty-state-title { font-size: 14px; font-weight: 700; color: var(--text-2); margin-bottom: 5px; }
.empty-state-desc  { font-size: 12.5px; color: var(--text-3); }

/* ── Order Not Found ── */
.order-not-found {
    border: 2px dashed var(--border); border-radius: 12px;
    padding: 28px 22px; text-align: center;
}

/* ── Tracking Chip ── */
.tracking-chip {
    display: inline-flex; align-items: center; gap: 6px;
    font-family: 'Courier New', monospace; font-size: 12px; font-weight: 800;
    color: var(--accent); background: var(--accent-dim);
    padding: 3px 10px; border-radius: 6px;
}
.tracking-chip svg { width: 12px; height: 12px; }

/* ── Modals ── */
.modal-overlay {
    position: fixed; inset: 0;
    background: rgba(15,23,42,0.5); backdrop-filter: blur(6px);
    z-index: 300; display: flex; align-items: center; justify-content: center;
    opacity: 0; pointer-events: none; transition: opacity 0.2s;
}
.modal-overlay.open { opacity: 1; pointer-events: auto; }
.modal-box {
    background: var(--panel); border-radius: 18px;
    width: 440px; max-width: 92vw;
    box-shadow: 0 25px 60px rgba(0,0,0,0.2), 0 0 0 1px var(--border);
    transform: scale(0.94) translateY(8px);
    transition: transform 0.22s cubic-bezier(0.34,1.56,0.64,1), opacity 0.2s;
    opacity: 0; overflow: hidden;
}
.modal-overlay.open .modal-box { transform: scale(1) translateY(0); opacity: 1; }
.modal-header { padding: 22px 24px 0; }
.modal-icon {
    width: 42px; height: 42px; border-radius: 11px;
    display: flex; align-items: center; justify-content: center; margin-bottom: 14px;
}
.modal-icon svg { width: 20px; height: 20px; }
.modal-icon.success { background: var(--green-dim); color: var(--green); }
.modal-icon.danger  { background: var(--red-dim);   color: var(--red); }
.modal-icon.warning { background: var(--amber-dim); color: var(--amber); }
.modal-title { font-size: 16px; font-weight: 800; color: var(--text-1); margin-bottom: 6px; }
.modal-desc  { font-size: 13px; color: var(--text-2); line-height: 1.6; }
.modal-alert {
    display: flex; align-items: flex-start; gap: 10px;
    border-radius: 9px; padding: 11px 14px; margin-top: 14px;
    font-size: 12.5px; font-weight: 600; line-height: 1.5;
}
.modal-alert.info {
    background: color-mix(in srgb, var(--accent) 8%, transparent);
    color: var(--accent);
    border: 1px solid color-mix(in srgb, var(--accent) 20%, transparent);
}
.modal-alert.warn { background: var(--red-dim); color: var(--red); border: 1px solid rgba(220,38,38,0.2); }
.modal-alert svg  { width: 14px; height: 14px; flex-shrink: 0; margin-top: 1px; }
.modal-body       { padding: 18px 24px; }
.form-label       { font-size: 12px; font-weight: 700; color: var(--text-2); margin-bottom: 7px; display: block; text-transform: uppercase; letter-spacing: 0.04em; }
.form-textarea    {
    width: 100%; padding: 10px 13px;
    border: 1px solid var(--border); border-radius: 9px;
    font-family: var(--font); font-size: 13px; color: var(--text-1);
    background: var(--surface); outline: none; resize: vertical; min-height: 80px;
    transition: border-color 0.15s, box-shadow 0.15s; box-sizing: border-box;
}
.form-textarea:focus { border-color: var(--accent); box-shadow: 0 0 0 3px var(--accent-glow); }
.form-textarea::placeholder { color: var(--text-4); }
.modal-footer     { padding: 0 24px 22px; display: flex; gap: 10px; justify-content: flex-end; }
.modal-divider    { height: 1px; background: var(--border); margin: 0 24px; }

.btn-modal { display: inline-flex; align-items: center; gap: 7px; border: none; border-radius: 9px; padding: 9px 18px; font-family: var(--font); font-weight: 700; font-size: 13px; cursor: pointer; transition: all 0.15s; }
.btn-modal svg { width: 14px; height: 14px; }
.btn-modal.outline    { background: transparent; color: var(--text-2); border: 1px solid var(--border); }
.btn-modal.outline:hover { border-color: var(--border-2); color: var(--text-1); }
.btn-modal.primary    { background: var(--accent); color: #fff; box-shadow: 0 2px 8px color-mix(in srgb, var(--accent) 35%, transparent); }
.btn-modal.primary:hover { opacity: 0.9; transform: translateY(-1px); }
.btn-modal.danger-btn { background: var(--red); color: #fff; box-shadow: 0 2px 8px rgba(220,38,38,0.3); }
.btn-modal.danger-btn:hover { opacity: 0.9; transform: translateY(-1px); }

/* ── Responsive ── */
@media (max-width: 700px) {
    .meta-strip { flex-wrap: wrap; }
    .meta-chip  { flex: 1 1 45%; border-right: none; border-bottom: 1px solid var(--border); }
    .meta-chip:nth-child(odd) { border-right: 1px solid var(--border); }
    .meta-chip:last-child,
    .meta-chip:nth-last-child(2):nth-child(odd) { border-bottom: none; }
    .header-actions { width: 100%; }
    .payment-grid   { grid-template-columns: 1fr 1fr; }
}
@media (max-width: 480px) {
    .payment-grid { grid-template-columns: 1fr; }
}
</style>
@endsection

@section('content')



{{-- ─────────────────────────────────────────────
     Breadcrumb
────────────────────────────────────────────── --}}
<div class="breadcrumb" style="margin-bottom: 20px;">
    <a href="{{ route('dashboard') }}" style="color: var(--text-3); text-decoration: none;">Dashboard</a> &raquo;
    <a href="{{ route('transactions.index') }}" style="color: var(--text-3); text-decoration: none;">Riwayat Transaksi</a> &raquo;
    <span style="color: var(--text-1); font-weight: 600;">{{ $transaction->transaction_code }}</span>
</div>

{{-- ─────────────────────────────────────────────
     Page Header
────────────────────────────────────────────── --}}
<div class="page-header">
    <div class="page-header-left">
        <div class="page-icon">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                <rect x="1" y="4" width="22" height="16" rx="2" ry="2"/>
                <line x1="1" y1="10" x2="23" y2="10"/>
            </svg>
        </div>
        <div class="page-header-text">
            <h1>
                Detail Transaksi
                <code>{{ $transaction->transaction_code }}</code>
            </h1>
            <p>
                Dibuat {{ $transaction->created_at->diffForHumans() }}
                &bull; {{ $transaction->created_at->format('d M Y, H:i') }} WIB
                @if($transaction->invoice)
                    &bull; Multi-Toko ({{ $transaction->invoice->orders->count() }} Pesanan)
                @elseif($transaction->order)
                    &bull; {{ $transaction->order->store->name ?? 'Toko Pusat' }}
                @endif
            </p>
        </div>
    </div>

    <div class="header-actions">
        <span class="status-badge status-{{ $transaction->status }}">
            {{ \App\Services\StatusService::getTransactionLabel($transaction->status) }}
        </span>
        <a href="{{ route('transactions.index') }}" class="btn-back">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/>
            </svg>
            Kembali
        </a>
    </div>
</div>

{{-- ─────────────────────────────────────────────
     Meta Strip
────────────────────────────────────────────── --}}
<div class="meta-strip">
    <div class="meta-chip">
        <span class="meta-chip-label">ID Transaksi</span>
        <span class="meta-chip-value" style="font-family: 'Courier New', monospace; font-size: 12px;">{{ $transaction->transaction_code }}</span>
    </div>
    <div class="meta-chip">
        <span class="meta-chip-label">Status</span>
        <span class="meta-chip-value">
            <span class="status-badge status-{{ $transaction->status }}" style="font-size:10px;">
                {{ \App\Services\StatusService::getTransactionLabel($transaction->status) }}
            </span>
        </span>
    </div>
    <div class="meta-chip">
        <span class="meta-chip-label">Metode Pembayaran</span>
        <span class="meta-chip-value">{{ $transaction->payment_method ?? '—' }}</span>
    </div>
    <div class="meta-chip">
        <span class="meta-chip-label">Total Transaksi</span>
        <span class="meta-chip-value" style="color: var(--accent);">Rp {{ number_format($transaction->amount, 0, ',', '.') }}</span>
    </div>
    @if($transaction->invoice)
    <div class="meta-chip">
        <span class="meta-chip-label">Pesanan Terkait</span>
        <span class="meta-chip-value" style="font-family: 'Courier New', monospace; font-size: 12px;">{{ $transaction->invoice->invoice_number ?? $transaction->invoice->midtrans_order_id }}</span>
    </div>
    @elseif($transaction->order)
    <div class="meta-chip">
        <span class="meta-chip-label">Pesanan Terkait</span>
        <span class="meta-chip-value" style="font-family: 'Courier New', monospace; font-size: 12px;">#{{ $transaction->order->order_number }}</span>
    </div>
    @endif
</div>

{{-- ─────────────────────────────────────────────
     Main Grid
────────────────────────────────────────────── --}}
<div class="detail-grid">

    {{-- ══════════════ LEFT COLUMN ══════════════ --}}
    <div class="detail-main">

        {{-- ── Card: Informasi Transaksi ── --}}
        <div class="detail-card">
            <div class="card-header">
                <span class="card-title">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="1" y="4" width="22" height="16" rx="2" ry="2"/><line x1="1" y1="10" x2="23" y2="10"/>
                    </svg>
                    Informasi Transaksi
                </span>
            </div>
            <div class="card-body">
                <div class="payment-grid">
                    <div class="payment-cell">
                        <div class="payment-cell-label">ID Transaksi</div>
                        <div class="payment-cell-value mono">{{ $transaction->transaction_code }}</div>
                    </div>
                    <div class="payment-cell">
                        <div class="payment-cell-label">Metode Pembayaran</div>
                        <div class="payment-cell-value">{{ $transaction->payment_method ?? '—' }}</div>
                    </div>
                    <div class="payment-cell">
                        <div class="payment-cell-label">Total Pembayaran</div>
                        <div class="payment-cell-value accent" style="font-size: 20px; letter-spacing: -0.03em;">Rp {{ number_format($transaction->amount, 0, ',', '.') }}</div>
                    </div>
                    <div class="payment-cell">
                        <div class="payment-cell-label">Status</div>
                        <div class="payment-cell-value">
                            <span class="status-badge status-{{ $transaction->status }}" style="font-size:11px;">
                                {{ \App\Services\StatusService::getTransactionLabel($transaction->status) }}
                            </span>
                        </div>
                    </div>
                    <div class="payment-cell">
                        <div class="payment-cell-label">Tanggal Dibuat</div>
                        <div class="payment-cell-value" style="font-size:12px;">{{ $transaction->created_at->format('d M Y, H:i') }} WIB</div>
                    </div>
                    <div class="payment-cell">
                        <div class="payment-cell-label">Tanggal Dikonfirmasi</div>
                        <div class="payment-cell-value" style="font-size:12px;">
                            @if($transaction->payment_date)
                                {{ $transaction->payment_date->format('d M Y, H:i') }} WIB
                            @else
                                <span style="color:var(--text-4); font-style:italic; font-weight:600;">Belum dikonfirmasi</span>
                            @endif
                        </div>
                    </div>
                </div>

                @if($transaction->notes)
                    <div style="height:1px; background:var(--border); margin: 18px 0;"></div>
                    <div style="font-size:10px; font-weight:800; color:var(--text-4); text-transform:uppercase; letter-spacing:0.06em; margin-bottom:8px;">Catatan</div>
                    <div class="notes-box">{{ $transaction->notes }}</div>
                @endif
            </div>
        </div>

        {{-- ── Card: Pesanan Terkait ── --}}
        @if($transaction->invoice)
        <div class="detail-card">
            <div class="card-header">
                <span class="card-title">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/><path d="M16 10a4 4 0 0 1-8 0"/>
                    </svg>
                    Pesanan Terkait (Invoice)
                </span>
            </div>
            <div class="card-body" style="padding: 0;">
                <div style="padding: 20px 22px;">
                    <div class="payment-grid">
                        <div class="payment-cell">
                            <div class="payment-cell-label">Nomor Invoice</div>
                            <div class="payment-cell-value mono">{{ $transaction->invoice->invoice_number ?? $transaction->invoice->midtrans_order_id }}</div>
                        </div>
                        <div class="payment-cell">
                            <div class="payment-cell-label">Nama Customer</div>
                            <div class="payment-cell-value">{{ $transaction->invoice->user?->name ?? '—' }}</div>
                        </div>
                    </div>
                </div>
                
                <table class="log-table" style="min-width:100%;">
                    <thead>
                        <tr>
                            <th>Nomor Pesanan</th>
                            <th>Toko</th>
                            <th>Status</th>
                            <th>Total Tagihan</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($transaction->invoice->orders as $invOrder)
                        <tr>
                            <td class="mono">#{{ $invOrder->order_number }}</td>
                            <td>{{ $invOrder->store->name ?? 'Toko Pusat' }}</td>
                            <td>{{ ucfirst($invOrder->status) }}</td>
                            <td class="accent">Rp {{ number_format($invOrder->total_amount, 0, ',', '.') }}</td>
                            <td>
                                <a href="{{ route('orders.show', $invOrder) }}" class="btn-sm" style="padding:4px 8px;">Detail</a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @elseif($transaction->order)
        <div class="detail-card">
            <div class="card-header">
                <span class="card-title">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/><path d="M16 10a4 4 0 0 1-8 0"/>
                    </svg>
                    Pesanan Terkait
                </span>
                <a href="{{ route('orders.show', $transaction->order) }}" class="btn-open-order">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/><polyline points="15 3 21 3 21 9"/><line x1="10" y1="14" x2="21" y2="3"/>
                    </svg>
                    Buka Pesanan
                </a>
            </div>
            <div class="card-body">
                <div class="payment-grid">
                    <div class="payment-cell">
                        <div class="payment-cell-label">Nomor Pesanan</div>
                        <div class="payment-cell-value mono">#{{ $transaction->order->order_number }}</div>
                    </div>
                    <div class="payment-cell">
                        <div class="payment-cell-label">Status Pesanan</div>
                        <div class="payment-cell-value">{{ ucfirst($transaction->order->status ?? '—') }}</div>
                    </div>
                    <div class="payment-cell">
                        <div class="payment-cell-label">Nama Customer</div>
                        <div class="payment-cell-value">{{ $transaction->order->customer_name ?? '—' }}</div>
                    </div>
                    <div class="payment-cell">
                        <div class="payment-cell-label">WhatsApp / No. HP</div>
                        <div class="payment-cell-value" style="font-size:12px;">{{ $transaction->order->customer_phone ?? '—' }}</div>
                    </div>
                    <div class="payment-cell">
                        <div class="payment-cell-label">Toko</div>
                        <div class="payment-cell-value accent">{{ $transaction->order->store->name ?? '—' }}</div>
                    </div>
                    <div class="payment-cell">
                        <div class="payment-cell-label">Tanggal Pesanan</div>
                        <div class="payment-cell-value" style="font-size:12px;">{{ $transaction->order->created_at->format('d M Y, H:i') }}</div>
                    </div>
                </div>

                @if($transaction->order->shipping_address)
                    <div style="height:1px; background:var(--border); margin: 18px 0;"></div>
                    <div style="font-size:10px; font-weight:800; color:var(--text-4); text-transform:uppercase; letter-spacing:0.06em; margin-bottom:8px;">Alamat Pengiriman</div>
                    <div class="notes-box">
                        <strong style="color:var(--text-1);">{{ $transaction->order->customer_name }}</strong><br>
                        {{ $transaction->order->shipping_address }}
                    </div>
                @endif
            </div>
        </div>
        @else
        <div class="detail-card">
            <div class="card-header">
                <span class="card-title">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/>
                    </svg>
                    Pesanan Terkait
                </span>
            </div>
            <div class="card-body">
                <div class="order-not-found">
                    <div class="empty-state-emoji">📦</div>
                    <div class="empty-state-title">Pesanan Tidak Ditemukan</div>
                    <div class="empty-state-desc">Pesanan yang dihubungkan ke transaksi ini telah dihapus dari sistem.</div>
                </div>
            </div>
        </div>
        @endif

        {{-- ── Card: Timeline Riwayat ── --}}
        <div class="detail-card">
            <div class="card-header">
                <span class="card-title">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/>
                    </svg>
                    Riwayat &amp; Timeline
                </span>
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
                            <div class="timeline-note">Customer mengajukan pembayaran via {{ $transaction->payment_method ?? 'N/A' }}.</div>
                        </div>
                    </div>

                    {{-- Step 2: Menunggu / Dikonfirmasi --}}
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
                                @if($transaction->status === 'pending') Menunggu Konfirmasi Administrator
                                @else Dikonfirmasi Administrator
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
                                <rect width="20" height="12" x="2" y="6" rx="2"/><circle cx="12" cy="12" r="2"/><path d="M6 12h.01M18 12h.01"/>
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
                            <div class="timeline-title">Dana Dikembalikan</div>
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
                            <div class="timeline-title" style="color:var(--text-3);">Menunggu Keputusan Administrator</div>
                            <div class="timeline-date">—</div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- ── Card: Log Aktivitas Sistem ── --}}
        <div class="detail-card">
            <div class="card-header">
                <span class="card-title">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                        <polyline points="14 2 14 8 20 8"/>
                        <line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/>
                    </svg>
                    Log Aktivitas Sistem
                </span>
            </div>
            <div style="overflow-x: auto;">
                <table class="log-table">
                    <thead>
                        <tr>
                            <th>Waktu</th>
                            <th>Aksi</th>
                            <th>Detail</th>
                        </tr>
                    </thead>
                    <tbody>
                        {{-- Confirmation / final status row --}}
                        @if($transaction->payment_date && in_array($transaction->status, ['paid', 'failed', 'refund']))
                        <tr>
                            <td style="font-size:12px; white-space:nowrap;">
                                {{ $transaction->payment_date->format('d M Y') }}<br>
                                <span style="color:var(--text-4);">{{ $transaction->payment_date->format('H:i') }} WIB</span>
                            </td>
                            <td>
                                <span class="status-badge status-{{ $transaction->status }}" style="font-size:10px;">
                                    {{ strtoupper(\App\Services\StatusService::getTransactionLabel($transaction->status)) }}
                                </span>
                            </td>
                            <td style="color:var(--text-3);">
                                @if($transaction->status === 'paid') Dana diterima &amp; pesanan diperbarui
                                @elseif($transaction->status === 'failed') Pembayaran ditolak oleh administrator
                                @elseif($transaction->status === 'refund') Dana dikembalikan
                                @endif
                            </td>
                        </tr>
                        @endif
                        {{-- Creation row --}}
                        <tr>
                            <td style="font-size:12px; white-space:nowrap;">
                                {{ $transaction->created_at->format('d M Y') }}<br>
                                <span style="color:var(--text-4);">{{ $transaction->created_at->format('H:i') }} WIB</span>
                            </td>
                            <td>
                                <span class="status-badge status-pending" style="font-size:10px;">DIBUAT</span>
                            </td>
                            <td style="color:var(--text-3);">Transaksi dibuat via {{ $transaction->payment_method ?? 'N/A' }}</td>
                        </tr>
                        @if(!$transaction->payment_date && $transaction->status === 'pending')
                        <tr>
                            <td colspan="3" style="padding: 30px; text-align:center; color:var(--text-4); font-style:italic;">
                                Belum ada konfirmasi administrator.
                            </td>
                        </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>

    </div>
    {{-- ══════════════ END LEFT COLUMN ══════════════ --}}

    {{-- ══════════════ RIGHT SIDEBAR ══════════════ --}}
    <div class="detail-side">


        {{-- ── Card: Ringkasan Pembayaran ── --}}
        <div class="detail-card">
            <div class="card-header">
                <span class="card-title">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <rect width="20" height="12" x="2" y="6" rx="2"/><circle cx="12" cy="12" r="2"/><path d="M6 12h.01M18 12h.01"/>
                    </svg>
                    Ringkasan Pembayaran
                </span>
            </div>
            <div class="card-body">
                <div class="info-row">
                    <span class="info-label">Subtotal</span>
                    <span class="info-value">Rp {{ number_format($transaction->amount, 0, ',', '.') }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Metode</span>
                    <span class="info-value" style="font-size:12px;">{{ $transaction->payment_method ?? 'N/A' }}</span>
                </div>
                <div style="height:1px; background:var(--border); margin: 14px 0;"></div>
                <div style="display:flex; justify-content:space-between; align-items:baseline; padding: 4px 0;">
                    <span style="font-size:14px; font-weight:700; color:var(--text-1);">Total Dibayar</span>
                    <span style="font-size:20px; font-weight:900; color:var(--accent); letter-spacing:-0.03em;">Rp {{ number_format($transaction->amount, 0, ',', '.') }}</span>
                </div>
            </div>
        </div>

        {{-- ── Card: Informasi Pesanan (sidebar summary) ── --}}
        @if($transaction->order)
        <div class="detail-card">
            <div class="card-header">
                <span class="card-title">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/>
                    </svg>
                    Informasi Customer
                </span>
            </div>
            <div class="card-body">
                <div class="info-row">
                    <span class="info-label">Nama</span>
                    <span class="info-value">{{ $transaction->order->customer_name ?? '—' }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Telepon</span>
                    <span class="info-value" style="font-size:12px;">
                        @if($transaction->order->customer_phone)
                            <a href="https://wa.me/{{ preg_replace('/\D/', '', $transaction->order->customer_phone) }}" target="_blank"
                                style="color: #16a34a; font-weight: 800; text-decoration:none; display:inline-flex; align-items:center; gap:5px;">
                                <svg viewBox="0 0 24 24" fill="currentColor" style="width:13px;height:13px;"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 0 1-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 0 1-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 0 1 2.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0 0 12.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 0 0 5.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 0 0-3.48-8.413Z"/></svg>
                                {{ $transaction->order->customer_phone }}
                            </a>
                        @else
                            —
                        @endif
                    </span>
                </div>
                <div class="info-row">
                    <span class="info-label">Toko</span>
                    <span class="info-value" style="color:var(--accent); font-size:12px;">{{ $transaction->order->store->name ?? '—' }}</span>
                </div>
                @if($transaction->order->shipping_address)
                    <div style="margin-top: 14px;">
                        <div style="font-size:10px; font-weight:800; color:var(--text-4); text-transform:uppercase; letter-spacing:0.06em; margin-bottom:8px;">Alamat</div>
                        <div class="notes-box" style="font-size:12px;">{{ $transaction->order->shipping_address }}</div>
                    </div>
                @endif
            </div>
        </div>
        @endif

        {{-- ── Card: Informasi Sistem ── --}}
        <div class="detail-card">
            <div class="card-header">
                <span class="card-title">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/>
                    </svg>
                    Informasi Sistem
                </span>
            </div>
            <div class="card-body">
                <div class="info-row">
                    <span class="info-label">ID Rekam</span>
                    <span class="info-value mono">#{{ $transaction->id }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Dibuat Pada</span>
                    <span class="info-value" style="font-size:12px;">{{ $transaction->created_at->format('d/m/Y H:i:s') }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Diperbarui</span>
                    <span class="info-value" style="font-size:12px;">{{ $transaction->updated_at->format('d/m/Y H:i:s') }}</span>
                </div>
            </div>
        </div>

    </div>
    {{-- ══════════════ END RIGHT SIDEBAR ══════════════ --}}

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


</script>
@endpush
