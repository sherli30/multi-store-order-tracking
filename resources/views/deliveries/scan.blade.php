@extends('layouts.app')

@section('title', 'Cek Resi Pengiriman')

@section('styles')

/* ── Layout ──────────────────────────────────── */
.scan-page {
max-width: 680px;
margin: 0 auto;
padding-bottom: 48px;
}

/* ── Page Header ─────────────────────────────── */
.scan-header {
text-align: center;
margin-bottom: 32px;
animation: rise 0.3s ease both;
}
.scan-header-icon {
width: 56px; height: 56px;
background: linear-gradient(135deg, var(--accent), color-mix(in srgb, var(--accent) 70%, #7c3aed));
border-radius: 16px;
display: inline-flex; align-items: center; justify-content: center;
margin-bottom: 14px;
box-shadow: 0 8px 24px color-mix(in srgb, var(--accent) 30%, transparent);
}
.scan-header-icon svg { width: 26px; height: 26px; color: #fff; }
.scan-header h1 {
font-size: 22px; font-weight: 800; letter-spacing: -0.04em;
color: var(--text-1); margin-bottom: 6px;
}
.scan-header p { font-size: 13px; color: var(--text-3); line-height: 1.5; }

/* ── Alert ───────────────────────────────────── */
.scan-alert {
display: flex; align-items: flex-start; gap: 12px;
padding: 14px 18px; border-radius: 12px;
font-size: 13px; font-weight: 600; line-height: 1.5;
margin-bottom: 20px;
animation: rise 0.25s ease both;
}
.scan-alert svg { width: 18px; height: 18px; flex-shrink: 0; margin-top: 1px; }
.scan-alert.success {
background: var(--green-dim); color: var(--green);
border: 1px solid rgba(22,163,74,0.2);
}
.scan-alert.error {
background: var(--red-dim); color: var(--red);
border: 1px solid rgba(220,38,38,0.2);
}

/* ── Scanner Card ────────────────────────────── */
.scanner-card {
background: var(--panel);
border: 1px solid var(--border);
border-radius: 16px;
padding: 28px 28px 24px;
box-shadow: var(--shadow-sm);
margin-bottom: 20px;
transition: border-color 0.2s, box-shadow 0.2s;
animation: rise 0.35s ease 0.05s both;
}
.scanner-card:focus-within {
border-color: var(--accent);
box-shadow: 0 0 0 3px var(--accent-glow), var(--shadow-md);
}

/* ── Status Pill ─────────────────────────────── */
.scanner-status {
display: inline-flex; align-items: center; gap: 8px;
font-size: 12px; font-weight: 700;
padding: 5px 12px; border-radius: 20px;
margin-bottom: 22px;
transition: all 0.2s;
user-select: none;
}
.scanner-status.ready {
background: var(--green-dim); color: var(--green);
border: 1px solid rgba(22,163,74,0.2);
}
.scanner-status.idle {
background: var(--surface-2); color: var(--text-3);
border: 1px solid var(--border);
}
.scanner-status.ready .dot,
.scanner-status.idle .dot {
width: 7px; height: 7px; border-radius: 50%; flex-shrink: 0;
}
.scanner-status.ready .dot { background: var(--green); animation: blink 2s ease-in-out infinite; }
.scanner-status.idle .dot { background: var(--text-4); }

@keyframes blink {
0%, 100% { opacity: 1; }
50% { opacity: 0.35; }
}

/* ── Input Row ───────────────────────────────── */
.scanner-input-row {
display: flex; gap: 10px; align-items: stretch;
}
.scanner-input {
flex: 1; min-width: 0;
font-size: 20px; font-family: var(--mono);
font-weight: 700; letter-spacing: 2px;
text-align: center;
padding: 14px 16px;
border: 2px solid var(--border); border-radius: 12px;
outline: none; color: var(--text-1); background: var(--surface);
transition: border-color 0.15s, box-shadow 0.15s;
box-sizing: border-box;
}
.scanner-input:focus {
border-color: var(--accent);
box-shadow: 0 0 0 3px var(--accent-glow);
background: var(--panel);
}
.scanner-input::placeholder {
color: var(--text-4); font-size: 14px;
letter-spacing: normal; font-weight: 500;
}
.btn-scan-submit {
display: inline-flex; align-items: center; justify-content: center; gap: 7px;
padding: 0 22px; border-radius: 12px;
background: var(--accent); color: #fff; border: none;
font-family: var(--font); font-size: 13px; font-weight: 700;
cursor: pointer; white-space: nowrap; flex-shrink: 0;
transition: all 0.15s;
box-shadow: 0 2px 8px color-mix(in srgb, var(--accent) 30%, transparent);
}
.btn-scan-submit:hover { opacity: 0.9; transform: translateY(-1px); }
.btn-scan-submit:active { transform: translateY(0); opacity: 1; }
.btn-scan-submit svg { width: 15px; height: 15px; }

/* ── Input hint ──────────────────────────────── */
.scanner-hint {
display: flex; align-items: center; justify-content: center; gap: 6px;
margin-top: 14px; font-size: 11.5px; color: var(--text-4); font-weight: 600;
}
.scanner-hint svg { width: 12px; height: 12px; }
.hint-divider { width: 1px; height: 12px; background: var(--border); margin: 0 4px; }

/* ── Result Card ─────────────────────────────── */
.result-card {
background: var(--panel);
border: 1px solid var(--border);
border-radius: 16px;
overflow: hidden;
box-shadow: var(--shadow-sm);
animation: rise 0.3s ease both;
}
.result-card-header {
display: flex; align-items: center; justify-content: space-between;
padding: 16px 20px;
background: var(--surface);
border-bottom: 1px solid var(--border);
gap: 12px;
}
.result-card-title {
display: flex; align-items: center; gap: 9px;
font-size: 13px; font-weight: 800; color: var(--text-1);
}
.result-card-title svg { width: 15px; height: 15px; color: var(--accent); }
.btn-clear-result {
display: inline-flex; align-items: center; gap: 6px;
padding: 6px 12px; border-radius: 8px;
font-family: var(--font); font-size: 12px; font-weight: 700;
color: var(--text-3); background: var(--panel);
border: 1px solid var(--border); cursor: pointer;
text-decoration: none; transition: all 0.15s;
white-space: nowrap;
}
.btn-clear-result:hover { color: var(--red); border-color: rgba(220,38,38,0.3); background: var(--red-dim); }
.btn-clear-result svg { width: 12px; height: 12px; }

/* ── Responsive ──────────────────────────────── */
@media (max-width: 600px) {
.scanner-card { padding: 20px 16px 18px; }
.scanner-input { font-size: 16px; letter-spacing: 1px; padding: 13px 12px; }
.scanner-input::placeholder { font-size: 13px; }
.btn-scan-submit span { display: none; }
.btn-scan-submit { padding: 0 16px; }
.scan-header-icon { width: 48px; height: 48px; }
.scan-header h1 { font-size: 19px; }
}

@keyframes rise { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }

/* ── Result Details ──────────────────────────── */
.scan-detail-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 24px; padding: 24px; border-bottom: 1px solid var(--border); }
@media (max-width: 600px) { .scan-detail-grid { grid-template-columns: 1fr; gap: 16px; } }
.scan-detail-col { display: flex; flex-direction: column; gap: 16px; }
.scan-info-block { background: var(--surface); padding: 16px; border-radius: 12px; border: 1px solid var(--border); }
.scan-info-title { font-size: 12px; font-weight: 800; color: var(--text-2); text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 12px; display: flex; align-items: center; gap: 6px; }
.scan-info-title svg { width: 14px; height: 14px; color: var(--accent); }
.scan-info-item { display: flex; flex-direction: column; gap: 3px; margin-bottom: 12px; }
.scan-info-item:last-child { margin-bottom: 0; }
.scan-info-label { font-size: 11px; font-weight: 700; color: var(--text-4); text-transform: uppercase; letter-spacing: 0.03em; }
.scan-info-value { font-size: 13.5px; font-weight: 700; color: var(--text-1); line-height: 1.4; }

.scan-items-wrapper { padding: 24px; border-bottom: 1px solid var(--border); }
.scan-items-table { width: 100%; border-collapse: collapse; }
.scan-items-table th { text-align: left; padding: 10px 0; border-bottom: 1px solid var(--border); font-size: 11px; font-weight: 800; color: var(--text-4); text-transform: uppercase; letter-spacing: 0.05em; }
.scan-items-table td { padding: 12px 0; border-bottom: 1px solid var(--surface); font-size: 13px; font-weight: 600; color: var(--text-1); vertical-align: top; }
.scan-items-table tr:last-child td { border-bottom: none; }

.scan-timeline-wrapper { padding: 30px 24px; }
.shopee-timeline { position: relative; padding-left: 45px; }
.shopee-timeline::before { content: ''; position: absolute; left: 21px; top: 8px; bottom: 8px; width: 2px; background: #e5e7eb; z-index: 1; }
.timeline-item { position: relative; margin-bottom: 35px; animation: rise 0.3s ease both; }
.timeline-item:last-child { margin-bottom: 0; }
.timeline-dot { position: absolute; left: -34px; top: 0; width: 22px; height: 22px; border-radius: 50%; background: #fff; border: 2px solid #d1d5db; z-index: 2; display: flex; align-items: center; justify-content: center; box-shadow: 0 0 0 4px #fff; }
.timeline-item.latest .timeline-dot { border-color: var(--accent); box-shadow: 0 0 0 4px var(--accent-dim); }
.timeline-dot-inner { width: 6px; height: 6px; border-radius: 50%; background: #d1d5db; }
.timeline-item.latest .timeline-dot-inner { background: var(--accent); width: 10px; height: 10px; animation: blink 1.5s infinite; }


@endsection

@section('content')
<div class="scan-page">

    {{-- Page Header --}}
    <div class="scan-header">
        <div class="scan-header-icon">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M4 7V4h3" />
                <path d="M4 17v3h3" />
                <path d="M20 7V4h-3" />
                <path d="M20 17v3h-3" />
                <line x1="7" y1="9" x2="7" y2="15" stroke-width="2.5" />
                <line x1="10" y1="8" x2="10" y2="16" stroke-width="1.5" />
                <line x1="13" y1="9" x2="13" y2="15" stroke-width="2.5" />
                <line x1="16" y1="8" x2="16" y2="16" stroke-width="1.5" />
            </svg>
        </div>
        <h1>Cek Resi Pengiriman</h1>
        <p>Arahkan scanner barcode atau ketik nomor resi / nomor pesanan untuk melihat status pengiriman.</p>
    </div>

    {{-- Flash Alerts --}}
    @if(session('success'))
    <div class="scan-alert success" role="alert">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
            <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14" />
            <polyline points="22 4 12 14.01 9 11.01" />
        </svg>
        {{ session('success') }}
    </div>
    @endif

    @if(session('error'))
    <div class="scan-alert error" role="alert">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
            <circle cx="12" cy="12" r="10" />
            <line x1="15" y1="9" x2="9" y2="15" />
            <line x1="9" y1="9" x2="15" y2="15" />
        </svg>
        {{ session('error') }}
    </div>
    @endif

    {{-- Scanner Card --}}
    <div class="scanner-card">

        {{-- Status pill --}}
        <div class="scanner-status ready" id="statusPill">
            <span class="dot"></span>
            <span id="statusText">Scanner Aktif — Siap Menerima Input</span>
        </div>

        {{-- Input + submit --}}
        <form action="{{ route('deliveries.scan') }}" method="GET" id="scanForm" novalidate>
            <div class="scanner-input-row">
                <input
                    type="text"
                    name="identifier"
                    id="barcodeInput"
                    class="scanner-input"
                    placeholder="Tembak barcode atau ketik resi / nomor pesanan..."
                    value="{{ request('identifier') }}"
                    autocomplete="off"
                    autocorrect="off"
                    autocapitalize="off"
                    spellcheck="false"
                    autofocus
                    required>
                <button type="submit" class="btn-scan-submit">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="11" cy="11" r="8" />
                        <line x1="21" y1="21" x2="16.65" y2="16.65" />
                    </svg>
                    <span>Cari</span>
                </button>
            </div>
        </form>

        {{-- Hint bar --}}
        <div class="scanner-hint">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                <rect x="4" y="7" width="16" height="12" rx="2" />
                <path d="M8 7V5a2 2 0 0 1 4 0" />
            </svg>
            Barcode scanner otomatis submit
            <span class="hint-divider"></span>
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                <polyline points="9 10 4 15 9 20" />
                <path d="M20 4v7a4 4 0 0 1-4 4H4" />
            </svg>
            Tekan Enter untuk cari manual
        </div>
    </div>

    {{-- Result Panel --}}
    @if(isset($order))
    <div class="result-card">
        <div class="result-card-header">
            <div class="result-card-title">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <polygon points="1 6 1 22 8 18 16 22 23 18 23 2 16 6 8 2 1 6" />
                    <line x1="8" y1="2" x2="8" y2="18" />
                    <line x1="16" y1="6" x2="16" y2="22" />
                </svg>
                Hasil Pencarian: <span style="color:var(--accent); font-family:var(--mono);">{{ $order->order_number }}</span>
            </div>
            <a href="{{ route('deliveries.scan') }}" class="btn-clear-result">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="18" y1="6" x2="6" y2="18" />
                    <line x1="6" y1="6" x2="18" y2="18" />
                </svg>
                Cari Lagi
            </a>
        </div>

        {{-- Order & Customer Details --}}
        <div class="scan-detail-grid">
            <div class="scan-detail-col">
                <div class="scan-info-block">
                    <div class="scan-info-title">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z" />
                            <polyline points="14 2 14 8 20 8" />
                            <line x1="16" y1="13" x2="8" y2="13" />
                            <line x1="16" y1="17" x2="8" y2="17" />
                            <polyline points="10 9 9 9 8 9" />
                        </svg>
                        Detail Pesanan
                    </div>
                    <div class="scan-info-item">
                        <span class="scan-info-label">Status</span>
                        <span class="scan-info-value" style="color:var(--accent);text-transform:uppercase;">{{ \App\Services\StatusService::getOrderLabel($order->status ?? '') }}</span>
                    </div>
                    <div class="scan-info-item">
                        <span class="scan-info-label">Tanggal Pemesanan</span>
                        <span class="scan-info-value">{{ $order->created_at->translatedFormat('d M Y, H:i') }}</span>
                    </div>
                    <div class="scan-info-item">
                        <span class="scan-info-label">Toko</span>
                        <span class="scan-info-value">{{ $order->store->name ?? '—' }}</span>
                    </div>
                </div>

                <div class="scan-info-block">
                    <div class="scan-info-title">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2" />
                            <circle cx="12" cy="7" r="4" />
                        </svg>
                        Informasi Pelanggan
                    </div>
                    <div class="scan-info-item">
                        <span class="scan-info-label">Nama Lengkap</span>
                        <span class="scan-info-value">{{ $order->customer_name }}</span>
                    </div>
                    <div class="scan-info-item">
                        <span class="scan-info-label">Kontak</span>
                        <span class="scan-info-value">{{ $order->customer_phone ?? '—' }}<br><span style="font-size:12px;color:var(--text-3);font-weight:500;">{{ $order->customer_email }}</span></span>
                    </div>
                </div>
            </div>

            <div class="scan-detail-col">
                <div class="scan-info-block">
                    <div class="scan-info-title">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <rect x="1" y="3" width="15" height="13" />
                            <polygon points="16 8 20 8 23 11 23 16 16 16 16 8" />
                            <circle cx="5.5" cy="18.5" r="2.5" />
                            <circle cx="18.5" cy="18.5" r="2.5" />
                        </svg>
                        Pengiriman
                    </div>
                    <div class="scan-info-item">
                        <span class="scan-info-label">Kurir & Layanan</span>
                        <span class="scan-info-value">{{ strtoupper($order->shipping_courier ?? '—') }} ({{ ucfirst($order->shipping_type) }})</span>
                    </div>
                    <div class="scan-info-item">
                        <span class="scan-info-label">Nomor Resi</span>
                        <span class="scan-info-value" style="font-family:var(--mono);color:var(--accent);">{{ $order->tracking_number ?? 'Belum ada resi' }}</span>
                    </div>
                    <div class="scan-info-item">
                        <span class="scan-info-label">Alamat Tujuan</span>
                        <span class="scan-info-value">{{ $order->shipping_address }}<br>{{ $order->city }}, {{ $order->province }} {{ $order->postal_code }}</span>
                    </div>
                </div>

                <div class="scan-info-block">
                    <div class="scan-info-title">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <rect x="2" y="5" width="20" height="14" rx="2" />
                            <line x1="2" y1="10" x2="22" y2="10" />
                        </svg>
                        Pembayaran
                    </div>
                    <div class="scan-info-item">
                        <span class="scan-info-label">Metode</span>
                        <span class="scan-info-value" style="text-transform:uppercase;">{{ $order->transaction->payment_method ?? '—' }}</span>
                    </div>
                    <div class="scan-info-item">
                        <span class="scan-info-label">Total Tagihan</span>
                        <span class="scan-info-value" style="color:var(--green);">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Order Items --}}
        <div class="scan-items-wrapper">
            <div class="scan-info-title">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z" />
                    <line x1="3" y1="6" x2="21" y2="6" />
                    <path d="M16 10a4 4 0 0 1-8 0" />
                </svg>
                Daftar Produk
            </div>
            <table class="scan-items-table">
                <thead>
                    <tr>
                        <th>Produk</th>
                        <th style="text-align:right;">Qty</th>
                        <th style="text-align:right;">Harga Satuan</th>
                        <th style="text-align:right;">Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($order->orderItems as $item)
                    <tr>
                        <td>
                            <div style="display:flex;align-items:center;gap:10px;">
                                @if($item->product && $item->product->image_url)
                                <img src="{{ $item->product->image_url }}" alt="" style="width:32px;height:32px;object-fit:cover;border-radius:6px;background:var(--surface-2);">
                                @else
                                <div style="width:32px;height:32px;border-radius:6px;background:var(--surface-2);"></div>
                                @endif
                                <span>{{ $item->product->name ?? 'Produk Dihapus' }}</span>
                            </div>
                        </td>
                        <td style="text-align:right;">{{ $item->quantity }}x</td>
                        <td style="text-align:right;color:var(--text-3);">Rp {{ number_format($item->price, 0, ',', '.') }}</td>
                        <td style="text-align:right;">Rp {{ number_format($item->price * $item->quantity, 0, ',', '.') }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Tracking History --}}
        <div class="scan-timeline-wrapper">
            <div class="scan-info-title" style="margin-bottom: 24px;">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <polyline points="22 12 18 12 15 21 9 3 6 12 2 12" />
                </svg>
                Riwayat Tracking
            </div>

            <div class="shopee-timeline">
                @forelse($order->trackingHistories()->whereIn('status', ['processing', 'shipping', 'completed', 'cancelled'])->latest()->get() as $index => $h)
                @php $isLatest = $index === 0; @endphp
                <div class="timeline-item {{ $isLatest ? 'latest' : '' }}" style="animation-delay: {{ $index * 0.1 }}s;">
                    <div class="timeline-dot">
                        <div class="timeline-dot-inner"></div>
                    </div>
                    <div style="display: flex; flex-direction: column; gap: 4px;">
                        <div style="display: flex; align-items: center; justify-content: space-between;">
                            <div style="font-size: 13px; font-weight: {{ $isLatest ? '900' : '700' }}; color: {{ $isLatest ? 'var(--text-1)' : 'var(--text-3)' }}; text-transform: uppercase; letter-spacing: 0.02em;">
                                {{ \App\Services\StatusService::getOrderLabel($h->status ?? '') }}
                            </div>
                            <div style="font-size: 11px; font-weight: 700; color: var(--text-4); background: var(--surface); padding: 3px 8px; border-radius: 6px;">
                                {{ $h->created_at->translatedFormat('d M Y, H:i') }}
                            </div>
                        </div>
                        <div style="font-size: 13px; line-height: 1.6; color: {{ $isLatest ? 'var(--text-2)' : 'var(--text-4)' }}; font-weight: {{ $isLatest ? '600' : '500' }};">
                            @if($h->notes)
                            {{ $h->notes }}
                            @else
                            {{ match($h->status) {
                                            'pending' => 'Pesanan telah berhasil dibuat oleh pelanggan.',
                                            'perlu_diproses' => 'Pesanan sedang menunggu untuk diproses oleh admin.',
                                            'processing' => 'Barang sedang dipersiapkan dan dikemas untuk pengiriman.',
                                            'shipping' => 'Pesanan telah diserahkan ke kurir untuk dikirim ke alamat tujuan.',
                                            'completed' => 'Pesanan telah sampai di tujuan dan diterima dengan baik.',
                                            'cancelled' => 'Pesanan dibatalkan dengan alasan tertentu.',
                                            'refund' => 'Pesanan dibatalkan dan dana dikembalikan (refund).',
                                            default => 'Status diperbarui oleh sistem.'
                                        } }}
                            @endif
                        </div>
                        <div style="display: flex; align-items: center; gap: 6px; margin-top: 4px;">
                            <div style="width: 16px; height: 16px; border-radius: 50%; background: var(--surface-2); display: flex; align-items: center; justify-content: center; font-size: 8px; font-weight: 800; color: var(--text-4);">
                                {{ substr($h->admin->name ?? 'S', 0, 1) }}
                            </div>
                            <span style="font-size: 11px; color: var(--text-4); font-weight: 600;">{{ $h->admin->name ?? 'Sistem Otomatis' }}</span>
                        </div>
                    </div>
                </div>
                @empty
                <div style="text-align: center; padding: 20px 0;">
                    <svg viewBox="0 0 24 24" width="36" height="36" stroke="currentColor" stroke-width="1.5" fill="none" style="color: var(--text-4); opacity: 0.5; margin-bottom: 15px;">
                        <circle cx="12" cy="12" r="10" />
                        <line x1="12" y1="8" x2="12" y2="12" />
                        <line x1="12" y1="16" x2="12.01" y2="16" />
                    </svg>
                    <p style="color: var(--text-4); font-size: 13px; font-weight: 600;">Belum ada riwayat aktivitas tracking tercatat.</p>
                </div>
                @endforelse
            </div>
        </div>

        {{-- Footer Actions --}}
        <div style="padding: 20px 24px; border-top: 1px solid var(--border); background: var(--surface); display: flex; justify-content: space-between; align-items: center;">
            <div style="font-size: 12px; color: var(--text-4); font-weight: 600;">
                <svg viewBox="0 0 24 24" width="12" fill="none" stroke="currentColor" stroke-width="3" style="display: inline-block; vertical-align: middle; margin-right: 4px;">
                    <circle cx="12" cy="12" r="10" />
                    <polyline points="12 6 12 12 16 14" />
                </svg>
                Waktu lokal Server
            </div>
            <a href="{{ route('orders.show', $order) }}" class="btn-scan-submit" style="padding: 8px 20px; font-size: 13px;">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6" />
                    <polyline points="15 3 21 3 21 9" />
                    <line x1="10" y1="14" x2="21" y2="3" />
                </svg>
                Buka Detail Pesanan
            </a>
        </div>
    </div>
    @endif

</div>
@endsection

@push('scripts')
<script>
    (function() {
        const input = document.getElementById('barcodeInput');
        const pill = document.getElementById('statusPill');
        const statusTxt = document.getElementById('statusText');

        function setReady() {
            pill.className = 'scanner-status ready';
            statusTxt.textContent = 'Scanner Aktif — Siap Menerima Input';
        }

        function setIdle() {
            pill.className = 'scanner-status idle';
            statusTxt.textContent = 'Fokus Hilang — Klik di sini untuk mengaktifkan';
        }

        input.addEventListener('focus', setReady);
        input.addEventListener('blur', setIdle);

        // Re-focus on any body click, except interactive elements that need their own focus
        document.body.addEventListener('click', function(e) {
            const tag = e.target.tagName;
            if (tag !== 'SELECT' && tag !== 'BUTTON' && tag !== 'A' && tag !== 'TEXTAREA') {
                input.focus();
            }
        });

        // Physical barcode scanners emit the barcode string followed by Enter (HID keyboard).
        // The form's native submit handles this; no extra JS needed.
    })();
</script>
@endpush
