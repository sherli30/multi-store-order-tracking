@extends('layouts.app')

@section('title', 'Data Kurir')

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
    .stat-icon { width: 42px; height: 42px; border-radius: 10px; display: flex; align-items: center; justify-content:
    center; flex-shrink: 0; }
    .stat-icon svg { width: 20px; height: 20px; }
    .stat-icon.blue { background: rgba(59,130,246,0.1); color: #3b82f6; }
    .stat-icon.green { background: var(--green-dim); color: var(--green); }
    .stat-icon.gray { background: var(--surface-2); color: var(--text-3); }
    .stat-value { font-size: 20px; font-weight: 800; color: var(--text-1); letter-spacing: -0.03em; }
    .stat-label { font-size: 11.5px; color: var(--text-3); font-weight: 500; margin-top: 2px; }
    @media (max-width: 700px) { .stats-bar { grid-template-columns: 1fr; } }

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
    .courier-info { display: flex; align-items: center; gap: 12px; }
    .courier-logo {
    width: 40px; height: 40px; border-radius: 10px;
    background: var(--surface-2); border: 1px solid var(--border);
    display: flex; align-items: center; justify-content: center;
    font-weight: 800; color: var(--accent); font-size: 16px; flex-shrink: 0;
    }
    .courier-name { font-size: 14px; font-weight: 700; color: var(--text-1); }
    .courier-code { font-size: 11px; color: var(--text-3); font-family: var(--mono); margin-top: 2px; }

    .service-pill {
    display: inline-flex; align-items: center; gap: 4px;
    font-size: 11.5px; font-weight: 600;
    color: #8b5cf6; background: rgba(139,92,246,0.1);
    padding: 3px 9px; border-radius: 6px;
    border: 1px solid rgba(139,92,246,0.2);
    }

    .badge { display: inline-flex; align-items: center; gap: 5px; padding: 4px 11px; border-radius: 20px; font-size: 11.5px;
    font-weight: 600; white-space: nowrap; }
    .badge::before { content: ''; width: 6px; height: 6px; border-radius: 50%; flex-shrink: 0; }
    .badge-active { background: var(--green-dim); color: var(--green); border: 1px solid rgba(22,163,74,0.2); }
    .badge-active::before { background: var(--green); }
    .badge-inactive { background: var(--surface-2); color: var(--text-3); border: 1px solid var(--border); }
    .badge-inactive::before { background: var(--text-4); }

    /* ── Actions ─────────────────────────────────── */
    .actions-cell { display: flex; gap: 6px; justify-content: center; }
    .btn-sm { display: inline-flex; align-items: center; gap: 5px; border: 1px solid var(--border); border-radius: 7px;
    font-family: var(--font); font-size: 11.5px; font-weight: 600; padding: 6px 12px; cursor: pointer; transition: all
    0.15s; background: var(--panel); color: var(--text-2); text-decoration: none; white-space: nowrap; }
    .btn-sm svg { width: 12px; height: 12px; }
    .btn-sm:hover { border-color: var(--accent); color: var(--accent); background: color-mix(in srgb, var(--accent) 5%,
    var(--panel)); }
    .btn-sm.danger:hover { border-color: rgba(220,38,38,0.4); color: var(--red); background: var(--red-dim); }

    .btn-primary {
    display: inline-flex;
    align-items: center;
    gap: 7px;
    background: var(--accent);
    color: #fff;
    border: none;
    padding: 9px 18px;
    border-radius: 9px;
    font-family: var(--font);
    font-weight: 700;
    font-size: 13px;
    cursor: pointer;
    text-decoration: none;
    transition: all 0.15s;
    box-shadow: 0 2px 8px color-mix(in srgb, var(--accent) 30%, transparent);
    white-space: nowrap;
    line-height: 1.2;
    height: 38px;
    box-sizing: border-box;
    }
    .btn-primary:hover { opacity: 0.9; transform: translateY(-1px); }

    /* ── Search Input ────────────────────────────── */
    .search-container {
    padding: 20px;
    border-bottom: 1px solid var(--border);
    display: flex;
    justify-content: flex-end;
    }
    .field-input {
    width: 100%; padding: 9px 13px; border: 1px solid var(--border); border-radius: 9px;
    font-family: var(--font); font-size: 13px; color: var(--text-1); background: var(--surface);
    outline: none; transition: border-color 0.15s, box-shadow 0.15s; box-sizing: border-box;
    }
    .field-input:focus { border-color: var(--accent); box-shadow: 0 0 0 3px var(--accent-glow); }

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
    .dataTables_wrapper .dataTables_paginate .paginate_button.disabled {
    color: var(--text-4) !important; background: var(--surface) !important;
    border-color: var(--border) !important; cursor: default !important;
    }

    /* ── Delete Modal ────────────────────────────── */
    .modal-overlay {
    position: fixed; inset: 0; background: rgba(15,23,42,0.45); backdrop-filter: blur(4px);
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
    .modal-icon { width: 52px; height: 52px; background: var(--red-dim); border-radius: 14px; display: flex; align-items:
    center; justify-content: center; margin-bottom: 16px; }
    .modal-icon svg { width: 24px; height: 24px; color: var(--red); }
    .modal-title { font-size: 16px; font-weight: 800; color: var(--text-1); margin-bottom: 6px; }
    .modal-desc { font-size: 13px; color: var(--text-2); margin-bottom: 22px; line-height: 1.6; }
    .modal-actions { display: flex; gap: 10px; justify-content: flex-end; }
    .btn-cancel { padding: 9px 18px; border: 1px solid var(--border); border-radius: 8px; font-family: var(--font);
    font-size: 13px; font-weight: 600; background: var(--surface); color: var(--text-2); cursor: pointer; transition: all
    0.15s; }
    .btn-cancel:hover { border-color: var(--border-2); color: var(--text-1); }
    .btn-danger-confirm { padding: 9px 18px; border: none; border-radius: 8px; font-family: var(--font); font-size: 13px;
    font-weight: 600; background: var(--red); color: #fff; cursor: pointer; transition: all 0.15s; box-shadow: 0 2px 8px
    rgba(220,38,38,0.25); }
    .btn-danger-confirm:hover { background: #b91c1c; transform: translateY(-1px); }

    /* ── Alerts ── */
    .alert { display:flex; align-items:center; gap:11px; border-radius:11px; padding:13px 18px; font-size:13px;
    font-weight:600; margin-bottom:22px; animation:rise 0.3s ease both; }
    .alert svg { width:18px; height:18px; flex-shrink:0; }
    .alert-success { background:var(--green-dim); border:1px solid rgba(22,163,74,0.25); color:var(--green); }
    .alert-error { background:var(--red-dim); border:1px solid rgba(220,38,38,0.25); color:var(--red); }

    /* ── Validation Errors ── */
    .error-list { background:var(--red-dim); border:1px solid rgba(220,38,38,0.2); border-radius:11px; padding:13px 18px;
    margin-bottom:22px; }
    .error-list-title { font-size:13px; font-weight:700; color:var(--red); margin-bottom:8px; }
    .error-list ul { list-style:none; padding:0; margin:0; display:flex; flex-direction:column; gap:4px; }
    .error-list ul li { font-size:12px; color:var(--red); display:flex; align-items:center; gap:6px; }
    .error-list ul li::before { content:""; width:4px; height:4px; background:var(--red); border-radius:50%; flex-shrink:0;
    }

    /* ── Form Modal Style ── */
    .modal-box-form {
    width: 480px !important;
    padding: 32px !important;
    border-radius: 20px !important;
    text-align: left !important;
    box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25) !important;
    }
    .form-modal-field { margin-bottom: 20px; }
    .form-modal-label {
    font-size: 11px;
    font-weight: 700;
    color: var(--text-3);
    text-transform: uppercase;
    letter-spacing: 0.06em;
    margin-bottom: 8px;
    display: block;
    text-align: left;
    }
    .form-modal-label span { color: var(--red); margin-left: 2px; }
    .form-modal-input {
    width: 100%;
    padding: 11px 16px;
    border: 1.5px solid var(--border);
    border-radius: 10px;
    font-family: var(--font);
    font-size: 14px;
    color: var(--text-1);
    background: var(--surface);
    outline: none;
    transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
    box-sizing: border-box;
    }
    .form-modal-input:focus {
    border-color: var(--accent);
    background: var(--panel);
    box-shadow: 0 0 0 4px color-mix(in srgb, var(--accent) 15%, transparent);
    }
    .form-modal-input.is-invalid {
    border-color: var(--red);
    background: color-mix(in srgb, var(--red) 2%, var(--panel));
    }
    .form-field-error { font-size: 11.5px; color: var(--red); font-weight: 600; margin-top: 6px; display: none; text-align:
    left; }

    /* ── TOGGLE ─── */
    .toggle-card {
    padding: 12px 16px; border-radius: 10px;
    background: linear-gradient(135deg, var(--surface) 0%, color-mix(in srgb, var(--accent) 1%, var(--surface)) 100%);
    border: 1px solid var(--border);
    display: flex; align-items: center; gap: 14px; transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    margin-top: 10px; margin-bottom: 12px;
    }
    .toggle-card:hover {
    border-color: var(--accent);
    box-shadow: 0 4px 12px color-mix(in srgb, var(--accent) 10%, transparent);
    }
    .toggle-switch { position: relative; width: 48px; height: 28px; flex-shrink: 0; }
    .toggle-switch input { opacity: 0; width: 0; height: 0; }
    .toggle-slider {
    position: absolute; inset: 0; background: #cbd5e1; border-radius: 14px; cursor: pointer;
    transition: all 0.35s cubic-bezier(0.4, 0, 0.2, 1);
    box-shadow: inset 0 2px 4px rgba(0, 0, 0, 0.08);
    }
    .toggle-slider::before {
    content: ''; position: absolute; width: 22px; height: 22px; left: 3px; top: 3px; background: #fff;
    border-radius: 50%; transition: all 0.35s cubic-bezier(0.4, 0, 0.2, 1);
    box-shadow: 0 2px 6px rgba(0, 0, 0, 0.12);
    }
    .toggle-switch input:checked + .toggle-slider {
    background: var(--green);
    box-shadow: 0 4px 12px color-mix(in srgb, var(--green) 35%, transparent);
    }
    .toggle-switch input:checked + .toggle-slider::before { transform: translateX(20px); }
    .toggle-label { font-size: 13.5px; font-weight: 700; color: var(--text-2); }

    .modal-icon-blue { background: rgba(59, 130, 246, 0.1) !important; }
    .modal-icon-blue svg { color: #3b82f6 !important; }
    .modal-icon-amber { background: rgba(245, 158, 11, 0.1) !important; }
    .modal-icon-amber svg { color: #f59e0b !important; }

    /* ── Filter Card ─────────────────────────────── */
    .filter-card {
    background: var(--panel); border: 1px solid var(--border); border-radius: 14px;
    padding: 20px 22px; box-shadow: var(--shadow-sm); margin-bottom: 20px;
    }
    .filter-card-top { display: flex; align-items: center; gap: 8px; margin-bottom: 16px; }
    .filter-card-title { font-size: 13px; font-weight: 700; color: var(--text-1); }
    .filter-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 15px; align-items: flex-end; }
    @media (max-width: 900px) { .filter-grid { grid-template-columns: 1fr 1fr; } }
    @media (max-width: 560px) { .filter-grid { grid-template-columns: 1fr; } }
    .form-group { display: flex; flex-direction: column; min-width: 0; }
    .form-label { display: block; font-size: 11.5px; font-weight: 700; color: var(--text-3); margin-bottom: 6px; text-transform: uppercase; letter-spacing: 0.05em; }
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
    .filter-actions .btn-primary, .filter-actions .btn-outline-reset { justify-content: center; width: 100%; padding: 11px; }
    }
    .btn-outline-reset {
    display: inline-flex; align-items: center; gap: 7px; background: var(--red-dim); color: var(--red);
    border: 1px solid rgba(220, 38, 38, 0.2); padding: 9px 16px; border-radius: 9px; font-family: var(--font);
    font-weight: 700; font-size: 13px; cursor: pointer; text-decoration: none; transition: all 0.15s;
    }
    .btn-outline-reset:hover { border-color: rgba(220, 38, 38, 0.4); background: color-mix(in srgb, var(--red-dim) 80%, var(--red)); color: var(--red); }
    .btn-outline-reset svg { width: 13px; height: 13px; }
@endsection

@section('content')
    <div class="page-header">
        <div class="page-header-left">
            <h1>
                <span class="page-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                        stroke-linejoin="round">
                        <rect x="1" y="3" width="15" height="13"></rect>
                        <polygon points="16 8 20 8 23 11 23 16 16 16 16 8"></polygon>
                        <circle cx="5.5" cy="18.5" r="2.5"></circle>
                        <circle cx="18.5" cy="18.5" r="2.5"></circle>
                    </svg>
                </span>
                Data Kurir
            </h1>
            <p>Kelola semua partner logistik untuk pengiriman pesanan.</p>
        </div>
        <button type="button" class="btn-primary" onclick="openCourierModal()">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"
                stroke-linejoin="round" style="width:14px;height:14px;">
                <line x1="12" y1="5" x2="12" y2="19"></line>
                <line x1="5" y1="12" x2="19" y2="12"></line>
            </svg>
            Tambah Kurir
        </button>
    </div>

    {{-- Flash & Validation Errors are handled globally by toast notifications in app.blade.php --}}

    {{-- Stats Bar --}}
    <div class="stats-bar">
        <div class="stat-card">
            <div class="stat-icon blue">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                    stroke-linejoin="round">
                    <rect x="1" y="3" width="15" height="13"></rect>
                    <polygon points="16 8 20 8 23 11 23 16 16 16 16 8"></polygon>
                    <circle cx="5.5" cy="18.5" r="2.5"></circle>
                    <circle cx="18.5" cy="18.5" r="2.5"></circle>
                </svg>
            </div>
            <div>
                <div class="stat-value">{{ $couriers->count() }}</div>
                <div class="stat-label">Total Kurir</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon green">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                    stroke-linejoin="round">
                    <polyline points="20 6 9 17 4 12"></polyline>
                </svg>
            </div>
            <div>
                <div class="stat-value">{{ $couriers->where('is_active', true)->count() }}</div>
                <div class="stat-label">Kurir Aktif</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon gray">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                    stroke-linejoin="round">
                    <circle cx="12" cy="12" r="10"></circle>
                    <line x1="4.93" y1="4.93" x2="19.07" y2="19.07"></line>
                </svg>
            </div>
            <div>
                <div class="stat-value">{{ $couriers->where('is_active', false)->count() }}</div>
                <div class="stat-label">Nonaktif</div>
            </div>
        </div>
    </div>

    {{-- Filter Card --}}
    <div class="filter-card">
        <div class="filter-card-top">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="width:16px;height:16px;color:var(--text-3);">
                <polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3" />
            </svg>
            <div class="filter-card-title">Filter Kurir</div>
        </div>
        <div class="filter-grid">
            <div class="form-group">
                <label class="form-label" for="dtFilterStatus">Status Aktif</label>
                <select id="dtFilterStatus" class="form-input">
                    <option value="">Semua Status</option>
                    <option value="1">Aktif</option>
                    <option value="0">Nonaktif</option>
                </select>
            </div>
            <div class="form-group">
                <label class="form-label" for="dtFilterServices">Layanan Kurir</label>
                <select id="dtFilterServices" class="form-input">
                    <option value="">Semua</option>
                    <option value="yes">Memiliki Layanan</option>
                    <option value="no">Belum Ada Layanan</option>
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
            <table class="data-table" id="courierTable">
                <thead>
                    <tr>
                        <th class="center" style="width:50px;">No</th>
                        <th>Nama Kurir</th>
                        <th>Kode</th>
                        <th>Layanan</th>
                        <th>Status</th>
                        <th class="center" style="width:130px;">Aksi</th>
                    </tr>
                </thead>
                <tbody id="courierTableBody">
                    @include('couriers._table_rows')
                </tbody>
            </table>
        </div>
    </div>

    {{-- Delete Modal --}}
    <div class="modal-overlay" id="deleteModal">
        <div class="modal-box">
            <div class="modal-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                    stroke-linejoin="round">
                    <polyline points="3 6 5 6 21 6"></polyline>
                    <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                </svg>
            </div>
            <div class="modal-title">Hapus Kurir?</div>
            <div class="modal-desc" id="modal-desc">
                Data kurir ini akan dihapus secara permanen.
            </div>
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

    {{-- Courier Form Modal (Create / Edit) --}}
    <div class="modal-overlay" id="courierModal">
        <div class="modal-box modal-box-form">
            <div style="display: flex; align-items: center; gap: 16px; margin-bottom: 24px;">
                <div class="modal-icon" id="courierModalIcon" style="margin-bottom: 0;">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                        stroke-linecap="round" stroke-linejoin="round" id="courierModalIconSvg">
                        <line x1="12" y1="5" x2="12" y2="19"></line>
                        <line x1="5" y1="12" x2="19" y2="12" />
                    </svg>
                </div>
                <div style="text-align: left;">
                    <div class="modal-title" id="courierModalTitle"
                        style="margin-bottom: 4px; font-size: 18px; font-weight: 800; color: var(--text-1); letter-spacing: -0.02em;">
                        Tambah Kurir</div>
                    <div style="font-size: 12.5px; color: var(--text-3); font-weight: 500;" id="courierModalSubtitle">Isi
                        detail kurir logistik di bawah ini.</div>
                </div>
            </div>

            <form id="courierForm" method="POST" novalidate>
                @csrf
                <div id="methodField"></div>
                <input type="hidden" name="id" id="courierIdInput" value="{{ old('id') }}">

                <div class="form-modal-field">
                    <label class="form-modal-label" for="courierNameInput">Nama Kurir <span>*</span></label>
                    <input type="text" id="courierNameInput" name="name"
                        class="form-modal-input @error('name') is-invalid @enderror"
                        placeholder="Contoh: J&T Express, JNE, SiCepat" value="{{ old('name') }}" required>
                    @error('name') <div class="form-field-error" style="display:block;">{{ $message }}</div> @enderror
                </div>

                <div class="form-modal-field">
                    <label class="form-modal-label" for="courierCodeInput">Kode Kurir <span>*</span></label>
                    <input type="text" id="courierCodeInput" name="code"
                        class="form-modal-input @error('code') is-invalid @enderror"
                        placeholder="Contoh: jnt, jne, sicepat" value="{{ old('code') }}" required>
                    @error('code') <div class="form-field-error" style="display:block;">{{ $message }}</div> @enderror
                </div>

                <div class="form-modal-field">
                    <label class="form-modal-label" for="courierContactInput">Contact Person <span>*</span></label>
                    <input type="text" id="courierContactInput" name="contact_person"
                        class="form-modal-input @error('contact_person') is-invalid @enderror"
                        placeholder="Nama Kontak" value="{{ old('contact_person') }}" required>
                    @error('contact_person') <div class="form-field-error" style="display:block;">{{ $message }}</div> @enderror
                </div>

                <div class="form-modal-field">
                    <label class="form-modal-label" for="courierPhoneInput">Nomor Telepon <span>*</span></label>
                    <input type="text" id="courierPhoneInput" name="phone_number"
                        class="form-modal-input @error('phone_number') is-invalid @enderror"
                        placeholder="081234567890" value="{{ old('phone_number') }}" required>
                    @error('phone_number') <div class="form-field-error" style="display:block;">{{ $message }}</div> @enderror
                </div>

                <div class="form-modal-field">
                    <label class="form-modal-label" for="courierEmailInput">Email <span>*</span></label>
                    <input type="email" id="courierEmailInput" name="email"
                        class="form-modal-input @error('email') is-invalid @enderror"
                        placeholder="email@kurir.com" value="{{ old('email') }}" required>
                    @error('email') <div class="form-field-error" style="display:block;">{{ $message }}</div> @enderror
                </div>

                <div class="form-modal-field">
                    <label class="form-modal-label" for="courierDescInput">Deskripsi</label>
                    <textarea id="courierDescInput" name="description"
                        class="form-modal-input @error('description') is-invalid @enderror"
                        placeholder="Deskripsi operasional kurir">{{ old('description') }}</textarea>
                    @error('description') <div class="form-field-error" style="display:block;">{{ $message }}</div> @enderror
                </div>

                <div class="toggle-card">
                    <label class="toggle-switch">
                        <input type="hidden" name="is_active" value="0">
                        <input type="checkbox" name="is_active" id="courierActiveInput" value="1"
                            {{ old('is_active', '1') == '1' ? 'checked' : '' }}>
                        <span class="toggle-slider"></span>
                    </label>
                    <div class="toggle-label">Kurir Aktif</div>
                </div>
                <div class="modal-actions">
                    <button type="button" class="btn-cancel" onclick="closeCourierModal()">Batal</button>
                    <button type="submit" class="btn-primary" id="courierModalSubmit" style="border:none;">Tambah
                        Kurir</button>
                </div>
            </form>
        </div>
    </div>

@endsection

@push('scripts')
    {{-- jQuery & DataTables --}}
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <link rel="stylesheet" href="https://cdn.datatables.net/2.3.7/css/dataTables.dataTables.min.css">
    <script src="https://cdn.datatables.net/2.3.7/js/dataTables.min.js"></script>

    <script>
        $.fn.dataTable.ext.errMode = 'none';

        // 1. FUNGSI GLOBAL
        function openDeleteModal(id, name) {
            document.getElementById('modal-desc').innerHTML =
                `Kurir <strong>"${name}"</strong> akan dihapus. Tindakan ini tidak dapat dibatalkan.`;
            document.getElementById('deleteForm').action = '/couriers/' + id;
            document.getElementById('deleteModal').classList.add('open');
        }

        function closeDeleteModal() {
            document.getElementById('deleteModal').classList.remove('open');
        }

        document.getElementById('deleteModal').addEventListener('click', function(e) {
            if (e.target === this) closeDeleteModal();
        });

        let table;

        // 2. Fungsi Helper untuk Konfigurasi DataTable
        function initDataTable() {
            return $('#courierTable').DataTable({
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
                            <rect x="1" y="3" width="15" height="13"></rect><polygon points="16 8 20 8 23 11 23 16 16 16 16 8"></polygon><circle cx="5.5" cy="18.5" r="2.5"></circle><circle cx="18.5" cy="18.5" r="2.5"></circle>
                        </svg>
                    </div>
                    <div class="empty-title" style="margin-bottom: 6px; font-size: 18px; font-weight: 800; color: var(--text-1); letter-spacing: -0.02em;">Tidak Ada Kurir Ditemukan</div>
                    <div class="empty-desc" style="font-size: 13px; color: var(--text-3);">Tidak ada kurir yang cocok dengan pencarian atau filter yang dipilih.</div>
                </div>`,
                    emptyTable: `
                <div class="empty-state" style="padding: 40px 20px; text-align: center; display: flex; flex-direction: column; align-items: center; justify-content: center; margin: 0 auto; width: 100%; border-bottom: none;">
                    <div class="empty-icon" style="margin-bottom: 20px; width: 88px; height: 88px; background: color-mix(in srgb, var(--accent) 10%, transparent); border-radius: 24px; display: flex; align-items: center; justify-content: center; border: 1px solid color-mix(in srgb, var(--accent) 15%, transparent);">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" style="width: 38px; height: 38px; color: var(--accent); opacity: 0.9;">
                            <rect x="1" y="3" width="15" height="13"></rect><polygon points="16 8 20 8 23 11 23 16 16 16 16 8"></polygon><circle cx="5.5" cy="18.5" r="2.5"></circle><circle cx="18.5" cy="18.5" r="2.5"></circle>
                        </svg>
                    </div>
                    <div class="empty-title" style="margin-bottom: 6px; font-size: 18px; font-weight: 800; color: var(--text-1); letter-spacing: -0.02em;">Tidak Ada Kurir Ditemukan</div>
                    <div class="empty-desc" style="font-size: 13px; color: var(--text-3);">Belum ada data kurir yang masuk saat ini.</div>
                </div>`,
                    paginate: {
                        first: "«",
                        last: "»",
                        next: "›",
                        previous: "‹"
                    }
                },
                columnDefs: [{
                        targets: 0,
                        orderable: false,
                        searchable: false
                    },
                    {
                        targets: -1,
                        orderable: false,
                        searchable: false
                    }
                ],
                order: [
                    [1, 'asc']
                ],
                drawCallback: function() {
                    let api = this.api();
                    let startIndex = api.page.info().start;

                    api.column(0, {
                        page: 'current'
                    }).nodes().each(function(cell, i) {
                        cell.innerHTML = startIndex + i + 1;
                    });
                }
            });
        }

        function checkResetVisibility() {
            const hasFilter = $('#dtFilterStatus').val() || $('#dtFilterServices').val();
            if (hasFilter) {
                $('#btnResetFilter').fadeIn(200).css('display', 'inline-flex');
            } else {
                $('#btnResetFilter').fadeOut(200);
            }
        }

        $(document).ready(function() {
            // Inisialisasi Pertama
            table = initDataTable();
            $('.dataTables_filter').show();

            $('#btnApplyFilter').on('click', function() {
                const params = {
                    search: '',
                    is_active: $('#dtFilterStatus').val(),
                    has_services: $('#dtFilterServices').val(),
                };

                $('#courierTable tbody').css('opacity', '0.4');

                $.ajax({
                    url: "{{ route('couriers.index') }}",
                    type: 'GET',
                    data: params,
                    success: function(html) {
                        if ($.fn.DataTable.isDataTable('#courierTable')) {
                            table.destroy();
                        }
                        $('#courierTable tbody').html(html);
                        $('#courierTable tbody tr').addClass('fade-in-animated');
                        table = initDataTable();
                        $('.dataTables_filter').show();
                        $('#courierTable tbody').css('opacity', '1');

                        checkResetVisibility();
                    },
                    error: function() {
                        $('#courierTable tbody').css('opacity', '1');
                        if (typeof showToast === 'function') {
                            showToast('Gagal memuat data kurir.', 'error');
                        }
                    }
                });
            });

            $('#btnResetFilter').on('click', function() {
                $('#dtFilterStatus').val('');
                $('#dtFilterServices').val('');
                $('#btnApplyFilter').click();
            });

            checkResetVisibility();
        });

        function openCourierModal(id = null, name = '', code = '', contact_person = '', phone_number = '', email = '', description = '', isActive = '1', isValidationError = false) {
            const modal = document.getElementById('courierModal');
            const form = document.getElementById('courierForm');
            const title = document.getElementById('courierModalTitle');
            const submitBtn = document.getElementById('courierModalSubmit');
            const methodField = document.getElementById('methodField');
            const modalIcon = document.getElementById('courierModalIcon');
            const modalIconSvg = document.getElementById('courierModalIconSvg');
            const subtitle = document.getElementById('courierModalSubtitle');

            // Reset validation error displays ONLY if it's NOT a validation error reload
            if (!isValidationError) {
                document.querySelectorAll('#courierModal .form-field-error').forEach(el => el.style.display = 'none');
                document.querySelectorAll('#courierModal .form-modal-input').forEach(el => el.classList.remove(
                    'is-invalid'));
            }

            if (id) {
                title.textContent = 'Edit Kurir';
                subtitle.textContent = 'Perbarui detail kurir logistik di bawah ini.';
                submitBtn.textContent = 'Simpan Perubahan';
                form.action = `/couriers/${id}`;
                methodField.innerHTML = '@method('PUT')';

                // Edit icon (Amber Pencil)
                modalIcon.className = 'modal-icon modal-icon-amber';
                modalIconSvg.innerHTML =
                    '<path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4L18.5 2.5z"></path>';

                document.getElementById('courierIdInput').value = id;
                document.getElementById('courierNameInput').value = name;
                document.getElementById('courierCodeInput').value = code;
                document.getElementById('courierContactInput').value = contact_person;
                document.getElementById('courierPhoneInput').value = phone_number;
                document.getElementById('courierEmailInput').value = email;
                document.getElementById('courierDescInput').value = description;
                document.getElementById('courierActiveInput').checked = (isActive == '1' || isActive === 'true');
            } else {
                title.textContent = 'Tambah Kurir Baru';
                subtitle.textContent = 'Isi detail kurir logistik di bawah ini.';
                submitBtn.textContent = 'Tambah Kurir';
                form.action = '{{ route('couriers.store') }}';
                methodField.innerHTML = '';

                // Add icon (Blue Plus)
                modalIcon.className = 'modal-icon modal-icon-blue';
                modalIconSvg.innerHTML =
                '<line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12">';

                document.getElementById('courierIdInput').value = '';
                document.getElementById('courierNameInput').value = '';
                document.getElementById('courierCodeInput').value = '';
                document.getElementById('courierContactInput').value = '';
                document.getElementById('courierPhoneInput').value = '';
                document.getElementById('courierEmailInput').value = '';
                document.getElementById('courierDescInput').value = '';
                document.getElementById('courierActiveInput').checked = (isActive == '1' || isActive === 'true' || isActive === true);
            }

            modal.classList.add('open');
            setTimeout(() => document.getElementById('courierNameInput').focus(), 200);
        }

        function closeCourierModal() {
            document.getElementById('courierModal').classList.remove('open');
        }

        document.getElementById('courierModal').addEventListener('click', function(e) {
            if (e.target === this) closeCourierModal();
        });

        @if ($errors->any())
            $(document).ready(function() {
                const oldId = '{{ old('id') }}';
                if (oldId) {
                    openCourierModal(oldId, `{!! addslashes(old('name')) !!}`, '{{ old('code') }}', '{{ old('contact_person') }}', '{{ old('phone_number') }}', '{{ old('email') }}', `{!! addslashes(old('description')) !!}`,
                        '{{ old('is_active') }}', true);
                } else {
                    openCourierModal(null, `{!! addslashes(old('name')) !!}`, '{{ old('code') }}', '{{ old('contact_person') }}', '{{ old('phone_number') }}', '{{ old('email') }}', `{!! addslashes(old('description')) !!}`,
                        '{{ old('is_active') }}', true);
                }
            });
        @endif
    </script>
@endpush

