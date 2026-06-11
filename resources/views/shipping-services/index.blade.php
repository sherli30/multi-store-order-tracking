@extends('layouts.app')

@section('title', 'Master Layanan Pengiriman')

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

    .filter-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 15px; align-items: flex-end; }
    @media (max-width: 900px) { .filter-grid { grid-template-columns: 1fr 1fr; } }
    @media (max-width: 560px) { .filter-grid { grid-template-columns: 1fr; } }

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
        color: var(--red); /* FIX: was missing in original */
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
    .courier-cell { display: flex; align-items: center; gap: 10px; }
    .courier-logo-sm {
        width: 32px; height: 32px; border-radius: 8px;
        background: var(--surface-2); border: 1px solid var(--border);
        display: flex; align-items: center; justify-content: center;
        font-weight: 800; color: var(--accent); font-size: 12px; flex-shrink: 0;
    }
    .service-name { font-size: 14px; font-weight: 700; color: var(--accent); }
    .type-pill {
        display: inline-flex; align-items: center;
        font-size: 10.5px; font-weight: 700; color: var(--text-3);
        background: var(--surface-2); padding: 2px 8px; border-radius: 4px;
        border: 1px solid var(--border);
    }

    .badge {
        display: inline-flex; align-items: center; gap: 5px;
        padding: 4px 11px; border-radius: 20px; font-size: 11.5px; font-weight: 600; white-space: nowrap;
    }
    .badge::before { content: ''; width: 6px; height: 6px; border-radius: 50%; flex-shrink: 0; }
    .badge-active   { background: var(--green-dim); color: var(--green); border: 1px solid rgba(22, 163, 74, 0.2); }
    .badge-active::before { background: var(--green); }
    .badge-inactive { background: var(--surface-2); color: var(--text-3); border: 1px solid var(--border); }
    .badge-inactive::before { background: var(--text-4); }

    /* ── Actions ─────────────────────────────────── */
    .actions-cell { display: flex; gap: 6px; justify-content: center; }
    .btn-sm {
        display: inline-flex; align-items: center; gap: 5px;
        border: 1px solid var(--border); border-radius: 7px;
        font-family: var(--font); font-size: 11.5px; font-weight: 600;
        padding: 6px 12px; cursor: pointer; transition: all 0.15s;
        background: var(--panel); color: var(--text-2); text-decoration: none; white-space: nowrap;
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
        width: 380px; max-width: 90vw; box-shadow: var(--shadow-lg);
        transform: scale(0.95) translateY(10px); transition: transform 0.2s;
    }
    .modal-overlay.open .modal-box { transform: scale(1) translateY(0); }
    .modal-icon { width: 52px; height: 52px; background: var(--red-dim); border-radius: 14px; display: flex; align-items: center; justify-content: center; margin-bottom: 16px; }
    .modal-icon svg { width: 24px; height: 24px; color: var(--red); }
    .modal-title { font-size: 16px; font-weight: 800; color: var(--text-1); margin-bottom: 6px; }
    .modal-desc  { font-size: 13px; color: var(--text-2); margin-bottom: 22px; line-height: 1.6; }
    .modal-actions { display: flex; gap: 10px; justify-content: flex-end; }

    .btn-cancel {
        padding: 9px 18px; border: 1px solid var(--border); border-radius: 8px;
        font-family: var(--font); font-size: 13px; font-weight: 600;
        background: var(--surface); color: var(--text-2); cursor: pointer; transition: all 0.15s;
    }
    .btn-cancel:hover { border-color: var(--border-2); color: var(--text-1); }
    .btn-danger-confirm {
        padding: 9px 18px; border: none; border-radius: 8px;
        font-family: var(--font); font-size: 13px; font-weight: 600;
        background: var(--red); color: #fff; cursor: pointer; transition: all 0.15s;
        box-shadow: 0 2px 8px rgba(220, 38, 38, 0.25);
    }
    .btn-danger-confirm:hover { background: #b91c1c; transform: translateY(-1px); }

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
    .form-modal-input.is-invalid { border-color: var(--red); background: color-mix(in srgb, var(--red) 2%, var(--panel)); }
    .form-field-error { font-size: 11.5px; color: var(--red); font-weight: 600; margin-top: 6px; display: none; text-align: left; }
    .field-hint { font-size: 11px; color: var(--text-4); margin-top: 4px; }

    .modal-icon-blue  { background: rgba(59, 130, 246, 0.1) !important; }
    .modal-icon-blue svg { color: #3b82f6 !important; }
    .modal-icon-amber { background: rgba(245, 158, 11, 0.1) !important; }
    .modal-icon-amber svg { color: #f59e0b !important; }

    /* ── Toggle ──────────────────────────────────── */
    .toggle-card {
        padding: 12px 16px; border-radius: 10px;
        background: linear-gradient(135deg, var(--surface) 0%, color-mix(in srgb, var(--accent) 1%, var(--surface)) 100%);
        border: 1px solid var(--border);
        display: flex; align-items: center; gap: 14px;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        margin-top: 10px; margin-bottom: 12px;
    }
    .toggle-card:hover { border-color: var(--accent); box-shadow: 0 4px 12px color-mix(in srgb, var(--accent) 10%, transparent); }
    .toggle-switch { position: relative; width: 48px; height: 28px; flex-shrink: 0; }
    .toggle-switch input { opacity: 0; width: 0; height: 0; }
    .toggle-slider {
        position: absolute; inset: 0; background: #cbd5e1; border-radius: 14px; cursor: pointer;
        transition: all 0.35s cubic-bezier(0.4, 0, 0.2, 1);
        box-shadow: inset 0 2px 4px rgba(0, 0, 0, 0.08);
    }
    .toggle-slider::before {
        content: ''; position: absolute; width: 22px; height: 22px; left: 3px; top: 3px;
        background: #fff; border-radius: 50%;
        transition: all 0.35s cubic-bezier(0.4, 0, 0.2, 1);
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.12);
    }
    .toggle-switch input:checked + .toggle-slider { background: var(--green); box-shadow: 0 4px 12px color-mix(in srgb, var(--green) 35%, transparent); }
    .toggle-switch input:checked + .toggle-slider::before { transform: translateX(20px); }
    .toggle-label { font-size: 13.5px; font-weight: 700; color: var(--text-2); }

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
                    <line x1="16.5" y1="9.4" x2="7.5" y2="4.21"></line>
                    <polygon points="12 22.08 12 12 3 6.92 3 17.08 12 22.08"></polygon>
                    <polygon points="12 22.08 12 12 21 6.92 21 17.08 12 22.08"></polygon>
                    <polygon points="12 2 3 6.92 12 12 21 6.92 12 2"></polygon>
                    <line x1="12" y1="22.08" x2="12" y2="12"></line>
                </svg>
            </span>
            Master Layanan
        </h1>
        <p>Atur tipe layanan (Reguler, Cargo, dll) untuk setiap kurir.</p>
    </div>
    <button class="btn-primary" onclick="openModal()">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
            <line x1="12" y1="5" x2="12" y2="19"></line>
            <line x1="5" y1="12" x2="19" y2="12"></line>
        </svg>
        Tambah Layanan
    </button>
</div>

{{-- Stats Bar --}}
<div class="stats-bar">
    <div class="stat-card">
        <div class="stat-icon blue">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path>
            </svg>
        </div>
        <div>
            <div class="stat-value">{{ $services->count() }}</div>
            <div class="stat-label">Total Layanan</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon purple">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <rect x="2" y="7" width="20" height="14" rx="2" ry="2"></rect>
                <path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"></path>
            </svg>
        </div>
        <div>
            <div class="stat-value">{{ $services->where('min_weight', '>=', 10000)->count() }}</div>
            <div class="stat-label">Cargo (≥10kg)</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon amber">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <polyline points="20 6 9 17 4 12"></polyline>
            </svg>
        </div>
        <div>
            <div class="stat-value">{{ $services->where('is_active', true)->count() }}</div>
            <div class="stat-label">Layanan Aktif</div>
        </div>
    </div>
</div>

{{-- Filter Card --}}
<div class="filter-card">
    <div class="filter-card-top">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="width:16px;height:16px;color:var(--text-3);">
            <polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3" />
        </svg>
        <div class="filter-card-title">Filter Layanan</div>
    </div>
    <div class="filter-grid">
        <div class="form-group">
            <label class="form-label" for="dtFilterCourier">Kurir Induk</label>
            <select id="dtFilterCourier" class="form-input">
                <option value="">Semua Kurir</option>
                @foreach ($couriers->where('is_active', true) as $courier)
                    <option value="{{ $courier->id }}">{{ $courier->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="form-group">
            <label class="form-label" for="dtFilterType">Tipe Layanan</label>
            <select id="dtFilterType" class="form-input">
                <option value="">Semua Tipe</option>
                <option value="reguler">Reguler (< 10kg)</option>
                <option value="cargo">Cargo (≥ 10kg)</option>
            </select>
        </div>
        <div class="form-group">
            <label class="form-label" for="dtFilterStatus">Status Aktif</label>
            <select id="dtFilterStatus" class="form-input">
                <option value="">Semua Status</option>
                <option value="active">Aktif</option>
                <option value="inactive">Nonaktif</option>
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
        <table class="data-table" id="serviceTable">
            <thead>
                <tr>
                    <th class="center" style="width:50px;">No</th>
                    <th>Kurir</th>
                    <th>Nama Layanan</th>
                    <th>Min. Berat</th>
                    <th>Tipe</th>
                    <th>Status</th>
                    <th class="center" style="width:130px;">Aksi</th>
                </tr>
            </thead>
            <tbody id="serviceTableBody">
                @include('shipping-services._table_rows')
            </tbody>
        </table>
    </div>
</div>

{{-- Delete Confirmation Modal --}}
<div class="modal-overlay" id="deleteModal">
    <div class="modal-box">
        <div class="modal-icon">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <polyline points="3 6 5 6 21 6"></polyline>
                <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
            </svg>
        </div>
        <div class="modal-title">Hapus Layanan?</div>
        <div class="modal-desc" id="modal-desc">Data layanan ini akan dihapus secara permanen.</div>
        <div class="modal-actions">
            <button class="btn-cancel" onclick="closeDeleteModal()">Batalkan</button>
            <form id="deleteForm" method="POST">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn-danger-confirm">Ya, Hapus</button>
            </form>
        </div>
    </div>
</div>

{{-- Service Form Modal (Create / Edit) --}}
<div class="modal-overlay" id="serviceModal">
    <div class="modal-box modal-box-form">
        <div style="display:flex; align-items:center; gap:16px; margin-bottom:24px;">
            <div class="modal-icon" id="serviceModalIcon" style="margin-bottom:0;">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" id="serviceModalIconSvg">
                    <line x1="12" y1="5" x2="12" y2="19"></line>
                    <line x1="5" y1="12" x2="19" y2="12"/>
                </svg>
            </div>
            <div style="text-align:left;">
                <div class="modal-title" id="serviceModalTitle" style="margin-bottom:4px; font-size:18px; font-weight:800; color:var(--text-1); letter-spacing:-0.02em;">Tambah Layanan</div>
                <div style="font-size:12.5px; color:var(--text-3); font-weight:500;" id="serviceModalSubtitle">Isi detail layanan kurir di bawah ini.</div>
            </div>
        </div>

        <form id="serviceForm" method="POST" novalidate>
            @csrf
            <div id="methodField"></div>
            <input type="hidden" name="id" id="serviceIdInput" value="{{ old('id') }}">

            <div class="form-modal-field">
                <label class="form-modal-label" for="courierId">Kurir <span>*</span></label>
                <select name="courier_id" id="courierId" class="form-modal-input @error('courier_id') is-invalid @enderror" required>
                    <option value="" data-active="1">-- Pilih Kurir --</option>
                    @foreach($couriers as $courier)
                        <option value="{{ $courier->id }}" data-active="{{ $courier->is_active ? '1' : '0' }}" {{ old('courier_id') == $courier->id ? 'selected' : '' }}>
                            {{ $courier->name }} {{ !$courier->is_active ? '(Non-aktif)' : '' }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="form-modal-field">
                <label class="form-modal-label" for="serviceName">Nama Layanan <span>*</span></label>
                <input type="text" name="service_name" id="serviceName" class="form-modal-input @error('service_name') is-invalid @enderror" required placeholder="Contoh: Reguler, Cargo, OKE" value="{{ old('service_name') }}">
            </div>

            <div class="form-modal-field">
                <label class="form-modal-label" for="minWeight">Minimal Berat (Gram) <span>*</span></label>
                <input type="number" name="min_weight" id="minWeight" class="form-modal-input @error('min_weight') is-invalid @enderror" required placeholder="Contoh: 0 (Reguler), 10000 (Cargo)" value="{{ old('min_weight') }}">
            </div>

            <div class="toggle-card" id="activeToggleCard">
                <label class="toggle-switch">
                    <input type="hidden" name="is_active" value="0">
                    <input type="checkbox" name="is_active" id="isActive" value="1" {{ old('is_active', '1') == '1' ? 'checked' : '' }}>
                    <span class="toggle-slider"></span>
                </label>
                <div class="toggle-label">Layanan Aktif</div>
            </div>
            <div id="courierInactiveWarning" style="display:none; align-items:flex-start; gap:10px; margin-top:6px; margin-bottom:18px; font-size:12px; line-height:1.5; color:var(--red); background:color-mix(in srgb, var(--red) 8%, var(--panel)); border:1.5px solid color-mix(in srgb, var(--red) 20%, transparent); border-radius:10px; padding:12px 14px; text-align:left;">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="width:16px; height:16px; margin-top:2px; flex-shrink:0; color:var(--red);"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path><line x1="12" y1="9" x2="12" y2="13"></line><line x1="12" y1="17" x2="12.01" y2="17"></line></svg>
                <div>
                    <span style="font-weight:800; display:block; margin-bottom:3px; font-size:12.5px;">Kurir Induk Non-Aktif</span>
                    Status operasional layanan ini dikunci karena ekspedisi kurir utama sedang dinonaktifkan di Master Kurir. Silakan aktifkan kurir terlebih dahulu jika ingin mengaktifkan layanan ini.
                </div>
            </div>

            <div class="modal-actions">
                <button type="button" class="btn-cancel" onclick="closeModal()">Batal</button>
                <button type="submit" class="btn-primary" id="serviceModalSubmit" style="border:none;">Tambah Layanan</button>
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
        $.fn.dataTable.ext.errMode = 'none';

        // 1. FUNGSI GLOBAL
        function openDeleteModal(id, name) {
            document.getElementById('modal-desc').innerHTML =
                `Layanan <strong>"${name}"</strong> akan dihapus. Tindakan ini tidak dapat dibatalkan.`;
            document.getElementById('deleteForm').action = '/shipping-services/' + id;
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
            return $('#serviceTable').DataTable({
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
                                    <line x1="16.5" y1="9.4" x2="7.5" y2="4.21"></line><polygon points="12 22.08 12 12 3 6.92 3 17.08 12 22.08"></polygon><polygon points="12 22.08 12 12 21 6.92 21 17.08 12 22.08"></polygon><polygon points="12 2 3 6.92 12 12 21 6.92 12 2"></polygon><line x1="12" y1="22.08" x2="12" y2="12"></line>
                                </svg>
                            </div>
                            <div style="margin-bottom: 6px; font-size: 18px; font-weight: 800; color: var(--text-1); letter-spacing: -0.02em;">Tidak Ada Layanan Ditemukan</div>
                            <div style="font-size: 13px; color: var(--text-3);">Tidak ada layanan yang cocok dengan pencarian atau filter yang dipilih.</div>
                        </div>`,
                    emptyTable: `
                        <div style="padding: 40px 20px; text-align: center; display: flex; flex-direction: column; align-items: center; justify-content: center; margin: 0 auto; width: 100%; border-bottom: none;">
                            <div style="margin-bottom: 20px; width: 88px; height: 88px; background: color-mix(in srgb, var(--accent) 10%, transparent); border-radius: 24px; display: flex; align-items: center; justify-content: center; border: 1px solid color-mix(in srgb, var(--accent) 15%, transparent);">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" style="width: 38px; height: 38px; color: var(--accent); opacity: 0.9;">
                                    <line x1="16.5" y1="9.4" x2="7.5" y2="4.21"></line><polygon points="12 22.08 12 12 3 6.92 3 17.08 12 22.08"></polygon><polygon points="12 22.08 12 12 21 6.92 21 17.08 12 22.08"></polygon><polygon points="12 2 3 6.92 12 12 21 6.92 12 2"></polygon><line x1="12" y1="22.08" x2="12" y2="12"></line>
                                </svg>
                            </div>
                            <div style="margin-bottom: 6px; font-size: 18px; font-weight: 800; color: var(--text-1); letter-spacing: -0.02em;">Belum Ada Data Layanan</div>
                            <div style="font-size: 13px; color: var(--text-3);">Belum ada data layanan pengiriman yang dimasukkan saat ini.</div>
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

        function checkResetVisibility() {
            const hasFilter = $('#dtFilterCourier').val() || $('#dtFilterType').val() || $('#dtFilterStatus').val();
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
                    courier_id: $('#dtFilterCourier').val(),
                    type:       $('#dtFilterType').val(),
                    status:     $('#dtFilterStatus').val(),
                };

                $('#serviceTable tbody').css('opacity', '0.4');

                $.ajax({
                    url:  "{{ route('shipping-services.index') }}",
                    type: 'GET',
                    data: params,
                    success: function(html) {
                        if ($.fn.DataTable.isDataTable('#serviceTable')) {
                            table.destroy();
                        }
                        $('#serviceTable tbody').html(html);
                        $('#serviceTable tbody tr').addClass('fade-in-animated');
                        table = initDataTable();
                        $('#serviceTable tbody').css('opacity', '1');
                        checkResetVisibility();
                    },
                    error: function() {
                        $('#serviceTable tbody').css('opacity', '1');
                        if (typeof showToast === 'function') {
                            showToast('Gagal memuat data layanan.', 'error');
                        }
                    }
                });
            });

            $('#btnResetFilter').on('click', function() {
                $('#dtFilterCourier').val('');
                $('#dtFilterType').val('');
                $('#dtFilterStatus').val('');
                $('#btnApplyFilter').trigger('click');
            });
        });

        function openModal(id = null, name = '', courierId = '', minWeight = 0, isActive = 1, isValidationError = false) {
            const modal        = document.getElementById('serviceModal');
            const form         = document.getElementById('serviceForm');
            const title        = document.getElementById('serviceModalTitle');
            const submitBtn    = document.getElementById('serviceModalSubmit');
            const methodField  = document.getElementById('methodField');
            const modalIcon    = document.getElementById('serviceModalIcon');
            const modalIconSvg = document.getElementById('serviceModalIconSvg');
            const subtitle     = document.getElementById('serviceModalSubtitle');

            if (!isValidationError) {
                document.querySelectorAll('#serviceModal .form-field-error').forEach(el => el.style.display = 'none');
                document.querySelectorAll('#serviceModal .form-modal-input').forEach(el => el.classList.remove('is-invalid'));
            }

            const courierSelect = document.getElementById('courierId');
            Array.from(courierSelect.options).forEach(option => {
                if (option.value === "") return;
                const isActiveOption = option.getAttribute('data-active') === '1';
                if (id) {
                    if (isActiveOption || option.value == courierId) {
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

            if (id) {
                title.textContent      = 'Edit Layanan';
                subtitle.textContent   = 'Perbarui detail layanan kurir logistik di bawah ini.';
                submitBtn.textContent  = 'Simpan Perubahan';
                form.action            = `/shipping-services/${id}`;
                methodField.innerHTML  = '@method("PUT")';
                modalIcon.className    = 'modal-icon modal-icon-amber';
                modalIconSvg.innerHTML = '<path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4L18.5 2.5z"></path>';

                document.getElementById('serviceIdInput').value = id;
                document.getElementById('serviceName').value   = name;
                document.getElementById('courierId').value     = courierId;
                document.getElementById('minWeight').value     = minWeight;
                document.getElementById('isActive').checked    = (isActive == '1' || isActive === 'true');
            } else {
                title.textContent      = 'Tambah Layanan Baru';
                subtitle.textContent   = 'Isi detail layanan kurir logistik di bawah ini.';
                submitBtn.textContent  = 'Tambah Layanan';
                form.action            = '{{ route("shipping-services.store") }}';
                methodField.innerHTML  = '';
                modalIcon.className    = 'modal-icon modal-icon-blue';
                modalIconSvg.innerHTML = '<line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12">';

                document.getElementById('serviceIdInput').value = '';
                document.getElementById('serviceName').value   = '';
                document.getElementById('courierId').value     = '';
                document.getElementById('minWeight').value     = '';
                document.getElementById('isActive').checked    = (isActive == '1' || isActive === 'true' || isActive === true);
            }

            checkCourierStatus();

            modal.classList.add('open');
            setTimeout(() => document.getElementById('serviceName').focus(), 200);
        }

        function checkCourierStatus() {
            const courierSelect    = document.getElementById('courierId');
            const selectedOption   = courierSelect.options[courierSelect.selectedIndex];
            const warningEl        = document.getElementById('courierInactiveWarning');
            const toggleCard       = document.getElementById('activeToggleCard');
            const isActiveCheckbox = document.getElementById('isActive');

            if (selectedOption && selectedOption.getAttribute('data-active') === '0') {
                warningEl.style.display      = 'flex';
                warningEl.style.alignItems   = 'flex-start';
                isActiveCheckbox.checked     = false;
                isActiveCheckbox.disabled    = true;
                toggleCard.style.opacity     = '0.55';
                toggleCard.style.pointerEvents = 'none';
            } else {
                warningEl.style.display        = 'none';
                isActiveCheckbox.disabled      = false;
                toggleCard.style.opacity       = '1';
                toggleCard.style.pointerEvents = 'auto';
            }
        }

        document.getElementById('courierId').addEventListener('change', checkCourierStatus);

        function closeModal() { document.getElementById('serviceModal').classList.remove('open'); }

        document.getElementById('serviceModal').addEventListener('click', function(e) {
            if (e.target === this) closeModal();
        });

        @if($errors->any())
            $(document).ready(function() {
                const oldId = '{{ old('id') }}';
                if (oldId) {
                    openModal(oldId, `{!! addslashes(old('service_name')) !!}`, '{{ old('courier_id') }}', '{{ old('min_weight') }}', '{{ old('is_active') }}', true);
                } else {
                    openModal(null, `{!! addslashes(old('service_name')) !!}`, '{{ old('courier_id') }}', '{{ old('min_weight') }}', '{{ old('is_active') }}', true);
                }
            });
        @endif
    </script>
@endpush
