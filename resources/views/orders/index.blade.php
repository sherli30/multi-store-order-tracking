@extends('layouts.app')

@section('title', 'Manajemen Pesanan')

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
    .stat-icon.purple { background: rgba(139,92,246,0.1); color: #8b5cf6; }
    .stat-icon.amber { background: var(--amber-dim); color: var(--amber); }
    .stat-value { font-size: 20px; font-weight: 800; color: var(--text-1); letter-spacing: -0.03em; }
    .stat-label { font-size: 11.5px; color: var(--text-3); font-weight: 500; margin-top: 2px; }
    @media (max-width: 1024px) { .stats-bar { grid-template-columns: repeat(2, 1fr); } }
    @media (max-width: 600px) { .stats-bar { grid-template-columns: 1fr; } }

    /* ── Status Tabs ── */
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

    /* ── Filter Card (EXACT COPY FROM PRODUCT) ─────────────────────────────── */
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
    .filter-grid {
    display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 15px; align-items: flex-end;
    }
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

    .filter-actions {
    display: flex; justify-content: flex-end; align-items: center;
    gap: 12px; margin-top: 16px; padding-top: 16px;
    border-top: 1px solid var(--border); min-height: 45px;
    }

    /* ── Tombol Terapkan (Primary) ── */
    .btn-primary {
    display: inline-flex; align-items: center; gap: 7px;
    background: var(--accent); color: #fff; border: none;
    padding: 9px 18px; border-radius: 9px; font-family: var(--font);
    font-weight: 700; font-size: 13px; cursor: pointer;
    text-decoration: none; transition: all 0.15s;
    box-shadow: 0 2px 8px color-mix(in srgb, var(--accent) 30%, transparent);
    white-space: nowrap;
    }
    .btn-primary:hover { opacity: 0.9; transform: translateY(-1px); }
    .btn-primary svg { width: 14px; height: 14px; }

    /* ── Tombol Reset Filter (Outline/Red Dim) ── */
    .btn-outline-reset {
    display: inline-flex; align-items: center; gap: 7px;
    background: var(--red-dim); color: var(--red);
    border: 1px solid rgba(220, 38, 38, 0.2);
    padding: 9px 16px; border-radius: 9px; font-family: var(--font);
    font-weight: 700; font-size: 13px; cursor: pointer;
    text-decoration: none; transition: all 0.15s;
    }
    .btn-outline-reset:hover {
    border-color: rgba(220, 38, 38, 0.4);
    background: color-mix(in srgb, var(--red-dim) 80%, var(--red));
    color: var(--red);
    }
    .btn-outline-reset svg { width: 13px; height: 13px; }

    /* ── Table Card (EXACT COPY FROM PRODUCT) ────── */
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

    /* ── Cells ─────────── */
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
    .product-slug { font-size: 11px; color: var(--text-3); font-family: var(--mono); margin-top: 2px; }

    .store-badge {
    display: inline-flex; align-items: center; font-size: 11.5px; font-weight: 600;
    color: var(--accent); background: var(--accent-dim); padding: 3px 9px; border-radius: 6px;
    border: 1px solid color-mix(in srgb, var(--accent) 20%, transparent); white-space: nowrap;
    }
    .price-value { font-size: 13px; font-weight: 800; color: var(--text-1); font-variant-numeric: tabular-nums; white-space: nowrap; }

    .badge { display: inline-flex; align-items: center; gap: 5px; padding: 4px 11px; border-radius: 20px; font-size: 11.5px;
    font-weight: 600; white-space: nowrap; }
    .badge::before { content: ''; width: 6px; height: 6px; border-radius: 50%; flex-shrink: 0; }
    
    .badge-pending    { background: var(--amber-dim); color: var(--amber); border: 1px solid rgba(245,158,11,0.2); }
    .badge-pending::before    { background: var(--amber); }
    .badge-perlu_diproses { background: rgba(59,130,246,0.08); color: #3b82f6; border: 1px solid rgba(59,130,246,0.2); }
    .badge-perlu_diproses::before { background: #3b82f6; }
    .badge-processing { background: rgba(139,92,246,0.08); color: #8b5cf6; border: 1px solid rgba(139,92,246,0.2); }
    .badge-processing::before { background: #8b5cf6; }
    .badge-shipping   { background: var(--accent-dim); color: var(--accent); border: 1px solid rgba(99,102,241,0.2); }
    .badge-shipping::before   { background: var(--accent); }
    .badge-completed  { background: var(--green-dim); color: var(--green); border: 1px solid rgba(22,163,74,0.2); }
    .badge-completed::before  { background: var(--green); }
    .badge-cancelled  { background: var(--red-dim); color: var(--red); border: 1px solid rgba(220,38,38,0.2); }
    .badge-cancelled::before  { background: var(--red); }

    .actions-cell { display: flex; gap: 6px; justify-content: center; }
    .btn-sm { display: inline-flex; align-items: center; gap: 5px; border: 1px solid var(--border); border-radius: 7px;
    font-family: var(--font); font-size: 11.5px; font-weight: 600; padding: 6px 12px; cursor: pointer; transition: all
    0.15s; background: var(--panel); color: var(--text-2); text-decoration: none; white-space: nowrap; }
    .btn-sm svg { width: 12px; height: 12px; }
    .btn-sm:hover { border-color: var(--accent); color: var(--accent); background: color-mix(in srgb, var(--accent) 5%, var(--panel)); }
    .btn-sm.danger { color: var(--red); border-color: rgba(220,38,38,0.2); background: var(--red-dim); }
    .btn-sm.danger:hover { background: var(--red); color: #fff; border-color: var(--red); }

    /* ── Modals (EXACT COPY FROM PRODUCT) ── */
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
    .modal-icon { width: 52px; height: 52px; border-radius: 14px; display: flex; align-items:
    center; justify-content: center; margin-bottom: 16px; }
    .modal-icon svg { width: 24px; height: 24px; }
    .modal-icon.red { background: var(--red-dim); color: var(--red); }
    .modal-icon.blue { background: rgba(59,130,246,0.1); color: #3b82f6; }
    
    .modal-title { font-size: 16px; font-weight: 800; color: var(--text-1); margin-bottom: 6px; }
    .modal-desc { font-size: 13px; color: var(--text-2); margin-bottom: 22px; line-height: 1.6; }
    .modal-actions { display: flex; gap: 10px; justify-content: flex-end; margin-top: 24px; }
    
    .btn-cancel { padding: 9px 18px; border: 1px solid var(--border); border-radius: 8px; font-family: var(--font);
    font-size: 13px; font-weight: 600; background: var(--surface); color: var(--text-2); cursor: pointer; transition: all
    0.15s; }
    .btn-cancel:hover { border-color: var(--border-2); color: var(--text-1); }
    
    .btn-danger { padding: 9px 18px; border: none; border-radius: 8px; font-family: var(--font); font-size: 13px;
    font-weight: 700; background: var(--red); color: #fff; cursor: pointer; transition: all 0.15s; box-shadow: 0 2px 8px
    rgba(220,38,38,0.25); }
    .btn-danger:hover { background: #b91c1c; transform: translateY(-1px); }

    /* ── DataTables Overrides (HIDDEN SEARCH/LENGTH) ── */
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
                        <path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"/>
                        <rect x="8" y="2" width="8" height="4" rx="1" ry="1"/>
                        <path d="M12 11h4"/><path d="M12 16h4"/><path d="M8 11h.01"/><path d="M8 16h.01"/>
                    </svg>
                </span>
                Manajemen Pesanan
            </h1>
            <p>Monitor dan kelola seluruh transaksi pesanan dari semua cabang toko Anda.</p>
        </div>
    </div>

    {{-- Stats Bar --}}
    <div class="stats-bar">
        <div class="stat-card">
            <div class="stat-icon blue">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"/><rect x="8" y="2" width="8" height="4" rx="1" ry="1"/></svg>
            </div>
            <div>
                <div class="stat-value">{{ number_format($tabCounts['all']) }}</div>
                <div class="stat-label">Total Pesanan</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon amber">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
            </div>
            <div>
                <div class="stat-value">{{ number_format($tabCounts['pending']) }}</div>
                <div class="stat-label">Belum Bayar</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon blue" style="background: rgba(59,130,246,0.1); color: #3b82f6;">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
            </div>
            <div>
                <div class="stat-value">{{ number_format($tabCounts['perlu_diproses']) }}</div>
                <div class="stat-label">Perlu Diproses</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon purple">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/><polyline points="3.27 6.96 12 12.01 20.73 6.96"/><line x1="12" y1="22.08" x2="12" y2="12"/></svg>
            </div>
            <div>
                <div class="stat-value">{{ number_format($tabCounts['processing']) }}</div>
                <div class="stat-label">Dikemas</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon green">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
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
            function appendTab($baseUrl, $tabName) {
                $query = request()->query();
                $query['tab'] = $tabName;
                return $baseUrl . '?' . http_build_query($query);
            }
        @endphp
        <a href="{{ appendTab(url()->current(), 'all') }}" class="tab-btn {{ $tab === 'all' ? 'active' : '' }}">
            Semua <span class="tab-count">{{ $tabCounts['all'] ?? 0 }}</span>
        </a>
        <a href="{{ appendTab(url()->current(), 'pending') }}" class="tab-btn {{ $tab === 'pending' ? 'active' : '' }}">
            Belum Bayar <span class="tab-count">{{ $tabCounts['pending'] ?? 0 }}</span>
        </a>
        <a href="{{ appendTab(url()->current(), 'perlu_diproses') }}" class="tab-btn {{ $tab === 'perlu_diproses' ? 'active' : '' }}">
            Perlu Diproses <span class="tab-count">{{ $tabCounts['perlu_diproses'] ?? 0 }}</span>
        </a>
        <a href="{{ appendTab(url()->current(), 'processing') }}" class="tab-btn {{ $tab === 'processing' ? 'active' : '' }}">
            Dikemas <span class="tab-count">{{ $tabCounts['processing'] ?? 0 }}</span>
        </a>
        <a href="{{ appendTab(url()->current(), 'shipping') }}" class="tab-btn {{ $tab === 'shipping' ? 'active' : '' }}">
            Dikirim <span class="tab-count">{{ $tabCounts['shipping'] ?? 0 }}</span>
        </a>
        <a href="{{ appendTab(url()->current(), 'completed') }}" class="tab-btn {{ $tab === 'completed' ? 'active' : '' }}">
            Selesai <span class="tab-count">{{ $tabCounts['completed'] ?? 0 }}</span>
        </a>
        <a href="{{ appendTab(url()->current(), 'cancelled') }}" class="tab-btn {{ $tab === 'cancelled' ? 'active' : '' }}">
            Dibatalkan <span class="tab-count">{{ $tabCounts['cancelled'] ?? 0 }}</span>
        </a>
    </div>

    {{-- Filter Card (EXACT COPY FROM PRODUCT) --}}
    <div class="filter-card">
        <div class="filter-card-top">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width:15px;height:15px;color:var(--text-3);">
                <polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3" />
            </svg>
            <span class="filter-card-title">Filter Pencarian</span>

            @if (request()->hasAny(['store_id', 'shipping_type', 'date', 'search', 'sort']))
                <span class="filter-active-tag">Filter Aktif</span>
            @endif
        </div>

        <form id="filter-form">
            <input type="hidden" name="tab" value="{{ $tab }}">
            
            <div class="filter-grid" style="grid-template-columns: repeat(4, 1fr);">
                {{-- Toko --}}
                <div class="form-group">
                    <label class="form-label">Toko Cabang</label>
                    <select name="store_id" id="filter_store_id" class="form-input">
                        <option value="">Semua Toko</option>
                        @foreach ($stores as $s)
                            <option value="{{ $s->id }}" {{ request('store_id') == $s->id ? 'selected' : '' }}>
                                {{ $s->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Tanggal --}}
                <div class="form-group">
                    <label class="form-label">Tanggal</label>
                    <input type="date" name="date" id="filter-date" class="form-input" value="{{ request('date') }}">
                </div>

                {{-- Pengiriman --}}
                <div class="form-group">
                    <label class="form-label">Pengiriman</label>
                    <select name="shipping_type" id="filter-shipping" class="form-input">
                        <option value="">Semua Tipe</option>
                        <option value="reguler" {{ request('shipping_type') == 'reguler' ? 'selected' : '' }}>Reguler</option>
                        <option value="cargo"   {{ request('shipping_type') == 'cargo'   ? 'selected' : '' }}>Cargo</option>
                    </select>
                </div>

                {{-- Urutan --}}
                <div class="form-group">
                    <label class="form-label">Urutan</label>
                    <select name="sort" id="filter-sort" class="form-input">
                        <option value="desc" {{ request('sort') == 'desc' ? 'selected' : '' }}>Terbaru</option>
                        <option value="asc"  {{ request('sort') == 'asc'  ? 'selected' : '' }}>Terlama</option>
                        <option value="total_high" {{ request('sort') == 'total_high' ? 'selected' : '' }}>Harga Tertinggi</option>
                        <option value="total_low"  {{ request('sort') == 'total_low'  ? 'selected' : '' }}>Harga Terendah</option>
                    </select>
                </div>
            </div>

            <div class="filter-actions">
                <a href="javascript:void(0)" id="btnResetFilter" class="btn-outline-reset" style="{{ request()->hasAny(['store_id', 'shipping_type', 'date', 'search', 'sort']) ? '' : 'display:none;' }}">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="18" y1="6" x2="6" y2="18" /><line x1="6" y1="6" x2="18" y2="18" />
                    </svg>
                    Reset Filter
                </a>
                <button type="button" id="btnApplyFilter" class="btn-primary">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="11" cy="11" r="8" /><line x1="21" y1="21" x2="16.65" y2="16.65" />
                    </svg>
                    Terapkan Filter
                </button>
            </div>
        </form>
    </div>

    {{-- Table Card --}}
    <div class="table-card">
        <div class="table-responsive">
            <table class="product-table" id="orderTable">
                <thead>
                    <tr>
                        <th class="center" style="width:50px;">No</th>
                        <th>ID Pesanan</th>
                        <th>Pelanggan</th>
                        <th>Toko</th>
                        <th>Waktu</th>
                        <th>Total Harga</th>
                        <th>Status</th>
                        <th class="center" style="width:100px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @include('orders.partials.table', ['orders' => $orders])
                </tbody>
            </table>
        </div>
    </div>

    {{-- Update Status Modal --}}
    <div id="statusModal" class="modal-overlay">
        <div class="modal-box">
            <div class="modal-icon blue">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2v4M12 18v4M4.93 4.93l2.83 2.83M16.24 16.24l2.83 2.83M2 12h4M18 12h4M4.93 19.07l2.83-2.83M16.24 7.76l2.83-2.83"></path></svg>
            </div>
            <h3 class="modal-title">Update Status Pesanan</h3>
            <p class="modal-desc">Ubah status pengerjaan untuk pesanan <strong id="statusOrderNumber">#ORD-XXXX</strong>.</p>
            
            <form id="statusForm" method="POST" action="">
                @csrf
                @method('PATCH')
                <div class="form-group">
                    <label class="form-label">Pilih Status Baru</label>
                    <select name="status" id="newStatus" class="form-input">
                        <option value="pending">Belum Bayar</option>
                        <option value="perlu_diproses">Perlu Diproses</option>
                        <option value="processing">Dikemas</option>
                        <option value="shipping">Dikirim</option>
                        <option value="completed">Selesai</option>
                    </select>
                </div>
                
                <div class="modal-actions">
                    <button type="button" class="btn-cancel" onclick="closeModals()">Batal</button>
                    <button type="submit" class="btn-primary">Update Status</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Cancel Order Modal --}}
    <div id="cancelModal" class="modal-overlay">
        <div class="modal-box">
            <div class="modal-icon red">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><line x1="15" y1="9" x2="9" y2="15"></line><line x1="9" y1="9" x2="15" y2="15"></line></svg>
            </div>
            <h3 class="modal-title">Batalkan Pesanan</h3>
            <p class="modal-desc">Apakah Anda yakin ingin membatalkan pesanan <strong id="cancelOrderNumber">#ORD-XXXX</strong>? Tindakan ini akan mengembalikan stok produk.</p>
            
            <form id="cancelForm" method="POST" action="">
                @csrf
                @method('PATCH')
                <div class="form-group">
                    <label class="form-label">Alasan Pembatalan</label>
                    <textarea name="cancel_reason" class="form-input" rows="3" placeholder="Masukkan alasan pembatalan..." required></textarea>
                </div>
                
                <div class="modal-actions">
                    <button type="button" class="btn-cancel" onclick="closeModals()">Kembali</button>
                    <button type="submit" class="btn-danger">Ya, Batalkan Pesanan</button>
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
        function openStatusModal(id, number, currentStatus) {
            $('#statusOrderNumber').text('#' + number);
            $('#newStatus').val(currentStatus);
            $('#statusForm').attr('action', `/orders/${id}/status`);
            $('#statusModal').addClass('open');
        }

        function openCancelModal(id, number) {
            $('#cancelOrderNumber').text('#' + number);
            $('#cancelForm').attr('action', `/orders/${id}/cancel`);
            $('#cancelModal').addClass('open');
        }

        function closeModals() {
            $('.modal-overlay').removeClass('open');
        }

        $(document).ready(function() {
            let table;

            function initDataTable() {
                return $('#orderTable').DataTable({
                    responsive: true,
                    autoWidth: false,
                    language: {
                        search: "Cari:",
                        searchPlaceholder: "Cari Data...",
                        lengthMenu: "Tampilkan _MENU_ data",
                        info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
                        infoEmpty: "Tidak ada data untuk ditampilkan",
                        zeroRecords: `
                            <div class="empty-state" style="padding: 40px 20px; text-align: center; display: flex; flex-direction: column; align-items: center; justify-content: center; margin: 0 auto; width: 100%; border-bottom: none;">
                                <div class="empty-icon" style="margin-bottom: 20px; width: 88px; height: 88px; background: color-mix(in srgb, var(--accent) 10%, transparent); border-radius: 24px; display: flex; align-items: center; justify-content: center; border: 1px solid color-mix(in srgb, var(--accent) 15%, transparent);">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" style="width: 38px; height: 38px; color: var(--accent); opacity: 0.9;">
                                        <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path>
                                        <polyline points="3.27 6.96 12 12.01 20.73 6.96"></polyline>
                                        <line x1="12" y1="22.08" x2="12" y2="12"></line>
                                    </svg>
                                </div>
                                <div class="empty-title" style="margin-bottom: 6px; font-size: 18px; font-weight: 800; color: var(--text-1); letter-spacing: -0.02em;">Tidak Ada Pesanan Ditemukan</div>
                                <div class="empty-desc" style="font-size: 13px; color: var(--text-3);">Tidak ada pesanan yang cocok dengan pencarian atau filter yang dipilih.</div>
                            </div>`,
                        emptyTable: `
                            <div class="empty-state" style="padding: 40px 20px; text-align: center; display: flex; flex-direction: column; align-items: center; justify-content: center; margin: 0 auto; width: 100%; border-bottom: none;">
                                <div class="empty-icon" style="margin-bottom: 20px; width: 88px; height: 88px; background: color-mix(in srgb, var(--accent) 10%, transparent); border-radius: 24px; display: flex; align-items: center; justify-content: center; border: 1px solid color-mix(in srgb, var(--accent) 15%, transparent);">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" style="width: 38px; height: 38px; color: var(--accent); opacity: 0.9;">
                                        <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path>
                                        <polyline points="3.27 6.96 12 12.01 20.73 6.96"></polyline>
                                        <line x1="12" y1="22.08" x2="12" y2="12"></line>
                                    </svg>
                                </div>
                                <div class="empty-title" style="margin-bottom: 6px; font-size: 18px; font-weight: 800; color: var(--text-1); letter-spacing: -0.02em;">Belum Ada Pesanan</div>
                                <div class="empty-desc" style="font-size: 13px; color: var(--text-3);">Belum ada data pesanan yang masuk saat ini.</div>
                            </div>`,
                        paginate: { first: "«", last: "»", next: "›", previous: "‹" }
                    },
                    columnDefs: [
                        { targets: 0, orderable: false, searchable: false },
                        { targets: -1, orderable: false, searchable: false }
                    ],
                    order: [[4, 'desc']],
                    drawCallback: function() {
                        let api = this.api();
                        let startIndex = api.page.info().start;
                        api.column(0, {page:'current'}).nodes().each(function(cell, i) {
                            cell.innerHTML = startIndex + i + 1;
                        });
                        $('.dataTables_filter').show();
                    }
                });
            }

            table = initDataTable();
            $('.dataTables_filter').show();

            // ── Handler Terapkan Filter (AJAX) ──
            $('#btnApplyFilter').on('click', function() {
                const params = {
                    store_id: $('#filter_store_id').val(),
                    date: $('#filter-date').val(),
                    shipping_type: $('#filter-shipping').val(),
                    sort: $('#filter-sort').val(),
                    tab: $('input[name="tab"]').val(),
                    search: '' // Search handled by DataTables internal search
                };

                $('#orderTable tbody').css('opacity', '0.4');

                $.ajax({
                    url: "{{ route('orders.index') }}",
                    type: 'GET',
                    data: params,
                    success: function(html) {
                        if ($.fn.DataTable.isDataTable('#orderTable')) {
                            table.destroy();
                        }
                        $('#orderTable tbody').html(html);
                        $('#orderTable tbody tr').addClass('fade-in-animated');
                        table = initDataTable();
                        $('.dataTables_filter').show(); // Re-show search
                        $('#orderTable tbody').css('opacity', '1');
                        
                        checkResetVisibility();
                    },
                    error: function() {
                        $('#orderTable tbody').css('opacity', '1');
                    }
                });
            });

            // ── Handler Reset Filter ──
            $('#btnResetFilter').on('click', function() {
                $('#filter_store_id').val('');
                $('#filter-date').val('');
                $('#filter-shipping').val('');
                $('#filter-sort').val('desc');
                
                // Clear DataTables search box
                if (table) {
                    table.search('').draw();
                }
                
                $('#btnApplyFilter').trigger('click');
            });

            function checkResetVisibility() {
                const hasFilter = $('#filter_store_id').val() || $('#filter-date').val() || $('#filter-shipping').val() || $('#filter-sort').val() !== 'desc';
                if (hasFilter) {
                    $('#btnResetFilter').fadeIn(200);
                    if ($('.filter-active-tag').length === 0) {
                        $('.filter-card-title').after('<span class="filter-active-tag">Filter Aktif</span>');
                    }
                } else {
                    $('#btnResetFilter').fadeOut(200);
                    $('.filter-active-tag').remove();
                }
            }

            // Close on escape
            $(document).on('keydown', function(e) {
                if (e.key === 'Escape') closeModals();
            });

            // Close on backdrop click
            $('.modal-overlay').on('click', function(e) {
                if (e.target === this) closeModals();
            });
        });
    </script>
@endpush
