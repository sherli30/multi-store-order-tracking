@extends('layouts.app')

@section('title', 'Riwayat Transaksi')

@section('styles')
/* =============================================
   TRANSACTION INDEX — Professional Design
   ============================================= */

/* ── Page Header ─────────────────────────────── */
.page-header {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    margin-bottom: 28px;
    flex-wrap: wrap;
    gap: 16px;
}
.page-header-left h1 {
    font-size: 22px;
    font-weight: 800;
    letter-spacing: -0.04em;
    color: var(--text-1);
    margin-bottom: 5px;
    display: flex;
    align-items: center;
    gap: 10px;
}
.page-header-left h1 .page-icon {
    width: 36px;
    height: 36px;
    background: linear-gradient(135deg, var(--accent), color-mix(in srgb, var(--accent) 70%, #7c3aed));
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
}
.page-header-left h1 .page-icon svg { width: 18px; height: 18px; color: #fff; }
.page-header-left p {
    font-size: 13px;
    color: var(--text-3);
    margin-left: 46px;
}
.page-header-actions { display: flex; gap: 10px; align-items: center; }

/* ── Summary Stats Bar ───────────────────────── */
.stats-bar {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 14px;
    margin-bottom: 24px;
}
.stat-card {
    background: var(--panel);
    border: 1px solid var(--border);
    border-radius: 12px;
    padding: 16px 18px;
    display: flex;
    align-items: center;
    gap: 14px;
    box-shadow: var(--shadow-sm);
    transition: box-shadow 0.2s, transform 0.2s;
}
.stat-card:hover { box-shadow: var(--shadow-md); transform: translateY(-1px); }
.stat-icon {
    width: 42px; height: 42px;
    border-radius: 10px;
    display: flex; align-items: center; justify-content: center;
    flex-shrink: 0;
}
.stat-icon svg { width: 20px; height: 20px; }
.stat-icon.blue   { background: rgba(59, 130, 246, 0.1); color: #3b82f6; }
.stat-icon.amber  { background: var(--amber-dim); color: var(--amber); }
.stat-icon.green  { background: var(--green-dim); color: var(--green); }
.stat-icon.red    { background: var(--red-dim); color: var(--red); }
.stat-info { flex: 1; min-width: 0; }
.stat-value { font-size: 18px; font-weight: 800; color: var(--text-1); letter-spacing: -0.03em; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.stat-label { font-size: 11.5px; color: var(--text-3); font-weight: 500; margin-top: 2px; }

/* ── Tab Navigation ──────────────────────────── */
.tabs-wrap {
    display: flex;
    gap: 2px;
    margin-bottom: 20px;
    background: var(--surface);
    border: 1px solid var(--border);
    border-radius: 10px;
    padding: 4px;
    overflow-x: auto;
    scrollbar-width: none;
    width: fit-content;
}
.tabs-wrap::-webkit-scrollbar { display: none; }
.tab-btn {
    padding: 7px 16px;
    font-size: 12.5px;
    font-weight: 600;
    color: var(--text-3);
    border-radius: 7px;
    cursor: pointer;
    transition: all 0.18s;
    text-decoration: none;
    white-space: nowrap;
    display: flex;
    align-items: center;
    gap: 7px;
}
.tab-btn:hover { color: var(--text-1); background: var(--panel); }
.tab-btn.active {
    color: var(--accent);
    background: var(--panel);
    box-shadow: 0 1px 4px rgba(0,0,0,0.08);
}
.tab-badge {
    background: var(--surface-2);
    color: var(--text-3);
    font-size: 10px;
    font-weight: 700;
    padding: 1px 6px;
    border-radius: 20px;
    min-width: 18px;
    text-align: center;
}
.tab-btn.active .tab-badge { background: color-mix(in srgb, var(--accent) 15%, transparent); color: var(--accent); }

/* ── Filter Bar ──────────────────────────────── */
.filter-bar {
    display: flex;
    align-items: center;
    gap: 10px;
    margin-bottom: 16px;
    flex-wrap: wrap;
}
.search-wrap { flex: 1; min-width: 240px; position: relative; }
.search-wrap svg { position: absolute; left: 11px; top: 50%; transform: translateY(-50%); width: 15px; height: 15px; color: var(--text-3); pointer-events: none; }
.search-input { width: 100%; padding: 9px 12px 9px 34px; border: 1px solid var(--border); border-radius: 9px; font-family: var(--font); font-size: 13px; background: var(--panel); color: var(--text-1); outline: none; transition: border-color 0.15s, box-shadow 0.15s; }
.search-input::placeholder { color: var(--text-4); }
.search-input:focus { border-color: var(--accent); box-shadow: 0 0 0 3px var(--accent-glow); }
.filter-group { display: flex; align-items: center; gap: 8px; }
.filter-label { font-size: 12px; font-weight: 600; color: var(--text-3); white-space: nowrap; }
.filter-select { padding: 9px 12px; border: 1px solid var(--border); border-radius: 9px; font-family: var(--font); font-size: 13px; background: var(--panel); color: var(--text-1); outline: none; cursor: pointer; transition: border-color 0.15s; }
.filter-select:focus { border-color: var(--accent); box-shadow: 0 0 0 3px var(--accent-glow); }
.btn-filter { display: inline-flex; align-items: center; gap: 6px; padding: 9px 16px; border: 1px solid var(--border); border-radius: 9px; font-family: var(--font); font-size: 13px; font-weight: 600; background: var(--panel); color: var(--text-2); cursor: pointer; transition: all 0.15s; text-decoration: none; }
.btn-filter:hover { border-color: var(--accent); color: var(--accent); }
.btn-reset { display: inline-flex; align-items: center; gap: 6px; padding: 9px 14px; border: 1px solid transparent; border-radius: 9px; font-family: var(--font); font-size: 13px; font-weight: 600; background: var(--red-dim); color: var(--red); cursor: pointer; transition: all 0.15s; text-decoration: none; }
.btn-reset:hover { border-color: rgba(220, 38, 38, 0.25); }
.filter-divider { width: 1px; height: 32px; background: var(--border); }

/* ── Table Card ──────────────────────────────── */
.table-card {
    background: var(--panel);
    border: 1px solid var(--border);
    border-radius: 14px;
    overflow: hidden;
    box-shadow: var(--shadow-sm);
    animation: rise 0.35s ease both;
}

.trx-table { width: 100%; border-collapse: collapse; }
.trx-table th {
    font-size: 10.5px;
    font-weight: 700;
    letter-spacing: 0.08em;
    text-transform: uppercase;
    color: var(--text-3);
    padding: 12px 20px;
    text-align: left;
    border-bottom: 1px solid var(--border);
    background: var(--surface);
    white-space: nowrap;
}
.trx-table th.center { text-align: center; }
.trx-table td { padding: 15px 20px; border-bottom: 1px solid var(--border); vertical-align: middle; }
.trx-table tr:last-child td { border-bottom: none; }
.trx-table tbody tr { transition: background 0.12s; }
.trx-table tbody tr:hover td { background: color-mix(in srgb, var(--accent) 3%, var(--surface)); }

/* ── Table Cell Components ───────────────────── */
.cell-no { text-align: center; color: var(--text-4); font-weight: 600; font-size: 12px; }

.trx-code { font-size: 13px; font-weight: 700; color: var(--text-1); font-family: 'Courier New', monospace; letter-spacing: 0.02em; }
.trx-date { font-size: 11px; color: var(--text-3); margin-top: 3px; }
.trx-method-badge { display: inline-flex; align-items: center; gap: 4px; font-size: 10.5px; color: var(--text-3); margin-top: 5px; background: var(--surface-2); padding: 2px 7px; border-radius: 4px; font-weight: 500; }

.order-link { color: var(--accent); text-decoration: none; font-weight: 700; font-size: 13px; font-family: 'Courier New', monospace; }
.order-link:hover { text-decoration: underline; }
.order-date { font-size: 11px; color: var(--text-3); margin-top: 3px; }

.customer-name { font-size: 13px; font-weight: 600; color: var(--text-1); }
.store-badge { display: inline-flex; align-items: center; gap: 4px; font-size: 11px; color: var(--text-3); margin-top: 3px; }

.amount-total { font-size: 14px; font-weight: 800; color: var(--text-1); letter-spacing: -0.02em; }
.amount-label { font-size: 10.5px; color: var(--text-3); margin-top: 2px; }

/* ── Status Pills ────────────────────────────── */
.status-pill {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    font-size: 11.5px;
    font-weight: 600;
    padding: 4px 11px;
    border-radius: 20px;
    white-space: nowrap;
}
.status-pill::before { content: ''; width: 6px; height: 6px; border-radius: 50%; flex-shrink: 0; }
.status-pill.pending { background: var(--amber-dim); color: var(--amber); border: 1px solid rgba(245, 158, 11, 0.2); }
.status-pill.pending::before { background: var(--amber); box-shadow: 0 0 0 2px rgba(245,158,11,0.25); animation: pulse-dot 1.5s ease-in-out infinite; }
.status-pill.paid { background: var(--green-dim); color: var(--green); border: 1px solid rgba(22, 163, 74, 0.2); }
.status-pill.paid::before { background: var(--green); }
.status-pill.failed { background: var(--surface-2); color: var(--text-3); border: 1px solid var(--border); }
.status-pill.failed::before { background: var(--text-4); }
.status-pill.refund { background: var(--red-dim); color: var(--red); border: 1px solid rgba(220, 38, 38, 0.2); }
.status-pill.refund::before { background: var(--red); }

.pay-date { font-size: 10.5px; color: var(--text-3); margin-top: 4px; }

@keyframes pulse-dot {
    0%, 100% { box-shadow: 0 0 0 2px rgba(245,158,11,0.25); }
    50% { box-shadow: 0 0 0 4px rgba(245,158,11,0.1); }
}

/* ── Action Buttons ──────────────────────────── */
.actions-cell { display: flex; gap: 6px; justify-content: center; }
.btn-sm { display: inline-flex; align-items: center; gap: 5px; border: 1px solid var(--border); border-radius: 7px; font-family: var(--font); font-size: 11.5px; font-weight: 600; padding: 6px 12px; cursor: pointer; transition: all 0.15s; background: var(--panel); color: var(--text-2); text-decoration: none; white-space: nowrap; }
.btn-sm svg { width: 12px; height: 12px; }
.btn-sm:hover { border-color: var(--accent); color: var(--accent); background: color-mix(in srgb, var(--accent) 5%, var(--panel)); }
.btn-sm.success:hover { border-color: var(--green); color: var(--green); background: var(--green-dim); }
.btn-sm.danger:hover { border-color: rgba(220,38,38,0.4); color: var(--red); background: var(--red-dim); }
.btn-sm.view-btn { color: var(--text-3); }
.btn-sm.view-btn:hover { border-color: var(--border-2); color: var(--text-1); }
.processed-label { font-size: 11.5px; color: var(--text-4); font-style: italic; }
.trx-notes-preview { font-size: 10.5px; color: var(--text-3); max-width: 130px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; margin-top: 3px; }

/* ── Empty State ─────────────────────────────── */
.empty-state { padding: 72px 20px; text-align: center; }
.empty-icon { width: 80px; height: 80px; background: var(--surface-2); border-radius: 20px; display: flex; align-items: center; justify-content: center; margin: 0 auto 18px; }
.empty-icon svg { width: 36px; height: 36px; color: var(--text-4); }
.empty-title { font-size: 15px; font-weight: 700; color: var(--text-1); margin-bottom: 7px; }
.empty-desc { font-size: 13px; color: var(--text-3); }

/* ── Pagination ──────────────────────────────── */
.pagination-wrap { display: flex; align-items: center; justify-content: space-between; padding: 14px 20px; border-top: 1px solid var(--border); background: var(--surface); gap: 12px; flex-wrap: wrap; }
.pagination-info { font-size: 12px; color: var(--text-3); }

/* ── Modal ───────────────────────────────────── */
.modal-overlay {
    position: fixed; inset: 0;
    background: rgba(15, 23, 42, 0.5);
    backdrop-filter: blur(6px);
    z-index: 300;
    display: flex; align-items: center; justify-content: center;
    opacity: 0; pointer-events: none;
    transition: opacity 0.2s;
}
.modal-overlay.open { opacity: 1; pointer-events: auto; }
.modal-box {
    background: var(--panel);
    border-radius: 18px;
    width: 440px;
    max-width: 92vw;
    box-shadow: 0 25px 60px rgba(0,0,0,0.2), 0 0 0 1px var(--border);
    transform: scale(0.94) translateY(8px);
    transition: transform 0.22s cubic-bezier(0.34,1.56,0.64,1), opacity 0.2s;
    opacity: 0;
    overflow: hidden;
}
.modal-overlay.open .modal-box { transform: scale(1) translateY(0); opacity: 1; }
.modal-header { padding: 22px 24px 0; }
.modal-header-top { display: flex; align-items: flex-start; justify-content: space-between; margin-bottom: 8px; }
.modal-icon { width: 42px; height: 42px; border-radius: 11px; display: flex; align-items: center; justify-content: center; margin-bottom: 14px; }
.modal-icon svg { width: 20px; height: 20px; }
.modal-icon.success { background: var(--green-dim); color: var(--green); }
.modal-icon.danger { background: var(--red-dim); color: var(--red); }
.modal-icon.warning { background: var(--amber-dim); color: var(--amber); }
.modal-title { font-size: 16px; font-weight: 800; color: var(--text-1); margin-bottom: 6px; }
.modal-desc { font-size: 13px; color: var(--text-2); line-height: 1.6; }
.modal-alert { display: flex; align-items: flex-start; gap: 10px; border-radius: 9px; padding: 11px 14px; margin-top: 14px; font-size: 12.5px; font-weight: 600; line-height: 1.5; }
.modal-alert.info { background: color-mix(in srgb, var(--accent) 8%, transparent); color: var(--accent); border: 1px solid color-mix(in srgb, var(--accent) 20%, transparent); }
.modal-alert.warn { background: var(--red-dim); color: var(--red); border: 1px solid rgba(220,38,38,0.2); }
.modal-alert svg { width: 14px; height: 14px; flex-shrink: 0; margin-top: 1px; }
.modal-body { padding: 18px 24px; }
.form-label { font-size: 12px; font-weight: 700; color: var(--text-2); margin-bottom: 7px; display: block; text-transform: uppercase; letter-spacing: 0.04em; }
.form-textarea { width: 100%; padding: 10px 13px; border: 1px solid var(--border); border-radius: 9px; font-family: var(--font); font-size: 13px; color: var(--text-1); background: var(--surface); outline: none; resize: vertical; min-height: 75px; transition: border-color 0.15s, box-shadow 0.15s; box-sizing: border-box; }
.form-textarea:focus { border-color: var(--accent); box-shadow: 0 0 0 3px var(--accent-glow); }
.form-textarea::placeholder { color: var(--text-4); }
.modal-footer { padding: 0 24px 22px; display: flex; gap: 10px; justify-content: flex-end; }
.btn-modal { display: inline-flex; align-items: center; gap: 7px; border: none; border-radius: 9px; padding: 9px 18px; font-family: var(--font); font-weight: 700; font-size: 13px; cursor: pointer; transition: all 0.15s; }
.btn-modal svg { width: 14px; height: 14px; }
.btn-modal.outline { background: transparent; color: var(--text-2); border: 1px solid var(--border); }
.btn-modal.outline:hover { border-color: var(--border-2); color: var(--text-1); }
.btn-modal.primary { background: var(--accent); color: #fff; box-shadow: 0 2px 8px color-mix(in srgb, var(--accent) 35%, transparent); }
.btn-modal.primary:hover { opacity: 0.9; transform: translateY(-1px); }
.btn-modal.danger-btn { background: var(--red); color: #fff; box-shadow: 0 2px 8px rgba(220,38,38,0.3); }
.btn-modal.danger-btn:hover { opacity: 0.9; transform: translateY(-1px); }
.modal-divider { height: 1px; background: var(--border); margin: 0 24px; }

/* ── Flash Alert ─────────────────────────────── */
.alert-success {
    display: flex; align-items: center; gap: 11px;
    background: var(--green-dim);
    border: 1px solid rgba(22, 163, 74, 0.25);
    border-radius: 11px;
    padding: 13px 18px;
    font-size: 13px;
    color: var(--green);
    font-weight: 600;
    margin-bottom: 22px;
    animation: rise 0.3s ease both;
}
.alert-success svg { width: 18px; height: 18px; flex-shrink: 0; }

/* ── Responsive ──────────────────────────────── */
@media (max-width: 900px) {
    .stats-bar { grid-template-columns: repeat(2, 1fr); }
}
@media (max-width: 600px) {
    .stats-bar { grid-template-columns: 1fr 1fr; }
    .page-header { flex-direction: column; }
}
@endsection

@section('content')

{{-- Page Header --}}
<div class="page-header">
    <div class="page-header-left">
        <h1>
            <span class="page-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="1" y="4" width="22" height="16" rx="2" ry="2"/>
                    <line x1="1" y1="10" x2="23" y2="10"/>
                </svg>
            </span>
            Riwayat Transaksi
        </h1>
        <p>Pantau status pembayaran dan riwayat transaksi dari semua toko.</p>
    </div>
</div>

{{-- Flash Alert --}}
@if(session('success'))
    <div class="alert-success" id="flash-success">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
            <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
            <polyline points="22 4 12 14.01 9 11.01"/>
        </svg>
        {{ session('success') }}
    </div>
@endif

{{-- Summary Stats --}}
<div class="stats-bar">
    <div class="stat-card">
        <div class="stat-icon blue">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/>
            </svg>
        </div>
        <div class="stat-info">
            <div class="stat-value">Rp {{ number_format($stats['total_revenue'] ?? 0, 0, ',', '.') }}</div>
            <div class="stat-label">Total Pendapatan (Lunas)</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon amber">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/>
            </svg>
        </div>
        <div class="stat-info">
            <div class="stat-value">{{ $stats['pending_count'] ?? 0 }}</div>
            <div class="stat-label">Menunggu Konfirmasi</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon green">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/>
            </svg>
        </div>
        <div class="stat-info">
            <div class="stat-value">{{ $stats['paid_count'] ?? 0 }}</div>
            <div class="stat-label">Transaksi Lunas</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon red">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/>
            </svg>
        </div>
        <div class="stat-info">
            <div class="stat-value">{{ ($stats['failed_count'] ?? 0) + ($stats['refund_count'] ?? 0) }}</div>
            <div class="stat-label">Gagal & Refund</div>
        </div>
    </div>
</div>

{{-- Tab Navigation --}}
@php
    function appendTab($tabName) {
        $query = request()->query();
        $query['tab'] = $tabName;
        unset($query['page']);
        return route('transactions.index') . '?' . http_build_query($query);
    }
@endphp
<div class="tabs-wrap">
    <a href="{{ appendTab('all') }}" class="tab-btn {{ $tab === 'all' ? 'active' : '' }}">
        Semua
        <span class="tab-badge">{{ $tabCounts['all'] ?? 0 }}</span>
    </a>
    <a href="{{ appendTab('pending') }}" class="tab-btn {{ $tab === 'pending' ? 'active' : '' }}">
        Menunggu
        <span class="tab-badge">{{ $tabCounts['pending'] ?? 0 }}</span>
    </a>
    <a href="{{ appendTab('paid') }}" class="tab-btn {{ $tab === 'paid' ? 'active' : '' }}">
        Lunas
        <span class="tab-badge">{{ $tabCounts['paid'] ?? 0 }}</span>
    </a>
    <a href="{{ appendTab('failed') }}" class="tab-btn {{ $tab === 'failed' ? 'active' : '' }}">
        Gagal / Kadaluarsa
        <span class="tab-badge">{{ $tabCounts['failed'] ?? 0 }}</span>
    </a>
    <a href="{{ appendTab('refund') }}" class="tab-btn {{ $tab === 'refund' ? 'active' : '' }}">
        Refund
        <span class="tab-badge">{{ $tabCounts['refund'] ?? 0 }}</span>
    </a>
</div>

{{-- Filter Bar --}}
<form method="GET" action="{{ route('transactions.index') }}" id="filter-form">
    <input type="hidden" name="tab" value="{{ $tab }}">
    <div class="filter-bar">
        <div class="search-wrap">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
            </svg>
            <input type="text" name="search" class="search-input"
                placeholder="Cari ID transaksi, nomor pesanan, nama customer..."
                value="{{ request('search') }}">
        </div>
        <div class="filter-divider"></div>
        <div class="filter-group">
            <span class="filter-label">Tanggal</span>
            <input type="date" name="date" class="filter-select" value="{{ request('date') }}" onchange="this.form.submit()">
        </div>
        <div class="filter-group">
            <span class="filter-label">Metode</span>
            <select name="payment_method" class="filter-select" onchange="this.form.submit()">
                <option value="">Semua Metode</option>
                <option value="transfer" {{ request('payment_method') === 'transfer' ? 'selected' : '' }}>Transfer Bank</option>
                <option value="cash" {{ request('payment_method') === 'cash' ? 'selected' : '' }}>Tunai</option>
                <option value="ewallet" {{ request('payment_method') === 'ewallet' ? 'selected' : '' }}>E-Wallet</option>
                <option value="qris" {{ request('payment_method') === 'qris' ? 'selected' : '' }}>QRIS</option>
            </select>
        </div>
        <button type="submit" class="btn-filter">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="width:13px;height:13px;">
                <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
            </svg>
            Cari
        </button>
        @if(request('search') || request('date') || request('payment_method'))
            <a href="{{ route('transactions.index', ['tab' => $tab]) }}" class="btn-reset">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="width:13px;height:13px;">
                    <line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/>
                </svg>
                Reset Filter
            </a>
        @endif
    </div>
</form>

{{-- Table --}}
<div class="table-card">
    @if($transactions->isEmpty())
        <div class="empty-state">
            <div class="empty-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="1" y="4" width="22" height="16" rx="2" ry="2"/>
                    <line x1="1" y1="10" x2="23" y2="10"/>
                </svg>
            </div>
            <div class="empty-title">Tidak Ada Transaksi</div>
            <div class="empty-desc">
                @if(request('search') || request('date'))
                    Tidak ada hasil untuk filter yang dipilih.
                @else
                    Belum ada data transaksi pada status ini.
                @endif
            </div>
        </div>
    @else
        <div style="overflow-x: auto;">
            <table class="trx-table">
                <thead>
                    <tr>
                        <th class="center" style="width:50px;">No</th>
                        <th>Transaksi</th>
                        <th>Nomor Pesanan</th>
                        <th>Customer / Toko</th>
                        <th>Total Bayar</th>
                        <th>Status</th>
                        <th class="center" style="width: 180px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @php $startIndex = ($transactions->currentPage() - 1) * $transactions->perPage() + 1; @endphp
                    @foreach($transactions as $trx)
                        <tr>
                            {{-- No --}}
                            <td class="cell-no">{{ $startIndex + $loop->index }}</td>

                            {{-- Transaksi --}}
                            <td>
                                <div class="trx-code">{{ $trx->transaction_code }}</div>
                                <div class="trx-date">
                                    {{ $trx->created_at->format('d M Y, H:i') }}
                                </div>
                                <div class="trx-method-badge">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width:10px;height:10px;">
                                        <rect x="1" y="4" width="22" height="16" rx="2" ry="2"/><line x1="1" y1="10" x2="23" y2="10"/>
                                    </svg>
                                    {{ $trx->payment_method ?? 'N/A' }}
                                </div>
                            </td>

                            {{-- Pesanan --}}
                            <td>
                                @if($trx->order)
                                    <a href="{{ route('orders.show', $trx->order) }}" class="order-link">
                                        {{ $trx->order->order_number }}
                                    </a>
                                    <div class="order-date">{{ $trx->order->created_at->format('d M Y') }}</div>
                                @else
                                    <span style="color:var(--text-4); font-size:12px; font-style:italic;">[Pesanan Dihapus]</span>
                                @endif
                            </td>

                            {{-- Customer / Toko --}}
                            <td>
                                <div class="customer-name">{{ $trx->order->customer_name ?? '—' }}</div>
                                <div class="store-badge">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width:10px;height:10px;">
                                        <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/>
                                        <polyline points="9 22 9 12 15 12 15 22"/>
                                    </svg>
                                    {{ $trx->order->store->name ?? '—' }}
                                </div>
                            </td>

                            {{-- Total Bayar --}}
                            <td>
                                <div class="amount-total">Rp {{ number_format($trx->amount, 0, ',', '.') }}</div>
                                <div class="amount-label">Total pembayaran</div>
                            </td>

                            {{-- Status --}}
                            <td>
                                @php
                                    $lbls = ['pending' => 'Menunggu', 'paid' => 'Lunas', 'failed' => 'Gagal', 'refund' => 'Refund'];
                                @endphp
                                <span class="status-pill {{ $trx->status }}">
                                    {{ $lbls[$trx->status] ?? $trx->status }}
                                </span>
                                @if($trx->payment_date && $trx->status === 'paid')
                                    <div class="pay-date">{{ $trx->payment_date->format('d/m/Y H:i') }}</div>
                                @endif
                            </td>

                            {{-- Aksi --}}
                            <td>
                                <div class="actions-cell">
                                    {{-- Lihat Detail --}}
                                    <a href="{{ route('transactions.show', $trx) }}" class="btn-sm view-btn">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/>
                                        </svg>
                                        Detail
                                    </a>

                                    @if($trx->status === 'pending')
                                        <button class="btn-sm success"
                                            onclick="openAction('paid', {{ $trx->id }}, '{{ $trx->transaction_code }}')">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                                <polyline points="20 6 9 17 4 12"/>
                                            </svg>
                                            Lunas
                                        </button>
                                        <button class="btn-sm danger"
                                            onclick="openAction('failed', {{ $trx->id }}, '{{ $trx->transaction_code }}')">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                                <line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/>
                                            </svg>
                                            Tolak
                                        </button>
                                    @elseif($trx->status === 'paid' && $trx->order && !in_array($trx->order->status, ['shipping', 'completed']))
                                        <button class="btn-sm danger"
                                            onclick="openAction('refund', {{ $trx->id }}, '{{ $trx->transaction_code }}')">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                <polyline points="1 4 1 10 7 10"/><path d="M3.51 15a9 9 0 1 0 .49-4"/>
                                            </svg>
                                            Refund
                                        </button>
                                    @else
                                        <span class="processed-label">Selesai</span>
                                        @if($trx->notes)
                                            <div class="trx-notes-preview" title="{{ $trx->notes }}">{{ $trx->notes }}</div>
                                        @endif
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        <div class="pagination-wrap">
            <div class="pagination-info">
                Menampilkan <strong>{{ $transactions->firstItem() }}–{{ $transactions->lastItem() }}</strong>
                dari <strong>{{ $transactions->total() }}</strong> transaksi
            </div>
            @if($transactions->hasPages())
                <div>{{ $transactions->appends(request()->query())->links('vendor.pagination.custom') }}</div>
            @endif
        </div>
    @endif
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
            <div class="modal-desc" id="actionDesc">Pastikan dana telah masuk sebelum mengkonfirmasi.</div>
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

            <div id="notesContainer" class="modal-body" style="display:none; padding-top: 16px;">
                <div class="modal-divider" style="margin: 0 0 16px; height:1px; background: var(--border);"></div>
                <label class="form-label">Catatan (Opsional)</label>
                <textarea name="notes" class="form-textarea"
                    placeholder="Tulis alasan penolakan atau catatan refund..."></textarea>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn-modal outline" onclick="closeActionModal()">
                    Batalkan
                </button>
                <button type="submit" class="btn-modal primary" id="actionSubmitBtn">
                    Lanjutkan
                </button>
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
            icon.className       = 'modal-icon success';
            title.innerText      = `Konfirmasi Pembayaran Lunas`;
            desc.innerHTML       = `ID Transaksi: <strong>${trxCode}</strong><br>Tandai pembayaran ini sebagai <strong>lunas</strong>.`;
            alert.className      = 'modal-alert info';
            alert.style.display  = 'flex';
            alertText.innerHTML  = 'Auto-Sync: Status pesanan terkait akan diperbarui otomatis menjadi <strong>Dalam Pengemasan (Processing)</strong>.';
            btn.className        = 'btn-modal primary';
            btn.innerHTML        = `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="width:14px;height:14px;"><polyline points="20 6 9 17 4 12"/></svg> Tandai Lunas`;
        } else if (type === 'failed') {
            icon.className       = 'modal-icon danger';
            title.innerText      = `Tolak Transaksi`;
            desc.innerHTML       = `ID Transaksi: <strong>${trxCode}</strong><br>Tandai pembayaran ini sebagai <strong>gagal/tidak valid</strong>.`;
            alert.className      = 'modal-alert warn';
            alert.style.display  = 'flex';
            alertText.innerHTML  = 'Auto-Sync: Pesanan terkait akan otomatis <strong>dibatalkan (Cancelled)</strong>.';
            btn.className        = 'btn-modal danger-btn';
            btn.innerHTML        = `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="width:14px;height:14px;"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg> Tolak Pembayaran`;
        } else if (type === 'refund') {
            icon.className       = 'modal-icon warning';
            title.innerText      = `Proses Refund`;
            desc.innerHTML       = `ID Transaksi: <strong>${trxCode}</strong><br>Ubah status transaksi menjadi <strong>Refund</strong>. Aksi ini tidak melakukan transfer dana secara nyata.`;
            alert.className      = 'modal-alert warn';
            alert.style.display  = 'flex';
            alertText.innerHTML  = 'Auto-Sync: Pesanan terkait akan otomatis <strong>dibatalkan</strong>.';
            btn.className        = 'btn-modal danger-btn';
            btn.innerHTML        = `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width:14px;height:14px;"><polyline points="1 4 1 10 7 10"/><path d="M3.51 15a9 9 0 1 0 .49-4"/></svg> Konfirmasi Refund`;
        }

        document.getElementById('actionModal').classList.add('open');
    }

    function closeActionModal() {
        document.getElementById('actionModal').classList.remove('open');
    }

    // Close on backdrop click
    document.getElementById('actionModal').addEventListener('click', function(e) {
        if (e.target === this) closeActionModal();
    });

    // Close on Escape
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') closeActionModal();
    });
</script>
@endpush
