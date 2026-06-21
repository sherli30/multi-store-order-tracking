@extends('layouts.app')

@section('title', 'Kelola Tarif Ongkos Kirim')

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
        grid-template-columns: repeat(3, 1fr);
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
    .stat-icon.blue   { background: rgba(59, 130, 246, 0.1); color: #3b82f6; }
    .stat-icon.purple { background: rgba(139, 92, 246, 0.1); color: #8b5cf6; }
    .stat-icon.amber  { background: var(--amber-dim); color: var(--amber); }
    .stat-icon.green  { background: var(--green-dim); color: var(--green); }
    .stat-value { font-size: 20px; font-weight: 800; color: var(--text-1); letter-spacing: -0.03em; }
    .stat-label { font-size: 11.5px; color: var(--text-3); font-weight: 500; margin-top: 2px; }
    @media (max-width: 700px) { .stats-bar { grid-template-columns: 1fr 1fr; } }

    /* ── Filter Card ─────────────────────────────── */
    .filter-card {
        background: var(--panel); border: 1px solid var(--border); border-radius: 14px;
        padding: 20px 22px; box-shadow: var(--shadow-sm); margin-bottom: 20px;
    }
    .filter-card-top { display: flex; align-items: center; gap: 8px; margin-bottom: 16px; }
    .filter-card-title { font-size: 13px; font-weight: 700; color: var(--text-1); }

    .filter-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 15px; align-items: flex-end; }
    @media (max-width: 1024px) { .filter-grid { grid-template-columns: 1fr 1fr; } }
    @media (max-width: 560px)  { .filter-grid { grid-template-columns: 1fr; } }

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
        display: flex; justify-content: flex-end; align-items: center; gap: 12px; margin-top: 16px;
        padding-top: 16px; border-top: 1px solid var(--border); min-height: 45px;
    }
    @media (max-width: 600px) {
        .filter-actions { flex-direction: column-reverse; align-items: stretch; gap: 10px; min-height: auto; }
        .filter-actions .btn-primary,
        .filter-actions .btn-outline-reset { justify-content: center; width: 100%; padding: 11px; }
    }

    /* ── Buttons ─────────────────────────────────── */
    .btn-primary {
        display: inline-flex; align-items: center; gap: 7px;
        background: var(--accent); color: #fff; border: none;
        padding: 9px 18px; border-radius: 9px;
        font-family: var(--font); font-weight: 700; font-size: 13px;
        cursor: pointer; text-decoration: none; transition: all 0.15s;
        box-shadow: 0 2px 8px color-mix(in srgb, var(--accent) 30%, transparent);
        white-space: nowrap; line-height: 1.2; height: 38px; box-sizing: border-box;
    }
    .btn-primary:hover { opacity: 0.9; transform: translateY(-1px); }
    .btn-primary svg { width: 14px; height: 14px; }

    .btn-outline-reset {
        display: inline-flex; align-items: center; gap: 7px;
        background: var(--red-dim); color: var(--red);
        border: 1px solid rgba(220, 38, 38, 0.2); padding: 9px 16px;
        border-radius: 9px; font-family: var(--font); font-weight: 700; font-size: 13px;
        cursor: pointer; text-decoration: none; transition: all 0.15s;
    }
    .btn-outline-reset:hover {
        border-color: rgba(220, 38, 38, 0.4);
        background: color-mix(in srgb, var(--red-dim) 80%, var(--red));
        color: var(--red);
    }
    .btn-outline-reset svg { width: 13px; height: 13px; }

    /* ── Table Card ──────────────────────────────── */
    .table-card {
        background: var(--panel); border: 1px solid var(--border); border-radius: 14px;
        overflow: hidden; box-shadow: var(--shadow-sm); animation: rise 0.35s ease both;
    }
    .table-responsive { overflow-x: auto; }
    .data-table { width: 100%; border-collapse: collapse; }
    .data-table th {
        background: var(--surface); padding: 12px 16px; text-align: left;
        font-size: 10.5px; font-weight: 700; color: var(--text-3);
        border-bottom: 1px solid var(--border); white-space: nowrap;
        text-transform: uppercase; letter-spacing: 0.08em;
    }
    .data-table th.center { text-align: center; }
    .data-table td { padding: 13px 16px; border-bottom: 1px solid var(--border); vertical-align: middle; }
    .data-table tr:last-child td { border-bottom: none; }
    .data-table tbody tr { transition: background 0.12s; }
    .data-table tbody tr:hover td { background: color-mix(in srgb, var(--accent) 3%, var(--surface)); }

    /* ── Cells ───────────────────────────────────── */
    .cell-no { text-align: center; color: var(--text-4); font-weight: 600; font-size: 12px; }
    .service-info { font-weight: 700; color: var(--text-1); }
    .service-badge { font-size: 11px; font-weight: 600; color: var(--accent); display: block; margin-top: 2px; }

    .route-info { display: flex; align-items: center; gap: 10px; }
    .route-dot { width: 8px; height: 8px; border-radius: 50%; flex-shrink: 0; }
    .origin-dot { background: #3b82f6; box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.15); }
    .dest-dot   { background: #f59e0b; box-shadow: 0 0 0 3px rgba(245, 158, 11, 0.15); }

    .cost-value { font-size: 14px; font-weight: 800; color: var(--text-1); }
    .cost-unit  { font-size: 10.5px; color: var(--text-4); margin-top: 2px; }

    .etd-badge {
        background: var(--surface-2); padding: 4px 10px; border-radius: 6px;
        display: inline-flex; align-items: center; gap: 6px;
        font-size: 12px; font-weight: 700; color: var(--text-2);
        border: 1px solid var(--border);
    }

    /* ── Actions ─────────────────────────────────── */
    .actions-cell { display: flex; gap: 6px; justify-content: center; }
    .btn-sm {
        display: inline-flex; align-items: center; gap: 5px;
        border: 1px solid var(--border); border-radius: 7px;
        font-family: var(--font); font-size: 11.5px; font-weight: 600;
        padding: 6px 12px; cursor: pointer; transition: all 0.15s;
        background: var(--panel); color: var(--text-2);
        text-decoration: none; white-space: nowrap;
    }
    .btn-sm svg { width: 12px; height: 12px; }
    .btn-sm:hover { border-color: var(--accent); color: var(--accent); background: color-mix(in srgb, var(--accent) 5%, var(--panel)); }
    .btn-sm.danger:hover { border-color: rgba(220, 38, 38, 0.4); color: var(--red); background: var(--red-dim); }

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

    /* ── Modals ──────────────────────────────────── */
    .modal-overlay {
        position: fixed; inset: 0; background: rgba(15, 23, 42, 0.45); backdrop-filter: blur(4px);
        z-index: 200; display: flex; align-items: center; justify-content: center;
        opacity: 0; visibility: hidden; transition: all 0.2s;
    }
    .modal-overlay.open { opacity: 1; visibility: visible; }
    .modal-box {
        background: var(--panel); border-radius: 16px; padding: 28px;
        width: 450px; max-width: 90vw; box-shadow: var(--shadow-lg);
        transform: scale(0.95) translateY(10px); transition: transform 0.2s;
    }
    .modal-overlay.open .modal-box { transform: scale(1) translateY(0); }
    .modal-title { font-size: 18px; font-weight: 800; color: var(--text-1); margin-bottom: 20px; }

    .modal-box-form {
        width: 480px !important; padding: 32px !important;
        border-radius: 20px !important; text-align: left !important;
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25) !important;
    }
    .form-modal-field { margin-bottom: 20px; }
    .form-modal-label {
        font-size: 11px; font-weight: 700; color: var(--text-3);
        text-transform: uppercase; letter-spacing: 0.06em;
        margin-bottom: 8px; display: block; text-align: left;
    }
    .form-modal-label span { color: var(--red); margin-left: 2px; }
    .form-modal-input {
        width: 100%; padding: 11px 16px; border: 1.5px solid var(--border); border-radius: 10px;
        font-family: var(--font); font-size: 14px; color: var(--text-1); background: var(--surface);
        outline: none; transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1); box-sizing: border-box;
    }
    .form-modal-input:focus {
        border-color: var(--accent); background: var(--panel);
        box-shadow: 0 0 0 4px color-mix(in srgb, var(--accent) 15%, transparent);
    }
    .form-modal-input.is-invalid {
        border-color: var(--red);
        background: color-mix(in srgb, var(--red) 2%, var(--panel));
    }
    .modal-icon { width: 48px; height: 48px; border-radius: 14px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
    .modal-icon svg { width: 22px; height: 22px; }
    .modal-icon-blue   { background: rgba(59, 130, 246, 0.1) !important; }
    .modal-icon-blue svg { color: #3b82f6 !important; }
    .modal-icon-amber  { background: rgba(245, 158, 11, 0.1) !important; }
    .modal-icon-amber svg { color: #f59e0b !important; }

    .btn-cancel {
        padding: 9px 18px; border: 1px solid var(--border); border-radius: 8px;
        font-family: var(--font); font-size: 13px; font-weight: 600;
        background: var(--surface); color: var(--text-2); cursor: pointer; transition: all 0.15s;
    }
    .btn-cancel:hover { border-color: var(--border-2); color: var(--text-1); }

    .toggle-card {
        background: var(--surface); border: 1px solid var(--border); border-radius: 12px;
        padding: 14px 18px; display: flex; align-items: center; justify-content: space-between;
        margin-bottom: 24px; transition: all 0.2s; cursor: pointer; user-select: none;
    }
    .toggle-card.active { border-color: color-mix(in srgb, var(--accent) 30%, transparent); background: color-mix(in srgb, var(--accent) 3%, var(--surface)); }
    .toggle-info { display: flex; flex-direction: column; gap: 4px; }
    .toggle-title { font-size: 13px; font-weight: 700; color: var(--text-1); }
    .toggle-desc { font-size: 11.5px; color: var(--text-3); font-weight: 500; }
    .custom-switch { position: relative; display: inline-block; width: 44px; height: 24px; flex-shrink: 0; }
    .custom-switch input { opacity: 0; width: 0; height: 0; }
    .slider {
        position: absolute; cursor: pointer; inset: 0; background-color: var(--border-2);
        transition: .3s; border-radius: 24px;
    }
    .slider:before {
        position: absolute; content: ""; height: 18px; width: 18px; left: 3px; bottom: 3px;
        background-color: white; transition: .3s; border-radius: 50%;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
    .custom-switch input:checked + .slider { background-color: var(--accent); }
    .custom-switch input:checked + .slider:before { transform: translateX(20px); }

    /* ── Delete Modal ────────────────────────────── */
    .modal-box-delete { width: 380px !important; padding: 28px !important; text-align: left !important; }
    .modal-box-delete .modal-title { font-size: 16px !important; margin-bottom: 6px !important; }
    .modal-icon-red {
        width: 52px !important; height: 52px !important;
        background: rgba(239, 68, 68, 0.1) !important;
        border-radius: 14px !important; display: flex !important;
        align-items: center !important; justify-content: center !important;
        margin: 0 0 16px 0 !important;
    }
    .modal-icon-red svg { color: #ef4444 !important; width: 24px !important; height: 24px !important; }
    .modal-desc { font-size: 13px; color: var(--text-2); margin-bottom: 22px; line-height: 1.6; text-align: left; }
    .btn-danger-confirm {
        padding: 9px 18px; border: none; border-radius: 8px;
        font-family: var(--font); font-size: 13px; font-weight: 600;
        background: var(--red); color: #fff; cursor: pointer; transition: all 0.15s;
        box-shadow: 0 2px 8px rgba(220, 38, 38, 0.25);
    }
    .btn-danger-confirm:hover { background: #b91c1c; transform: translateY(-1px); }

    /* Hide number input spinners */
    input::-webkit-outer-spin-button,
    input::-webkit-inner-spin-button { -webkit-appearance: none; margin: 0; }
    input[type=number] { -moz-appearance: textfield; }

    @keyframes rise { from { opacity: 0; transform: translateY(12px); } to { opacity: 1; transform: translateY(0); } }
    @keyframes fadeInRow { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
    .fade-in-animated { animation: fadeInRow 0.4s ease forwards; }
@endsection

@section('content')
<div class="page-header">
    <div class="page-header-left">
        <h1>
            <span class="page-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z"></path>
                    <line x1="7" y1="7" x2="7.01" y2="7"></line>
                </svg>
            </span>
            Kelola Ongkir
        </h1>
        <p>Atur biaya pengiriman berdasarkan rute dan layanan logistik.</p>
    </div>
    <button class="btn-primary" onclick="openModal()">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
            <line x1="12" y1="5" x2="12" y2="19"></line>
            <line x1="5" y1="12" x2="19" y2="12"></line>
        </svg>
        Tambah Ongkir
    </button>
</div>

{{-- Stats Bar --}}
<div class="stats-bar">
    <div class="stat-card">
        <div class="stat-icon blue">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="12" cy="12" r="10"></circle>
                <line x1="2" y1="12" x2="22" y2="12"></line>
                <path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"></path>
            </svg>
        </div>
        <div>
            <div class="stat-value">{{ $rates->count() }}</div>
            <div class="stat-label">Total Rute Tarif</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon green">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                <rect x="1" y="4" width="22" height="16" rx="2" ry="2"></rect>
                <line x1="1" y1="10" x2="23" y2="10"></line>
            </svg>
        </div>
        <div>
            <div class="stat-value">Rp {{ number_format($rates->avg('cost_per_kg') ?? 0, 0, ',', '.') }}</div>
            <div class="stat-label">Rata-rata Biaya / kg</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon purple">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                <rect x="1" y="3" width="15" height="13" rx="2" ry="2"></rect>
                <polygon points="16 8 20 8 23 11 23 16 16 16 16 8"></polygon>
                <circle cx="5.5" cy="18.5" r="2.5"></circle>
                <circle cx="18.5" cy="18.5" r="2.5"></circle>
            </svg>
        </div>
        <div>
            <div class="stat-value">{{ $services->count() }}</div>
            <div class="stat-label">Layanan Kurir Aktif</div>
        </div>
    </div>
</div>

{{-- Filter Card --}}
<div class="filter-card">
    <div class="filter-card-top">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="width:16px;height:16px;color:var(--text-3);">
            <polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3" />
        </svg>
        <div class="filter-card-title">Filter Ongkir</div>
    </div>
    <div class="filter-grid">
        <div class="form-group">
            <label class="form-label" for="dtFilterService">Layanan / Kurir</label>
            <select id="dtFilterService" class="form-input">
                <option value="">Semua Layanan</option>
                @foreach ($services->where('is_active', true)->filter(fn($s) => $s->courier?->is_active) as $service)
                    <option value="{{ $service->id }}">{{ $service->courier->name ?? '' }} - {{ $service->service_name }}</option>
                @endforeach
            </select>
        </div>

        <div class="form-group">
            <label class="form-label" for="dtFilterDestination">Kota Tujuan</label>
            <select id="dtFilterDestination" class="form-input">
                <option value="">Semua Kota Tujuan</option>
                @foreach ($cities as $city)
                    <option value="{{ $city->id }}">{{ $city->name }}</option>
                @endforeach
            </select>
        </div>
    </div>
    <div class="filter-actions">
        <a href="javascript:void(0)" id="btnResetFilter" class="btn-outline-reset" style="display:none;">
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
</div>

<div class="table-card">
    <div class="table-responsive">
        <table class="data-table" id="rateTable">
            <thead>
                <tr>
                    <th class="center" style="width:50px;">No</th>
                    <th>Layanan</th>
                    <th>Rute (Asal → Tujuan)</th>
                    <th>Biaya / kg</th>
                    <th>Estimasi (ETD)</th>
                    <th class="center" style="width:150px;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @include('shipping-rates._table_rows')
            </tbody>
        </table>
    </div>
</div>

{{-- Rate Form Modal --}}
<div class="modal-overlay" id="rateModal">
    <div class="modal-box modal-box-form">
        <div style="display:flex; align-items:center; gap:16px; margin-bottom:24px;">
            <div class="modal-icon modal-icon-blue" id="modalIcon">
                <svg id="modalIconSvg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="12" y1="5" x2="12" y2="19"></line>
                    <line x1="5" y1="12" x2="19" y2="12"></line>
                </svg>
            </div>
            <div>
                <h2 id="modalTitle" class="modal-title" style="margin-bottom:2px;">Tambah Ongkir Baru</h2>
                <p id="modalSubtitle" style="font-size:12px; color:var(--text-3); margin:0;">Isi detail ongkir pengiriman di bawah ini.</p>
            </div>
        </div>

        <form id="rateForm" method="POST" novalidate>
            @csrf
            <div id="methodField"></div>
            <input type="hidden" name="id" id="rateIdInput">

            <div class="form-modal-field">
                <label class="form-modal-label">Layanan Kurir <span>*</span></label>
                <select name="shipping_service_id" id="serviceId" class="form-modal-input @error('shipping_service_id') is-invalid @enderror" required>
                    <option value="">-- Pilih Layanan --</option>
                    @foreach($services as $service)
                        <option value="{{ $service->id }}" data-active="{{ ($service->is_active && $service->courier && $service->courier->is_active) ? '1' : '0' }}">
                            {{ $service->courier->name }} - {{ $service->service_name }}
                            {{ (!$service->is_active || ($service->courier && !$service->courier->is_active)) ? '(Non-aktif)' : '' }}
                        </option>
                    @endforeach
                </select>
                <div id="serviceInactiveWarning" style="display:none; align-items:flex-start; gap:10px; margin-top:8px; font-size:12px; line-height:1.5; color:var(--red); background:color-mix(in srgb, var(--red) 8%, var(--panel)); border:1.5px solid color-mix(in srgb, var(--red) 20%, transparent); border-radius:10px; padding:12px 14px; text-align:left;">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="width:16px; height:16px; margin-top:2px; flex-shrink:0; color:var(--red);"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path><line x1="12" y1="9" x2="12" y2="13"></line><line x1="12" y1="17" x2="12.01" y2="17"></line></svg>
                    <div>
                        <span style="font-weight:800; display:block; margin-bottom:3px; font-size:12.5px;">Layanan / Kurir Non-Aktif</span>
                        Data tarif ongkir ini tetap dapat diperbarui, tetapi tidak akan aktif secara operasional di sistem karena jenis layanan atau ekspedisi kurir induk sedang dinonaktifkan.
                    </div>
                </div>
                @error('shipping_service_id') <div style="color:var(--red); font-size:11.5px; font-weight:600; margin-top:6px;">{{ $message }}</div> @enderror
            </div>

            <div style="font-size:12px; font-weight:800; color:var(--text-1); margin-bottom:12px; padding-bottom:8px; border-bottom:1px solid var(--border);">Lokasi Asal</div>
            <div style="display:grid; grid-template-columns:1fr 1fr; gap:16px; margin-bottom:20px;">
                <div class="form-modal-field" style="margin-bottom:0;">
                    <label class="form-modal-label">Provinsi Asal <span>*</span></label>
                    <select name="origin_province_id" id="originProvinceId" class="form-modal-input @error('origin_province_id') is-invalid @enderror" required>
                        <option value="">-- Pilih Provinsi --</option>
                        @foreach($provinces as $province)
                            <option value="{{ $province->id }}">{{ $province->name }}</option>
                        @endforeach
                    </select>
                    @error('origin_province_id') <div style="color:var(--red); font-size:11.5px; font-weight:600; margin-top:6px;">{{ $message }}</div> @enderror
                </div>
                <div class="form-modal-field" style="margin-bottom:0;">
                    <label class="form-modal-label">Kota Asal (Origin) <span>*</span></label>
                    <select name="origin_city_id" id="originId" class="form-modal-input @error('origin_city_id') is-invalid @enderror" required>
                        <option value="">-- Pilih Kota --</option>
                        @foreach($cities as $city)
                            <option value="{{ $city->id }}" data-province="{{ $city->province_id }}">{{ $city->full_name }}</option>
                        @endforeach
                    </select>
                    @error('origin_city_id') <div style="color:var(--red); font-size:11.5px; font-weight:600; margin-top:6px;">{{ $message }}</div> @enderror
                </div>
            </div>

            <div style="font-size:12px; font-weight:800; color:var(--text-1); margin-bottom:12px; padding-bottom:8px; border-bottom:1px solid var(--border);">Lokasi Tujuan</div>
            <div style="display:grid; grid-template-columns:1fr 1fr; gap:16px; margin-bottom:20px;">
                <div class="form-modal-field" style="margin-bottom:0;">
                    <label class="form-modal-label">Provinsi Tujuan <span>*</span></label>
                    <select name="destination_province_id" id="destProvinceId" class="form-modal-input @error('destination_province_id') is-invalid @enderror" required>
                        <option value="">-- Pilih Provinsi --</option>
                        @foreach($provinces as $province)
                            <option value="{{ $province->id }}">{{ $province->name }}</option>
                        @endforeach
                    </select>
                    @error('destination_province_id') <div style="color:var(--red); font-size:11.5px; font-weight:600; margin-top:6px;">{{ $message }}</div> @enderror
                </div>
                <div class="form-modal-field" style="margin-bottom:0;">
                    <label class="form-modal-label">Kota Tujuan (Dest) <span>*</span></label>
                    <select name="destination_city_id" id="destId" class="form-modal-input @error('destination_city_id') is-invalid @enderror" required>
                        <option value="">-- Pilih Kota --</option>
                        @foreach($cities as $city)
                            <option value="{{ $city->id }}" data-province="{{ $city->province_id }}">{{ $city->full_name }}</option>
                        @endforeach
                    </select>
                    @error('destination_city_id') <div style="color:var(--red); font-size:11.5px; font-weight:600; margin-top:6px;">{{ $message }}</div> @enderror
                </div>
            </div>

            <div style="font-size:12px; font-weight:800; color:var(--text-1); margin-bottom:12px; padding-bottom:8px; border-bottom:1px solid var(--border);">Tarif & Parameter</div>
            <div style="display:grid; grid-template-columns:1fr 1fr; gap:16px; margin-bottom:16px;">
                <div class="form-modal-field" style="margin-bottom:0;">
                    <label class="form-modal-label">Berat Minimum (kg) <span>*</span></label>
                    <input type="number" step="0.01" name="min_weight" id="minWeight" class="form-modal-input @error('min_weight') is-invalid @enderror" required placeholder="Contoh: 1.00" value="{{ old('min_weight', '1.00') }}">
                    @error('min_weight') <div style="color:var(--red); font-size:11.5px; font-weight:600; margin-top:6px;">{{ $message }}</div> @enderror
                </div>
                <div class="form-modal-field" style="margin-bottom:0;">
                    <label class="form-modal-label">Berat Maksimum (kg)</label>
                    <input type="number" step="0.01" name="max_weight" id="maxWeight" class="form-modal-input @error('max_weight') is-invalid @enderror" placeholder="Boleh dikosongkan" value="{{ old('max_weight') }}">
                    @error('max_weight') <div style="color:var(--red); font-size:11.5px; font-weight:600; margin-top:6px;">{{ $message }}</div> @enderror
                </div>
            </div>

            <div class="form-modal-field">
                <label class="form-modal-label">Biaya per kg (Rp) <span>*</span></label>
                <input type="text" name="cost_per_kg" id="costKg" class="form-modal-input @error('cost_per_kg') is-invalid @enderror" required placeholder="Contoh: 12.000" value="{{ old('cost_per_kg') }}">
                @error('cost_per_kg') <div style="color:var(--red); font-size:11.5px; font-weight:600; margin-top:6px;">{{ $message }}</div> @enderror
            </div>

            <div style="display:grid; grid-template-columns:1fr 1fr; gap:16px; margin-bottom:20px;">
                <div class="form-modal-field" style="margin-bottom:0;">
                    <label class="form-modal-label">Estimasi Min (Hari) <span>*</span></label>
                    <input type="number" name="etd_min" id="etdMin" class="form-modal-input @error('etd_min') is-invalid @enderror" required placeholder="0" value="{{ old('etd_min') }}">
                    @error('etd_min') <div style="color:var(--red); font-size:11.5px; font-weight:600; margin-top:6px;">{{ $message }}</div> @enderror
                </div>
                <div class="form-modal-field" style="margin-bottom:0;">
                    <label class="form-modal-label">Estimasi Max (Hari) <span>*</span></label>
                    <input type="number" name="etd_max" id="etdMax" class="form-modal-input @error('etd_max') is-invalid @enderror" required placeholder="0" value="{{ old('etd_max') }}">
                    @error('etd_max') <div style="color:var(--red); font-size:11.5px; font-weight:600; margin-top:6px;">{{ $message }}</div> @enderror
                </div>
            </div>

            <div class="toggle-card" id="activeToggleCard" onclick="document.getElementById('isActive').click()">
                <div class="toggle-info">
                    <div class="toggle-title">Status Aktif</div>
                    <div class="toggle-desc" id="activeToggleDesc">Tarif pengiriman ini dapat digunakan oleh pesanan.</div>
                </div>
                <label class="custom-switch" onclick="event.stopPropagation()">
                    <input type="checkbox" name="is_active" id="isActive" value="1" {{ old('is_active', '1') == '1' ? 'checked' : '' }}>
                    <span class="slider"></span>
                </label>
            </div>
            @error('is_active') <div style="color:var(--red); font-size:11.5px; font-weight:600; margin-top:-14px; margin-bottom:20px;">{{ $message }}</div> @enderror

            <div style="display:flex; gap:10px; justify-content:flex-end;">
                <button type="button" class="btn-cancel" onclick="closeModal()">Batal</button>
                <button type="submit" class="btn-primary" style="border:none;">Simpan</button>
            </div>
        </form>
    </div>
</div>

{{-- Delete Confirmation Modal --}}
<div class="modal-overlay" id="deleteModal">
    <div class="modal-box modal-box-delete">
        <div class="modal-icon-red">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                <polyline points="3 6 5 6 21 6"></polyline>
                <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                <line x1="10" y1="11" x2="10" y2="17"></line>
                <line x1="14" y1="11" x2="14" y2="17"></line>
            </svg>
        </div>
        <div class="modal-title" id="deleteModalTitle">Hapus Ongkir</div>
        <div class="modal-desc" id="delete-modal-desc">Data ongkir ini akan dihapus secara permanen.</div>
        <div style="display:flex; gap:10px; justify-content:flex-end;">
            <button type="button" class="btn-cancel" onclick="closeDeleteModal()">Batalkan</button>
            <form id="deleteForm" method="POST">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn-danger-confirm">Ya, Hapus</button>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <link rel="stylesheet" href="https://cdn.datatables.net/2.3.7/css/dataTables.dataTables.min.css">
    <script src="https://cdn.datatables.net/2.3.7/js/dataTables.min.js"></script>

    <script>
        $.fn.dataTable.ext.errMode = 'none';

        // 1. FUNGSI GLOBAL
        function openDeleteModal(id, serviceName, routeName) {
            document.getElementById('delete-modal-desc').innerHTML =
                `Data ongkir layanan <strong>"${serviceName}"</strong> rute <strong>"${routeName}"</strong> akan dihapus. Tindakan ini tidak dapat dibatalkan.`;
            document.getElementById('deleteForm').action = '/shipping-rates/' + id;
            document.getElementById('deleteModal').classList.add('open');
        }

        function closeDeleteModal() {
            document.getElementById('deleteModal').classList.remove('open');
        }

        document.getElementById('deleteModal').addEventListener('click', function(e) {
            if (e.target === this) closeDeleteModal();
        });

        let table;

        function initDataTable() {
            return $('#rateTable').DataTable({
                responsive: true,
                autoWidth: false,
                language: {
                    search: "",
                    searchPlaceholder: "Cari Data...",
                    lengthMenu: "Tampilkan _MENU_ data",
                    info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
                    infoEmpty: "Tidak ada data untuk ditampilkan",
                    zeroRecords: `
                        <div style="padding: 40px 20px; text-align: center; display: flex; flex-direction: column; align-items: center; justify-content: center; margin: 0 auto; width: 100%; border-bottom: none;">
                            <div style="margin-bottom: 20px; width: 88px; height: 88px; background: color-mix(in srgb, var(--accent) 10%, transparent); border-radius: 24px; display: flex; align-items: center; justify-content: center; border: 1px solid color-mix(in srgb, var(--accent) 15%, transparent);">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" style="width: 38px; height: 38px; color: var(--accent); opacity: 0.9;">
                                    <path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z"></path><line x1="7" y1="7" x2="7.01" y2="7"></line>
                                </svg>
                            </div>
                            <div style="margin-bottom: 6px; font-size: 18px; font-weight: 800; color: var(--text-1); letter-spacing: -0.02em;">Tidak Ada Ongkir Ditemukan</div>
                            <div style="font-size: 13px; color: var(--text-3);">Tidak ada ongkir yang cocok dengan pencarian atau filter yang dipilih.</div>
                        </div>`,
                    emptyTable: `
                        <div style="padding: 40px 20px; text-align: center; display: flex; flex-direction: column; align-items: center; justify-content: center; margin: 0 auto; width: 100%; border-bottom: none;">
                            <div style="margin-bottom: 20px; width: 88px; height: 88px; background: color-mix(in srgb, var(--accent) 10%, transparent); border-radius: 24px; display: flex; align-items: center; justify-content: center; border: 1px solid color-mix(in srgb, var(--accent) 15%, transparent);">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" style="width: 38px; height: 38px; color: var(--accent); opacity: 0.9;">
                                    <path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z"></path><line x1="7" y1="7" x2="7.01" y2="7"></line>
                                </svg>
                            </div>
                            <div style="margin-bottom: 6px; font-size: 18px; font-weight: 800; color: var(--text-1); letter-spacing: -0.02em;">Belum Ada Data Ongkir</div>
                            <div style="font-size: 13px; color: var(--text-3);">Belum ada data ongkir pengiriman yang dimasukkan saat ini.</div>
                        </div>`,
                    paginate: { first: "«", last: "»", next: "›", previous: "‹" }
                },
                columnDefs: [
                    { targets: 0, orderable: false, searchable: false },
                    { targets: -1, orderable: false, searchable: false }
                ],
                order: [[1, 'asc']],
                drawCallback: function() {
                    let api = this.api();
                    let startIndex = api.page.info().start;
                    api.column(0, { page: 'current' }).nodes().each(function(cell, i) {
                        cell.innerHTML = startIndex + i + 1;
                    });
                }
            });
        }

        function formatNumber(num) {
            return num.toString().replace(/\D/g, "").replace(/\B(?=(\d{3})+(?!\d))/g, ".");
        }

        function checkResetVisibility() {
            const hasFilter = $('#dtFilterService').val() || $('#dtFilterDestination').val();
            if (hasFilter) {
                $('#btnResetFilter').fadeIn(200).css('display', 'inline-flex');
            } else {
                $('#btnResetFilter').fadeOut(200);
            }
        }

        $(document).ready(function() {
            table = initDataTable();

            checkResetVisibility();

            // FIX: Corrected broken AJAX closure from original
            $('#btnApplyFilter').on('click', function() {
                const params = {
                    service_id:          $('#dtFilterService').val(),
                    destination_city_id: $('#dtFilterDestination').val(),
                };

                $('#rateTable tbody').css('opacity', '0.4');

                $.ajax({
                    url:  "{{ route('shipping-rates.index') }}",
                    type: 'GET',
                    data: params,
                    success: function(html) {
                        if ($.fn.DataTable.isDataTable('#rateTable')) {
                            table.destroy();
                        }
                        $('#rateTable tbody').html(html);
                        $('#rateTable tbody tr').addClass('fade-in-animated');
                        table = initDataTable();
                        $('#rateTable tbody').css('opacity', '1');
                        checkResetVisibility();
                    },
                    error: function() {
                        $('#rateTable tbody').css('opacity', '1');
                        if (typeof showToast === 'function') {
                            showToast('Gagal memuat data tarif ongkir.', 'error');
                        }
                    }
                });
            });

            $('#btnResetFilter').on('click', function() {
                $('#dtFilterService').val('');
                $('#dtFilterDestination').val('');
                $('#btnApplyFilter').trigger('click');
            });

            const costInput = document.getElementById('costKg');
            if (costInput) {
                costInput.addEventListener('input', function() {
                    let val = this.value.replace(/\D/g, "");
                    this.value = val ? formatNumber(val) : "";
                });
            }

            const rateForm = document.getElementById('rateForm');
            if (rateForm) {
                rateForm.addEventListener('submit', function() {
                    if (costInput) {
                        costInput.value = costInput.value.replace(/\./g, "");
                    }
                });
            }

            const serviceSelectInput = document.getElementById('serviceId');
            if (serviceSelectInput) {
                serviceSelectInput.addEventListener('change', checkServiceStatus);
            }
        });

        function checkServiceStatus() {
            const serviceSelect = document.getElementById('serviceId');
            const selectedOption = serviceSelect.options[serviceSelect.selectedIndex];
            const warningEl = document.getElementById('serviceInactiveWarning');
            if (!warningEl) return;

            if (selectedOption && selectedOption.getAttribute('data-active') === '0') {
                warningEl.style.display = 'flex';
            } else {
                warningEl.style.display = 'none';
            }
        }

        document.getElementById('isActive').addEventListener('change', function() {
            const card = document.getElementById('activeToggleCard');
            const desc = document.getElementById('activeToggleDesc');
            if(this.checked) {
                card.classList.add('active');
                desc.textContent = 'Tarif pengiriman ini dapat digunakan oleh pesanan.';
            } else {
                card.classList.remove('active');
                desc.textContent = 'Nonaktif (disembunyikan dari pilihan checkout pengguna).';
            }
        });

        function filterCitiesByProvince(provinceSelectId, citySelectId, selectedCityId = null) {
            const provinceId = document.getElementById(provinceSelectId).value;
            const citySelect = document.getElementById(citySelectId);
            const options = citySelect.querySelectorAll('option:not([value=""])');

            options.forEach(opt => {
                if (!provinceId || opt.getAttribute('data-province') === provinceId) {
                    opt.style.display = 'block';
                } else {
                    opt.style.display = 'none';
                }
            });

            if (selectedCityId) {
                citySelect.value = selectedCityId;
            } else {
                citySelect.value = '';
            }
        }

        document.getElementById('originProvinceId').addEventListener('change', function() { filterCitiesByProvince('originProvinceId', 'originId'); });
        document.getElementById('destProvinceId').addEventListener('change', function() { filterCitiesByProvince('destProvinceId', 'destId'); });

        function openModal(id = null, serviceId = '', originProvId = '', originId = '', destProvId = '', destId = '', minWeight = '1.00', maxWeight = '', cost = '', etdMin = '', etdMax = '', isActive = 1, isValidationError = false) {
            const modal       = document.getElementById('rateModal');
            const form        = document.getElementById('rateForm');
            const title       = document.getElementById('modalTitle');
            const subtitle    = document.getElementById('modalSubtitle');
            const modalIcon   = document.getElementById('modalIcon');
            const modalIconSvg = document.getElementById('modalIconSvg');

            if (!isValidationError) {
                document.querySelectorAll('#rateModal .form-modal-input').forEach(el => el.classList.remove('is-invalid'));
            }

            const serviceSelect = document.getElementById('serviceId');
            Array.from(serviceSelect.options).forEach(option => {
                if (option.value === "") return;
                const isActiveOption = option.getAttribute('data-active') === '1';
                if (id) {
                    if (isActiveOption || option.value == serviceId) {
                        option.style.display = 'block'; option.disabled = false;
                    } else {
                        option.style.display = 'none'; option.disabled = true;
                    }
                } else {
                    if (isActiveOption) {
                        option.style.display = 'block'; option.disabled = false;
                    } else {
                        option.style.display = 'none'; option.disabled = true;
                    }
                }
            });

            document.getElementById('serviceId').value = serviceId;
            document.getElementById('originProvinceId').value = originProvId;
            document.getElementById('destProvinceId').value = destProvId;
            
            filterCitiesByProvince('originProvinceId', 'originId', originId);
            filterCitiesByProvince('destProvinceId', 'destId', destId);
            
            document.getElementById('minWeight').value = minWeight;
            document.getElementById('maxWeight').value = maxWeight;
            document.getElementById('costKg').value    = cost ? formatNumber(cost) : '';
            document.getElementById('etdMin').value    = etdMin;
            document.getElementById('etdMax').value    = etdMax;
            document.getElementById('isActive').checked = (isActive == '1' || isActive === 'true' || isActive === true);

            checkServiceStatus();
            document.getElementById('isActive').dispatchEvent(new Event('change'));

            if (id) {
                title.innerText    = 'Edit Ongkir';
                subtitle.innerText = 'Perbarui biaya dan estimasi pengiriman di bawah ini.';
                form.action        = `/shipping-rates/${id}`;
                document.getElementById('methodField').innerHTML = '@method("PUT")';
                document.getElementById('rateIdInput').value    = id;
                modalIcon.className    = 'modal-icon modal-icon-amber';
                modalIconSvg.innerHTML = '<path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4L18.5 2.5z"></path>';
            } else {
                title.innerText    = 'Tambah Ongkir Baru';
                subtitle.innerText = 'Isi detail ongkir pengiriman di bawah ini.';
                form.action        = '{{ route("shipping-rates.store") }}';
                document.getElementById('methodField').innerHTML = '';
                document.getElementById('rateIdInput').value    = '';
                modalIcon.className    = 'modal-icon modal-icon-blue';
                modalIconSvg.innerHTML = '<line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12">';
            }
            modal.classList.add('open');
            setTimeout(() => document.getElementById('serviceId').focus(), 200);
        }

        function closeModal() { document.getElementById('rateModal').classList.remove('open'); }

        document.getElementById('rateModal').addEventListener('click', function(e) {
            if (e.target === this) closeModal();
        });

        @if($errors->any())
            $(document).ready(function() {
                const oldId = '{{ old('id') }}';
                if (oldId) {
                    openModal(oldId, '{{ old('shipping_service_id') }}', '{{ old('origin_province_id') }}', '{{ old('origin_city_id') }}', '{{ old('destination_province_id') }}', '{{ old('destination_city_id') }}', '{{ old('min_weight') }}', '{{ old('max_weight') }}', '{{ old('cost_per_kg') }}', '{{ old('etd_min') }}', '{{ old('etd_max') }}', '{{ old('is_active') }}', true);
                } else {
                    openModal(null, '{{ old('shipping_service_id') }}', '{{ old('origin_province_id') }}', '{{ old('origin_city_id') }}', '{{ old('destination_province_id') }}', '{{ old('destination_city_id') }}', '{{ old('min_weight') }}', '{{ old('max_weight') }}', '{{ old('cost_per_kg') }}', '{{ old('etd_min') }}', '{{ old('etd_max') }}', '{{ old('is_active') }}', true);
                }
            });
        @endif
    </script>
@endpush
