@extends('layouts.app')

@section('title', 'Detail Pesanan ' . $order->order_number)

@section('styles')

/* ── Breadcrumb ── */
.breadcrumb { display: flex; align-items: center; gap: 6px; font-size: 12.5px; color: var(--text-3); margin-bottom: 22px; }
.breadcrumb a { color: var(--text-3); text-decoration: none; font-weight: 500; transition: color 0.15s; }
.breadcrumb a:hover { color: var(--accent); }
.breadcrumb-sep { color: var(--text-4); font-size: 11px; }
.breadcrumb-current { color: var(--text-1); font-weight: 600; }

/* ── Page Header ── */
.page-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 24px;
    flex-wrap: wrap;
    gap: 14px;
}
.page-title h1 {
    font-size: 19px;
    font-weight: 800;
    color: var(--text-1);
    margin-bottom: 6px;
    display: flex;
    align-items: center;
    gap: 10px;
    letter-spacing: -0.02em;
}
.page-title p { font-size: 12.5px; color: var(--text-3); }

/* ── Status Badge ── */
.order-badge {
    font-size: 11.5px;
    font-weight: 700;
    padding: 4px 12px;
    border-radius: 20px;
    display: inline-flex;
    align-items: center;
    gap: 5px;
}
.order-badge::before {
    content: '';
    display: block;
    width: 6px; height: 6px;
    border-radius: 50%;
    background: currentColor;
}
.status-pending    { background: var(--amber-dim); color: var(--amber); border: 1px solid rgba(245,158,11,0.2); }
.status-processing { background: rgba(59,130,246,0.08); color: #3b82f6; border: 1px solid rgba(59,130,246,0.2); }
.status-shipping   { background: var(--accent-dim); color: var(--accent); border: 1px solid rgba(99,102,241,0.2); }
.status-completed  { background: var(--green-dim); color: var(--green); border: 1px solid rgba(22,163,74,0.2); }
.status-cancelled  { background: var(--red-dim); color: var(--red); border: 1px solid rgba(220,38,38,0.2); }

/* ── Grid ── */
.invoice-grid { display: grid; grid-template-columns: 1fr 320px; gap: 20px; align-items: start; }
@media (max-width: 900px) { .invoice-grid { grid-template-columns: 1fr; } }

/* ── Panel ── */
.panel {
    background: var(--panel);
    border: 1px solid var(--border);
    border-radius: 14px;
    overflow: hidden;
    box-shadow: var(--shadow-sm);
    margin-bottom: 16px;
    animation: rise 0.3s ease both;
}
.panel:last-child { margin-bottom: 0; }
.panel-head {
    padding: 14px 20px;
    border-bottom: 1px solid var(--border);
    background: var(--surface);
    display: flex;
    justify-content: space-between;
    align-items: center;
}
.panel-title {
    font-size: 13px;
    font-weight: 700;
    color: var(--text-1);
    display: flex;
    align-items: center;
    gap: 7px;
}
.panel-title-icon {
    width: 28px; height: 28px;
    border-radius: 7px;
    background: var(--accent-dim);
    color: var(--accent);
    display: flex; align-items: center; justify-content: center;
}
.panel-title-icon svg { width: 14px; height: 14px; }
.panel-body { padding: 20px; }

/* ── Item Table ── */
.item-table { width: 100%; border-collapse: collapse; }
.item-table th {
    font-size: 10.5px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.07em;
    color: var(--text-3);
    padding: 10px 16px;
    text-align: left;
    border-bottom: 1px solid var(--border);
    background: var(--surface);
}
.item-table td { padding: 14px 16px; border-bottom: 1px solid var(--border); vertical-align: middle; }
.item-table tbody tr:last-child td { border-bottom: none; }
.item-table tbody tr:hover td { background: var(--surface); }

.item-info { display: flex; align-items: center; gap: 12px; }
.item-img {
    width: 42px; height: 42px;
    border-radius: 8px;
    object-fit: cover;
    background: var(--surface-2);
    border: 1px solid var(--border);
    flex-shrink: 0;
}
.item-name { font-size: 13px; font-weight: 600; color: var(--text-1); line-height: 1.4; }
.item-sku  { font-size: 11.5px; color: var(--text-3); margin-top: 2px; }

/* ── Summary Box ── */
.summary-box { padding: 16px 20px; background: var(--surface); border-top: 1px solid var(--border); }
.summary-row { display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px; font-size: 13px; color: var(--text-2); }
.summary-row:last-child { margin-bottom: 0; }
.summary-row.total {
    margin-top: 14px;
    padding-top: 14px;
    border-top: 1px dashed var(--border-2);
    font-size: 15px;
    font-weight: 800;
    color: var(--accent);
}

/* ── Detail Rows (sidebar) ── */
.detail-row { display: flex; gap: 12px; margin-bottom: 14px; font-size: 13px; }
.detail-row:last-child { margin-bottom: 0; }
.detail-icon {
    width: 30px; height: 30px;
    border-radius: 7px;
    background: var(--surface-2);
    display: flex; align-items: center; justify-content: center;
    flex-shrink: 0;
    margin-top: 1px;
}
.detail-icon svg { width: 13px; height: 13px; color: var(--text-3); }
.detail-label { font-size: 11px; font-weight: 600; color: var(--text-3); text-transform: uppercase; letter-spacing: 0.04em; margin-bottom: 3px; }
.detail-value { font-size: 13px; font-weight: 500; color: var(--text-1); line-height: 1.5; }

/* ── Shipping type chip ── */
.shipping-chip {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    font-size: 12px;
    font-weight: 700;
    padding: 5px 12px;
    border-radius: 8px;
    text-transform: uppercase;
    letter-spacing: 0.04em;
}
.shipping-reguler { background: var(--accent-dim); color: var(--accent); border: 1px solid rgba(99,102,241,0.2); }
.shipping-cargo   { background: var(--amber-dim); color: var(--amber); border: 1px solid rgba(245,158,11,0.2); }

/* ── Cancelled alert ── */
.cancel-alert {
    background: var(--red-dim);
    border: 1px solid rgba(220,38,38,0.2);
    border-radius: 12px;
    padding: 14px 18px;
    margin-bottom: 22px;
    display: flex;
    gap: 12px;
    align-items: flex-start;
}
.cancel-alert-icon { flex-shrink: 0; color: var(--red); margin-top: 1px; }
.cancel-alert-icon svg { width: 16px; height: 16px; }
.cancel-title { font-weight: 700; font-size: 13px; color: var(--red); margin-bottom: 4px; }
.cancel-reason { font-size: 12.5px; color: var(--red); opacity: 0.85; line-height: 1.5; }



/* ── Buttons ── */
.btn {
    display: inline-flex; align-items: center; justify-content: center;
    gap: 6px; border: none; border-radius: 9px;
    font-family: var(--font); font-size: 13px; font-weight: 600;
    padding: 9px 16px; cursor: pointer;
    transition: all 0.18s; text-decoration: none;
    white-space: nowrap;
}
.btn svg { width: 14px; height: 14px; }
.btn-primary { background: var(--accent); color: #fff; box-shadow: 0 2px 8px var(--accent-glow); }
.btn-primary:hover { background: #4f51e8; transform: translateY(-1px); }
.btn-success { background: var(--green); color: #fff; }
.btn-success:hover { background: #15803d; transform: translateY(-1px); }
.btn-outline { background: #fff; border: 1px solid var(--border); color: var(--text-2); }
.btn-outline:hover { border-color: var(--border-2); color: var(--text-1); }
.btn-danger-outline { background: var(--red-dim); border: 1px solid rgba(220,38,38,0.25); color: var(--red); }
.btn-danger-outline:hover { border-color: var(--red); }
.btn-sm { padding: 5px 10px; font-size: 11.5px; border-radius: 7px; }

.action-bar { display: flex; gap: 8px; flex-wrap: wrap; }

/* ── Modals ── */
.modal-overlay {
    position: fixed; inset: 0;
    background: rgba(15,23,42,0.4);
    backdrop-filter: blur(4px);
    z-index: 200;
    display: flex; align-items: center; justify-content: center;
    opacity: 0; pointer-events: none; transition: all 0.2s;
}
.modal-overlay.open { opacity: 1; pointer-events: auto; }
.modal-box {
    background: #fff; border-radius: 16px; padding: 28px;
    width: 420px; max-width: 90vw;
    box-shadow: var(--shadow-lg);
    transform: translateY(8px) scale(0.97);
    transition: transform 0.2s;
}
.modal-overlay.open .modal-box { transform: translateY(0) scale(1); }
.modal-header { display: flex; align-items: center; gap: 12px; margin-bottom: 16px; }
.modal-icon-wrap {
    width: 42px; height: 42px; border-radius: 12px;
    display: flex; align-items: center; justify-content: center;
    flex-shrink: 0;
}
.modal-title { font-size: 15px; font-weight: 800; color: var(--text-1); }
.modal-desc { font-size: 13px; color: var(--text-2); margin-bottom: 18px; line-height: 1.6; }
.form-label { font-size: 11.5px; font-weight: 700; color: var(--text-2); text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 6px; display: block; }
.form-input {
    width: 100%; padding: 9px 13px;
    border: 1px solid var(--border); border-radius: 9px;
    font-family: var(--font); font-size: 13.5px;
    outline: none; transition: 0.15s; background: var(--surface);
    color: var(--text-1);
}
textarea.form-input { min-height: 90px; resize: vertical; }
.form-input:focus { border-color: var(--accent); box-shadow: 0 0 0 3px var(--accent-glow); background: #fff; }
.modal-actions { display: flex; gap: 8px; justify-content: flex-end; margin-top: 20px; }

/* ── Store card ── */
.store-card {
    display: flex; align-items: center; gap: 12px;
}
.store-avatar {
    width: 38px; height: 38px; border-radius: 10px;
    background: var(--accent-dim); color: var(--accent);
    display: flex; align-items: center; justify-content: center;
    font-size: 16px; font-weight: 800; flex-shrink: 0;
}
.store-name { font-size: 14px; font-weight: 700; color: var(--text-1); }
.store-slug { font-size: 11.5px; color: var(--text-3); margin-top: 2px; }

/* ── Tracking Timeline ── */
.timeline { position: relative; padding-left: 28px; }
.timeline::before { content: ''; position: absolute; left: 9px; top: 8px; bottom: 8px; width: 2px; background: var(--border); border-radius: 2px; }
.timeline-item { position: relative; margin-bottom: 20px; }
.timeline-item:last-child { margin-bottom: 0; }
.timeline-dot {
    position: absolute; left: -23px; top: 3px;
    width: 12px; height: 12px; border-radius: 50%;
    border: 2px solid var(--panel); z-index: 1;
}
.tl-pending    { background: var(--amber); }
.tl-processing { background: #3b82f6; }
.tl-shipping   { background: var(--accent); }
.tl-completed  { background: var(--green); }
.tl-cancelled  { background: var(--red); }
.tl-default    { background: var(--text-4); }
.tl-status { font-size: 12.5px; font-weight: 700; color: var(--text-1); }
.tl-time   { font-size: 11.5px; color: var(--text-3); margin-top: 2px; }
.tl-notes  { font-size: 12.5px; color: var(--text-2); margin-top: 5px; line-height: 1.5; background: var(--surface); border-radius: 7px; padding: 7px 10px; border: 1px solid var(--border); }

@endsection

@section('content')

{{-- Breadcrumb --}}
<nav class="breadcrumb" aria-label="Breadcrumb">
    <a href="{{ route('dashboard') }}">Dashboard</a>
    <span class="breadcrumb-sep">›</span>
    <a href="{{ route('orders.index') }}">Pesanan</a>
    <span class="breadcrumb-sep">›</span>
    <span class="breadcrumb-current">{{ $order->order_number }}</span>
</nav>



{{-- Cancelled Alert --}}
@if($order->status === 'cancelled')
<div class="cancel-alert">
    <div class="cancel-alert-icon">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
            <circle cx="12" cy="12" r="10"/>
            <line x1="15" y1="9" x2="9" y2="15"/>
            <line x1="9" y1="9" x2="15" y2="15"/>
        </svg>
    </div>
    <div>
        <div class="cancel-title">Pesanan Dibatalkan</div>
        <div class="cancel-reason">{{ $order->cancel_reason }}</div>
    </div>
</div>
@endif

{{-- Page Header --}}
<div class="page-header">
    <div class="page-title">
        <h1>
            {{ $order->order_number }}
            @php
                $statusMap = [
                    'pending'    => 'Menunggu Konfirmasi',
                    'processing' => 'Dikemas',
                    'shipping'   => 'Dikirim',
                    'completed'  => 'Selesai',
                    'cancelled'  => 'Dibatalkan',
                ];
            @endphp
            <span class="order-badge status-{{ $order->status }}">
                {{ $statusMap[$order->status] ?? $order->status }}
            </span>
        </h1>
        <p>Dibuat {{ $order->created_at->format('d M Y, H:i') }} · Toko {{ $order->store->name ?? '-' }}</p>
    </div>

    <div class="action-bar">
        @if($order->status === 'pending')
            <form action="{{ route('orders.update-status', $order) }}" method="POST">
                @csrf @method('PATCH')
                <input type="hidden" name="status" value="processing">
                <button type="submit" class="btn btn-primary">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="20 6 9 17 4 12"/>
                    </svg>
                    Konfirmasi Pesanan
                </button>
            </form>
            <button onclick="openModal('cancelModal')" class="btn btn-danger-outline">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="18" y1="6" x2="6" y2="18"/>
                    <line x1="6" y1="6" x2="18" y2="18"/>
                </svg>
                Batalkan
            </button>
        @elseif($order->status === 'processing')
            <form action="{{ route('orders.update-status', $order) }}" method="POST">
                @csrf @method('PATCH')
                <input type="hidden" name="status" value="shipping">
                <button type="submit" class="btn btn-success">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="1" y="3" width="15" height="13"/>
                        <polygon points="16 8 20 8 23 11 23 16 16 16 16 8"/>
                        <circle cx="5.5" cy="18.5" r="2.5"/>
                        <circle cx="18.5" cy="18.5" r="2.5"/>
                    </svg>
                    Kirim Paket
                </button>
            </form>
        @elseif($order->status === 'shipping')
            <form action="{{ route('orders.update-status', $order) }}" method="POST">
                @csrf @method('PATCH')
                <input type="hidden" name="status" value="completed">
                <button type="submit" class="btn btn-primary">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="20 6 9 17 4 12"/>
                    </svg>
                    Tandai Selesai
                </button>
            </form>
        @endif
    </div>
</div>

{{-- Main Grid --}}
<div class="invoice-grid">

    {{-- Kiri: Produk --}}
    <div>
        <div class="panel">
            <div class="panel-head">
                <div class="panel-title">
                    <span class="panel-title-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"/>
                            <line x1="3" y1="6" x2="21" y2="6"/>
                            <path d="M16 10a4 4 0 0 1-8 0"/>
                        </svg>
                    </span>
                    Produk Dipesan
                </div>
                <span style="font-size:12px;color:var(--text-3);font-weight:600;">
                    {{ $order->orderItems->count() }} item
                </span>
            </div>

            <div style="overflow-x:auto;">
                <table class="item-table">
                    <thead>
                        <tr>
                            <th>Produk</th>
                            <th style="width:90px; text-align:center;">Qty</th>
                            <th style="width:130px; text-align:right; padding-right:20px;">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($order->orderItems as $item)
                        @php
                            $variant = $item->productVariant;
                            $product = $variant?->product;
                            $imgPath = $product?->image ? asset('storage/' . $product->image) : null;
                        @endphp
                        <tr>
                            <td>
                                <div class="item-info">
                                    <img
                                        src="{{ $imgPath ?? '' }}"
                                        class="item-img"
                                        alt="{{ $product?->name ?? '' }}"
                                        onerror="this.src='data:image/svg+xml;utf8,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 24 24%22 fill=%22%23cbd5e1%22><path d=%22M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z%22></path></svg>'"
                                    >
                                    <div>
                                        <div class="item-name">{{ $product?->name ?? 'Produk Dihapus' }}</div>
                                        @if($variant?->name)
                                            <div class="item-sku">Varian: {{ $variant->name }}</div>
                                        @endif
                                        <div class="item-sku">Rp {{ number_format($item->price, 0, ',', '.') }} / item</div>
                                    </div>
                                </div>
                            </td>
                            <td style="text-align:center; font-weight:700; color:var(--text-1);">
                                {{ $item->quantity }}
                            </td>
                            <td style="text-align:right; font-weight:700; color:var(--text-1); padding-right:20px; font-variant-numeric:tabular-nums;">
                                Rp {{ number_format($item->price * $item->quantity, 0, ',', '.') }}
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Summary --}}
            @php $subtotal = $order->orderItems->sum(fn($i) => $i->price * $i->quantity); @endphp
            <div class="summary-box">
                <div class="summary-row">
                    <span>Subtotal Produk</span>
                    <span style="font-weight:600; color:var(--text-1);">Rp {{ number_format($subtotal, 0, ',', '.') }}</span>
                </div>
                <div class="summary-row">
                    <span>Ongkos Kirim <span style="font-size:11.5px; color:var(--text-3);">({{ ucfirst($order->shipping_type) }})</span></span>
                    <span style="font-weight:600; color:var(--text-1);">Rp {{ number_format($order->total_amount - $subtotal, 0, ',', '.') }}</span>
                </div>
                <div class="summary-row total">
                    <span>Total Pembayaran</span>
                    <span>Rp {{ number_format($order->total_amount, 0, ',', '.') }}</span>
                </div>
            </div>
        </div>
    </div>

    {{-- Kanan: Sidebar --}}
    <div>

        {{-- Info Toko --}}
        <div class="panel">
            <div class="panel-head">
                <div class="panel-title">
                    <span class="panel-title-icon" style="background:var(--surface-2); color:var(--text-2);">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/>
                            <polyline points="9 22 9 12 15 12 15 22"/>
                        </svg>
                    </span>
                    Asal Toko
                </div>
            </div>
            <div class="panel-body">
                <div class="store-card">
                    <div class="store-avatar">{{ substr($order->store->name ?? 'T', 0, 1) }}</div>
                    <div>
                        <div class="store-name">{{ $order->store->name ?? '-' }}</div>
                        @if($order->store->slug ?? false)
                        <div class="store-slug">{{ $order->store->slug }}</div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- Info Pelanggan --}}
        <div class="panel">
            <div class="panel-head">
                <div class="panel-title">
                    <span class="panel-title-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                            <circle cx="12" cy="7" r="4"/>
                        </svg>
                    </span>
                    Informasi Pelanggan
                </div>
            </div>
            <div class="panel-body">
                <div class="detail-row">
                    <div class="detail-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                            <circle cx="12" cy="7" r="4"/>
                        </svg>
                    </div>
                    <div>
                        <div class="detail-label">Nama</div>
                        <div class="detail-value">{{ $order->customer_name }}</div>
                    </div>
                </div>

                @if($order->customer_email)
                <div class="detail-row">
                    <div class="detail-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/>
                            <polyline points="22,6 12,13 2,6"/>
                        </svg>
                    </div>
                    <div>
                        <div class="detail-label">Email</div>
                        <div class="detail-value" style="word-break:break-all;">{{ $order->customer_email }}</div>
                    </div>
                </div>
                @endif

                @if($order->customer_phone)
                <div class="detail-row">
                    <div class="detail-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/>
                        </svg>
                    </div>
                    <div>
                        <div class="detail-label">Telepon</div>
                        <div class="detail-value">{{ $order->customer_phone }}</div>
                    </div>
                </div>
                @endif

                <div class="detail-row" style="margin-bottom:0;">
                    <div class="detail-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/>
                            <circle cx="12" cy="10" r="3"/>
                        </svg>
                    </div>
                    <div>
                        <div class="detail-label">Alamat Pengiriman</div>
                        <div class="detail-value">{{ $order->shipping_address }}</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Metode Pengiriman --}}
        <div class="panel">
            <div class="panel-head">
                <div class="panel-title">
                    <span class="panel-title-icon" style="background:{{ $order->shipping_type == 'cargo' ? 'var(--amber-dim)' : 'var(--accent-dim)' }}; color:{{ $order->shipping_type == 'cargo' ? 'var(--amber)' : 'var(--accent)' }};">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="1" y="3" width="15" height="13"/>
                            <polygon points="16 8 20 8 23 11 23 16 16 16 16 8"/>
                            <circle cx="5.5" cy="18.5" r="2.5"/>
                            <circle cx="18.5" cy="18.5" r="2.5"/>
                        </svg>
                    </span>
                    Pengiriman
                </div>
                @if(in_array($order->status, ['pending', 'processing']))
                <button onclick="openModal('shippingModal')" class="btn btn-outline btn-sm">Ubah</button>
                @endif
            </div>
            <div class="panel-body">
                <span class="shipping-chip shipping-{{ $order->shipping_type }}">
                    {{ ucfirst($order->shipping_type) }}
                </span>
                <p style="font-size:12px; color:var(--text-3); margin-top:10px; line-height:1.6; margin-bottom:0;">
                    @if($order->shipping_type == 'cargo')
                        Ideal untuk paket berat atau volume besar (>10 kg). Estimasi lebih lama dengan ongkir lebih ekonomis.
                    @else
                        Cocok untuk paket standar berukuran kecil dan ringan.
                    @endif
                </p>
            </div>
        </div>
    </div>
</div>

{{-- Tracking History Timeline --}}
@if($order->trackingHistories->isNotEmpty())
<div class="panel" style="margin-top: 20px;">
    <div class="panel-head">
        <div class="panel-title">
            <span class="panel-title-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/>
                </svg>
            </span>
            Riwayat Tracking
        </div>
        <span style="font-size:12px; color:var(--text-3); font-weight:600;">{{ $order->trackingHistories->count() }} perubahan</span>
    </div>
    <div class="panel-body">
        @php
            $tlStatusMap = [
                'pending'    => 'Menunggu Konfirmasi',
                'processing' => 'Sedang Dikemas',
                'shipping'   => 'Sedang Dikirim',
                'completed'  => 'Selesai',
                'cancelled'  => 'Dibatalkan',
            ];
        @endphp
        <div class="timeline">
            @foreach($order->trackingHistories as $history)
            <div class="timeline-item">
                <div class="timeline-dot tl-{{ $history->status }} {{ in_array($history->status, ['pending','processing','shipping','completed','cancelled']) ? '' : 'tl-default' }}"></div>
                <div class="tl-status">{{ $tlStatusMap[$history->status] ?? ucfirst($history->status) }}</div>
                <div class="tl-time">
                    {{ $history->created_at->format('d M Y, H:i') }}
                    @if($history->admin)
                        &middot; oleh {{ $history->admin->name }}
                    @endif
                </div>
                @if($history->notes)
                    <div class="tl-notes">{{ $history->notes }}</div>
                @endif
            </div>
            @endforeach
        </div>
    </div>
</div>
@endif

{{-- Modal: Batalkan --}}
<div class="modal-overlay" id="cancelModal">
    <div class="modal-box">
        <div class="modal-header">
            <div class="modal-icon-wrap" style="background:var(--red-dim); color:var(--red);">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="10"/>
                    <line x1="15" y1="9" x2="9" y2="15"/>
                    <line x1="9" y1="9" x2="15" y2="15"/>
                </svg>
            </div>
            <div class="modal-title">Batalkan Pesanan</div>
        </div>
        <p class="modal-desc">Tindakan ini tidak bisa dikembalikan. Sertakan alasan pembatalan untuk keperluan pelaporan.</p>
        <form action="{{ route('orders.cancel', $order) }}" method="POST">
            @csrf @method('PATCH')
            <label class="form-label">Alasan Pembatalan</label>
            <textarea name="cancel_reason" class="form-input" placeholder="Contoh: stok habis, permintaan pelanggan..." required></textarea>
            <div class="modal-actions">
                <button type="button" class="btn btn-outline" onclick="closeModal('cancelModal')">Kembali</button>
                <button type="submit" class="btn btn-primary" style="background:var(--red); box-shadow:0 2px 8px rgba(220,38,38,0.25);">Konfirmasi Batalkan</button>
            </div>
        </form>
    </div>
</div>

{{-- Modal: Edit Pengiriman --}}
<div class="modal-overlay" id="shippingModal">
    <div class="modal-box">
        <div class="modal-header">
            <div class="modal-icon-wrap" style="background:var(--accent-dim); color:var(--accent);">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="1" y="3" width="15" height="13"/>
                    <polygon points="16 8 20 8 23 11 23 16 16 16 16 8"/>
                    <circle cx="5.5" cy="18.5" r="2.5"/>
                    <circle cx="18.5" cy="18.5" r="2.5"/>
                </svg>
            </div>
            <div class="modal-title">Ubah Metode Pengiriman</div>
        </div>
        <p class="modal-desc">Pilih jenis pengiriman yang sesuai dengan berat dan volume paket.</p>
        <form action="{{ route('orders.update-shipping', $order) }}" method="POST">
            @csrf @method('PATCH')
            <label class="form-label">Metode Pengiriman</label>
            <select name="shipping_type" class="form-input" style="cursor:pointer;" required>
                <option value="reguler" {{ $order->shipping_type == 'reguler' ? 'selected' : '' }}>Reguler — Paket standar</option>
                <option value="cargo"   {{ $order->shipping_type == 'cargo'   ? 'selected' : '' }}>Cargo — Berat / Volume Besar</option>
            </select>
            <div class="modal-actions">
                <button type="button" class="btn btn-outline" onclick="closeModal('shippingModal')">Batal</button>
                <button type="submit" class="btn btn-primary">Simpan</button>
            </div>
        </form>
    </div>
</div>

@endsection

@push('scripts')
<script>
    function openModal(id)  { document.getElementById(id).classList.add('open'); }
    function closeModal(id) { document.getElementById(id).classList.remove('open'); }

    document.querySelectorAll('.modal-overlay').forEach(el => {
        el.addEventListener('click', function(e) {
            if (e.target === this) this.classList.remove('open');
        });
    });
</script>
@endpush
