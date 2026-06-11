@extends('layouts.app')

@section('title', 'Manajemen Data Produk')

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
    .stat-icon { width: 42px; height: 42px; border-radius: 10px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
    .stat-icon svg { width: 20px; height: 20px; }
    .stat-icon.blue   { background: rgba(59, 130, 246, 0.1); color: #3b82f6; }
    .stat-icon.green  { background: var(--green-dim); color: var(--green); }
    .stat-icon.gray   { background: var(--surface-2); color: var(--text-3); }
    .stat-icon.amber  { background: var(--amber-dim); color: var(--amber); }
    .stat-icon.purple { background: rgba(139, 92, 246, 0.1); color: #8b5cf6; }
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

    /* FIX: removed duplicated .filter-grid.filter-grid double selector */
    .filter-grid {
        display: grid;
        grid-template-columns: repeat(5, 1fr);
        gap: 15px;
        align-items: flex-end;
    }
    @media (max-width: 1100px) { .filter-grid { grid-template-columns: repeat(3, 1fr); } }
    @media (max-width: 700px)  { .filter-grid { grid-template-columns: 1fr 1fr; } }
    @media (max-width: 480px)  { .filter-grid { grid-template-columns: 1fr; } }

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

    /* ── Flash Alert ─────────────────────────────── */
    .alert-success {
        display: flex; align-items: center; gap: 11px;
        background: var(--green-dim); border: 1px solid rgba(22, 163, 74, 0.25);
        border-radius: 11px; padding: 13px 18px;
        font-size: 13px; color: var(--green); font-weight: 600;
        margin-bottom: 22px; animation: rise 0.3s ease both;
    }
    .alert-success svg { width: 18px; height: 18px; flex-shrink: 0; }

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
    .product-table td { padding: 13px 16px; border-bottom: 1px solid var(--border); vertical-align: middle; }
    .product-table tr:last-child td { border-bottom: none; }
    .product-table tbody tr { transition: background 0.12s; }
    .product-table tbody tr:hover td { background: color-mix(in srgb, var(--accent) 3%, var(--surface)); }

    /* ── Cells ───────────────────────────────────── */
    .cell-no { text-align: center; color: var(--text-4); font-weight: 600; font-size: 12px; }
    .product-info { display: flex; align-items: center; gap: 10px; }
    .product-thumb {
        width: 40px; height: 40px; border-radius: 10px; object-fit: cover;
        background: var(--surface-2); border: 1px solid var(--border); flex-shrink: 0;
        display: flex; align-items: center; justify-content: center; overflow: hidden;
    }
    .product-thumb img { width: 100%; height: 100%; object-fit: cover; border-radius: 10px; }
    .product-thumb svg { width: 18px; height: 18px; color: var(--text-4); }
    .product-name { font-size: 13px; font-weight: 700; color: var(--text-1); }
    .product-meta { font-size: 10.5px; color: var(--text-4); margin-top: 4px; line-height: 1.4; display: flex; flex-direction: column; gap: 2px; }

    .category-pill {
        display: inline-flex; align-items: center; gap: 4px;
        font-size: 11.5px; font-weight: 600;
        color: #8b5cf6; background: rgba(139, 92, 246, 0.1);
        padding: 3px 9px; border-radius: 6px;
        border: 1px solid rgba(139, 92, 246, 0.2);
        white-space: nowrap; text-decoration: none; transition: all 0.15s;
    }
    .category-pill:hover { background: rgba(139, 92, 246, 0.18); border-color: rgba(139, 92, 246, 0.4); }
    .category-none { font-size: 11.5px; color: var(--text-4); font-style: italic; }

    .store-badge {
        display: inline-flex; align-items: center; font-size: 11.5px; font-weight: 600;
        color: var(--accent); background: var(--accent-dim); padding: 3px 9px; border-radius: 6px;
        border: 1px solid color-mix(in srgb, var(--accent) 20%, transparent); white-space: nowrap;
    }
    .price-value { font-size: 13px; font-weight: 800; color: var(--text-1); font-variant-numeric: tabular-nums; white-space: nowrap; }
    .stock-value { font-size: 14px; font-weight: 800; font-variant-numeric: tabular-nums; }
    .stock-label { font-size: 10.5px; color: var(--text-3); margin-top: 2px; }
    .stock-ok    { color: var(--green); }
    .stock-low   { color: var(--amber); }
    .stock-empty { color: var(--red); }

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

    /* ── Stock Quick-Action ──────────────────────── */
    .stock-cell { display: flex; flex-direction: column; gap: 4px; }
    .quick-stock-actions { display: flex; gap: 4px; margin-top: 4px; }
    .btn-stock-quick {
        width: 24px; height: 24px; border-radius: 6px; border: 1px solid var(--border);
        background: var(--surface); color: var(--text-3); display: inline-flex;
        align-items: center; justify-content: center; cursor: pointer; transition: all 0.15s; padding: 0; flex-shrink: 0;
    }
    .btn-stock-quick svg { width: 11px; height: 11px; }
    .btn-add-stock:hover    { border-color: var(--green); color: var(--green); background: var(--green-dim); }
    .btn-deduct-stock:hover { border-color: var(--red); color: var(--red); background: var(--red-dim); }
    .btn-disabled { opacity: 0.35; cursor: not-allowed !important; pointer-events: none; }

    .stock-tag { font-size: 10px; font-weight: 700; padding: 2px 6px; border-radius: 4px; }
    .stock-tag-ok    { background: var(--green-dim); color: var(--green); }
    .stock-tag-low   { background: var(--amber-dim); color: var(--amber); }
    .stock-tag-empty { background: var(--red-dim); color: var(--red); }

    .row-unavailable { opacity: 0.55; }
    .row-unavailable td {
        background: repeating-linear-gradient(
            -45deg, transparent, transparent 4px,
            color-mix(in srgb, var(--surface-2) 30%, transparent) 4px,
            color-mix(in srgb, var(--surface-2) 30%, transparent) 8px
        );
    }
    .badge-warning { background: var(--amber-dim); color: var(--amber); border: 1px solid rgba(245, 158, 11, 0.2); }
    .badge-warning::before { background: var(--amber); }
    .pill-inactive { opacity: 0.6; border-style: dashed; }
    .store-badge-inactive { opacity: 0.6; border-style: dashed; }

    /* ── Empty State ─────────────────────────────── */
    .empty-state {
        padding: 72px 20px; text-align: center;
        display: flex; flex-direction: column; align-items: center; justify-content: center;
        width: 100%; max-width: 480px; margin: 0 auto;
    }
    .empty-icon {
        width: 88px; height: 88px; margin-bottom: 20px; border-radius: 24px;
        background: linear-gradient(135deg,
            color-mix(in srgb, var(--accent) 10%, transparent),
            color-mix(in srgb, var(--accent) 4%, transparent));
        display: flex; align-items: center; justify-content: center;
        border: 1px solid color-mix(in srgb, var(--accent) 15%, transparent);
    }
    .empty-icon svg { width: 38px; height: 38px; color: var(--accent); opacity: 0.9; }
    .empty-title { font-size: 18px; font-weight: 800; color: var(--text-1); letter-spacing: -0.02em; margin-bottom: 6px; }
    .empty-desc  { font-size: 13px; color: var(--text-3); }

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

    /* ── Delete Modal ────────────────────────────── */
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
    .btn-danger {
        padding: 9px 18px; border: none; border-radius: 8px;
        font-family: var(--font); font-size: 13px; font-weight: 600;
        background: var(--red); color: #fff; cursor: pointer; transition: all 0.15s;
        box-shadow: 0 2px 8px rgba(220, 38, 38, 0.25);
    }
    .btn-danger:hover { background: #b91c1c; transform: translateY(-1px); }

    #productTable tbody { transition: opacity 0.3s ease-in-out; }

    @keyframes rise { from { opacity: 0; transform: translateY(12px); } to { opacity: 1; transform: translateY(0); } }
    @keyframes fadeInRow { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
    .fade-in-animated { animation: fadeInRow 0.4s ease forwards; }
@endsection

@section('content')

    {{-- Page Header --}}
    <div class="page-header">
        <div class="page-header-left">
            <h1>
                <span class="page-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z" />
                        <line x1="3" y1="6" x2="21" y2="6" />
                        <path d="M16 10a4 4 0 0 1-8 0" />
                    </svg>
                </span>
                Manajemen Produk
            </h1>
            <p>Kelola semua katalog produk dari seluruh toko Anda.</p>
        </div>
        <a href="{{ route('products.create') }}" class="btn-primary">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                <line x1="12" y1="5" x2="12" y2="19" />
                <line x1="5" y1="12" x2="19" y2="12" />
            </svg>
            Tambah Produk
        </a>
    </div>

    {{-- Stats Bar --}}
    <div class="stats-bar">
        <div class="stat-card">
            <div class="stat-icon blue">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z" />
                    <line x1="3" y1="6" x2="21" y2="6" />
                    <path d="M16 10a4 4 0 0 1-8 0" />
                </svg>
            </div>
            <div>
                <div class="stat-value">{{ number_format($stats['total']) }}</div>
                <div class="stat-label">Total Produk</div>
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
                <div class="stat-value">{{ number_format($stats['active']) }}</div>
                <div class="stat-label">Produk Aktif</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon gray">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="10" />
                    <line x1="4.93" y1="4.93" x2="19.07" y2="19.07" />
                </svg>
            </div>
            <div>
                <div class="stat-value">{{ number_format($stats['inactive']) }}</div>
                <div class="stat-label">Produk Nonaktif</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon amber">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z" />
                    <line x1="12" y1="9" x2="12" y2="13" />
                    <line x1="12" y1="17" x2="12.01" y2="17" />
                </svg>
            </div>
            <div>
                <div class="stat-value">{{ number_format($stats['low_stock']) }}</div>
                <div class="stat-label">Stok Menipis</div>
            </div>
        </div>
    </div>

    {{-- Filter Card --}}
    <div class="filter-card">
        <div class="filter-card-top">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width:15px;height:15px;color:var(--text-3);">
                <polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3" />
            </svg>
            <span class="filter-card-title">Filter Produk</span>
        </div>

        <form method="GET" action="{{ route('products.index') }}" novalidate>
            <div class="filter-grid">

                <div class="form-group">
                    <label class="form-label">Kategori</label>
                    <select name="category_id" id="filter_category_id" class="form-input">
                        <option value="">Semua Kategori</option>
                        @foreach ($categories as $cat)
                            <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>
                                {{ $cat->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label">Toko</label>
                    <select name="store_id" id="filter_store_id" class="form-input">
                        <option value="">Semua Toko</option>
                        @foreach ($stores->where('is_active', true) as $s)
                            <option value="{{ $s->id }}" {{ request('store_id') == $s->id ? 'selected' : '' }}>
                                {{ $s->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label">Stok</label>
                    <select name="stock" class="form-input">
                        <option value="">Semua Stok</option>
                        <option value="available" {{ request('stock') == 'available' ? 'selected' : '' }}>Tersedia</option>
                        <option value="low"       {{ request('stock') == 'low'       ? 'selected' : '' }}>Menipis (≤10)</option>
                        <option value="empty"     {{ request('stock') == 'empty'     ? 'selected' : '' }}>Habis</option>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label">Harga</label>
                    <select name="price" class="form-input">
                        <option value="">Semua Harga</option>
                        <option value="low"    {{ request('price') == 'low'    ? 'selected' : '' }}>Murah (&lt; 50rb)</option>
                        <option value="medium" {{ request('price') == 'medium' ? 'selected' : '' }}>Sedang (50rb–200rb)</option>
                        <option value="high"   {{ request('price') == 'high'   ? 'selected' : '' }}>Mahal (&gt; 200rb)</option>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-input">
                        <option value="">Semua Status</option>
                        <option value="active"   {{ request('status') === 'active'   ? 'selected' : '' }}>Aktif</option>
                        <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Nonaktif</option>
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
        </form>
    </div>

    {{-- Table Card --}}
    <div class="table-card">
        <div class="table-responsive">
            <table class="product-table" id="productTable">
                <thead>
                    <tr>
                        <th class="center" style="width:50px;">No</th>
                        <th>Nama Produk</th>
                        <th>Kategori</th>
                        <th>Toko</th>
                        <th>Harga</th>
                        <th>Stok</th>
                        <th>Status</th>
                        <th class="center" style="width:130px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @include('products._table_rows', ['products' => $products])
                </tbody>
            </table>
        </div>
    </div>

    {{-- Delete Modal --}}
    <div class="modal-overlay" id="deleteModal">
        <div class="modal-box">
            <div class="modal-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="3 6 5 6 21 6" />
                    <path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6" />
                    <path d="M10 11v6M14 11v6" />
                    <path d="M9 6V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2" />
                </svg>
            </div>
            <div class="modal-title">Hapus Produk?</div>
            <div class="modal-desc" id="modal-desc">Produk ini akan dihapus. Tindakan ini tidak dapat dibatalkan.</div>
            <div class="modal-actions">
                <button class="btn-cancel" onclick="closeDeleteModal()">Batalkan</button>
                <form id="deleteForm" method="POST" novalidate>
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn-danger">Ya, Hapus</button>
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
        function openDeleteModal(productId, productName) {
            document.getElementById('modal-desc').innerHTML =
                `Produk <strong>"${productName}"</strong> akan dihapus. Data stok tidak akan diubah. Tindakan ini tidak dapat dibatalkan.`;
            document.getElementById('deleteForm').action = '/products/' + productId;
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
            return $('#productTable').DataTable({
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
                            <div class="empty-icon" style="margin-bottom: 20px;">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z" />
                                    <line x1="3" y1="6" x2="21" y2="6" />
                                    <path d="M16 10a4 4 0 0 1-8 0" />
                                </svg>
                            </div>
                            <div class="empty-title" style="margin-bottom: 6px;">Tidak Ada Produk Ditemukan</div>
                            <div class="empty-desc">Tidak ada produk yang cocok dengan pencarian atau filter yang dipilih.</div>
                        </div>`,
                    emptyTable: `
                        <div class="empty-state" style="padding: 40px 20px; text-align: center; display: flex; flex-direction: column; align-items: center; justify-content: center; margin: 0 auto; width: 100%; border-bottom: none;">
                            <div class="empty-icon" style="margin-bottom: 20px;">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z" />
                                    <line x1="3" y1="6" x2="21" y2="6" />
                                    <path d="M16 10a4 4 0 0 1-8 0" />
                                </svg>
                            </div>
                            <div class="empty-title" style="margin-bottom: 6px;">Belum Ada Produk</div>
                            <div class="empty-desc">Belum ada produk. Mulai tambahkan produk pertama Anda.</div>
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
            const category = $('#filter_category_id').val();
            const store    = $('#filter_store_id').val();
            const stock    = $('select[name="stock"]').val();
            const price    = $('select[name="price"]').val();
            const status   = $('select[name="status"]').val();

            if (category || store || stock || price || status) {
                $('#btnResetFilter').fadeIn(300).css('display', 'inline-flex');
            } else {
                $('#btnResetFilter').fadeOut(300);
            }
        }

        $(document).ready(function() {
            table = initDataTable();

            // FIX: call checkResetVisibility on load so Reset shows if URL params are active
            checkResetVisibility();

            $('#btnApplyFilter').on('click', function() {
                const params = {
                    category_id: $('#filter_category_id').val(),
                    store_id:    $('#filter_store_id').val(),
                    stock:       $('select[name="stock"]').val(),
                    price:       $('select[name="price"]').val(),
                    status:      $('select[name="status"]').val(),
                };

                $('#productTable tbody').css('opacity', '0.4');

                $.ajax({
                    url:  "{{ route('products.index') }}",
                    type: 'GET',
                    data: params,
                    success: function(html) {
                        if ($.fn.DataTable.isDataTable('#productTable')) {
                            table.destroy();
                        }
                        $('#productTable tbody').html(html);
                        $('#productTable tbody tr').addClass('fade-in-animated');
                        table = initDataTable();
                        $('#productTable tbody').css('opacity', '1');
                        checkResetVisibility();
                    },
                    error: function() {
                        $('#productTable tbody').css('opacity', '1');
                        if (typeof showToast === 'function') {
                            showToast('Gagal memuat data produk.', 'error');
                        }
                    }
                });
            });

            // Dynamic category filter by store
            $('#filter_store_id').on('change', function() {
                const storeId = $(this).val();
                const categorySelect = $('#filter_category_id');

                if (!storeId) {
                    categorySelect.html('<option value="">Semua Kategori</option>');
                    return;
                }

                categorySelect.prop('disabled', true).html('<option value="">Memuat...</option>');

                const url = "{{ route('stores.categories', ':id') }}".replace(':id', storeId);

                $.get(url, function(data) {
                    let html = '<option value="">Semua Kategori</option>';
                    data.forEach(cat => {
                        html += `<option value="${cat.id}">${cat.name}</option>`;
                    });
                    categorySelect.html(html).prop('disabled', false);
                }).fail(function() {
                    categorySelect.html('<option value="">Gagal memuat</option>').prop('disabled', false);
                });
            });

            $('#btnResetFilter').on('click', function() {
                $('#filter_category_id').val('');
                $('#filter_store_id').val('');
                $('select[name="stock"]').val('');
                $('select[name="price"]').val('');
                $('select[name="status"]').val('');
                $('#btnApplyFilter').trigger('click');
            });
        });
    </script>
@endpush
