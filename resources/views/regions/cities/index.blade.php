@extends('layouts.app')

@section('title', 'Data Kota / Kabupaten')

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
    .city-name { font-size: 14px; font-weight: 700; color: var(--text-1); }
    .province-pill {
        display: inline-flex; align-items: center; font-size: 11.5px; font-weight: 600;
        color: var(--accent); background: var(--accent-dim); padding: 3px 9px; border-radius: 6px;
        border: 1px solid color-mix(in srgb, var(--accent) 20%, transparent);
    }
    .status-pill {
        display: inline-flex; align-items: center; font-size: 11px; font-weight: 700;
        padding: 4px 10px; border-radius: 6px; text-transform: uppercase; letter-spacing: 0.05em;
    }
    .status-pill.blue  { background: rgba(59, 130, 246, 0.1); color: #3b82f6; border: 1px solid rgba(59, 130, 246, 0.2); }
    .status-pill.amber { background: rgba(245, 158, 11, 0.1); color: #f59e0b; border: 1px solid rgba(245, 158, 11, 0.2); }

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
    .modal-title  { font-size: 16px; font-weight: 800; color: var(--text-1); margin-bottom: 6px; }
    .modal-desc   { font-size: 13px; color: var(--text-2); margin-bottom: 22px; line-height: 1.6; }
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

    .modal-icon-blue  { background: rgba(59, 130, 246, 0.1) !important; }
    .modal-icon-blue svg { color: #3b82f6 !important; }
    .modal-icon-amber { background: rgba(245, 158, 11, 0.1) !important; }
    .modal-icon-amber svg { color: #f59e0b !important; }

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
                        <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path>
                        <circle cx="12" cy="10" r="3"></circle>
                    </svg>
                </span>
                Data Kota / Kabupaten
            </h1>
            <p>Kelola data kota dan kabupaten untuk pemetaan rute pengiriman.</p>
        </div>
        <button type="button" class="btn-primary" onclick="openCityModal()">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                <line x1="12" y1="5" x2="12" y2="19"></line>
                <line x1="5" y1="12" x2="19" y2="12"></line>
            </svg>
            Tambah Kota
        </button>
    </div>

    {{-- Stats Bar --}}
    <div class="stats-bar">
        <div class="stat-card">
            <div class="stat-icon blue">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="10"></circle>
                    <line x1="2" y1="12" x2="22" y2="12"></line>
                    <path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"></path>
                </svg>
            </div>
            <div>
                <div class="stat-value">{{ $cities->count() }}</div>
                <div class="stat-label">Total Wilayah</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon purple">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="4" y="2" width="16" height="20" rx="2" ry="2"></rect>
                    <line x1="9" y1="22" x2="9" y2="16"></line>
                    <line x1="15" y1="22" x2="15" y2="16"></line>
                    <line x1="9" y1="16" x2="15" y2="16"></line>
                    <path d="M9 6h6"></path>
                    <path d="M9 10h6"></path>
                </svg>
            </div>
            <div>
                <div class="stat-value">{{ $cities->where('type', 'Kota')->count() }}</div>
                <div class="stat-label">Tipe Kota</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon amber">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
                    <polyline points="9 22 9 12 15 12 15 22"></polyline>
                </svg>
            </div>
            <div>
                <div class="stat-value">{{ $cities->where('type', 'Kabupaten')->count() }}</div>
                <div class="stat-label">Tipe Kabupaten</div>
            </div>
        </div>
    </div>

    {{-- Filter Card --}}
    <div class="filter-card">
        <div class="filter-card-top">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="width:16px;height:16px;color:var(--text-3);">
                <polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3" />
            </svg>
            <div class="filter-card-title">Filter Kota / Kabupaten</div>
        </div>
        <div class="filter-grid">
            <div class="form-group">
                <label class="form-label" for="dtFilterProvince">Provinsi</label>
                <select id="dtFilterProvince" class="form-input">
                    <option value="">Semua Provinsi</option>
                    @foreach ($provinces as $province)
                        <option value="{{ $province->id }}">{{ $province->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label class="form-label" for="dtFilterType">Tipe Wilayah</label>
                <select id="dtFilterType" class="form-input">
                    <option value="">Semua Tipe</option>
                    <option value="Kota">Kota</option>
                    <option value="Kabupaten">Kabupaten</option>
                </select>
            </div>
            <div class="form-group">
                <label class="form-label" for="dtFilterPostalCode">Kodepos</label>
                <input type="text" id="dtFilterPostalCode" class="form-input" placeholder="Contoh: 601">
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
            <table class="data-table" id="cityTable">
                <thead>
                    <tr>
                        <th class="center" style="width:60px;">No</th>
                        <th>Nama Kota</th>
                        <th>Provinsi</th>
                        <th>Tipe</th>
                        <th>Kodepos</th>
                        <th>Status</th>
                        <th class="center" style="width:150px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @include('regions.cities._table_rows')
                </tbody>
            </table>
        </div>
    </div>

    {{-- Delete Modal --}}
    <div class="modal-overlay" id="deleteModal">
        <div class="modal-box">
            <div class="modal-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="3 6 5 6 21 6"></polyline>
                    <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                </svg>
            </div>
            <div class="modal-title">Hapus Wilayah?</div>
            <div class="modal-desc" id="modal-desc">Data wilayah ini akan dihapus secara permanen.</div>
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

    {{-- City Form Modal (Create / Edit) --}}
    <div class="modal-overlay" id="cityModal">
        <div class="modal-box modal-box-form">
            <div style="display:flex; align-items:center; gap:16px; margin-bottom:24px;">
                <div class="modal-icon" id="cityModalIcon" style="margin-bottom:0;">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" id="cityModalIconSvg">
                        <line x1="12" y1="5" x2="12" y2="19"></line>
                        <line x1="5" y1="12" x2="19" y2="12" />
                    </svg>
                </div>
                <div style="text-align:left;">
                    <div class="modal-title" id="cityModalTitle" style="margin-bottom:4px; font-size:18px; font-weight:800; color:var(--text-1); letter-spacing:-0.02em;">Tambah Kota</div>
                    <div style="font-size:12.5px; color:var(--text-3); font-weight:500;" id="cityModalSubtitle">Isi detail data kota / kabupaten di bawah ini.</div>
                </div>
            </div>

            <form id="cityForm" method="POST" novalidate>
                @csrf
                <div id="methodField"></div>
                <input type="hidden" name="id" id="cityIdInput" value="{{ old('id') }}">

                <div class="form-modal-field">
                    <label class="form-modal-label" for="provinceId">Provinsi <span>*</span></label>
                    <select name="province_id" id="provinceId" class="form-modal-input @error('province_id') is-invalid @enderror" required>
                        <option value="">-- Pilih Provinsi --</option>
                        @foreach($provinces as $province)
                            <option value="{{ $province->id }}" data-active="{{ $province->is_active ? '1' : '0' }}" {{ old('province_id') == $province->id ? 'selected' : '' }}>
                                {{ $province->name }} {{ !$province->is_active ? '(Nonaktif)' : '' }}
                            </option>
                        @endforeach
                    </select>
                    @error('province_id') <div class="form-field-error" style="display:block;">{{ $message }}</div> @enderror
                </div>

                <div class="form-modal-field">
                    <label class="form-modal-label" for="cityNameInput">Nama Kota / Kabupaten <span>*</span></label>
                    <input type="text" name="name" id="cityNameInput"
                        class="form-modal-input @error('name') is-invalid @enderror"
                        required placeholder="Contoh: Kota Bandung" value="{{ old('name') }}">
                    @error('name') <div class="form-field-error" style="display:block;">{{ $message }}</div> @enderror
                </div>

                <div class="form-modal-field">
                    <label class="form-modal-label" for="cityCodeInput">Kode Kota <span>*</span></label>
                    <input type="text" name="code" id="cityCodeInput"
                        class="form-modal-input @error('code') is-invalid @enderror"
                        required placeholder="Contoh: BDG" value="{{ old('code') }}">
                    @error('code') <div class="form-field-error" style="display:block;">{{ $message }}</div> @enderror
                </div>

                <div style="display:grid; grid-template-columns:1fr 1fr; gap:16px; margin-bottom:20px;">
                    <div class="form-modal-field" style="margin-bottom:0;">
                        <label class="form-modal-label" for="cityTypeInput">Tipe <span>*</span></label>
                        <select name="type" id="cityTypeInput" class="form-modal-input @error('type') is-invalid @enderror" required>
                            <option value="Kota"      {{ old('type') == 'Kota'      ? 'selected' : '' }}>Kota</option>
                            <option value="Kabupaten" {{ old('type') == 'Kabupaten' ? 'selected' : '' }}>Kabupaten</option>
                        </select>
                        @error('type') <div class="form-field-error" style="display:block;">{{ $message }}</div> @enderror
                    </div>
                    <div class="form-modal-field" style="margin-bottom:0;">
                        <label class="form-modal-label" for="postalCodeInput">Kodepos <span>*</span></label>
                        <input type="text" name="postal_code" id="postalCodeInput"
                            class="form-modal-input @error('postal_code') is-invalid @enderror"
                            required placeholder="40xxx" value="{{ old('postal_code') }}">
                        @error('postal_code') <div class="form-field-error" style="display:block;">{{ $message }}</div> @enderror
                    </div>
                </div>

                <div class="toggle-card" id="activeToggleCard" onclick="document.getElementById('isActive').click()">
                    <div class="toggle-info">
                        <div class="toggle-title">Status Kota Aktif</div>
                        <div class="toggle-desc" id="activeToggleDesc">Wilayah ini dapat dipilih oleh pengguna.</div>
                    </div>
                    <label class="custom-switch" onclick="event.stopPropagation()">
                        <input type="checkbox" name="is_active" id="isActive" value="1" {{ old('is_active', '1') == '1' ? 'checked' : '' }}>
                        <span class="slider"></span>
                    </label>
                </div>
                @error('is_active') <div class="form-field-error" style="display:block; margin-top:-14px; margin-bottom:20px;">{{ $message }}</div> @enderror

                <div class="modal-actions">
                    <button type="button" class="btn-cancel" onclick="closeCityModal()">Batal</button>
                    <button type="submit" class="btn-primary" id="cityModalSubmit" style="border:none;">Tambah Kota</button>
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
        function openDeleteModal(id, name, type) {
            document.getElementById('modal-desc').innerHTML =
                `${type} <strong>"${name}"</strong> akan dihapus. Tindakan ini tidak dapat dibatalkan.`;
            document.getElementById('deleteForm').action = '/cities/' + id;
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
            return $('#cityTable').DataTable({
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
                                    <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path><circle cx="12" cy="10" r="3"></circle>
                                </svg>
                            </div>
                            <div style="margin-bottom: 6px; font-size: 18px; font-weight: 800; color: var(--text-1); letter-spacing: -0.02em;">Tidak Ada Kota Ditemukan</div>
                            <div style="font-size: 13px; color: var(--text-3);">Tidak ada kota yang cocok dengan pencarian atau filter yang dipilih.</div>
                        </div>`,
                    emptyTable: `
                        <div style="padding: 40px 20px; text-align: center; display: flex; flex-direction: column; align-items: center; justify-content: center; margin: 0 auto; width: 100%; border-bottom: none;">
                            <div style="margin-bottom: 20px; width: 88px; height: 88px; background: color-mix(in srgb, var(--accent) 10%, transparent); border-radius: 24px; display: flex; align-items: center; justify-content: center; border: 1px solid color-mix(in srgb, var(--accent) 15%, transparent);">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" style="width: 38px; height: 38px; color: var(--accent); opacity: 0.9;">
                                    <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path><circle cx="12" cy="10" r="3"></circle>
                                </svg>
                            </div>
                            <div style="margin-bottom: 6px; font-size: 18px; font-weight: 800; color: var(--text-1); letter-spacing: -0.02em;">Belum Ada Data Kota</div>
                            <div style="font-size: 13px; color: var(--text-3);">Belum ada data kota yang dimasukkan saat ini.</div>
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
            const hasFilter = $('#dtFilterProvince').val() || $('#dtFilterType').val() || $('#dtFilterPostalCode').val();
            if (hasFilter) {
                $('#btnResetFilter').fadeIn(200).css('display', 'inline-flex');
            } else {
                $('#btnResetFilter').fadeOut(200);
            }
        }

        $(document).ready(function() {
            table = initDataTable();

            checkResetVisibility();

            $('#btnApplyFilter').on('click', function() {
                const params = {
                    province_id:  $('#dtFilterProvince').val(),
                    type:         $('#dtFilterType').val(),
                    postal_code:  $('#dtFilterPostalCode').val(),
                };

                $('#cityTable tbody').css('opacity', '0.4');

                $.ajax({
                    url:  "{{ route('cities.index') }}",
                    type: 'GET',
                    data: params,
                    success: function(html) {
                        if ($.fn.DataTable.isDataTable('#cityTable')) {
                            table.destroy();
                        }
                        $('#cityTable tbody').html(html);
                        $('#cityTable tbody tr').addClass('fade-in-animated');
                        table = initDataTable();
                        $('#cityTable tbody').css('opacity', '1');
                        checkResetVisibility();
                    },
                    error: function() {
                        $('#cityTable tbody').css('opacity', '1');
                        if (typeof showToast === 'function') {
                            showToast('Gagal memuat data kota.', 'error');
                        }
                    }
                });
            });

            $('#btnResetFilter').on('click', function() {
                $('#dtFilterProvince').val('');
                $('#dtFilterType').val('');
                $('#dtFilterPostalCode').val('');
                $('#btnApplyFilter').trigger('click');
            });
        });

        document.getElementById('isActive').addEventListener('change', function() {
            const card = document.getElementById('activeToggleCard');
            const desc = document.getElementById('activeToggleDesc');
            if(this.checked) {
                card.classList.add('active');
                desc.textContent = 'Wilayah ini dapat dipilih oleh pengguna.';
            } else {
                card.classList.remove('active');
                desc.textContent = 'Disembunyikan dari pilihan pengguna (Nonaktif).';
            }
        });

        function openCityModal(id = null, name = '', code = '', provinceId = '', type = 'Kota', postalCode = '', isActive = 1, isValidationError = false) {
            const modal        = document.getElementById('cityModal');
            const form         = document.getElementById('cityForm');
            const title        = document.getElementById('cityModalTitle');
            const submitBtn    = document.getElementById('cityModalSubmit');
            const methodField  = document.getElementById('methodField');
            const modalIcon    = document.getElementById('cityModalIcon');
            const modalIconSvg = document.getElementById('cityModalIconSvg');
            const subtitle     = document.getElementById('cityModalSubtitle');

            if (!isValidationError) {
                document.querySelectorAll('#cityModal .form-field-error').forEach(el => el.style.display = 'none');
                document.querySelectorAll('#cityModal .form-modal-input').forEach(el => el.classList.remove('is-invalid'));
            }

            if (id) {
                title.textContent      = 'Edit Kota';
                subtitle.textContent   = 'Perbarui detail data kota / kabupaten di bawah ini.';
                submitBtn.textContent  = 'Simpan Perubahan';
                form.action            = `/cities/${id}`;
                methodField.innerHTML  = '@method("PUT")';
                modalIcon.className    = 'modal-icon modal-icon-amber';
                modalIconSvg.innerHTML = '<path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4L18.5 2.5z"></path>';

                document.getElementById('cityIdInput').value    = id;
                document.getElementById('cityNameInput').value  = name;
                document.getElementById('cityCodeInput').value  = code;
                document.getElementById('provinceId').value     = provinceId;
                document.getElementById('cityTypeInput').value  = type;
                document.getElementById('postalCodeInput').value = postalCode;
                document.getElementById('isActive').checked = (isActive == '1' || isActive === 'true');
            } else {
                title.textContent      = 'Tambah Kota';
                subtitle.textContent   = 'Isi detail data kota / kabupaten di bawah ini.';
                submitBtn.textContent  = 'Tambah Kota';
                form.action            = '{{ route("cities.store") }}';
                methodField.innerHTML  = '';
                modalIcon.className    = 'modal-icon modal-icon-blue';
                modalIconSvg.innerHTML = '<line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12">';

                document.getElementById('cityIdInput').value    = '';
                document.getElementById('cityNameInput').value  = '';
                document.getElementById('cityCodeInput').value  = '';
                document.getElementById('provinceId').value     = '';
                document.getElementById('cityTypeInput').value  = 'Kota';
                document.getElementById('postalCodeInput').value = '';
                document.getElementById('isActive').checked = (isActive == '1' || isActive === 'true' || isActive === true);
            }

            document.getElementById('isActive').dispatchEvent(new Event('change'));

            modal.classList.add('open');
            setTimeout(() => document.getElementById('cityNameInput').focus(), 200);
        }

        function closeCityModal() {
            document.getElementById('cityModal').classList.remove('open');
        }

        document.getElementById('cityModal').addEventListener('click', function(e) {
            if (e.target === this) closeCityModal();
        });

        @if ($errors->any())
            $(document).ready(function() {
                const oldId = '{{ old('id') }}';
                if (oldId) {
                    openCityModal(oldId, `{!! addslashes(old('name')) !!}`, '{{ old('code') }}', '{{ old('province_id') }}',
                        '{{ old('type') }}', '{{ old('postal_code') }}', '{{ old('is_active') }}', true);
                } else {
                    openCityModal(null, `{!! addslashes(old('name')) !!}`, '{{ old('code') }}', '{{ old('province_id') }}',
                        '{{ old('type') }}', '{{ old('postal_code') }}', '{{ old('is_active') }}', true);
                }
            });
        @endif
    </script>
@endpush
