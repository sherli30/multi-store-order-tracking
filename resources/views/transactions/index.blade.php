@extends('layouts.app')

@section('title', 'Riwayat Transaksi')

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

/* ── Stats Bar ───────────────────────────────── */
.stats-bar {
display: grid;
grid-template-columns: repeat(4, 1fr);
gap: 14px;
margin-bottom: 24px;
}
.stat-card {
background: var(--panel); border: 1px solid var(--border); border-radius: 12px;
padding: 16px 18px; display: flex; align-items: center; gap: 14px;
box-shadow: var(--shadow-sm); transition: box-shadow 0.2s, transform 0.2s;
}
.stat-card:hover { box-shadow: var(--shadow-md); transform: translateY(-1px); }
.stat-icon { width: 42px; height: 42px; border-radius: 10px; display: flex; align-items: center;
justify-content: center; flex-shrink: 0; }
.stat-icon svg { width: 20px; height: 20px; }
.stat-icon.blue { background: rgba(59,130,246,0.1); color: #3b82f6; }
.stat-icon.green { background: var(--green-dim); color: var(--green); }
.stat-icon.amber { background: var(--amber-dim); color: var(--amber); }
.stat-icon.red { background: var(--red-dim); color: var(--red); }

.stat-value { font-size: 19px; font-weight: 800; color: var(--text-1); letter-spacing: -0.03em; }
.stat-label { font-size: 11.5px; color: var(--text-3); font-weight: 500; margin-top: 2px; }
@media (max-width: 1024px) { .stats-bar { grid-template-columns: repeat(2, 1fr); } }
@media (max-width: 600px) { .stats-bar { grid-template-columns: 1fr; } }

/* ── Status Tabs ── */
.tabs-wrap {
display: flex;
gap: 4px;
background: var(--surface);
padding: 4px;
border-radius: 12px;
margin-bottom: 20px;
overflow-x: auto;
scrollbar-width: none;
border: 1px solid var(--border);
}
.tabs-wrap::-webkit-scrollbar { display: none; }
.tab-btn {
padding: 8px 16px;
font-size: 13px;
font-weight: 700;
color: var(--text-3);
border-radius: 9px;
cursor: pointer;
transition: all 0.2s;
text-decoration: none;
white-space: nowrap;
display: inline-flex;
align-items: center;
gap: 8px;
}
.tab-btn:hover { color: var(--text-1); background: rgba(0,0,0,0.03); }
.tab-btn.active { background: var(--panel); color: var(--accent); box-shadow: var(--shadow-sm); border: 1px solid var(--border); }
.tab-count { font-size: 10px; font-weight: 800; padding: 1px 6px; border-radius: 6px; background: var(--surface-2); color: var(--text-3); }
.tab-btn.active .tab-count { background: var(--accent-dim); color: var(--accent); }

/* ── Filter Card ─────────────────────────────── */
.filter-card {
background: var(--panel); border: 1px solid var(--border); border-radius: 14px;
padding: 20px 22px; box-shadow: var(--shadow-sm); margin-bottom: 20px;
}
.filter-card-top { display: flex; align-items: center; gap: 8px; margin-bottom: 16px; }
.filter-card-title { font-size: 13px; font-weight: 700; color: var(--text-1); }
.filter-active-tag {
font-size: 10px; font-weight: 800; color: var(--accent);
background: var(--accent-dim); padding: 2px 8px; border-radius: 5px;
text-transform: uppercase; letter-spacing: 0.05em; margin-left: auto;
}

/* ── Filter Grid: always 4 columns, responsive ── */
.filter-grid {
display: grid;
grid-template-columns: repeat(4, 1fr);
gap: 15px;
align-items: flex-end;
}
@media (max-width: 1024px) { .filter-grid { grid-template-columns: repeat(2, 1fr); } }
@media (max-width: 600px)  { .filter-grid { grid-template-columns: 1fr; } }

.form-group { display: flex; flex-direction: column; min-width: 0; }
.form-label { display: block; font-size: 11.5px; font-weight: 700; color: var(--text-3); margin-bottom: 6px;
text-transform: uppercase; letter-spacing: 0.05em; }
.form-input {
width: 100%; padding: 9px 13px; border: 1px solid var(--border); border-radius: 9px;
font-family: var(--font); font-size: 13px; color: var(--text-1); background: var(--surface);
outline: none; transition: border-color 0.15s, box-shadow 0.15s; box-sizing: border-box;
}
.form-input:focus { border-color: var(--accent); box-shadow: 0 0 0 3px var(--accent-glow); }
.form-input::placeholder { color: var(--text-4); }

/* ── Filter Actions ── */
.filter-actions {
display: flex;
justify-content: flex-end;
align-items: center;
gap: 12px;
margin-top: 16px;
padding-top: 16px;
border-top: 1px solid var(--border);
min-height: 45px;
}
@media (max-width: 600px) {
    .filter-actions { flex-direction: column-reverse; align-items: stretch; gap: 10px; min-height: auto; }
    .filter-actions .btn-primary,
    .filter-actions .btn-outline-reset { justify-content: center; width: 100%; padding: 11px; }
}

.btn-primary {
display: inline-flex; align-items: center; gap: 7px; background: var(--accent); color: #fff; border: none;
padding: 9px 18px; border-radius: 9px; font-family: var(--font); font-weight: 700; font-size: 13px;
cursor: pointer; text-decoration: none; transition: all 0.15s;
box-shadow: 0 2px 8px color-mix(in srgb, var(--accent) 30%, transparent); white-space: nowrap;
}
.btn-primary:hover { opacity: 0.9; transform: translateY(-1px); }
.btn-primary svg { width: 14px; height: 14px; }

.btn-outline-reset {
display: inline-flex; align-items: center; gap: 7px; background: var(--red-dim); color: var(--red);
border: 1px solid rgba(220, 38, 38, 0.2); padding: 9px 16px; border-radius: 9px;
font-family: var(--font); font-weight: 700; font-size: 13px; cursor: pointer; text-decoration: none; transition: all 0.15s;
}
.btn-outline-reset:hover { border-color: rgba(220,38,38,0.4); background: color-mix(in srgb, var(--red-dim) 80%, var(--red)); color: var(--red); }
.btn-outline-reset svg { width: 13px; height: 13px; }

/* ── Table Card ──────────────────────────────── */
.table-card {
background: var(--panel); border: 1px solid var(--border); border-radius: 14px;
overflow: hidden; box-shadow: var(--shadow-sm); animation: rise 0.35s ease both;
}
.table-responsive { overflow-x: auto; }
.product-table { width: 100%; border-collapse: collapse; }
.product-table th {
background: var(--surface); padding: 12px 16px; text-align: left;
font-size: 10.5px; font-weight: 700; color: var(--text-3);
border-bottom: 1px solid var(--border); white-space: nowrap;
text-transform: uppercase; letter-spacing: 0.08em;
}
.product-table th.center { text-align: center; }
.product-table td { padding: 14px 16px; border-bottom: 1px solid var(--border); vertical-align: middle; }
.product-table tr:last-child td { border-bottom: none; }
.product-table tbody tr { transition: background 0.12s; }
.product-table tbody tr:hover td { background: color-mix(in srgb, var(--accent) 3%, var(--surface)); }

/* ── Cells ── */
.cell-no { text-align: center; color: var(--text-4); font-weight: 600; font-size: 12px; }
.trx-id { font-size: 13px; font-weight: 700; color: var(--text-1); font-family: var(--mono); letter-spacing: -0.02em; }
.trx-date { font-size: 11px; color: var(--text-3); margin-top: 3px; }
.order-link { color: var(--accent); text-decoration: none; font-weight: 700; font-size: 12px; font-family: var(--mono); }
.order-link:hover { text-decoration: underline; }
.customer-name { font-size: 13px; font-weight: 700; color: var(--text-1); }
.store-info { display: flex; align-items: center; gap: 5px; font-size: 11px; color: var(--text-3); margin-top: 3px; }
.amount-val { font-size: 14px; font-weight: 800; color: var(--text-1); letter-spacing: -0.01em; }
.payment-method { font-size: 10px; font-weight: 700; color: var(--text-4); text-transform: uppercase; margin-top: 2px; }

/* ── Status Badges ── */
.badge { display: inline-flex; align-items: center; gap: 5px; padding: 4px 11px; border-radius: 20px; font-size: 11.5px;
font-weight: 600; white-space: nowrap; }
.badge::before { content: ''; width: 6px; height: 6px; border-radius: 50%; flex-shrink: 0; }
.badge-pending { background: var(--amber-dim); color: var(--amber); border: 1px solid rgba(245,158,11,0.2); }
.badge-pending::before { background: var(--amber); }
.badge-paid { background: var(--green-dim); color: var(--green); border: 1px solid rgba(22,163,74,0.2); }
.badge-paid::before { background: var(--green); }
.badge-failed { background: var(--surface-2); color: var(--text-3); border: 1px solid var(--border); }
.badge-failed::before { background: var(--text-4); }
.badge-refund { background: var(--red-dim); color: var(--red); border: 1px solid rgba(220,38,38,0.2); }
.badge-refund::before { background: var(--red); }

.actions-cell { display: flex; gap: 6px; justify-content: center; align-items: center; }
.btn-sm { display: inline-flex; align-items: center; gap: 5px; border: 1px solid var(--border); border-radius: 7px;
font-family: var(--font); font-size: 11.5px; font-weight: 600; padding: 6px 12px; cursor: pointer; transition: all 0.15s;
background: var(--panel); color: var(--text-2); text-decoration: none; white-space: nowrap; }
.btn-sm svg { width: 12px; height: 12px; }
.btn-sm:hover { border-color: var(--accent); color: var(--accent); background: color-mix(in srgb, var(--accent) 5%, var(--panel)); }
.btn-sm.success:hover { border-color: var(--green); color: var(--green); background: var(--green-dim); }
.btn-sm.danger:hover { border-color: var(--red); color: var(--red); background: var(--red-dim); }

/* ── Modals ── */
.modal-overlay {
position: fixed; inset: 0; background: rgba(15,23,42,0.45); backdrop-filter: blur(4px);
z-index: 200; display: flex; align-items: center; justify-content: center;
opacity: 0; visibility: hidden; transition: all 0.2s;
}
.modal-overlay.open { opacity: 1; visibility: visible; }
.modal-box {
background: var(--panel); border-radius: 16px; padding: 28px;
width: 420px; max-width: 90vw; box-shadow: var(--shadow-lg);
transform: scale(0.95) translateY(10px); transition: transform 0.2s;
}
.modal-overlay.open .modal-box { transform: scale(1) translateY(0); }
.modal-icon { width: 52px; height: 52px; border-radius: 14px; display: flex; align-items: center;
justify-content: center; margin-bottom: 16px; }
.modal-icon svg { width: 24px; height: 24px; }
.modal-icon.success { background: var(--green-dim); color: var(--green); }
.modal-icon.danger { background: var(--red-dim); color: var(--red); }
.modal-icon.warning { background: var(--amber-dim); color: var(--amber); }
.modal-title { font-size: 16px; font-weight: 800; color: var(--text-1); margin-bottom: 6px; }
.modal-desc { font-size: 13px; color: var(--text-2); margin-bottom: 22px; line-height: 1.6; }
.modal-actions { display: flex; gap: 10px; justify-content: flex-end; margin-top: 24px; }
.btn-cancel { padding: 9px 18px; border: 1px solid var(--border); border-radius: 8px; font-family: var(--font);
font-size: 13px; font-weight: 600; background: var(--surface); color: var(--text-2); cursor: pointer; transition: all 0.15s; }
.btn-cancel:hover { border-color: var(--border-2); color: var(--text-1); }
.btn-modal-primary { padding: 9px 18px; border: none; border-radius: 8px; font-family: var(--font); font-size: 13px;
font-weight: 700; background: var(--accent); color: #fff; cursor: pointer; transition: all 0.15s;
box-shadow: 0 2px 8px rgba(99,102,241,0.25); }
.btn-modal-primary:hover { opacity: 0.9; transform: translateY(-1px); }
.btn-modal-danger { padding: 9px 18px; border: none; border-radius: 8px; font-family: var(--font); font-size: 13px;
font-weight: 700; background: var(--red); color: #fff; cursor: pointer; transition: all 0.15s;
box-shadow: 0 2px 8px rgba(220,38,38,0.25); }
.btn-modal-danger:hover { background: #b91c1c; transform: translateY(-1px); }

/* ── DataTables Overrides ── */
.dataTables_wrapper .dataTables_filter,
.dataTables_wrapper .dataTables_length { display: none !important; }
.dataTables_wrapper .dataTables_info { font-size: 12px; color: var(--text-3); padding: 14px 20px; }
.dataTables_wrapper .dataTables_paginate { padding: 10px 20px; }
.dataTables_wrapper .dataTables_paginate .paginate_button {
font-family: var(--font) !important; font-size: 12px !important; font-weight: 600 !important;
border-radius: 7px !important; border: 1px solid var(--border) !important;
color: var(--text-2) !important; background: var(--panel) !important;
padding: 5px 10px !important; margin: 0 2px !important; transition: all 0.15s !important;
}
.dataTables_wrapper .dataTables_paginate .paginate_button:hover {
border-color: var(--accent) !important; color: var(--accent) !important;
background: color-mix(in srgb, var(--accent) 5%, var(--panel)) !important;
}
.dataTables_wrapper .dataTables_paginate .paginate_button.current {
background: var(--accent) !important; border-color: var(--accent) !important; color: #fff !important;
}
.dataTables_wrapper .dataTables_paginate .paginate_button.disabled,
.dataTables_wrapper .dataTables_paginate .paginate_button.disabled:hover {
color: var(--text-4) !important; background: var(--surface) !important;
border-color: var(--border) !important; cursor: default !important;
}

@keyframes rise { from { opacity: 0; transform: translateY(12px); } to { opacity: 1; transform: translateY(0); } }
.fade-in-animated { animation: rise 0.3s ease-out both; }
@endsection

@section('content')

{{-- Page Header --}}
<div class="page-header">
    <div class="page-header-left">
        <h1>
            <span class="page-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="2" y="5" width="20" height="14" rx="2" ry="2"></rect>
                    <line x1="2" y1="10" x2="22" y2="10"></line>
                </svg>
            </span>
            Riwayat Transaksi
        </h1>
        <p>Pantau status pembayaran dan riwayat transaksi dari semua toko.</p>
    </div>
</div>

{{-- Stats Bar --}}
<div class="stats-bar">
    <div class="stat-card">
        <div class="stat-icon blue">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <line x1="12" y1="1" x2="12" y2="23" />
                <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6" />
            </svg>
        </div>
        <div>
            <div class="stat-value">Rp {{ number_format($stats['total_revenue'] ?? 0, 0, ',', '.') }}</div>
            <div class="stat-label">Pendapatan (Lunas)</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon amber">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="12" cy="12" r="10" />
                <polyline points="12 6 12 12 16 14" />
            </svg>
        </div>
        <div>
            <div class="stat-value">{{ number_format($tabCounts['pending'] ?? 0) }}</div>
            <div class="stat-label">Menunggu Konfirmasi</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon green">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14" />
                <polyline points="22 4 12 14.01 9 11.01" />
            </svg>
        </div>
        <div>
            <div class="stat-value">{{ number_format($tabCounts['paid'] ?? 0) }}</div>
            <div class="stat-label">Transaksi Lunas</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon red">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="12" cy="12" r="10" />
                <line x1="12" y1="8" x2="12" y2="12" />
                <line x1="12" y1="16" x2="12.01" y2="16" />
            </svg>
        </div>
        <div>
            <div class="stat-value">{{ number_format(($tabCounts['failed'] ?? 0) + ($tabCounts['refund'] ?? 0)) }}</div>
            <div class="stat-label">Gagal & Refund</div>
        </div>
    </div>
</div>

{{-- Status Tabs --}}
<div class="tabs-wrap">
    <a href="{{ request()->fullUrlWithQuery(['tab' => 'all']) }}" class="tab-btn {{ $tab === 'all' ? 'active' : '' }}">
        Semua <span class="tab-count">{{ $tabCounts['all'] ?? 0 }}</span>
    </a>
    <a href="{{ request()->fullUrlWithQuery(['tab' => 'pending']) }}" class="tab-btn {{ $tab === 'pending' ? 'active' : '' }}">
        Menunggu <span class="tab-count">{{ $tabCounts['pending'] ?? 0 }}</span>
    </a>
    <a href="{{ request()->fullUrlWithQuery(['tab' => 'paid']) }}" class="tab-btn {{ $tab === 'paid' ? 'active' : '' }}">
        Lunas <span class="tab-count">{{ $tabCounts['paid'] ?? 0 }}</span>
    </a>
    <a href="{{ request()->fullUrlWithQuery(['tab' => 'failed']) }}" class="tab-btn {{ $tab === 'failed' ? 'active' : '' }}">
        Gagal <span class="tab-count">{{ $tabCounts['failed'] ?? 0 }}</span>
    </a>
    <a href="{{ request()->fullUrlWithQuery(['tab' => 'refund']) }}" class="tab-btn {{ $tab === 'refund' ? 'active' : '' }}">
        Dikembalikan<span class="tab-count">{{ $tabCounts['refund'] ?? 0 }}</span>
    </a>
</div>

{{-- Filter Card --}}
<div class="filter-card">
    <div class="filter-card-top">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width:15px;height:15px;color:var(--text-3);">
            <polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3" />
        </svg>
        <span class="filter-card-title">Filter Transaksi</span>
        @if(request()->hasAny(['store_id', 'date', 'sort', 'amount_sort']))
            <span class="filter-active-tag">Filter Aktif</span>
        @endif
    </div>

    <form id="filter-form">
        <input type="hidden" name="tab" value="{{ $tab }}">

        {{--
            Filter columns mapped 1-to-1 to visible table data:
            1. Toko        → "Pelanggan & Toko" column
            2. Tanggal     → date shown inside "ID Transaksi" cell
            3. Urutan      → sorts by created_at (date), newest / oldest
            4. Total Bayar → sorts by amount, highest / lowest — replaces a
                             min/max range pair which would need extra validation
                             and two extra inputs for marginal benefit at this scale
        --}}
        <div class="filter-grid">

            {{-- 1. Toko --}}
            <div class="form-group">
                <label class="form-label">Toko</label>
                <select name="store_id" id="filter-store" class="form-input">
                    <option value="">Semua Toko</option>
                    @foreach ($stores as $s)
                        <option value="{{ $s->id }}" {{ request('store_id') == $s->id ? 'selected' : '' }}>
                            {{ $s->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- 2. Tanggal Transaksi --}}
            <div class="form-group">
                <label class="form-label">Tanggal Transaksi</label>
                <input type="date" name="date" id="filter-date" class="form-input" value="{{ request('date') }}">
            </div>

            {{-- 3. Urutan Tanggal --}}
            <div class="form-group">
                <label class="form-label">Urutan Tanggal</label>
                <select name="sort" id="filter-sort" class="form-input">
                    <option value="desc" {{ request('sort', 'desc') === 'desc' ? 'selected' : '' }}>Terbaru</option>
                    <option value="asc"  {{ request('sort') === 'asc'          ? 'selected' : '' }}>Terlama</option>
                </select>
            </div>

            {{-- 4. Urutan Total Bayar --}}
            <div class="form-group">
                <label class="form-label">Urutan Total Bayar</label>
                <select name="amount_sort" id="filter-amount-sort" class="form-input">
                    <option value=""     {{ !request('amount_sort')               ? 'selected' : '' }}>Default</option>
                    <option value="desc" {{ request('amount_sort') === 'desc'     ? 'selected' : '' }}>Tertinggi</option>
                    <option value="asc"  {{ request('amount_sort') === 'asc'      ? 'selected' : '' }}>Terendah</option>
                </select>
            </div>

        </div>

        <div class="filter-actions">
            <a href="javascript:void(0)" id="btnResetFilter" class="btn-outline-reset"
               style="{{ request()->hasAny(['store_id', 'date', 'sort', 'amount_sort']) ? '' : 'display:none;' }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="18" y1="6" x2="6" y2="18" />
                    <line x1="6" y1="6" x2="18" y2="18" />
                </svg>
                Reset Filter
            </a>
            <button type="button" id="btnApplyFilter" class="btn-primary">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="11" cy="11" r="8" />
                    <line x1="21" y1="21" x2="16.65" y2="16.65" />
                </svg>
                Terapkan Filter
            </button>
        </div>
    </form>
</div>

{{-- Table Card --}}
<div class="table-card">
    <div class="table-responsive">
        <table class="product-table" id="trxTable">
            <thead>
                <tr>
                    <th class="center" style="width:50px;">No</th>
                    <th>ID Transaksi</th>
                    <th>Nomor Pesanan</th>
                    <th>Pelanggan & Toko</th>
                    <th>Total Bayar</th>
                    <th>Status</th>
                    <th class="center" style="width:120px;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @include('transactions.partials._table_rows', ['transactions' => $transactions])
            </tbody>
        </table>
    </div>
</div>

{{-- Modal Konfirmasi --}}
<div class="modal-overlay" id="actionModal">
    <div class="modal-box">
        <div class="modal-icon" id="modalIcon">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                <polyline points="20 6 9 17 4 12" />
            </svg>
        </div>
        <h3 class="modal-title" id="actionTitle">Konfirmasi Transaksi</h3>
        <p class="modal-desc" id="actionDesc">Pastikan dana telah masuk sebelum mengkonfirmasi.</p>

        <form id="actionForm" method="POST" action="" novalidate>
            @csrf
            @method('PATCH')
            <input type="hidden" name="status" id="actionStatus">

            <div id="notesContainer" class="form-group" style="display:none; margin-bottom: 20px;">
                <label class="form-label">Catatan (Opsional)</label>
                <textarea name="notes" class="form-input" rows="3" placeholder="Alasan penolakan atau catatan refund...">{{ old('notes') }}</textarea>
            </div>

            <div class="modal-actions">
                <button type="button" class="btn-cancel" onclick="closeModals()">Batal</button>
                <button type="submit" class="btn-modal-primary" id="actionSubmitBtn">Konfirmasi</button>
            </div>
        </form>
    </div>
</div>

@endsection

@push('scripts')
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<link rel="stylesheet" href="https://cdn.datatables.net/2.3.7/css/dataTables.dataTables.min.css">
<script src="https://cdn.datatables.net/2.3.7/js/dataTables.min.js"></script>

<script>
    // ── Global Modal Handlers ──
    const baseUrl = "{{ url('/') }}";

    function openAction(type, trxId, trxCode) {
        const form    = $('#actionForm');
        const icon    = $('#modalIcon');
        const title   = $('#actionTitle');
        const desc    = $('#actionDesc');
        const notes   = $('#notesContainer');
        const btn     = $('#actionSubmitBtn');

        form.attr('action', `${baseUrl}/transactions/${trxId}/status`);
        $('#actionStatus').val(type);
        notes.toggle(type === 'failed' || type === 'refund');

        icon.removeClass('success danger warning');
        btn.removeClass('btn-modal-primary btn-modal-danger');

        if (type === 'paid') {
            icon.addClass('success');
            title.text('Konfirmasi Pembayaran Lunas');
            desc.html(`ID Transaksi: <strong>${trxCode}</strong><br>Tandai pembayaran ini sebagai lunas?`);
            btn.addClass('btn-modal-primary').text('Ya, Konfirmasi Lunas');
        } else if (type === 'failed') {
            icon.addClass('danger').html('<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>');
            title.text('Tolak Transaksi');
            desc.html(`ID Transaksi: <strong>${trxCode}</strong><br>Tandai pembayaran ini sebagai gagal?`);
            btn.addClass('btn-modal-danger').text('Ya, Tolak');
        } else if (type === 'refund') {
            icon.addClass('warning').html('<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="1 4 1 10 7 10"/><path d="M3.51 15a9 9 0 1 0 .49-4"/></svg>');
            title.text('Proses Refund');
            desc.html(`ID Transaksi: <strong>${trxCode}</strong><br>Ubah status transaksi menjadi refund?`);
            btn.addClass('btn-modal-danger').text('Konfirmasi Refund');
        }

        $('#actionModal').addClass('open');
    }

    function closeModals() {
        $('.modal-overlay').removeClass('open');
    }

    $(document).ready(function () {
        let table;

        function initDataTable() {
            return $('#trxTable').DataTable({
                responsive: true,
                autoWidth: false,
                ordering: false, // server-side ordering owns this
                language: {
                    search: "",
                    searchPlaceholder: "Cari Data...",
                    lengthMenu: "Tampilkan _MENU_ data",
                    info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
                    infoEmpty: "Tidak ada data untuk ditampilkan",
                    zeroRecords: `
                        <div class="empty-state" style="padding: 40px 20px; text-align: center; display: flex; flex-direction: column; align-items: center; justify-content: center; margin: 0 auto; width: 100%;">
                            <div style="margin-bottom: 20px; width: 88px; height: 88px; background: color-mix(in srgb, var(--accent) 10%, transparent); border-radius: 24px; display: flex; align-items: center; justify-content: center; border: 1px solid color-mix(in srgb, var(--accent) 15%, transparent);">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" style="width: 38px; height: 38px; color: var(--accent); opacity: 0.9;">
                                    <rect x="1" y="4" width="22" height="16" rx="2" ry="2"></rect>
                                    <line x1="1" y1="10" x2="23" y2="10"></line>
                                </svg>
                            </div>
                            <div style="margin-bottom: 6px; font-size: 18px; font-weight: 800; color: var(--text-1); letter-spacing: -0.02em;">Data Tidak Ditemukan</div>
                            <div style="font-size: 13px; color: var(--text-3);">Tidak ada riwayat transaksi yang cocok dengan filter yang dipilih.</div>
                        </div>`,
                    emptyTable: `
                        <div class="empty-state" style="padding: 40px 20px; text-align: center; display: flex; flex-direction: column; align-items: center; justify-content: center; margin: 0 auto; width: 100%;">
                            <div style="margin-bottom: 20px; width: 88px; height: 88px; background: color-mix(in srgb, var(--accent) 10%, transparent); border-radius: 24px; display: flex; align-items: center; justify-content: center; border: 1px solid color-mix(in srgb, var(--accent) 15%, transparent);">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" style="width: 38px; height: 38px; color: var(--accent); opacity: 0.9;">
                                    <rect x="1" y="4" width="22" height="16" rx="2" ry="2"></rect>
                                    <line x1="1" y1="10" x2="23" y2="10"></line>
                                </svg>
                            </div>
                            <div style="margin-bottom: 6px; font-size: 18px; font-weight: 800; color: var(--text-1); letter-spacing: -0.02em;">Belum Ada Transaksi</div>
                            <div style="font-size: 13px; color: var(--text-3);">Riwayat transaksi Anda akan muncul di sini setelah pesanan dibuat.</div>
                        </div>`,
                    paginate: { first: "«", last: "»", next: "›", previous: "‹" }
                },
                columnDefs: [
                    { targets: 0, searchable: false },
                    { targets: -1, searchable: false }
                ],
                drawCallback: function () {
                    const api = this.api();
                    const startIndex = api.page.info().start;
                    api.column(0, { page: 'current' }).nodes().each(function (cell, i) {
                        cell.innerHTML = startIndex + i + 1;
                    });
                }
            });
        }

        table = initDataTable();

        // ── Terapkan Filter (AJAX) ──
        $('#btnApplyFilter').on('click', function () {
            const params = {
                store_id:    $('#filter-store').val(),
                date:        $('#filter-date').val(),
                sort:        $('#filter-sort').val(),
                amount_sort: $('#filter-amount-sort').val(),
                tab:         $('input[name="tab"]').val()
            };

            $('#trxTable tbody').css('opacity', '0.4');

            $.ajax({
                url: "{{ route('transactions.index') }}",
                type: 'GET',
                data: params,
                success: function (html) {
                    if ($.fn.DataTable.isDataTable('#trxTable')) {
                        table.destroy();
                    }
                    $('#trxTable tbody').html(html);
                    $('#trxTable tbody tr').addClass('fade-in-animated');
                    table = initDataTable();
                    $('#trxTable tbody').css('opacity', '1');
                    checkResetVisibility();

                    // Update URL state so filters persist on refresh
                    window.history.pushState(null, '', '?' + $.param(params));
                },
                error: function () {
                    $('#trxTable tbody').css('opacity', '1');
                    if (typeof showToast === 'function') {
                        showToast('Gagal memuat data. Silakan coba lagi.', 'error');
                    }
                }
            });
        });

        // ── Reset Filter ──
        $('#btnResetFilter').on('click', function () {
            $('#filter-store').val('');
            $('#filter-date').val('');
            $('#filter-sort').val('desc');
            $('#filter-amount-sort').val('');
            $('#btnApplyFilter').trigger('click');
        });

        function checkResetVisibility() {
            const hasFilter =
                $('#filter-store').val() ||
                $('#filter-date').val()  ||
                $('#filter-sort').val()        !== 'desc' ||
                $('#filter-amount-sort').val() !== '';

            if (hasFilter) {
                $('#btnResetFilter').fadeIn(200).css('display', 'inline-flex');
            } else {
                $('#btnResetFilter').fadeOut(200);
            }
        }

        // Close on Escape
        $(document).on('keydown', function (e) {
            if (e.key === 'Escape') closeModals();
        });

        // Close on backdrop click
        $('.modal-overlay').on('click', function (e) {
            if (e.target === this) closeModals();
        });
    });
</script>
@endpush
