@extends('layouts.app')

@section('title', 'Manajemen Data Customer')

@section('styles')
    /* =============================================
    CUSTOMER INDEX — Professional Design
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
    width: 36px; height: 36px;
    background: linear-gradient(135deg, var(--accent), color-mix(in srgb, var(--accent) 70%, #7c3aed));
    border-radius: 10px;
    display: flex; align-items: center; justify-content: center;
    flex-shrink: 0;
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
    .stat-icon { width: 42px; height: 42px; border-radius: 10px; display: flex; align-items: center; justify-content:
    center; flex-shrink: 0; }
    .stat-icon svg { width: 20px; height: 20px; }
    .stat-icon.blue { background: rgba(59, 130, 246, 0.1); color: #3b82f6; }
    .stat-icon.green { background: var(--green-dim); color: var(--green); }
    .stat-icon.red { background: var(--red-dim); color: var(--red); }
    .stat-icon.purple { background: rgba(139, 92, 246, 0.1); color: #8b5cf6; }
    .stat-value { font-size: 20px; font-weight: 800; color: var(--text-1); letter-spacing: -0.03em; }
    .stat-label { font-size: 11.5px; color: var(--text-3); font-weight: 500; margin-top: 2px; }

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
    .filter-card-title { font-size: 13px; font-weight: 700; color: var(--text-1); }
    .filter-grid {
    display: grid;
    grid-template-columns: repeat(5, 1fr);
    gap: 14px;
    margin-bottom: 14px;
    }
    .filter-grid-row2 {
    display: grid;
    grid-template-columns: 1fr 1fr 1fr;
    gap: 14px;
    }
    @media (max-width: 900px) {
    .filter-grid { grid-template-columns: 1fr 1fr; }
    }

    @media (max-width: 560px) {
    .filter-grid { grid-template-columns: 1fr; }
    }

    .form-group {}
    .form-label { display: block; font-size: 11.5px; font-weight: 700; color: var(--text-3); margin-bottom: 6px;
    text-transform: uppercase; letter-spacing: 0.05em; }
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
    .form-input:focus { border-color: var(--accent); box-shadow: 0 0 0 3px var(--accent-glow); }
    .form-input::placeholder { color: var(--text-4); }

    .filter-actions { display: flex; justify-content: flex-end; gap: 10px; margin-top: 16px; padding-top: 16px; border-top:
    1px solid var(--border); }
    .btn-primary { display: inline-flex; align-items: center; gap: 7px; background: var(--accent); color: #fff; border:
    none; padding: 9px 18px; border-radius: 9px; font-family: var(--font); font-weight: 700; font-size: 13px; cursor:
    pointer; transition: all 0.15s; box-shadow: 0 2px 8px color-mix(in srgb, var(--accent) 30%, transparent); }
    .btn-primary:hover { opacity: 0.9; transform: translateY(-1px); }
    .btn-primary svg { width: 14px; height: 14px; }
    .btn-outline-reset { display: inline-flex; align-items: center; gap: 7px; background: var(--red-dim); color: var(--red);
    border: 1px solid rgba(220,38,38,0.2); padding: 9px 16px; border-radius: 9px; font-family: var(--font); font-weight:
    700; font-size: 13px; cursor: pointer; text-decoration: none; transition: all 0.15s; }
    .btn-outline-reset:hover { border-color: rgba(220,38,38,0.4); }
    .btn-outline-reset svg { width: 13px; height: 13px; }

    /* ── Flash Alert ─────────────────────────────── */
    .alert-success {
    display: flex; align-items: center; gap: 11px;
    background: var(--green-dim);
    border: 1px solid rgba(22, 163, 74, 0.25);
    border-radius: 11px;
    padding: 13px 18px;
    font-size: 13px; color: var(--green); font-weight: 600;
    margin-bottom: 22px;
    animation: rise 0.3s ease both;
    }
    .alert-success svg { width: 18px; height: 18px; flex-shrink: 0; }

    /* ── Table Card ──────────────────────────────── */
    .table-card {
    background: var(--panel);
    border: 1px solid var(--border);
    border-radius: 14px;
    overflow: hidden;
    box-shadow: var(--shadow-sm);
    animation: rise 0.35s ease both;
    }
    .table-responsive { overflow-x: auto; }
    .cust-table { width: 100%; border-collapse: collapse; }
    .cust-table th {
    background: var(--surface);
    padding: 12px 20px; /* ← PADDING BARU */
    text-align: left;
    font-size: 10.5px; font-weight: 700; color: var(--text-3);
    border-bottom: 1px solid var(--border);
    white-space: nowrap; text-transform: uppercase; letter-spacing: 0.08em;
    }
    .cust-table th.center { text-align: center; }
    .cust-table td { padding: 15px 20px; border-bottom: 1px solid var(--border); vertical-align: middle; } /* ← PADDING BARU
    */
    .cust-table tr:last-child td { border-bottom: none; }
    .cust-table tbody tr { transition: background 0.12s; }
    .cust-table tbody tr:hover td { background: color-mix(in srgb, var(--accent) 3%, var(--surface)); }
    
    /* ── Cell Components ─────────────────────────── */
    .cell-no { text-align: center; color: var(--text-4); font-weight: 600; font-size: 12px; }

    .avatar-wrap { display: flex; align-items: center; gap: 12px; }
    .avatar {
    width: 40px; height: 40px;
    border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    font-weight: 800; font-size: 15px;
    flex-shrink: 0;
    background: linear-gradient(135deg, color-mix(in srgb, var(--accent) 15%, transparent), color-mix(in srgb, var(--accent)
    8%, transparent));
    color: var(--accent);
    border: 1.5px solid color-mix(in srgb, var(--accent) 20%, transparent);
    }
    .customer-name { font-size: 13.5px; font-weight: 700; color: var(--text-1); }
    .customer-id { font-size: 10.5px; color: var(--text-4); margin-top: 2px; font-family: 'Courier New', monospace; }

    .contact-email { font-size: 12.5px; font-weight: 600; color: var(--accent); }
    .contact-phone { font-size: 11.5px; color: var(--text-3); margin-top: 3px; display: flex; align-items: center; gap: 4px;
    }
    .contact-phone svg { width: 11px; height: 11px; }

    .order-count { font-size: 15px; font-weight: 800; color: var(--text-1); }
    .order-label { font-size: 10.5px; color: var(--text-3); margin-top: 2px; }

    .badge {
    display: inline-flex; align-items: center; gap: 5px;
    padding: 4px 11px; border-radius: 20px;
    font-size: 11.5px; font-weight: 600;
    white-space: nowrap;
    }
    .badge::before { content: ''; width: 6px; height: 6px; border-radius: 50%; flex-shrink: 0; }
    .badge-active { background: var(--green-dim); color: var(--green); border: 1px solid rgba(22,163,74,0.2); }
    .badge-active::before { background: var(--green); }
    .badge-inactive { background: var(--red-dim); color: var(--red); border: 1px solid rgba(220,38,38,0.2); }
    .badge-inactive::before { background: var(--red); }

    .reg-date { font-size: 12.5px; color: var(--text-2); font-weight: 500; }
    .reg-relative { font-size: 11px; color: var(--text-4); margin-top: 2px; }

    .actions-cell { display: flex; gap: 6px; justify-content: center; }
    .btn-sm { display: inline-flex; align-items: center; gap: 5px; border: 1px solid var(--border); border-radius: 7px;
    font-family: var(--font); font-size: 11.5px; font-weight: 600; padding: 6px 12px; cursor: pointer; transition: all
    0.15s; background: var(--panel); color: var(--text-2); text-decoration: none; white-space: nowrap; }
    .btn-sm svg { width: 12px; height: 12px; }
    .btn-sm:hover { border-color: var(--accent); color: var(--accent); background: color-mix(in srgb, var(--accent) 5%,
    var(--panel)); }
    .btn-sm.danger:hover { border-color: rgba(220,38,38,0.4); color: var(--red); background: var(--red-dim); }

    /* ── Empty State ─────────────────────────────── */
    .empty-state { padding: 72px 20px; text-align: center; }
    .empty-icon { width: 80px; height: 80px; background: var(--surface-2); border-radius: 20px; display: flex; align-items:
    center; justify-content: center; margin: 0 auto 18px; }
    .empty-icon svg { width: 36px; height: 36px; color: var(--text-4); }
    .empty-title { font-size: 15px; font-weight: 700; color: var(--text-1); margin-bottom: 7px; }
    .empty-desc { font-size: 13px; color: var(--text-3); }

    /* ── Pagination ──────────────────────────────── */
    .pagination-wrap {
    display: flex; align-items: center; justify-content: space-between;
    padding: 14px 20px; border-top: 1px solid var(--border);
    background: var(--surface); gap: 12px; flex-wrap: wrap;
    }
    .pagination-info { font-size: 12px; color: var(--text-3); }

    @media (max-width: 700px) { .stats-bar { grid-template-columns: 1fr 1fr; } }

    /* ── DataTables — hide default search ──────── */
    .dataTables_wrapper .dataTables_filter { display: none !important; }

    /* ── DataTables overrides ───────────────────── */
    .dataTables_wrapper .dataTables_filter,
    .dataTables_wrapper .dataTables_length { display: none !important; }
    .dataTables_wrapper .dataTables_info { font-size: 12px; color: var(--text-3); padding: 14px 20px; }
    .dataTables_wrapper .dataTables_paginate { padding: 10px 20px; }
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

    .dataTables_wrapper .dataTables_info { font-size: 12px; color: var(--text-3); padding: 14px 20px; }
    .dataTables_wrapper .dataTables_paginate { padding: 10px 20px; }

    .btn-primary:disabled {
    opacity: 0.7;
    cursor: not-allowed;
    }
@endsection

@section('content')

    {{-- Page Header --}}
    <div class="page-header">
        <div class="page-header-left">
            <h1>
                <span class="page-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                        stroke-linejoin="round">
                        <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2" />
                        <circle cx="9" cy="7" r="4" />
                        <path d="M23 21v-2a4 4 0 0 0-3-3.87" />
                        <path d="M16 3.13a4 4 0 0 1 0 7.75" />
                    </svg>
                </span>
                Manajemen Customer
            </h1>
            <p>Kelola data pelanggan, pantau aktivitas pembelian, dan kontrol akses akun.</p>
        </div>
    </div>

    {{-- Stats Bar --}}
    <div class="stats-bar">
        <div class="stat-card">
            <div class="stat-icon blue">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                    stroke-linejoin="round">
                    <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2" />
                    <circle cx="9" cy="7" r="4" />
                    <path d="M23 21v-2a4 4 0 0 0-3-3.87" />
                    <path d="M16 3.13a4 4 0 0 1 0 7.75" />
                </svg>
            </div>
            <div>
                <div class="stat-value">{{ number_format($stats['total'] ?? 0) }}</div>
                <div class="stat-label">Total Customer</div>
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
                <div class="stat-value">{{ number_format($stats['active'] ?? 0) }}</div>
                <div class="stat-label">Akun Aktif</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon red">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                    stroke-linejoin="round">
                    <circle cx="12" cy="12" r="10" />
                    <line x1="4.93" y1="4.93" x2="19.07" y2="19.07" />
                </svg>
            </div>
            <div>
                <div class="stat-value">{{ number_format($stats['blocked'] ?? 0) }}</div>
                <div class="stat-label">Akun Diblokir</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon purple">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                    stroke-linejoin="round">
                    <line x1="12" y1="1" x2="12" y2="23" />
                    <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6" />
                </svg>
            </div>
            <div>
                <div class="stat-value">Rp {{ number_format(($stats['total_spent'] ?? 0) / 1000000, 1) }}M</div>
                <div class="stat-label">Total Transaksi</div>
            </div>
        </div>
    </div>

    {{-- Flash Alert --}}
    @if(session('success'))
        <div class="alert-success" id="flash-alert">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"
                stroke-linejoin="round">
                <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14" />
                <polyline points="22 4 12 14.01 9 11.01" />
            </svg>
            {{ session('success') }}
        </div>
    @endif

    {{-- Filter Card --}}
    <div class="filter-card">
        <div class="filter-card-top">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                stroke-linejoin="round" style="width:15px;height:15px;color:var(--text-3);">
                <polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3" />
            </svg>
            <span class="filter-card-title">Filter</span>
        </div>

        <div class="filter-grid">

            {{-- Filter Toko --}}
            <div class="form-group">
                <label class="form-label">Filter Toko</label>
                <select id="dtFilterStore" class="form-input">
                    <option value="">Semua Toko</option>
                    @foreach($stores as $store)
                        <option value="{{ $store->name }}">{{ $store->name }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Urutkan --}}
            <div class="form-group">
                <label class="form-label">Urutkan</label>
                <select id="dtSortBy" class="form-input">
                    <option value="">Default</option>
                    <option value="name_az">Nama A–Z</option>
                    <option value="name_za">Nama Z–A</option>
                    <option value="orders">Terbanyak Pesanan</option>
                </select>
            </div>

            {{-- Status --}}
            <div class="form-group">
                <label class="form-label">Status Akun</label>
                <select id="dtFilterStatus" class="form-input">
                    <option value="">Semua Status</option>
                    <option value="Aktif">Aktif</option>
                    <option value="Diblokir">Diblokir</option>
                </select>
            </div>

            {{-- Tanggal --}}
            <div class="form-group">
                <label class="form-label">Mendaftar Mulai</label>
                <input type="date" id="dtDateFrom" class="form-input">
            </div>

            <div class="form-group">
                <label class="form-label">Mendaftar Sampai</label>
                <input type="date" id="dtDateTo" class="form-input">
            </div>

        </div>

        {{-- Baris 2: Status, Tanggal Mulai, Tanggal Selesai --}}
        <div class="filter-actions">

            {{-- RESET --}}
            <a href="javascript:void(0)" id="btnResetFilter" class="btn-outline-reset" style="display:none;">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                    <line x1="18" y1="6" x2="6" y2="18" />
                    <line x1="6" y1="6" x2="18" y2="18" />
                </svg>
                Reset Filter
            </a>

            {{-- APPLY --}}
            <button type="button" id="btnApplyFilter" class="btn-primary">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                    <circle cx="11" cy="11" r="8" />
                    <line x1="21" y1="21" x2="16.65" y2="16.65" />
                </svg>
                Terapkan Filter
            </button>

        </div>
    </div>

    {{-- Table Card --}}
    <div class="table-card">
        @if($customers->isEmpty())
            <div class="empty-state">
                <div class="empty-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                        stroke-linejoin="round">
                        <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2" />
                        <circle cx="9" cy="7" r="4" />
                        <path d="M23 21v-2a4 4 0 0 0-3-3.87" />
                        <path d="M16 3.13a4 4 0 0 1 0 7.75" />
                    </svg>
                </div>
                <div class="empty-title">Tidak Ada Customer Ditemukan</div>
                <div class="empty-desc">
                    @if(request()->anyFilled(['search', 'status', 'date_from', 'date_to']))
                        Coba ubah atau reset filter pencarian.
                    @else
                        Belum ada pelanggan yang mendaftar.
                    @endif
                </div>
            </div>
        @else
            <div class="table-responsive">
                <table class="cust-table" id="customerTable">
                    <thead>
                        <tr>
                            <th class="center" style="width:50px;">No</th>
                            <th>Nama Pelanggan</th>
                            <th>Kontak</th>
                            <th>Total Pesanan</th>
                            <th>Status Akun</th>
                            <th>Terdaftar</th>
                            <th class="center" style="width:120px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($customers as $index => $customer)
                            <tr>
                                {{-- No --}}
                                <td class="cell-no"></td>

                                {{-- Nama --}}
                                <td>
                                    <div class="avatar-wrap">
                                        <div class="avatar">{{ strtoupper(substr($customer->name, 0, 1)) }}</div>
                                        <div>
                                            <div class="customer-name">{{ $customer->name }}</div>
                                            <div class="customer-id">#{{ $customer->id }}</div>
                                        </div>
                                    </div>
                                </td>

                                {{-- Kontak --}}
                                <td>
                                    <div class="contact-email">{{ $customer->email }}</div>
                                    <div class="contact-phone">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                            stroke-linecap="round" stroke-linejoin="round">
                                            <path
                                                d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07A19.5 19.5 0 0 1 4.69 12 19.79 19.79 0 0 1 1.61 3.38 2 2 0 0 1 3.58 1h3a2 2 0 0 1 2 1.72c.127.96.361 1.903.7 2.81a2 2 0 0 1-.45 2.11L7.91 8.54a16 16 0 0 0 6 6l.92-.92a2 2 0 0 1 2.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0 1 22 16.92z" />
                                        </svg>
                                        {{ $customer->phone ?: 'Belum diisi' }}
                                    </div>
                                </td>

                                {{-- Total Pesanan --}}
                                <td>
                                    <div class="order-count">{{ number_format($customer->orders_count) }}</div>
                                    <div class="order-label">Transaksi</div>
                                </td>

                                {{-- Status --}}
                                <td>
                                    @if($customer->is_active)
                                        <span class="badge badge-active">Aktif</span>
                                    @else
                                        <span class="badge badge-inactive">Diblokir</span>
                                    @endif
                                </td>

                                {{-- Terdaftar --}}
                                <td>
                                    <div class="reg-date">{{ $customer->created_at->format('d M Y') }}</div>
                                    <div class="reg-relative">{{ $customer->created_at->diffForHumans() }}</div>
                                </td>

                                {{-- Aksi --}}
                                <td>
                                    <div class="actions-cell">
                                        <a href="{{ route('customers.show', $customer->id) }}" class="btn-sm">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                                stroke-linecap="round" stroke-linejoin="round">
                                                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z" />
                                                <circle cx="12" cy="12" r="3" />
                                            </svg>
                                            Detail
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

@endsection
@push('scripts')

    {{-- DataTables CSS --}}
    <link rel="stylesheet" href="https://cdn.datatables.net/2.3.7/css/dataTables.dataTables.min.css">

    {{-- jQuery --}}
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

    {{-- DataTables JS --}}
    <script src="https://cdn.datatables.net/2.3.7/js/dataTables.min.js"></script>

    <script>
        // Flash alert auto hide
        setTimeout(() => {
            const el = document.getElementById('flash-alert');
            if (el) {
                el.style.transition = 'opacity 0.5s';
                el.style.opacity = '0';
                setTimeout(() => el.remove(), 500);
            }
        }, 4500);

        let table;

        $(document).ready(function () {
            table = $('#customerTable').DataTable({
                responsive: true,
                autoWidth: false,

                language: {
                    search: "",
                    searchPlaceholder: "Cari...",
                    lengthMenu: "Tampilkan _MENU_ data",
                    info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
                    infoEmpty: "Data tidak ditemukan",
                    zeroRecords: "Tidak ada data yang cocok",
                    paginate: {
                        first: "«",
                        last: "»",
                        next: "›",
                        previous: "‹"
                    }
                },

                columnDefs: [
                    { targets: 0, orderable: false, searchable: false },
                    { targets: -1, orderable: false, searchable: false }
                ],

                order: [[5, 'desc']],

                drawCallback: function () {
                    let api = this.api();
                    let startIndex = api.page.info().start;
                    api.column(0, { search: 'applied', order: 'applied' }).nodes()
                        .each(function (cell, i) {
                            cell.innerHTML = startIndex + i + 1;
                        });
                }
            });

            // Connect custom search input to DataTables
            $('#dtSearch').on('keyup', function () {
                table.search(this.value).draw();
            });
        });
        $('#btnApplyFilter').on('click', function () {

            table.column(2).search($('#dtFilterStore').val());
            table.column(4).search($('#dtFilterStatus').val());

            table.draw();

            $('#btnResetFilter').show();
        });

        $('#btnResetFilter').on('click', function () {

            $('#dtFilterStore').val('');
            $('#dtSortBy').val('');
            $('#dtFilterStatus').val('');
            $('#dtDateFrom').val('');
            $('#dtDateTo').val('');

            table.search('').columns().search('').order([1, 'asc']).draw();

            $('#btnResetFilter').hide();
        });
    </script>

@endpush
