@extends('layouts.app')

@section('title', 'Monitoring Pengiriman')

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
    grid-template-columns: repeat(5, 1fr);
    gap: 14px;
    margin-bottom: 24px;
}
.stat-card {
    background: var(--panel); border: 1px solid var(--border); border-radius: 12px;
    padding: 16px 18px; display: flex; align-items: center; gap: 14px;
    box-shadow: var(--shadow-sm); transition: box-shadow 0.2s, transform 0.2s;
}
.stat-card:hover { box-shadow: var(--shadow-md); transform: translateY(-1px); }
.stat-icon { width: 42px; height: 42px; border-radius: 10px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
.stat-icon svg { width: 20px; height: 20px; }
.stat-icon.blue   { background: rgba(59,130,246,0.1); color: #3b82f6; }
.stat-icon.green  { background: var(--green-dim); color: var(--green); }
.stat-icon.purple { background: rgba(139,92,246,0.1); color: #8b5cf6; }
.stat-icon.accent { background: var(--accent-dim); color: var(--accent); }
.stat-icon.rose   { background: #ffe4e6; color: #e11d48; }
.stat-value { font-size: 20px; font-weight: 800; color: var(--text-1); letter-spacing: -0.03em; }
.stat-label { font-size: 11.5px; color: var(--text-3); font-weight: 500; margin-top: 2px; }
@media (max-width: 1200px) { .stats-bar { grid-template-columns: repeat(3, 1fr); } }
@media (max-width: 900px) { .stats-bar { grid-template-columns: repeat(2, 1fr); } }
@media (max-width: 600px)  { .stats-bar { grid-template-columns: 1fr; } }

/* ── Status Tabs ─────────────────────────────── */
.tabs-wrap {
    display: flex; gap: 4px; background: var(--surface); padding: 4px;
    border-radius: 12px; margin-bottom: 20px; overflow-x: auto;
    scrollbar-width: none; border: 1px solid var(--border);
}
.tabs-wrap::-webkit-scrollbar { display: none; }
.tab-btn {
    padding: 8px 16px; font-size: 13px; font-weight: 700; color: var(--text-3);
    border-radius: 9px; cursor: pointer; transition: all 0.2s; text-decoration: none;
    white-space: nowrap; display: inline-flex; align-items: center; gap: 8px;
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
.filter-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 15px;
    align-items: flex-end;
}
@media (max-width: 1024px) { .filter-grid { grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); } }
@media (max-width: 600px)  { .filter-grid { grid-template-columns: 1fr; } }

.form-group { display: flex; flex-direction: column; min-width: 0; }
.form-label {
    display: block; font-size: 11.5px; font-weight: 700; color: var(--text-3);
    margin-bottom: 6px; text-transform: uppercase; letter-spacing: 0.05em;
}
.form-input {
    width: 100%; padding: 9px 13px; border: 1px solid var(--border); border-radius: 9px;
    font-family: var(--font); font-size: 13px; color: var(--text-1); background: var(--surface);
    outline: none; transition: border-color 0.15s, box-shadow 0.15s; box-sizing: border-box;
}
.form-input:focus { border-color: var(--accent); box-shadow: 0 0 0 3px var(--accent-glow); }
.form-input::placeholder { color: var(--text-4); }

.filter-actions {
    display: flex; justify-content: flex-end; align-items: center;
    gap: 12px; margin-top: 16px; padding-top: 16px;
    border-top: 1px solid var(--border); min-height: 45px;
}
@media (max-width: 600px) {
    .filter-actions { flex-direction: column-reverse; align-items: stretch; gap: 10px; min-height: auto; }
    .filter-actions .btn-primary,
    .filter-actions .btn-outline-reset { justify-content: center; width: 100%; padding: 11px; }
}

.btn-primary {
    display: inline-flex; align-items: center; gap: 7px;
    background: var(--accent); color: #fff; border: none;
    padding: 9px 18px; border-radius: 9px; font-family: var(--font);
    font-weight: 700; font-size: 13px; cursor: pointer; text-decoration: none;
    transition: all 0.15s;
    box-shadow: 0 2px 8px color-mix(in srgb, var(--accent) 30%, transparent);
    white-space: nowrap;
}
.btn-primary:hover { opacity: 0.9; transform: translateY(-1px); }
.btn-primary svg { width: 14px; height: 14px; }

.btn-outline-reset {
    display: inline-flex; align-items: center; gap: 7px;
    background: var(--red-dim); color: var(--red);
    border: 1px solid rgba(220,38,38,0.2);
    padding: 9px 16px; border-radius: 9px; font-family: var(--font);
    font-weight: 700; font-size: 13px; cursor: pointer; text-decoration: none;
    transition: all 0.15s;
}
.btn-outline-reset:hover {
    border-color: rgba(220,38,38,0.4);
    background: color-mix(in srgb, var(--red-dim) 80%, var(--red));
}
.btn-outline-reset svg { width: 13px; height: 13px; }

/* ── Table Card ──────────────────────────────── */
.table-card {
    background: var(--panel); border: 1px solid var(--border); border-radius: 14px;
    overflow: hidden; box-shadow: var(--shadow-sm); animation: rise 0.35s ease both;
}
.table-responsive { overflow-x: auto; }
.product-table { width: 100% !important; border-collapse: collapse; }
.product-table th {
    background: var(--surface); padding: 12px 16px; text-align: left;
    font-size: 10.5px; font-weight: 700; color: var(--text-3);
    border-bottom: 1px solid var(--border); white-space: nowrap;
    text-transform: uppercase; letter-spacing: 0.08em;
}
.product-table th.center { text-align: center; }
.product-table td { padding: 13px 16px; border-bottom: 1px solid var(--border); vertical-align: middle; }
.product-table tr:last-child td { border-bottom: none; }
.product-table tbody tr { transition: background 0.12s; }
.product-table tbody tr:hover td { background: color-mix(in srgb, var(--accent) 3%, var(--surface)); }

/* ── Cells ───────────────────────────────────── */
.cell-no { text-align: center; color: var(--text-4); font-weight: 600; font-size: 12px; }
.order-id { font-weight: 800; color: var(--text-1); display: block; font-size: 13px; }
.store-tag {
    display: inline-flex; align-items: center; gap: 4px;
    font-size: 11px; color: var(--accent); font-weight: 700; margin-top: 3px;
    background: var(--accent-dim); padding: 2px 7px; border-radius: 5px;
    border: 1px solid color-mix(in srgb, var(--accent) 20%, transparent);
}
.resi-val {
    font-family: var(--mono); font-weight: 800; color: var(--accent);
    background: var(--accent-dim); padding: 2px 7px; border-radius: 5px; font-size: 12px;
    border: 1px solid color-mix(in srgb, var(--accent) 20%, transparent);
}
.last-update { font-size: 12px; color: var(--text-2); font-weight: 600; }
.update-admin { font-size: 11px; color: var(--text-4); font-weight: 600; margin-top: 2px; }

.badge { display: inline-flex; align-items: center; gap: 5px; padding: 4px 11px; border-radius: 20px; font-size: 11.5px; font-weight: 600; white-space: nowrap; }
.badge::before { content: ''; width: 6px; height: 6px; border-radius: 50%; flex-shrink: 0; }
.badge-perlu_diproses { background: rgba(59,130,246,0.08); color: #3b82f6; border: 1px solid rgba(59,130,246,0.2); }
.badge-perlu_diproses::before { background: #3b82f6; }
.badge-processing { background: rgba(139,92,246,0.08); color: #8b5cf6; border: 1px solid rgba(139,92,246,0.2); }
.badge-processing::before { background: #8b5cf6; }
.badge-shipping   { background: var(--accent-dim); color: var(--accent); border: 1px solid rgba(99,102,241,0.2); }
.badge-shipping::before { background: var(--accent); }
.badge-completed  { background: var(--green-dim); color: var(--green); border: 1px solid rgba(22,163,74,0.2); }
.badge-completed::before { background: var(--green); }
.badge-refunded { background: #ffe4e6; color: #e11d48; border: 1px solid rgba(225,29,72,0.2); }
.badge-refunded::before { background: #e11d48; }

/* ── Actions ─────────────────────────────────── */
.actions-cell { display: flex; gap: 6px; justify-content: center; }
.btn-icon-only {
    width: 32px; height: 32px; border-radius: 8px; display: flex; align-items: center;
    justify-content: center; background: var(--surface-2); color: var(--text-2);
    border: 1px solid var(--border); transition: all 0.2s; text-decoration: none; cursor: pointer;
}
.btn-icon-only:hover { background: var(--panel); border-color: var(--accent); color: var(--accent); }
.btn-icon-only svg { width: 14px; height: 14px; }

/* ── Modal ───────────────────────────────────── */
.modal-overlay {
    position: fixed; inset: 0; background: rgba(15,23,42,0.45); backdrop-filter: blur(4px);
    z-index: 2000; display: none; align-items: center; justify-content: center;
    opacity: 0; transition: opacity 0.2s;
}
.modal-overlay.open { opacity: 1; display: flex; }
.modal-box {
    background: var(--panel); border-radius: 16px;
    width: 520px; max-width: 92vw; max-height: 88vh;
    box-shadow: var(--shadow-lg); overflow: hidden; display: flex; flex-direction: column;
    transform: scale(0.95) translateY(10px); transition: transform 0.2s;
}
.modal-overlay.open .modal-box { transform: scale(1) translateY(0); }

/* ── DataTables Overrides ────────────────────── */
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
.fade-in-animated { animation: rise 0.35s ease both; }
@keyframes spin { to { transform: rotate(360deg); } }
@endsection

@section('content')

{{-- Page Header --}}
<div class="page-header">
    <div class="page-header-left">
        <h1>
            <span class="page-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <polygon points="1 6 1 22 8 18 16 22 23 18 23 2 16 6 8 2 1 6"/>
                    <line x1="8" y1="2" x2="8" y2="18"/>
                    <line x1="16" y1="6" x2="16" y2="22"/>
                </svg>
            </span>
            Monitoring Pengiriman
        </h1>
        <p>Pantau alur logistik dan riwayat status pesanan secara real-time.</p>
    </div>
</div>

{{-- Stats Bar --}}
<div class="stats-bar">
    <div class="stat-card">
        <div class="stat-icon blue">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
        </div>
        <div>
            <div class="stat-value">{{ number_format($tabCounts['perlu_diproses']) }}</div>
            <div class="stat-label">Perlu Diproses</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon purple">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/><polyline points="3.27 6.96 12 12.01 20.73 6.96"/><line x1="12" y1="22.08" x2="12" y2="12"/></svg>
        </div>
        <div>
            <div class="stat-value">{{ number_format($tabCounts['processing']) }}</div>
            <div class="stat-label">Dikemas</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon accent">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect x="1" y="3" width="15" height="13"/><polygon points="16 8 20 8 23 11 23 16 16 16 16 8"/><circle cx="5.5" cy="18.5" r="2.5"/><circle cx="18.5" cy="18.5" r="2.5"/></svg>
        </div>
        <div>
            <div class="stat-value">{{ number_format($tabCounts['shipping']) }}</div>
            <div class="stat-label">Dikirim</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon green">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
        </div>
        <div>
            <div class="stat-value">{{ number_format($tabCounts['completed']) }}</div>
            <div class="stat-label">Selesai</div>
        </div>
    </div>
</div>

{{-- Status Tabs --}}
<div class="tabs-wrap">
    @php
        function deliveryTabUrl(string $tabName): string {
            $query        = request()->query();
            $query['tab'] = $tabName;
            return url()->current() . '?' . http_build_query($query);
        }
    @endphp
    <a href="{{ deliveryTabUrl('all') }}" class="tab-btn {{ $tab === 'all' ? 'active' : '' }}">
        Semua <span class="tab-count">{{ $tabCounts['all'] }}</span>
    </a>
    <a href="{{ deliveryTabUrl('perlu_diproses') }}" class="tab-btn {{ $tab === 'perlu_diproses' ? 'active' : '' }}">
        Perlu Diproses <span class="tab-count">{{ $tabCounts['perlu_diproses'] }}</span>
    </a>
    <a href="{{ deliveryTabUrl('processing') }}" class="tab-btn {{ $tab === 'processing' ? 'active' : '' }}">
        Dikemas <span class="tab-count">{{ $tabCounts['processing'] }}</span>
    </a>
    <a href="{{ deliveryTabUrl('shipping') }}" class="tab-btn {{ $tab === 'shipping' ? 'active' : '' }}">
        Dikirim <span class="tab-count">{{ $tabCounts['shipping'] }}</span>
    </a>
    <a href="{{ deliveryTabUrl('completed') }}" class="tab-btn {{ $tab === 'completed' ? 'active' : '' }}">
        Selesai <span class="tab-count">{{ $tabCounts['completed'] }}</span>
    </a>
</div>

{{-- Filter Card --}}
<div class="filter-card">
    <div class="filter-card-top">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width:15px;height:15px;color:var(--text-3);">
            <polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"/>
        </svg>
        <span class="filter-card-title">Filter Pengiriman</span>
    </div>

    <form method="GET" action="{{ route('deliveries.index') }}" novalidate>
        <input type="hidden" id="dtFilterTab" name="tab" value="{{ $tab }}">

        <div class="filter-grid">

            {{-- Toko (active only) --}}
            <div class="form-group">
                <label class="form-label">Toko</label>
                <select name="store_id" id="dtFilterStore" class="form-input">
                    <option value="">Semua Toko</option>
                    @foreach($stores as $s)
                        <option value="{{ $s->id }}" {{ request('store_id') == $s->id ? 'selected' : '' }}>
                            {{ $s->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Kurir (from master couriers table) --}}
            <div class="form-group">
                <label class="form-label">Kurir</label>
                <select name="courier" id="dtFilterCourier" class="form-input">
                    <option value="">Semua Kurir</option>
                    @foreach($couriers as $courier)
                        <option value="{{ $courier->name }}"
                            {{ request('courier') === $courier->name ? 'selected' : '' }}>
                            {{ strtoupper($courier->name) }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Tanggal Update Terakhir --}}
            <div class="form-group">
                <label class="form-label">Tanggal Update</label>
                <input type="date" name="date" id="dtFilterDate" class="form-input" value="{{ request('date') }}">
            </div>

        </div>

        <div class="filter-actions">
            <a href="javascript:void(0)" id="btnResetFilter" class="btn-outline-reset"
               style="{{ request()->hasAny(['store_id', 'courier', 'date']) ? '' : 'display:none;' }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/>
                </svg>
                Reset Filter
            </a>
            <button type="button" id="btnApplyFilter" class="btn-primary">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
                </svg>
                Terapkan Filter
            </button>
        </div>
    </form>
</div>

{{-- Table Card --}}
<div class="table-card">
    <div class="table-responsive">
        <table class="product-table" id="deliveryTable">
            <thead>
                <tr>
                    <th class="center" style="width:50px;">No</th>
                    <th>Pesanan &amp; Toko</th>
                    <th>Logistik &amp; Kurir</th>
                    <th>Nomor Resi</th>
                    <th>Update Terakhir</th>
                    <th>Status</th>
                    <th class="center" style="width:90px;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @include('deliveries._table_rows')
            </tbody>
        </table>
    </div>
</div>

{{-- Tracking Modal --}}
<div id="trackingModal" class="modal-overlay">
    <div class="modal-box" id="trackingModalBox">
        <div id="trackingModalContent"></div>
    </div>
</div>

@endsection

@push('scripts')
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<link rel="stylesheet" href="https://cdn.datatables.net/2.3.7/css/dataTables.dataTables.min.css">
<script src="https://cdn.datatables.net/2.3.7/js/dataTables.min.js"></script>

<script>
    $.fn.dataTable.ext.errMode = 'none';

    // ── Tracking Modal ────────────────────────────────────────────────────
    function openTrackingModal(url) {
        const $modal   = $('#trackingModal');
        const $content = $('#trackingModalContent');

        $modal.addClass('open');
        $content.html(`
            <div style="padding:50px;text-align:center;">
                <div style="width:40px;height:40px;border:3px solid var(--accent-dim);border-top-color:var(--accent);border-radius:50%;animation:spin 0.8s linear infinite;margin:0 auto 15px;"></div>
                <p style="font-size:13px;color:var(--text-3);font-weight:600;">Memuat data tracking...</p>
            </div>
        `);

        $.get(url, function(html) {
            $content.html(html);
        }).fail(function() {
            $content.html(`
                <div style="padding:40px;text-align:center;">
                    <p style="color:var(--red);font-weight:700;">Gagal memuat data.</p>
                    <button onclick="closeTrackingModal()" class="btn-primary" style="margin-top:10px;">Tutup</button>
                </div>
            `);
        });
    }

    function closeTrackingModal() {
        $('#trackingModal').removeClass('open');
    }

    // ── DataTable ─────────────────────────────────────────────────────────
    let table;

    function initDataTable() {
        return $('#deliveryTable').DataTable({
            responsive: true,
            autoWidth: false,
            language: {
                search: "",
                searchPlaceholder: "Cari Data...",
                lengthMenu: "Tampilkan _MENU_ data",
                info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
                infoEmpty: "Tidak ada data untuk ditampilkan",
                zeroRecords: `
                    <div class="empty-state" style="padding: 40px 20px; text-align: center; display: flex; flex-direction: column; align-items: center; justify-content: center; margin: 0 auto; width: 100%; border-bottom: none;">
                        <div class="empty-icon" style="margin-bottom: 20px; width: 88px; height: 88px; background: color-mix(in srgb, var(--accent) 10%, transparent); border-radius: 24px; display: flex; align-items: center; justify-content: center; border: 1px solid color-mix(in srgb, var(--accent) 15%, transparent);">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" style="width: 38px; height: 38px; color: var(--accent); opacity: 0.9;">
                                <polygon points="1 6 1 22 8 18 16 22 23 18 23 2 16 6 8 2 1 6"></polygon>
                                <line x1="8" y1="2" x2="8" y2="18"></line>
                                <line x1="16" y1="6" x2="16" y2="22"></line>
                            </svg>
                        </div>
                        <div class="empty-title" style="margin-bottom: 6px; font-size: 18px; font-weight: 800; color: var(--text-1); letter-spacing: -0.02em;">Tidak Ada Pengiriman Ditemukan</div>
                        <div class="empty-desc" style="font-size: 13px; color: var(--text-3);">Tidak ada data pengiriman yang cocok dengan pencarian atau filter yang dipilih.</div>
                    </div>`,
                emptyTable: `
                    <div class="empty-state" style="padding: 40px 20px; text-align: center; display: flex; flex-direction: column; align-items: center; justify-content: center; margin: 0 auto; width: 100%; border-bottom: none;">
                        <div class="empty-icon" style="margin-bottom: 20px; width: 88px; height: 88px; background: color-mix(in srgb, var(--accent) 10%, transparent); border-radius: 24px; display: flex; align-items: center; justify-content: center; border: 1px solid color-mix(in srgb, var(--accent) 15%, transparent);">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" style="width: 38px; height: 38px; color: var(--accent); opacity: 0.9;">
                                <polygon points="1 6 1 22 8 18 16 22 23 18 23 2 16 6 8 2 1 6"></polygon>
                                <line x1="8" y1="2" x2="8" y2="18"></line>
                                <line x1="16" y1="6" x2="16" y2="22"></line>
                            </svg>
                        </div>
                        <div class="empty-title" style="margin-bottom: 6px; font-size: 18px; font-weight: 800; color: var(--text-1); letter-spacing: -0.02em;">Tidak Ada Pengiriman Ditemukan</div>
                        <div class="empty-desc" style="font-size: 13px; color: var(--text-3);">Belum ada data pengiriman yang tersedia.</div>
                    </div>`,
                paginate: {
                    first: "«",
                    last: "»",
                    next: "›",
                    previous: "‹"
                }
            },
            columnDefs: [
                { targets: 0, orderable: false, searchable: false },
                { targets: 6, orderable: false, searchable: false }
            ],
            order: [[4, 'desc']],
            drawCallback: function() {
                let api = this.api();
                let startIndex = api.page.info().start;
                api.column(0, { page: 'current' }).nodes().each(function(cell, i) {
                    cell.innerHTML = startIndex + i + 1;
                });
            }
        });
    }

    $(document).ready(function() {
        table = initDataTable();

        // ── Apply Filter (AJAX) ───────────────────────────────────────────
        $('#btnApplyFilter').on('click', function() {
            const params = {
                tab:      $('#dtFilterTab').val(),
                store_id: $('#dtFilterStore').val(),
                courier:  $('#dtFilterCourier').val(),
                date:     $('#dtFilterDate').val(),
            };

            $('#deliveryTable tbody').css('opacity', '0.4');

            $.ajax({
                url:  "{{ route('deliveries.index') }}",
                type: 'GET',
                data: params,
                success: function(res) {
                    if ($.fn.DataTable.isDataTable('#deliveryTable')) {
                        table.destroy();
                    }
                    $('#deliveryTable tbody').html(res.html || res);
                    $('#deliveryTable tbody tr').addClass('fade-in-animated');
                    table = initDataTable();
                    $('#deliveryTable tbody').css('opacity', '1');

                    // Update Tab Counts
                    if(res.counts) {
                        $('.tab-btn').each(function() {
                            const href = $(this).attr('href');
                            const match = href.match(/tab=([^&]+)/);
                            const tabName = match ? match[1] : 'all';
                            const countVal = res.counts[tabName] !== undefined ? res.counts[tabName] : 0;
                            $(this).find('.tab-count').text(countVal.toLocaleString('id-ID'));
                        });

                        // Update Stat Bar Counts
                        const statVals = $('.stat-value');
                        if(statVals.length >= 4) {
                            $(statVals[0]).text((res.counts['perlu_diproses'] || 0).toLocaleString('id-ID'));
                            $(statVals[1]).text((res.counts['processing'] || 0).toLocaleString('id-ID'));
                            $(statVals[2]).text((res.counts['shipping'] || 0).toLocaleString('id-ID'));
                            $(statVals[3]).text((res.counts['completed'] || 0).toLocaleString('id-ID'));
                        }
                    }

                    checkResetVisibility();
                },
                error: function() {
                    $('#deliveryTable tbody').css('opacity', '1');
                }
            });
        });

        // ── Realtime Notification Listener ──
        document.addEventListener('realtime-notification', function(e) {
            $('#btnApplyFilter').trigger('click');
        });

        // ── Reset Filter ──────────────────────────────────────────────────
        $('#btnResetFilter').on('click', function() {
            $('#dtFilterStore').val('');
            $('#dtFilterCourier').val('');
            $('#dtFilterDate').val('');
            if (table) { table.search('').draw(); }
            $('#btnApplyFilter').trigger('click');
        });

        function checkResetVisibility() {
            const hasFilter = $('#dtFilterStore').val()
                || $('#dtFilterCourier').val()
                || $('#dtFilterDate').val();

            if (hasFilter) {
                $('#btnResetFilter').fadeIn(200).css('display', 'inline-flex');
            } else {
                $('#btnResetFilter').fadeOut(200);
            }
        }

        // ── Modal close behaviours ────────────────────────────────────────
        $(document).on('keydown', function(e) {
            if (e.key === 'Escape') closeTrackingModal();
        });

        $('#trackingModal').on('click', function(e) {
            if (e.target === this) closeTrackingModal();
        });
    });
</script>
@endpush
