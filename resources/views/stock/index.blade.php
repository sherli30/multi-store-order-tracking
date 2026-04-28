@extends('layouts.app')

@section('title', 'Riwayat Stok — ' . $product->name)

@section('styles')
    /* ── Page Header ─────────────────────────────── */
    .page-header { display:flex; align-items:flex-start; justify-content:space-between; margin-bottom:28px; flex-wrap:wrap; gap:16px; }
    .page-header-left h1 { font-size:22px; font-weight:800; letter-spacing:-0.04em; color:var(--text-1); margin-bottom:5px; display:flex; align-items:center; gap:10px; }
    .page-icon { width:36px; height:36px; background:linear-gradient(135deg, var(--accent), color-mix(in srgb, var(--accent) 70%, #7c3aed)); border-radius:10px; display:flex; align-items:center; justify-content:center; flex-shrink:0; }
    .page-icon svg { width:18px; height:18px; color:#fff; }
    .breadcrumb { font-size:12px; color:var(--text-3); margin-bottom:4px; display:flex; align-items:center; gap:6px; flex-wrap:wrap; }
    .breadcrumb a { color:var(--accent); text-decoration:none; font-weight:600; }
    .breadcrumb a:hover { text-decoration:underline; }

    /* ── Product Info Card ───────────────────────── */
    .product-info-card {
        background:var(--panel); border:1px solid var(--border); border-radius:14px;
        padding:20px 22px; margin-bottom:24px; box-shadow:var(--shadow-sm);
        display:flex; align-items:center; gap:18px; flex-wrap:wrap;
    }
    .product-thumb-lg {
        width:58px; height:58px; border-radius:12px; object-fit:cover;
        background:var(--surface-2); border:1px solid var(--border); flex-shrink:0;
        display:flex; align-items:center; justify-content:center; overflow:hidden;
    }
    .product-thumb-lg img { width:100%; height:100%; object-fit:cover; border-radius:12px; }
    .product-thumb-lg svg { width:24px; height:24px; color:var(--text-4); }
    .product-meta { flex:1; min-width:0; }
    .product-meta-name { font-size:15px; font-weight:800; color:var(--text-1); margin-bottom:4px; }
    .product-meta-sub { font-size:12px; color:var(--text-3); display:flex; gap:10px; flex-wrap:wrap; }
    .product-meta-pill { display:inline-flex; align-items:center; gap:4px; font-size:11px; font-weight:600; padding:2px 8px; border-radius:5px; }
    .pill-cat { background:rgba(139,92,246,0.1); color:#8b5cf6; }
    .pill-store { background:var(--accent-dim); color:var(--accent); }
    .inactive-chip { background:var(--surface-2); color:var(--text-3); border:1px solid var(--border); font-size:11px; font-weight:600; padding:2px 8px; border-radius:5px; }

    /* ── Stats Bar ───────────────────────────────── */
    .stats-bar { display:grid; grid-template-columns:repeat(3,1fr); gap:14px; margin-bottom:24px; }
    .stat-card { background:var(--panel); border:1px solid var(--border); border-radius:12px; padding:16px 18px; display:flex; align-items:center; gap:14px; box-shadow:var(--shadow-sm); }
    .stat-icon { width:42px; height:42px; border-radius:10px; display:flex; align-items:center; justify-content:center; flex-shrink:0; }
    .stat-icon svg { width:20px; height:20px; }
    .stat-icon.green { background:var(--green-dim); color:var(--green); }
    .stat-icon.red { background:var(--red-dim); color:var(--red); }
    .stat-icon.blue { background:rgba(59,130,246,0.1); color:#3b82f6; }
    .stat-value { font-size:20px; font-weight:800; color:var(--text-1); letter-spacing:-0.03em; }
    .stat-label { font-size:11.5px; color:var(--text-3); font-weight:500; margin-top:2px; }

    /* ── Stock Action Buttons ────────────────────── */
    .stock-actions { display:flex; gap:10px; flex-wrap:wrap; }
    .btn-add {
        display:inline-flex; align-items:center; gap:7px;
        background:var(--green); color:#fff; border:none; padding:10px 20px;
        border-radius:9px; font-family:var(--font); font-weight:700; font-size:13px;
        cursor:pointer; transition:all 0.15s; text-decoration:none;
        box-shadow:0 2px 8px rgba(22,163,74,0.25);
    }
    .btn-add:hover { background:#15803d; transform:translateY(-1px); }
    .btn-add svg { width:14px; height:14px; }
    .btn-deduct {
        display:inline-flex; align-items:center; gap:7px;
        background:var(--surface); color:var(--red); border:1px solid rgba(220,38,38,0.25);
        padding:10px 20px; border-radius:9px; font-family:var(--font); font-weight:700;
        font-size:13px; cursor:pointer; transition:all 0.15s; text-decoration:none;
    }
    .btn-deduct:hover { background:var(--red-dim); border-color:rgba(220,38,38,0.4); transform:translateY(-1px); }
    .btn-deduct svg { width:14px; height:14px; }
    .btn-deduct.disabled { opacity:0.35; pointer-events:none; cursor:not-allowed; }
    .btn-back {
        display:inline-flex; align-items:center; gap:7px;
        background:var(--surface); color:var(--text-2); border:1px solid var(--border);
        padding:10px 18px; border-radius:9px; font-family:var(--font); font-weight:600;
        font-size:13px; text-decoration:none; transition:all 0.15s;
    }
    .btn-back:hover { border-color:var(--border-2); color:var(--text-1); }
    .btn-back svg { width:14px; height:14px; }

    /* ── Flash ────────────────────────────────────── */
    .alert { display:flex; align-items:center; gap:11px; border-radius:11px; padding:13px 18px; font-size:13px; font-weight:600; margin-bottom:22px; animation:rise 0.3s ease both; }
    .alert svg { width:18px; height:18px; flex-shrink:0; }
    .alert-success { background:var(--green-dim); border:1px solid rgba(22,163,74,0.25); color:var(--green); }
    .alert-error   { background:var(--red-dim);   border:1px solid rgba(220,38,38,0.25);  color:var(--red); }

    /* ── Validation Errors ──────────────────────── */
    .error-list { background:var(--red-dim); border:1px solid rgba(220,38,38,0.2); border-radius:11px; padding:13px 18px; margin-bottom:22px; }
    .error-list-title { font-size:13px; font-weight:700; color:var(--red); margin-bottom:8px; }
    .error-list ul { list-style:none; padding:0; margin:0; display:flex; flex-direction:column; gap:4px; }
    .error-list ul li { font-size:12px; color:var(--red); display:flex; align-items:center; gap:6px; }
    .error-list ul li::before { content:""; width:4px; height:4px; background:var(--red); border-radius:50%; flex-shrink:0; }

    /* ── Table ────────────────────────────────────── */
    .table-card { background:var(--panel); border:1px solid var(--border); border-radius:14px; overflow:hidden; box-shadow:var(--shadow-sm); }
    .table-header { padding:16px 20px; border-bottom:1px solid var(--border); display:flex; align-items:center; justify-content:space-between; gap:12px; }
    .table-title { font-size:14px; font-weight:700; color:var(--text-1); }
    .table-count { font-size:12px; color:var(--text-3); background:var(--surface); border:1px solid var(--border); padding:3px 10px; border-radius:20px; font-weight:600; }
    .table-responsive { overflow-x:auto; }
    .movement-table { width:100%; border-collapse:collapse; }
    .movement-table th {
        background:var(--surface); padding:11px 16px; text-align:left;
        font-size:10.5px; font-weight:700; color:var(--text-3);
        border-bottom:1px solid var(--border); white-space:nowrap;
        text-transform:uppercase; letter-spacing:0.08em;
    }
    .movement-table td { padding:12px 16px; border-bottom:1px solid var(--border); vertical-align:middle; font-size:13px; }
    .movement-table tr:last-child td { border-bottom:none; }
    .movement-table tbody tr:hover td { background:color-mix(in srgb, var(--accent) 3%, var(--surface)); }

    /* Type badge */
    .type-in  { display:inline-flex; align-items:center; gap:5px; padding:3px 10px; border-radius:6px; font-size:12px; font-weight:700; background:var(--green-dim); color:var(--green); }
    .type-out { display:inline-flex; align-items:center; gap:5px; padding:3px 10px; border-radius:6px; font-size:12px; font-weight:700; background:var(--red-dim); color:var(--red); }
    .type-in::before  { content:"↑"; font-size:11px; }
    .type-out::before { content:"↓"; font-size:11px; }

    .qty-in  { font-weight:800; color:var(--green); }
    .qty-out { font-weight:800; color:var(--red); }
    .source-chip { font-size:11.5px; color:var(--text-3); background:var(--surface-2); padding:2px 8px; border-radius:5px; }
    .ref-link { font-size:11.5px; color:var(--accent); font-weight:600; text-decoration:none; }
    .ref-link:hover { text-decoration:underline; }
    .ref-none { font-size:11.5px; color:var(--text-4); }
    .date-cell { font-size:12px; color:var(--text-3); white-space:nowrap; }

    /* ── Pagination ───────────────────────────────── */
    .pagination-wrap { padding:14px 20px; display:flex; align-items:center; justify-content:space-between; gap:12px; border-top:1px solid var(--border); flex-wrap:wrap; }
    .pagination-info { font-size:12px; color:var(--text-3); }
    .pagination { display:flex; gap:4px; align-items:center; }
    .page-link {
        display:inline-flex; align-items:center; justify-content:center;
        width:30px; height:30px; border-radius:7px; border:1px solid var(--border);
        font-size:12px; font-weight:600; color:var(--text-2); background:var(--panel);
        text-decoration:none; transition:all 0.15s;
    }
    .page-link:hover { border-color:var(--accent); color:var(--accent); }
    .page-link.active { background:var(--accent); border-color:var(--accent); color:#fff; }
    .page-link.disabled { opacity:0.35; pointer-events:none; }

    /* ── Empty State ─────────────────────────────── */
    .empty-state { padding:60px 20px; text-align:center; display:flex; flex-direction:column; align-items:center; }
    .empty-icon { width:72px; height:72px; margin-bottom:16px; border-radius:20px; background:var(--surface-2); display:flex; align-items:center; justify-content:center; }
    .empty-icon svg { width:32px; height:32px; color:var(--text-4); }
    .empty-title { font-size:16px; font-weight:700; color:var(--text-1); margin-bottom:5px; }
    .empty-desc { font-size:13px; color:var(--text-3); }

    /* ── Stock Modal (reused from index) ─────────── */
    .modal-overlay { position:fixed; inset:0; background:rgba(15,23,42,0.45); backdrop-filter:blur(4px); z-index:200; display:flex; align-items:center; justify-content:center; opacity:0; visibility:hidden; transition:all 0.2s; }
    .modal-overlay.open { opacity:1; visibility:visible; }
    .modal-box { background:var(--panel); border-radius:16px; padding:28px; width:420px; max-width:90vw; box-shadow:var(--shadow-lg); transform:scale(0.95) translateY(10px); transition:transform 0.2s; }
    .modal-overlay.open .modal-box { transform:scale(1) translateY(0); }
    .modal-icon { width:52px; height:52px; border-radius:14px; display:flex; align-items:center; justify-content:center; margin-bottom:16px; }
    .modal-icon-green { background:var(--green-dim); }
    .modal-icon-green svg { color:var(--green); }
    .modal-icon-red { background:var(--red-dim); }
    .modal-icon-red svg { color:var(--red); }
    .modal-title { font-size:16px; font-weight:800; color:var(--text-1); margin-bottom:6px; }
    .modal-desc { font-size:13px; color:var(--text-2); margin-bottom:18px; }
    .modal-actions { display:flex; gap:10px; justify-content:flex-end; }
    .btn-cancel { padding:9px 18px; border:1px solid var(--border); border-radius:8px; font-family:var(--font); font-size:13px; font-weight:600; background:var(--surface); color:var(--text-2); cursor:pointer; transition:all 0.15s; }
    .btn-cancel:hover { border-color:var(--border-2); color:var(--text-1); }
    .btn-success { padding:9px 18px; border:none; border-radius:8px; font-family:var(--font); font-size:13px; font-weight:600; background:var(--green); color:#fff; cursor:pointer; transition:all 0.15s; box-shadow:0 2px 8px rgba(22,163,74,0.25); }
    .btn-success:hover { background:#15803d; transform:translateY(-1px); }
    .btn-danger-modal { padding:9px 18px; border:none; border-radius:8px; font-family:var(--font); font-size:13px; font-weight:600; background:var(--red); color:#fff; cursor:pointer; transition:all 0.15s; box-shadow:0 2px 8px rgba(220,38,38,0.25); }
    .btn-danger-modal:hover { background:#b91c1c; transform:translateY(-1px); }
    .stock-modal-info { background:var(--surface); border:1px solid var(--border); border-radius:10px; padding:12px 16px; margin-bottom:18px; display:flex; align-items:center; justify-content:space-between; gap:12px; }
    .stock-modal-info-label { font-size:11.5px; color:var(--text-3); font-weight:600; text-transform:uppercase; letter-spacing:0.05em; }
    .stock-modal-info-value { font-size:22px; font-weight:800; color:var(--text-1); letter-spacing:-0.04em; }
    .stock-modal-field { margin-bottom:14px; }
    .stock-modal-label { font-size:11.5px; font-weight:700; color:var(--text-3); text-transform:uppercase; letter-spacing:0.05em; margin-bottom:7px; display:block; }
    .stock-modal-label span { color:var(--red); }
    .stock-modal-input { width:100%; padding:10px 14px; border:1px solid var(--border); border-radius:9px; font-family:var(--font); font-size:14px; color:var(--text-1); background:var(--surface); outline:none; transition:border-color 0.15s, box-shadow 0.15s; box-sizing:border-box; }
    .stock-modal-input:focus { border-color:var(--accent); box-shadow:0 0 0 3px var(--accent-glow); }
    .stock-modal-input.is-invalid { border-color:var(--red); }
    .stock-field-error { font-size:11.5px; color:var(--red); font-weight:600; margin-top:5px; display:none; }
@endsection

@section('content')

    {{-- Breadcrumb --}}
    <div class="breadcrumb">
        <a href="{{ route('products.index') }}">Produk</a>
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:12px;height:12px;"><polyline points="9 18 15 12 9 6"/></svg>
        <span>Riwayat Stok</span>
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:12px;height:12px;"><polyline points="9 18 15 12 9 6"/></svg>
        <span style="color:var(--text-1);font-weight:600;">{{ $product->name }}</span>
    </div>

    {{-- Page Header --}}
    <div class="page-header">
        <div class="page-header-left">
            <h1>
                <span class="page-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/>
                    </svg>
                </span>
                Riwayat Stok
            </h1>
        </div>
        <div class="stock-actions">
            @if($product->is_active && $product->category?->is_active && $product->store?->is_active)
                <button type="button" class="btn-add" onclick="openStockModal('add')">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/>
                    </svg>
                    Tambah Stok
                </button>
                <button type="button"
                    class="btn-deduct {{ $product->stock === 0 ? 'disabled' : '' }}"
                    onclick="openStockModal('deduct')"
                    {{ $product->stock === 0 ? 'disabled' : '' }}
                    title="{{ $product->stock === 0 ? 'Stok sudah habis, tidak bisa dikurangi' : 'Kurangi Stok' }}">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="5" y1="12" x2="19" y2="12"/>
                    </svg>
                    Kurangi Stok
                </button>
            @else
                <span style="font-size:12px;color:var(--amber);background:var(--amber-dim);padding:8px 14px;border-radius:8px;font-weight:600;">
                    ⚠ Manajemen stok dinonaktifkan (produk/kategori/toko nonaktif)
                </span>
            @endif
            <a href="{{ route('products.index') }}" class="btn-back">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="15 18 9 12 15 6"/>
                </svg>
                Kembali
            </a>
        </div>
    </div>



    {{-- Product Info Card --}}
    <div class="product-info-card">
        <div class="product-thumb-lg">
            @if($product->image)
                <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}">
            @else
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="3" y="3" width="18" height="18" rx="2"/>
                    <circle cx="8.5" cy="8.5" r="1.5"/>
                    <polyline points="21 15 16 10 5 21"/>
                </svg>
            @endif
        </div>
        <div class="product-meta">
            <div class="product-meta-name">{{ $product->name }}</div>
            <div class="product-meta-sub">
                @if($product->category)
                    <span class="product-meta-pill pill-cat">{{ $product->category->name }}</span>
                @endif
                @if($product->store)
                    <span class="product-meta-pill pill-store">{{ $product->store->name }}</span>
                @endif
                @if(!$product->is_active)
                    <span class="inactive-chip">Produk Nonaktif</span>
                @endif
                @if($product->category && !$product->category->is_active)
                    <span class="inactive-chip">Kategori Nonaktif</span>
                @endif
                @if($product->store && !$product->store->is_active)
                    <span class="inactive-chip">Toko Nonaktif</span>
                @endif
            </div>
        </div>
    </div>

    {{-- Stats Bar --}}
    <div class="stats-bar">
        <div class="stat-card">
            <div class="stat-icon blue">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M22 12h-4l-3 9L9 3l-3 9H2"/>
                </svg>
            </div>
            <div>
                <div class="stat-value">{{ number_format($stats['current']) }}</div>
                <div class="stat-label">Stok Saat Ini</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon green">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="12" y1="19" x2="12" y2="5"/><polyline points="5 12 12 5 19 12"/>
                </svg>
            </div>
            <div>
                <div class="stat-value">{{ number_format($stats['total_in']) }}</div>
                <div class="stat-label">Total Stok Masuk</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon red">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="12" y1="5" x2="12" y2="19"/><polyline points="19 12 12 19 5 12"/>
                </svg>
            </div>
            <div>
                <div class="stat-value">{{ number_format($stats['total_out']) }}</div>
                <div class="stat-label">Total Stok Keluar</div>
            </div>
        </div>
    </div>

    {{-- Movement History Table --}}
    <div class="table-card">
        <div class="table-header">
            <span class="table-title">Riwayat Pergerakan Stok</span>
            <span class="table-count">{{ $movements->total() }} transaksi</span>
        </div>

        @if($movements->isEmpty())
            <div class="empty-state">
                <div class="empty-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M22 12h-4l-3 9L9 3l-3 9H2"/>
                    </svg>
                </div>
                <div class="empty-title">Belum Ada Riwayat</div>
                <div class="empty-desc">Belum ada pergerakan stok untuk produk ini.</div>
            </div>
        @else
            <div class="table-responsive">
                <table class="movement-table">
                    <thead>
                        <tr>
                            <th style="width:50px;text-align:center;">No</th>
                            <th>Tipe</th>
                            <th>Jumlah</th>
                            <th>Sumber</th>
                            <th>Referensi</th>
                            <th>Waktu</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($movements as $i => $movement)
                            <tr>
                                <td style="text-align:center;color:var(--text-4);font-weight:600;font-size:12px;">
                                    {{ $movements->firstItem() + $i }}
                                </td>
                                <td>
                                    @if($movement->type === 'in')
                                        <span class="type-in">Masuk</span>
                                    @else
                                        <span class="type-out">Keluar</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="{{ $movement->type === 'in' ? 'qty-in' : 'qty-out' }}">
                                        {{ $movement->type === 'in' ? '+' : '-' }}{{ number_format($movement->quantity) }}
                                    </span>
                                </td>
                                <td>
                                    <span class="source-chip">{{ $movement->source_label }}</span>
                                </td>
                                <td>
                                    @if($movement->reference_id)
                                        <a href="{{ route('orders.show', $movement->reference_id) }}" class="ref-link">
                                            #{{ $movement->reference_id }}
                                        </a>
                                    @else
                                        <span class="ref-none">—</span>
                                    @endif
                                </td>
                                <td class="date-cell">
                                    {{ $movement->created_at->setTimezone('Asia/Jakarta')->format('d M Y, H:i') }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            @if($movements->hasPages())
                <div class="pagination-wrap">
                    <span class="pagination-info">
                        Menampilkan {{ $movements->firstItem() }}–{{ $movements->lastItem() }}
                        dari {{ $movements->total() }} transaksi
                    </span>
                    <div class="pagination">
                        @if($movements->onFirstPage())
                            <span class="page-link disabled">‹</span>
                        @else
                            <a class="page-link" href="{{ $movements->previousPageUrl() }}">‹</a>
                        @endif

                        @foreach($movements->getUrlRange(1, $movements->lastPage()) as $page => $url)
                            <a class="page-link {{ $movements->currentPage() === $page ? 'active' : '' }}"
                               href="{{ $url }}">{{ $page }}</a>
                        @endforeach

                        @if($movements->hasMorePages())
                            <a class="page-link" href="{{ $movements->nextPageUrl() }}">›</a>
                        @else
                            <span class="page-link disabled">›</span>
                        @endif
                    </div>
                </div>
            @endif
        @endif
    </div>

    {{-- Stock Adjustment Modal --}}
    <div class="modal-overlay" id="stockModal">
        <div class="modal-box">
            <div class="modal-icon" id="stockModalIcon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="12" y1="5" x2="12" y2="19" id="stockModalIconLine2"/>
                    <line x1="5" y1="12" x2="19" y2="12"/>
                </svg>
            </div>
            <div class="modal-title" id="stockModalTitle">Tambah Stok</div>
            <div class="modal-desc" id="stockModalDesc">{{ $product->name }}</div>

            <div class="stock-modal-info">
                <div>
                    <div class="stock-modal-info-label">Stok Saat Ini</div>
                    <div class="stock-modal-info-value">{{ number_format($product->stock) }}</div>
                </div>
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" style="width:32px;height:32px;color:var(--text-4);">
                    <path d="M22 12h-4l-3 9L9 3l-3 9H2"/>
                </svg>
            </div>

            <form id="stockForm" method="POST" novalidate>
                @csrf
                <div class="stock-modal-field">
                    <label class="stock-modal-label" for="stockQtyInput">Jumlah <span>*</span></label>
                    <input type="number" id="stockQtyInput" name="qty"
                           class="stock-modal-input" placeholder="Masukkan jumlah unit..."
                           min="1" max="999999" required>
                </div>
                <div class="stock-modal-field">
                    <label class="stock-modal-label" for="stockNoteInput">Catatan <span>*</span></label>
                    <input type="text" id="stockNoteInput" name="note"
                           class="stock-modal-input" placeholder="Alasan penyesuaian stok..."
                           maxlength="500" required>
                </div>
                <div class="modal-actions">
                    <button type="button" class="btn-cancel" onclick="closeStockModal()">Batal</button>
                    <button type="submit" class="btn-success" id="stockModalSubmit">Tambah Stok</button>
                </div>
            </form>
        </div>
    </div>

@endsection

@push('scripts')
<script>


    const _currentStock = {{ $product->stock }};
    const _productSlug  = '{{ $product->slug }}';
    let   _stockType    = 'add';

    function openStockModal(type) {
        _stockType = type;
        const isAdd = type === 'add';

        document.getElementById('stockModalIcon').className = 'modal-icon ' + (isAdd ? 'modal-icon-green' : 'modal-icon-red');
        document.getElementById('stockModalIconLine2').style.display = isAdd ? '' : 'none';
        document.getElementById('stockModalTitle').textContent = isAdd ? 'Tambah Stok' : 'Kurangi Stok';

        const btn = document.getElementById('stockModalSubmit');
        btn.textContent = isAdd ? 'Tambah Stok' : 'Kurangi Stok';
        btn.className   = isAdd ? 'btn-success' : 'btn-danger-modal';

        document.getElementById('stockForm').action =
            `/products/${_productSlug}/stock/${isAdd ? 'add' : 'deduct'}`;

        document.getElementById('stockQtyInput').value = '';
        document.getElementById('stockNoteInput').value = '';
        document.getElementById('stockQtyInput').classList.remove('is-invalid');

        document.getElementById('stockModal').classList.add('open');
        setTimeout(() => document.getElementById('stockQtyInput').focus(), 200);
    }

    function closeStockModal() {
        document.getElementById('stockModal').classList.remove('open');
    }

    document.getElementById('stockModal').addEventListener('click', e => {
        if (e.target === document.getElementById('stockModal')) closeStockModal();
    });
</script>
@endpush
