@extends('layouts.app')

@section('title', 'Kategori Produk')

@section('styles')
    /* =============================================
    PRODUCT CATEGORIES INDEX — Professional Design
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
    .page-icon {
    width: 36px;
    height: 36px;
    background: linear-gradient(135deg, var(--accent), color-mix(in srgb, var(--accent) 70%, #7c3aed));
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    }
    .page-icon svg {
    width: 18px;
    height: 18px;
    color: #fff;
    }
    .page-header-left p {
    font-size: 13px;
    color: var(--text-3);
    margin-left: 46px;
    }

    /* ── Buttons ─────────────────────────────────── */
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
    }
    .btn-primary:hover {
    opacity: 0.9;
    transform: translateY(-1px);
    }
    .btn-primary svg {
    width: 14px;
    height: 14px;
    }

    .btn-outline-reset {
    display: inline-flex;
    align-items: center;
    gap: 7px;
    background: var(--red-dim);
    color: var(--red);
    border: 1px solid rgba(220, 38, 38, 0.2);
    padding: 9px 16px;
    border-radius: 9px;
    font-family: var(--font);
    font-weight: 700;
    font-size: 13px;
    cursor: pointer;
    text-decoration: none;
    transition: all 0.15s;
    }
    .btn-outline-reset:hover {
    border-color: rgba(220, 38, 38, 0.4);
    }
    .btn-outline-reset svg {
    width: 13px;
    height: 13px;
    }

    /* ── Stats Bar ───────────────────────────────── */
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
    .stat-card:hover {
    box-shadow: var(--shadow-md);
    transform: translateY(-1px);
    }
    .stat-icon {
    width: 42px;
    height: 42px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    }
    .stat-icon svg {
    width: 20px;
    height: 20px;
    }
    .stat-icon.blue {
    background: rgba(59, 130, 246, 0.1);
    color: #3b82f6;
    }
    .stat-icon.green {
    background: var(--green-dim);
    color: var(--green);
    }
    .stat-icon.gray {
    background: var(--surface-2);
    color: var(--text-3);
    }
    .stat-icon.purple {
    background: rgba(139, 92, 246, 0.1);
    color: #8b5cf6;
    }
    .stat-value {
    font-size: 20px;
    font-weight: 800;
    color: var(--text-1);
    letter-spacing: -0.03em;
    }
    .stat-label {
    font-size: 11.5px;
    color: var(--text-3);
    font-weight: 500;
    margin-top: 2px;
    }
    @media (max-width: 700px) {
    .stats-bar {
    grid-template-columns: 1fr 1fr;
    }
    }

    /* ── Filter Card ─────────────────────────────── */
    .filter-card {
    background: var(--panel);
    border: 1px solid var(--border);
    border-radius: 14px;
    padding: 20px 22px;
    box-shadow: var(--shadow-sm);
    margin-bottom: 20px;
    }
    .filter-card-top {
    display: flex;
    align-items: center;
    gap: 8px;
    margin-bottom: 16px;
    }
    .filter-card-title {
    font-size: 13px;
    font-weight: 700;
    color: var(--text-1);
    }
    .filter-grid {
    display: flex;
    align-items: flex-end;
    gap: 14px;
    margin-bottom: 0;
    flex-wrap: wrap;
    }
    .filter-grid .form-group {
    flex: 1;
    min-width: 160px;
    }
    @media (max-width: 900px) {
    .filter-grid {
    flex-direction: column;
    align-items: stretch;
    }
    .filter-grid .form-group {
    min-width: 100%;
    }
    }

    .form-group {
    display: flex;
    flex-direction: column;
    }
    .form-label {
    display: block;
    font-size: 11.5px;
    font-weight: 700;
    color: var(--text-3);
    margin-bottom: 6px;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    }
    .form-input {
    width: 100%;
    padding: 9px 13px;
    border: 1px solid var(--border);
    border-radius: 9px;
    font-family: var(--font);
    font-size: 13px;
    color: var(--text-1);
    background: var(--surface);
    outline: none;
    transition: border-color 0.15s, box-shadow 0.15s;
    box-sizing: border-box;
    }
    .form-input:focus {
    border-color: var(--accent);
    box-shadow: 0 0 0 3px var(--accent-glow);
    }
    .form-input::placeholder {
    color: var(--text-4);
    }

    /* ── Filter Actions (Perbaikan agar Stay & Stabil) ── */
    .filter-actions {
    display: flex;
    justify-content: flex-end;
    align-items: center;
    gap: 12px;
    margin-top: 16px;
    padding-top: 16px;
    border-top: 1px solid var(--border);
    /* KUNCI: Menjaga tinggi tetap 45px agar tabel di bawah tidak goyang saat Reset muncul */
    min-height: 45px;
    }

    /* Responsivitas: Agar tombol rapi di HP dan tidak menumpuk */
    @media (max-width: 600px) {
    .filter-actions {
    flex-direction: column-reverse; /* Terapkan di atas, Reset di bawah */
    align-items: stretch;
    gap: 10px;
    min-height: auto; /* Di mobile biarkan fleksibel mengikuti tumpukan tombol */
    }

    .filter-actions .btn-primary,
    .filter-actions .btn-outline-reset {
    justify-content: center;
    width: 100%;
    padding: 11px;
    }
    }

    /* ── Flash Alert ─────────────────────────────── */
    .alert-success {
    display: flex;
    align-items: center;
    gap: 11px;
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
    .alert-success svg {
    width: 18px;
    height: 18px;
    flex-shrink: 0;
    }

    /* ── Table Card ──────────────────────────────── */
    .table-card {
    background: var(--panel);
    border: 1px solid var(--border);
    border-radius: 14px;
    overflow: hidden;
    box-shadow: var(--shadow-sm);
    animation: rise 0.35s ease both;
    }
    .table-responsive {
    overflow-x: auto;
    }
    .cat-table {
    width: 100%;
    border-collapse: collapse;
    }
    .cat-table th {
    background: var(--surface);
    padding: 12px 16px;
    text-align: left;
    font-size: 10.5px;
    font-weight: 700;
    color: var(--text-3);
    border-bottom: 1px solid var(--border);
    white-space: nowrap;
    text-transform: uppercase;
    letter-spacing: 0.08em;
    }
    .cat-table th.center {
    text-align: center;
    }
    .cat-table td {
    padding: 13px 16px;
    border-bottom: 1px solid var(--border);
    vertical-align: middle;
    }
    .cat-table tr:last-child td {
    border-bottom: none;
    }
    .cat-table tbody tr {
    transition: background 0.12s;
    }
    .cat-table tbody tr:hover td {
    background: color-mix(in srgb, var(--accent) 3%, var(--surface));
    }

    /* ── Cell Types ──────────────────────────────── */
    .cell-no {
    text-align: center;
    color: var(--text-4);
    font-weight: 600;
    font-size: 12px;
    }

    .cat-name {
    font-size: 13.5px;
    font-weight: 700;
    color: var(--text-1);
    }
    .cat-slug {
    font-size: 10.5px;
    color: var(--text-4);
    margin-top: 2px;
    font-family: var(--mono);
    }

    .cat-desc {
    font-size: 12.5px;
    color: var(--text-3);
    line-height: 1.5;
    max-width: 320px;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
    }
    .cat-desc.empty {
    font-style: italic;
    color: var(--text-4);
    }

    .product-count {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    font-size: 13px;
    font-weight: 800;
    color: #8b5cf6;
    background: rgba(139, 92, 246, 0.1);
    padding: 4px 11px;
    border-radius: 20px;
    border: 1px solid rgba(139, 92, 246, 0.2);
    }

    .badge {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    padding: 4px 11px;
    border-radius: 20px;
    font-size: 11.5px;
    font-weight: 600;
    white-space: nowrap;
    }
    .badge::before {
    content: '';
    width: 6px;
    height: 6px;
    border-radius: 50%;
    flex-shrink: 0;
    }
    .badge-active {
    background: var(--green-dim);
    color: var(--green);
    border: 1px solid rgba(22, 163, 74, 0.2);
    }
    .badge-active::before {
    background: var(--green);
    }
    .badge-inactive {
    background: var(--surface-2);
    color: var(--text-3);
    border: 1px solid var(--border);
    }
    .badge-inactive::before {
    background: var(--text-4);
    }

    /* ── Actions ─────────────────────────────────── */
    .actions-cell {
    display: flex;
    gap: 6px;
    justify-content: center;
    }
    .btn-sm {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    border: 1px solid var(--border);
    border-radius: 7px;
    font-family: var(--font);
    font-size: 11.5px;
    font-weight: 600;
    padding: 6px 12px;
    cursor: pointer;
    transition: all 0.15s;
    background: var(--panel);
    color: var(--text-2);
    text-decoration: none;
    white-space: nowrap;
    }
    .btn-sm svg {
    width: 12px;
    height: 12px;
    }
    .btn-sm:hover {
    border-color: var(--accent);
    color: var(--accent);
    background: color-mix(in srgb, var(--accent) 5%, var(--panel));
    }
    .btn-sm.danger:hover {
    border-color: rgba(220, 38, 38, 0.4);
    color: var(--red);
    background: var(--red-dim);
    }

    /* ── Empty State ─────────────────────────────── */
    .empty-state {
    padding: 72px 20px;
    text-align: center;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    width: 100%;
    max-width: 480px;
    margin: 0 auto;
    }
    .empty-icon {
    width: 88px;
    height: 88px;
    margin-bottom: 20px;
    border-radius: 24px;
    background: linear-gradient(135deg, color-mix(in srgb, var(--accent) 10%, transparent), color-mix(in srgb, var(--accent)
    4%, transparent));
    display: flex;
    align-items: center;
    justify-content: center;
    border: 1px solid color-mix(in srgb, var(--accent) 15%, transparent);
    }
    .empty-icon svg {
    width: 38px;
    height: 38px;
    color: var(--accent);
    opacity: 0.9;
    }
    .empty-title {
    font-size: 18px;
    font-weight: 800;
    color: var(--text-1);
    letter-spacing: -0.02em;
    margin-bottom: 6px;
    }
    .empty-desc {
    font-size: 13px;
    color: var(--text-3);
    max-width: 100%;
    }

    /* ── DataTables Overrides ────────────────────── */
    .dataTables_wrapper .dataTables_filter,
    .dataTables_wrapper .dataTables_length { display: none !important; }

    .dataTables_wrapper .dataTables_info {
    font-size: 12px;
    color: var(--text-3);
    padding: 14px 20px;
    }
    .dataTables_wrapper .dataTables_paginate {
    padding: 10px 20px;
    }
    .dataTables_wrapper .dataTables_paginate .paginate_button {
    font-family: var(--font) !important;
    font-size: 12px !important;
    font-weight: 600 !important;
    border-radius: 7px !important;
    border: 1px solid var(--border) !important;
    color: var(--text-2) !important;
    background: var(--panel) !important;
    padding: 5px 10px !important;
    margin: 0 2px !important;
    transition: all 0.15s !important;
    }
    .dataTables_wrapper .dataTables_paginate .paginate_button:hover {
    border-color: var(--accent) !important;
    color: var(--accent) !important;
    background: color-mix(in srgb, var(--accent) 5%, var(--panel)) !important;
    }
    .dataTables_wrapper .dataTables_paginate .paginate_button.current {
    background: var(--accent) !important;
    border-color: var(--accent) !important;
    color: #fff !important;
    }
    .dataTables_wrapper .dataTables_paginate .paginate_button.disabled,
    .dataTables_wrapper .dataTables_paginate .paginate_button.disabled:hover {
    color: var(--text-4) !important;
    background: var(--surface) !important;
    border-color: var(--border) !important;
    cursor: default !important;
    }

    /* ── Delete Modal ────────────────────────────── */
    .modal-overlay {
    position: fixed;
    inset: 0;
    background: rgba(15, 23, 42, 0.45);
    backdrop-filter: blur(4px);
    z-index: 200;
    display: flex;
    align-items: center;
    justify-content: center;
    opacity: 0;
    visibility: hidden;
    transition: all 0.2s;
    }
    .modal-overlay.open {
    opacity: 1;
    visibility: visible;
    }
    .modal-box {
    background: var(--panel);
    border-radius: 16px;
    padding: 28px;
    width: 420px;
    max-width: 90vw;
    box-shadow: var(--shadow-lg);
    transform: scale(0.95) translateY(10px);
    transition: transform 0.2s;
    }
    .modal-overlay.open .modal-box {
    transform: scale(1) translateY(0);
    }
    .modal-icon {
    width: 52px;
    height: 52px;
    background: var(--red-dim);
    border-radius: 14px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 16px;
    }
    .modal-icon svg {
    width: 24px;
    height: 24px;
    color: var(--red);
    }
    .modal-title {
    font-size: 16px;
    font-weight: 800;
    color: var(--text-1);
    margin-bottom: 6px;
    }
    .modal-desc {
    font-size: 13px;
    color: var(--text-2);
    margin-bottom: 22px;
    line-height: 1.6;
    }
    .modal-actions {
    display: flex;
    gap: 10px;
    justify-content: flex-end;
    }
    .btn-cancel {
    padding: 9px 18px;
    border: 1px solid var(--border);
    border-radius: 8px;
    font-family: var(--font);
    font-size: 13px;
    font-weight: 600;
    background: var(--surface);
    color: var(--text-2);
    cursor: pointer;
    transition: all 0.15s;
    }
    .btn-cancel:hover {
    border-color: var(--border-2);
    color: var(--text-1);
    }
    .btn-danger {
    padding: 9px 18px;
    border: none;
    border-radius: 8px;
    font-family: var(--font);
    font-size: 13px;
    font-weight: 600;
    background: var(--red);
    color: #fff;
    cursor: pointer;
    transition: all 0.15s;
    box-shadow: 0 2px 8px rgba(220, 38, 38, 0.25);
    }
    .btn-danger:hover {
    background: #b91c1c;
    transform: translateY(-1px);
    }

    /* Animasi haluskan transisi tabel */
    #categoryTable tbody {
    transition: opacity 0.3s ease-in-out;
    }

    /* Animasi untuk baris baru yang muncul */
    @keyframes fadeInRow {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
    }

    .fade-in-animated {
    animation: fadeInRow 0.4s ease forwards;
    }
@endsection

@section('content')

    {{-- Page Header --}}
    <div class="page-header">
        <div class="page-header-left">
            <h1>
                <span class="page-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <polygon points="12 2 2 7 12 12 22 7 12 2"></polygon>
                        <polygon points="2 17 12 22 22 17"></polygon>
                        <polygon points="2 12 12 17 22 12"></polygon>
                    </svg>
                </span>
                Kategori Produk
            </h1>
            <p>Kelola semua kategori untuk mengorganisir produk di toko Anda.</p>
        </div>
        <a href="{{ route('product-categories.create') }}" class="btn-primary">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"
                stroke-linejoin="round">
                <line x1="12" y1="5" x2="12" y2="19" />
                <line x1="5" y1="12" x2="19" y2="12" />
            </svg>
            Tambah Kategori
        </a>
    </div>

    {{-- Stats Bar --}}
    <div class="stats-bar">
        <div class="stat-card">
            <div class="stat-icon blue">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                    stroke-linejoin="round">
                    <path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z" />
                </svg>
            </div>
            <div>
                <div class="stat-value">{{ number_format($stats['total']) }}</div>
                <div class="stat-label">Total Kategori</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon green">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                    stroke-linejoin="round">
                    <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14" />
                    <polyline points="22 4 12 14.01 9 11.01" />
                </svg>
            </div>
            <div>
                <div class="stat-value">{{ number_format($stats['active']) }}</div>
                <div class="stat-label">Kategori Aktif</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon gray">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                    stroke-linejoin="round">
                    <circle cx="12" cy="12" r="10" />
                    <line x1="4.93" y1="4.93" x2="19.07" y2="19.07" />
                </svg>
            </div>
            <div>
                <div class="stat-value">{{ number_format($stats['inactive']) }}</div>
                <div class="stat-label">Kategori Nonaktif</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon purple">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                    stroke-linejoin="round">
                    <path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z" />
                    <line x1="3" y1="6" x2="21" y2="6" />
                    <path d="M16 10a4 4 0 0 1-8 0" />
                </svg>
            </div>
            <div>
                <div class="stat-value">{{ number_format($stats['total_products']) }}</div>
                <div class="stat-label">Produk Berkategori</div>
            </div>
        </div>
    </div>

    {{-- Flash Alert Handled by Global Toast --}}

    {{-- Filter Card --}}
    <div class="filter-card">
        <div class="filter-card-top">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                stroke-linejoin="round" style="width:15px;height:15px;color:var(--text-3);">
                <polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3" />
            </svg>
            <span class="filter-card-title">Filter Kategori</span>
        </div>

        <div class="filter-grid">
            {{-- 1. Toko (ID: dtFilterStore) --}}
            <div class="form-group">
                <label class="form-label">Toko</label>
                <select id="dtFilterStore" class="form-input">
                    <option value="">Semua Toko</option>
                    @foreach ($stores->where('is_active', true) as $store)
                        <option value="{{ $store->id }}">{{ $store->name }}</option>
                    @endforeach
                </select>
            </div>

            {{-- 2. Status (ID disatukan ke dtFilterStatus agar dibaca JS) --}}
            <div class="form-group">
                <label class="form-label">Status</label>
                <select id="dtFilterStatus" class="form-input">
                    <option value="">Semua Status</option>
                    <option value="active">Aktif</option>
                    <option value="inactive">Nonaktif</option>
                </select>
            </div>

            {{-- 3. Jumlah Produk (Tambahkan ID: dtFilterProducts) --}}
            <div class="form-group">
                <label class="form-label">Jumlah Produk</label>
                <select id="dtFilterProducts" name="products" class="form-input">
                    <option value="">Semua Jumlah</option>
                    <option value="empty">Kosong (0)</option>
                    <option value="few">Sedikit (1-5)</option>
                    <option value="many">Banyak (> 5)</option>
                </select>
            </div>

            {{-- 4. Rentang Tanggal (ID: dtFilterDate) --}}
            <div class="form-group" style="flex: 1.5;">
                <label class="form-label">Dibuat Tanggal</label>
                <input type="date" id="dtFilterDate" class="form-input">
            </div>
        </div>

        <div class="filter-actions">
            <a href="javascript:void(0)" id="btnResetFilter" class="btn-outline-reset" style="display:none;">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"
                    stroke-linejoin="round">
                    <line x1="18" y1="6" x2="6" y2="18" />
                    <line x1="6" y1="6" x2="18" y2="18" />
                </svg>
                Reset Filter
            </a>
            <button type="button" id="btnApplyFilter" class="btn-primary">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"
                    stroke-linejoin="round">
                    <circle cx="11" cy="11" r="8" />
                    <line x1="21" y1="21" x2="16.65" y2="16.65" />
                </svg>
                Terapkan Filter
            </button>
        </div>
    </div>

    {{-- Table Card --}}
    <div class="table-card">

        <div class="table-responsive">
            <table class="cat-table" id="categoryTable">
                <thead>
                    <tr>
                        <th class="center" style="width:50px;">No</th>
                        <th>Nama Kategori</th>
                        <th>Toko</th>
                        <th>Deskripsi</th>
                        <th class="center">Jumlah Produk</th>
                        <th>Dibuat</th>
                        <th>Status</th>
                        <th class="center" style="width:140px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @include('product-categories._table_rows')
                </tbody>
            </table>
        </div>
    </div>

    {{-- Delete Confirmation Modal --}}
    <div class="modal-overlay" id="deleteModal">
        <div class="modal-box">
            <div class="modal-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                    stroke-linejoin="round">
                    <polyline points="3 6 5 6 21 6" />
                    <path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6" />
                    <path d="M10 11v6M14 11v6" />
                    <path d="M9 6V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2" />
                </svg>
            </div>
            <div class="modal-title">Hapus Kategori?</div>
            <div class="modal-desc" id="modal-desc">
                Kategori ini akan dihapus permanen. Tindakan ini tidak dapat dibatalkan.
            </div>
            <div class="modal-actions">
                <button class="btn-cancel" onclick="closeDeleteModal()">Batalkan</button>
                <form id="deleteForm" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn-danger">Ya, Hapus</button>
                </form>
            </div>
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
        function openDeleteModal(categoryId, categoryName, productCount) {
            let desc =
                `Kategori <strong>"${categoryName}"</strong> akan dihapus permanen. Tindakan ini tidak dapat dibatalkan.`;
            if (productCount > 0) {
                desc += `<br><br><div style="padding:12px; background:var(--red-dim); border-radius:10px; color:var(--red); font-weight:600; font-size:12.5px; border:1px solid rgba(220,38,38,0.2);">
                    ⚠ PERINGATAN: Menghapus kategori ini akan menyebabkan <strong>${productCount} produk</strong> kehilangan kategorinya.
                </div>`;
            }
            document.getElementById('modal-desc').innerHTML = desc;
            document.getElementById('deleteForm').action = '/product-categories/' + categoryId;
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
            return $('#categoryTable').DataTable({
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
                            <polygon points="12 2 2 7 12 12 22 7 12 2"></polygon>
                            <polygon points="2 17 12 22 22 17"></polygon>
                            <polygon points="2 12 12 17 22 12"></polygon>
                        </svg>
                    </div>
                    <div class="empty-title" style="margin-bottom: 6px;">Tidak Ada Kategori Ditemukan</div>
                    <div class="empty-desc">Tidak ada kategori yang cocok dengan pencarian atau filter yang dipilih.</div>
                </div>`,
                    emptyTable: `
                <div class="empty-state" style="padding: 40px 20px; text-align: center; display: flex; flex-direction: column; align-items: center; justify-content: center; margin: 0 auto; width: 100%; border-bottom: none;">
                    <div class="empty-icon" style="margin-bottom: 20px;">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                            <polygon points="12 2 2 7 12 12 22 7 12 2"></polygon>
                            <polygon points="2 17 12 22 22 17"></polygon>
                            <polygon points="2 12 12 17 22 12"></polygon>
                        </svg>
                    </div>
                    <div class="empty-title" style="margin-bottom: 6px;">Tidak Ada Kategori Ditemukan</div>
                    <div class="empty-desc">Belum ada kategori. Mulai tambahkan kategori pertama Anda.</div>
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
                        targets: 7,
                        orderable: false,
                        searchable: false
                    }
                ],
                order: [
                    [5, 'desc']
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

        $(document).ready(function() {
            // Inisialisasi Pertama
            table = initDataTable();

            // Tampilkan search input
            $('.dataTables_filter').show();

            // 3. Handler Tombol Terapkan (AJAX)
            $('#btnApplyFilter').on('click', function() {
                const params = {
                    store_id: $('#dtFilterStore').val(),
                    date: $('#dtFilterDate').val(),
                    status: $('#dtFilterStatus').val(),
                    products: $('#dtFilterProducts').val(),
                    search: ''
                };

                $('#categoryTable tbody').css('opacity', '0.4');

                $.ajax({
                    url: "{{ route('product-categories.index') }}",
                    type: 'GET',
                    data: params,
                    success: function(html) {
                        if ($.fn.DataTable.isDataTable('#categoryTable')) {
                            table.destroy();
                        }
                        $('#categoryTable tbody').html(html);
                        // FIX 5: fade-in-animated is applied here via JS (after AJAX),
                        // removing reliance on the server-side class in the partial.
                        // The partial (_table_rows) should no longer include
                        // class="fade-in-animated" on <tr> elements so that the
                        // initial page load renders without the animation class,
                        // and only AJAX-refreshed rows receive it.
                        $('#categoryTable tbody tr').addClass('fade-in-animated');
                        table = initDataTable();
                        $('.dataTables_filter').show();
                        $('#categoryTable tbody').css('opacity', '1');

                        checkResetVisibility();
                    },
                    // FIX 4: Restore opacity on AJAX failure so the table does not
                    // remain stuck at 40% opacity when the request fails.
                    error: function() {
                        $('#categoryTable tbody').css('opacity', '1');
                        if (typeof showToast === 'function') {
                            showToast('Gagal memuat data. Silakan coba lagi.', 'error');
                        }
                    }
                });
            });

            // Handler Tombol Reset
            $('#btnResetFilter').on('click', function() {
                $('#dtFilterStore').val('');
                $('#dtFilterDate').val('');
                $('#dtFilterStatus').val('');
                $('#dtFilterProducts').val('');
                $('input[type="search"]').val('');
                $(this).hide();
                $('#btnApplyFilter').trigger('click');
            });

            // Fungsi Visibilitas Reset
            function checkResetVisibility() {
                const store = $('#dtFilterStore').val();
                const date = $('#dtFilterDate').val();
                const status = $('#dtFilterStatus').val();
                const products = $('#dtFilterProducts').val();

                if (store || date || status || products) {
                    $('#btnResetFilter').fadeIn(300).css('display', 'inline-flex');
                } else {
                    $('#btnResetFilter').fadeOut(300);
                }
            }
        });
    </script>
@endpush
