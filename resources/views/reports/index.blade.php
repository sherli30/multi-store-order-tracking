@extends('layouts.app')

@section('title', 'Laporan Penjualan')

@section('styles')
    /* ── Layout & Typography ── */
    .main-wrap, .page { min-width: 0; } /* Fix flex blowout horizontal scroll (sama seperti orders/index.blade.php) */
    .report-wrap *, .report-wrap *::before, .report-wrap *::after { box-sizing: border-box; }
    .report-wrap { display: flex; flex-direction: column; gap: 24px; padding-bottom: 32px; min-width: 0; max-width: 100%; overflow-x: hidden; }
    .page-header { display: flex; align-items: flex-start; justify-content: space-between; flex-wrap: wrap; gap: 16px; }
    .page-header-left h1 { font-size: 22px; font-weight: 800; letter-spacing: -0.04em; color: var(--text-1); margin-bottom: 5px; display: flex; align-items: center; gap: 10px; }
    .page-icon { width: 36px; height: 36px; background: linear-gradient(135deg, var(--accent), color-mix(in srgb, var(--accent) 70%, #7c3aed)); border-radius: 10px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
    .page-icon svg { width: 18px; height: 18px; color: #fff; }
    .page-header-left p { font-size: 13px; color: var(--text-3); margin-left: 46px; }

    /* ── Sections & Cards ── */
    .report-section { background: var(--panel); border: 1px solid var(--border); border-radius: 14px; padding: 20px 24px; box-shadow: var(--shadow-sm); min-width: 0; max-width: 100%; }
    .table-card { background: var(--panel); border: 1px solid var(--border); border-radius: 14px; overflow: hidden; box-shadow: var(--shadow-sm); display: flex; flex-direction: column; min-width: 0; max-width: 100%; animation: rise 0.35s ease both; }
    .table-card-head { padding: 20px 24px; border-bottom: 1px solid var(--border); background: var(--panel); }
    .table-card-head .section-title { margin-bottom: 0; }
    .section-title { font-size: 15px; font-weight: 800; color: var(--text-1); margin-bottom: 16px; display: flex; align-items: center; gap: 8px; }
    .section-title svg { width: 18px; height: 18px; color: var(--accent); }

    /* ── Filter Form (disamakan dengan orders/index.blade.php) ── */
    .filter-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 16px; margin-bottom: 16px; align-items: flex-end; }
    .form-group { display: flex; flex-direction: column; min-width: 0; }
    .form-label { font-size: 11px; font-weight: 700; color: var(--text-3); margin-bottom: 6px; text-transform: uppercase; letter-spacing: 0.05em; }
    .form-input { width: 100%; padding: 9px 12px; border: 1px solid var(--border); border-radius: 8px; font-family: var(--font); font-size: 13px; color: var(--text-1); background: var(--surface); outline: none; transition: all 0.15s; box-sizing: border-box; }
    .form-input:focus { border-color: var(--accent); box-shadow: 0 0 0 3px var(--accent-glow); }
    .filter-actions { display: flex; justify-content: flex-end; align-items: center; gap: 10px; padding-top: 16px; border-top: 1px solid var(--border); min-height: 45px; }
    @media(max-width:600px) {
        .filter-actions { flex-direction: column-reverse; align-items: stretch; gap: 10px; min-height: auto; }
        .filter-actions .btn { justify-content: center; width: 100%; padding: 11px; }
    }
    .btn { display: inline-flex; align-items: center; gap: 6px; padding: 9px 16px; border-radius: 8px; font-family: var(--font); font-weight: 700; font-size: 13px; cursor: pointer; transition: all 0.15s; text-decoration: none; border: none; box-sizing: border-box; white-space: nowrap; }
    .btn-primary { background: var(--accent); color: #fff; box-shadow: 0 2px 8px color-mix(in srgb, var(--accent) 30%, transparent); }
    .btn-primary:hover { transform: translateY(-1px); opacity: 0.9; }
    .btn-outline { background: var(--surface); color: var(--text-2); border: 1px solid var(--border); }
    .btn-outline:hover { background: var(--panel); border-color: var(--border-2); color: var(--text-1); }
    .btn-outline-reset { background: var(--red-dim); color: var(--red); border: 1px solid rgba(220, 38, 38, 0.2); }
    .btn-outline-reset:hover { border-color: rgba(220, 38, 38, 0.4); background: color-mix(in srgb, var(--red-dim) 80%, var(--red)); color: var(--red); }

    /* ── Executive Summary ── */
    .exec-grid { display: grid; grid-template-columns: repeat(5, 1fr); gap: 14px; }
    @media(max-width:1200px) { .exec-grid { grid-template-columns: repeat(3, 1fr); } }
    @media(max-width:768px) { .exec-grid { grid-template-columns: repeat(2, 1fr); } }
    @media(max-width:480px) { .exec-grid { grid-template-columns: 1fr; } }
    .exec-card { background: var(--surface); border: 1px solid var(--border); border-radius: 10px; padding: 16px; }
    .exec-label { font-size: 11px; font-weight: 700; color: var(--text-4); text-transform: uppercase; margin-bottom: 8px; }
    .exec-value { font-size: 20px; font-weight: 800; color: var(--text-1); letter-spacing: -0.02em; }
    .exec-value.accent { color: var(--accent); }

    /* ── Store Grid ── */
    .store-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 14px; }
    @media(max-width:1200px) { .store-grid { grid-template-columns: repeat(3, 1fr); } }
    @media(max-width:900px) { .store-grid { grid-template-columns: repeat(2, 1fr); } }
    @media(max-width:600px) { .store-grid { grid-template-columns: 1fr; } }

    /* ── Tables (disamakan dengan orders/index.blade.php .product-table) ── */
    .table-responsive { overflow-x: auto; width: 100%; -webkit-overflow-scrolling: touch; }
    .data-table { width: 100%; border-collapse: collapse; min-width: 800px; }
    .data-table th { background: var(--surface); padding: 12px 16px; text-align: left; font-size: 10.5px; font-weight: 700; color: var(--text-3); text-transform: uppercase; letter-spacing: 0.08em; border-bottom: 1px solid var(--border); white-space: nowrap; }
    .data-table td { padding: 13px 16px; border-bottom: 1px solid var(--border); font-size: 13px; color: var(--text-2); vertical-align: middle; }
    .data-table tbody tr:last-child td { border-bottom: none; }
    .data-table tbody tr { transition: background 0.12s; }
    .data-table tbody tr:hover td { background: color-mix(in srgb, var(--accent) 3%, var(--surface)); }
    .data-table tfoot td { background: var(--surface); font-weight: 800; border-top: none; border-bottom: none; }
    .data-table .mono { font-family: var(--mono); font-weight: 700; }
    .data-table .numeric { text-align: right; }
    @keyframes rise { from { opacity: 0; transform: translateY(12px); } to { opacity: 1; transform: translateY(0); } }

    /* ── Pagination ── */
    .pagination-wrap { width: 100%; max-width: 100%; overflow-x: auto; }
    .pagination-wrap nav > div:first-child { display: none; }
    .pagination-wrap nav > div:last-child { display: flex !important; justify-content: center !important; width: 100%; }
    .pagination-wrap nav { width: 100%; }
    .pagination-wrap span[aria-current="page"] span,
    .pagination-wrap a, .pagination-wrap span:not([aria-current]) {
        font-family: var(--font) !important;
        font-size: 12.5px !important;
    }
    .pagination-wrap ul, .pagination-wrap nav > div:last-child > span {
        display: flex !important;
        flex-wrap: wrap;
        justify-content: center;
        gap: 4px;
        list-style: none;
        padding: 0;
        margin: 0;
    }
    .pagination-wrap a, .pagination-wrap span[aria-current="page"] span {
        display: inline-flex !important;
        align-items: center;
        justify-content: center;
        min-width: 32px;
        height: 32px;
        padding: 0 8px;
        border-radius: 8px;
        border: 1px solid var(--border) !important;
        background: var(--surface) !important;
        color: var(--text-2) !important;
        text-decoration: none !important;
        transition: all 0.15s;
    }
    .pagination-wrap a:hover { border-color: var(--accent) !important; color: var(--accent) !important; }
    .pagination-wrap span[aria-current="page"] span {
        background: var(--accent) !important;
        border-color: var(--accent) !important;
        color: #fff !important;
        font-weight: 700;
    }
    .pagination-wrap span[disabled], .pagination-wrap span.opacity-50 {
        border: 1px solid var(--border) !important;
        background: var(--surface) !important;
        color: var(--text-4) !important;
        border-radius: 8px;
        min-width: 32px;
        height: 32px;
        display: inline-flex !important;
        align-items: center;
        justify-content: center;
    }

    /* ── Badges ── */
    .badge { display: inline-flex; align-items: center; padding: 4px 10px; border-radius: 20px; font-size: 10.5px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.04em; }
    .badge-success { background: var(--green-dim); color: var(--green); }
    .badge-warning { background: var(--amber-dim); color: var(--amber); }
    .badge-danger { background: var(--red-dim); color: var(--red); }
    .badge-neutral { background: var(--surface-2); color: var(--text-3); }

    /* ── Export Preview ── */
    .export-preview { background: var(--surface-2); border: 1px dashed var(--border-2); border-radius: 10px; padding: 20px; margin-top: 16px; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 16px; box-sizing: border-box; }
    .export-info { font-size: 13px; color: var(--text-2); line-height: 1.6; }
    .export-info strong { color: var(--text-1); }
@endsection

@section('content')
<div class="report-wrap">

    <div class="page-header">
        <div class="page-header-left">
            <h1>
                <span class="page-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                        <polyline points="14 2 14 8 20 8"></polyline>
                        <line x1="16" y1="13" x2="8" y2="13"></line>
                        <line x1="16" y1="17" x2="8" y2="17"></line>
                        <polyline points="10 9 9 9 8 9"></polyline>
                    </svg>
                </span>
                Laporan Penjualan Eksekutif
            </h1>
            <p>Sistem pelaporan transaksi, performa toko, dan histori penjualan terpadu.</p>
        </div>
    </div>

    {{-- SECTION 1: Advanced Filters --}}
    <div class="report-section">
        <div class="section-title">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"></polygon>
            </svg>
            Filter Laporan
        </div>
        <form method="GET" action="{{ route('reports.index') }}">
            <div class="filter-grid">
                <div class="form-group">
                    <label class="form-label">Periode Mulai</label>
                    <input type="date" name="start_date" class="form-input" value="{{ $startDate }}">
                </div>
                <div class="form-group">
                    <label class="form-label">Periode Akhir</label>
                    <input type="date" name="end_date" class="form-input" value="{{ $endDate }}">
                </div>
                <div class="form-group">
                    <label class="form-label">Toko</label>
                    <select name="store_id" class="form-input">
                        <option value="">Semua Toko</option>
                        @foreach($storesList as $store)
                            <option value="{{ $store->id }}" {{ $storeId == $store->id ? 'selected' : '' }}>{{ $store->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Customer</label>
                    <select name="customer_id" class="form-input">
                        <option value="">Semua Customer</option>
                        @foreach($customers as $customer)
                            <option value="{{ $customer->id }}" {{ $customerId == $customer->id ? 'selected' : '' }}>{{ $customer->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Status Pesanan</label>
                    <select name="order_status" class="form-input">
                        <option value="">Semua Status</option>
                        <option value="pending" {{ $orderStatus == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="processed" {{ $orderStatus == 'processed' ? 'selected' : '' }}>Diproses</option>
                        <option value="shipped" {{ $orderStatus == 'shipped' ? 'selected' : '' }}>Dikirim</option>
                        <option value="completed" {{ $orderStatus == 'completed' ? 'selected' : '' }}>Selesai</option>
                        <option value="cancelled" {{ $orderStatus == 'cancelled' ? 'selected' : '' }}>Dibatalkan</option>
                        <option value="refunded" {{ $orderStatus == 'refunded' ? 'selected' : '' }}>Pengembalian</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Status Pembayaran</label>
                    <select name="payment_status" class="form-input">
                        <option value="">Semua Status</option>
                        <option value="pending" {{ $paymentStatus == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="settlement" {{ $paymentStatus == 'settlement' ? 'selected' : '' }}>Lunas (Settlement)</option>
                        <option value="capture" {{ $paymentStatus == 'capture' ? 'selected' : '' }}>Lunas (Capture)</option>
                        <option value="failed" {{ $paymentStatus == 'failed' ? 'selected' : '' }}>Gagal</option>
                        <option value="expire" {{ $paymentStatus == 'expire' ? 'selected' : '' }}>Kedaluwarsa</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">No. Invoice</label>
                    <input type="text" name="invoice_number" class="form-input" placeholder="INV-..." value="{{ $invoiceNumber }}">
                </div>
                <div class="form-group">
                    <label class="form-label">Produk</label>
                    <select name="product_id" class="form-input">
                        <option value="">Semua Produk</option>
                        @foreach($products as $product)
                            <option value="{{ $product->id }}" {{ $productId == $product->id ? 'selected' : '' }}>{{ $product->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="filter-actions">
                <a href="{{ route('reports.index') }}" class="btn btn-outline-reset" style="{{ ($storeId || $customerId || $orderStatus || $paymentStatus || $invoiceNumber || $productId) ? '' : 'display:none;' }}">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                    Reset Filter
                </a>
                <button type="submit" class="btn btn-primary">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                    Terapkan Filter
                </button>
            </div>
        </form>
    </div>

    {{-- SECTION 2: Executive Summary --}}
    <div class="report-section">
        <div class="section-title">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <polyline points="22 12 18 12 15 21 9 3 6 12 2 12"></polyline>
            </svg>
            Ringkasan Eksekutif
        </div>
        <div class="exec-grid">
            <div class="exec-card">
                <div class="exec-label">Total Transaksi</div>
                <div class="exec-value">{{ number_format($totalTransactions) }}</div>
            </div>
            <div class="exec-card">
                <div class="exec-label">Total Pendapatan Lunas</div>
                <div class="exec-value accent">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</div>
            </div>
            <div class="exec-card">
                <div class="exec-label">Produk Terjual</div>
                <div class="exec-value">{{ number_format($totalProductsSold) }}</div>
            </div>
            <div class="exec-card">
                <div class="exec-label">Customer Unik</div>
                <div class="exec-value">{{ number_format($totalCustomers) }}</div>
            </div>
            <div class="exec-card">
                <div class="exec-label">Rata-rata Nilai Pesanan</div>
                <div class="exec-value">Rp {{ number_format($averageOrderValue, 0, ',', '.') }}</div>
            </div>
        </div>
    </div>

    {{-- SECTION 3: Main Sales Transaction Report --}}
    <div class="table-card">
        <div class="table-card-head">
            <div class="section-title">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><line x1="3" y1="9" x2="21" y2="9"></line><line x1="9" y1="21" x2="9" y2="9"></line>
                </svg>
                Laporan Transaksi Penjualan
            </div>
        </div>
        <div class="table-responsive">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>No. Invoice</th>
                        <th>Tgl. Pesanan</th>
                        <th>Customer</th>
                        <th>Toko</th>
                        <th>Produk</th>
                        <th class="numeric">Qty</th>
                        <th class="numeric">Harga Satuan</th>
                        <th class="numeric">Subtotal</th>
                        <th class="numeric">Ongkir</th>
                        <th>Pembayaran</th>
                        <th>Pesanan</th>
                        <th class="numeric">Total Pesanan</th>
                    </tr>
                </thead>
                <tbody>
                    @php $orderRowNumber = ($orders->currentPage() - 1) * $orders->perPage(); @endphp
                    @forelse($orders as $order)
                        @php $orderRowNumber++; @endphp
                        @foreach($order->orderItems as $idx => $item)
                        <tr>
                            @if($idx === 0)
                                <td class="numeric" rowspan="{{ $order->orderItems->count() }}" style="color:var(--text-3);">{{ $orderRowNumber }}</td>
                            @endif
                            <td class="mono" style="color:var(--accent);">{{ $order->invoice->invoice_number ?? $order->invoice->midtrans_order_id ?? '-' }}</td>
                            <td>{{ $order->created_at->format('d M Y H:i') }}</td>
                            <td style="font-weight:600; color:var(--text-1);">{{ $order->customer_name }}</td>
                            <td>{{ $order->store->name ?? '-' }}</td>
                            <td style="font-weight:600;">{{ $item->product->name ?? 'Produk Dihapus' }}</td>
                            <td class="numeric">{{ $item->quantity }}</td>
                            <td class="numeric">Rp {{ number_format($item->price, 0, ',', '.') }}</td>
                            <td class="numeric">Rp {{ number_format($item->quantity * $item->price, 0, ',', '.') }}</td>
                            @if($idx === 0)
                                <td class="numeric" rowspan="{{ $order->orderItems->count() }}">Rp {{ number_format($order->shipping_cost, 0, ',', '.') }}</td>
                                <td rowspan="{{ $order->orderItems->count() }}">
                                    @php
                                        $pStatus = $order->payment_status;
                                        $pBadge = in_array($pStatus, ['settlement','capture','paid']) ? 'badge-success' : ($pStatus == 'pending' ? 'badge-warning' : 'badge-danger');
                                    @endphp
                                    <span class="badge {{ $pBadge }}">{{ $pStatus }}</span>
                                </td>
                                <td rowspan="{{ $order->orderItems->count() }}">
                                    @php
                                        $oStatus = $order->status;
                                        $oBadge = $oStatus == 'completed' ? 'badge-success' : (in_array($oStatus, ['cancelled','refunded']) ? 'badge-danger' : 'badge-neutral');
                                    @endphp
                                    <span class="badge {{ $oBadge }}">{{ $oStatus }}</span>
                                </td>
                                <td class="numeric" rowspan="{{ $order->orderItems->count() }}" style="font-weight:700;">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</td>
                            @endif
                        </tr>
                        @endforeach
                    @empty
                        <tr>
                            <td colspan="13" style="text-align:center; padding: 40px 0;">Tidak ada data transaksi yang sesuai filter.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div style="padding: 16px 24px; background: var(--surface); border-top: 1px solid var(--border);">
            <div class="pagination-wrap">
                {{ $orders->appends(request()->except('page'))->links() }}
            </div>
        </div>
    </div>

    {{-- SECTION 4: Product Sales Detail Report --}}
    <div class="table-card">
        <div class="table-card-head">
            <div class="section-title">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path><polyline points="3.27 6.96 12 12.01 20.73 6.96"></polyline><line x1="12" y1="22.08" x2="12" y2="12"></line>
                </svg>
                Laporan Detail Penjualan Produk
            </div>
        </div>
        <div class="table-responsive">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>No. Invoice</th>
                        <th>Toko</th>
                        <th>Nama Produk</th>
                        <th>SKU</th>
                        <th class="numeric">Kuantitas</th>
                        <th class="numeric">Harga Satuan</th>
                        <th class="numeric">Total Pendapatan</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($orderItems as $item)
                        <tr>
                            <td class="mono">{{ $item->order->invoice->invoice_number ?? $item->order->invoice->midtrans_order_id ?? '-' }}</td>
                            <td>{{ $item->order->store->name ?? '-' }}</td>
                            <td style="font-weight:600; color:var(--text-1);">{{ $item->product->name ?? 'Produk Dihapus' }}</td>
                            <td class="mono">{{ $item->product->sku ?? '-' }}</td>
                            <td class="numeric" style="font-weight:700;">{{ $item->quantity }}</td>
                            <td class="numeric">Rp {{ number_format($item->price, 0, ',', '.') }}</td>
                            <td class="numeric" style="font-weight:700; color:var(--accent);">Rp {{ number_format($item->quantity * $item->price, 0, ',', '.') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" style="text-align:center; padding: 40px 0;">Tidak ada data produk yang sesuai filter.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div style="padding: 16px 24px; background: var(--surface); border-top: 1px solid var(--border);">
            <div class="pagination-wrap">
                {{ $orderItems->appends(request()->except('product_page'))->links() }}
            </div>
        </div>
    </div>

    {{-- SECTION 5: Store Performance --}}
    <div class="report-section">
        <div class="section-title">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path><polyline points="9 22 9 12 15 12 15 22"></polyline>
            </svg>
            Performa Toko
        </div>
        <div class="store-grid">
            @foreach($consolidatedReport as $row)
            <div class="exec-card" style="display: flex; flex-direction: column; justify-content: space-between;">
                <div>
                    <h3 style="font-size: 15px; margin: 0 0 10px 0; color: var(--text-1);">{{ $row->store_name }}</h3>
                    <div style="font-size: 12px; margin-bottom: 5px; display:flex; justify-content:space-between;">
                        <span style="color:var(--text-3);">Total Orders:</span> <strong style="color:var(--text-1);">{{ number_format($row->total_orders) }}</strong>
                    </div>
                    <div style="font-size: 12px; margin-bottom: 5px; display:flex; justify-content:space-between;">
                        <span style="color:var(--text-3);">Total Revenue:</span> <strong style="color:var(--accent);">Rp {{ number_format($row->total_revenue, 0, ',', '.') }}</strong>
                    </div>
                    <div style="font-size: 12px; margin-bottom: 5px; display:flex; justify-content:space-between;">
                        <span style="color:var(--text-3);">Products Sold:</span> <strong style="color:var(--text-1);">{{ number_format($row->products_sold) }}</strong>
                    </div>
                    <div style="font-size: 12px; margin-bottom: 5px; display:flex; justify-content:space-between;">
                        <span style="color:var(--text-3);">Unique Customers:</span> <strong style="color:var(--text-1);">{{ number_format($row->unique_customers) }}</strong>
                    </div>
                    <div style="font-size: 12px; margin-bottom: 5px; display:flex; justify-content:space-between;">
                        <span style="color:var(--text-3);">Avg Order Value:</span> <strong style="color:var(--text-1);">Rp {{ number_format($row->total_orders > 0 ? $row->total_revenue / $row->total_orders : 0, 0, ',', '.') }}</strong>
                    </div>
                    <div style="font-size: 12px; margin-bottom: 15px; display:flex; justify-content:space-between;">
                        <span style="color:var(--text-3);">Success Rate:</span> <strong style="color:var(--green);">{{ $row->success_rate }}%</strong>
                    </div>
                </div>
                <a href="{{ route('reports.export', array_merge(request()->all(), ['store_id' => $row->store_id])) }}" target="_blank" class="btn btn-outline" style="width: 100%; justify-content: center; text-align: center;">Download PDF</a>
            </div>
            @endforeach
        </div>
    </div>

    {{-- SECTION 6: Consolidated Multi-Store Report --}}
    <div class="table-card">
        <div class="table-card-head">
            <div class="section-title">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="22 12 18 12 15 21 9 3 6 12 2 12"></polyline>
                </svg>
                Laporan Konsolidasi Multi-Toko
            </div>
        </div>
        <div class="table-responsive">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Nama Toko</th>
                        <th class="numeric">Total Pesanan</th>
                        <th class="numeric">Selesai</th>
                        <th class="numeric">Batal/Retur</th>
                        <th class="numeric">Produk Terjual</th>
                        <th class="numeric">Customer Unik</th>
                        <th class="numeric">Avg Order Value</th>
                        <th class="numeric">Pendapatan Ongkir</th>
                        <th class="numeric">Net Revenue</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($consolidatedReport as $row)
                        <tr>
                            <td style="font-weight:700; color:var(--text-1);">{{ $row->store_name }}</td>
                            <td class="numeric">{{ number_format($row->total_orders) }}</td>
                            <td class="numeric" style="color:var(--green); font-weight:700;">{{ number_format($row->completed_orders) }}</td>
                            <td class="numeric" style="color:var(--red);">{{ number_format($row->cancelled_orders + $row->refunded_orders) }}</td>
                            <td class="numeric">{{ number_format($row->products_sold) }}</td>
                            <td class="numeric">{{ number_format($row->unique_customers) }}</td>
                            <td class="numeric">Rp {{ number_format($row->total_orders > 0 ? $row->total_revenue / $row->total_orders : 0, 0, ',', '.') }}</td>
                            <td class="numeric">Rp {{ number_format($row->shipping_revenue, 0, ',', '.') }}</td>
                            <td class="numeric" style="font-weight:800; color:var(--accent);">Rp {{ number_format($row->total_revenue, 0, ',', '.') }}</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <td>TOTAL KESELURUHAN</td>
                        <td class="numeric">{{ number_format($consolidatedReport->sum('total_orders')) }}</td>
                        <td class="numeric">{{ number_format($consolidatedReport->sum('completed_orders')) }}</td>
                        <td class="numeric">{{ number_format($consolidatedReport->sum('cancelled_orders') + $consolidatedReport->sum('refunded_orders')) }}</td>
                        <td class="numeric">{{ number_format($consolidatedReport->sum('products_sold')) }}</td>
                        <td class="numeric">{{ number_format($consolidatedReport->sum('unique_customers')) }}</td>
                        <td class="numeric">-</td>
                        <td class="numeric">Rp {{ number_format($consolidatedReport->sum('shipping_revenue'), 0, ',', '.') }}</td>
                        <td class="numeric" style="color:var(--accent);">Rp {{ number_format($consolidatedReport->sum('total_revenue'), 0, ',', '.') }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    {{-- SECTION 7: Report Preview Before Export --}}
    <div class="report-section" style="margin-bottom:0;">
        <div class="section-title">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline>
            </svg>
            Ekspor Laporan Penjualan Eksekutif
        </div>
        <p style="font-size:13px; color:var(--text-3); margin-bottom: 0;">Laporan yang diekspor akan mencakup semua data transaksi, detail produk, dan performa toko berdasarkan filter yang sedang aktif di atas.</p>

        <div class="export-preview">
            <div class="export-info">
                <div><strong>Periode Laporan:</strong> {{ \Carbon\Carbon::parse($startDate)->format('d M Y') }} s/d {{ \Carbon\Carbon::parse($endDate)->format('d M Y') }}</div>
                <div><strong>Ruang Lingkup:</strong> {{ $storeId ? $storesList->where('id', $storeId)->first()->name ?? 'Semua Toko' : 'Semua Toko Konsolidasi' }}</div>
                <div><strong>Total Transaksi Diekspor:</strong> {{ number_format($totalTransactions) }} Transaksi</div>
                <div><strong>Total Pendapatan Diekspor:</strong> Rp {{ number_format($totalRevenue, 0, ',', '.') }}</div>
            </div>

            <form method="GET" action="{{ route('reports.export') }}" target="_blank">
                <input type="hidden" name="start_date" value="{{ $startDate }}">
                <input type="hidden" name="end_date" value="{{ $endDate }}">
                <input type="hidden" name="store_id" value="{{ $storeId }}">
                <input type="hidden" name="customer_id" value="{{ $customerId }}">
                <input type="hidden" name="order_status" value="{{ $orderStatus }}">
                <input type="hidden" name="payment_status" value="{{ $paymentStatus }}">
                <input type="hidden" name="invoice_number" value="{{ $invoiceNumber }}">
                <input type="hidden" name="product_id" value="{{ $productId }}">

                <button type="submit" class="btn btn-primary" style="padding: 12px 24px;">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line>
                    </svg>
                    Generate & Cetak PDF Laporan
                </button>
            </form>
        </div>
    </div>

</div>
@endsection
