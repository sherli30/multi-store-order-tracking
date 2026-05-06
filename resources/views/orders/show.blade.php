@extends('layouts.app')

@section('title', 'Detail Pesanan #' . $order->order_number)

@section('styles')
    /* ── Page Header ── */
    .page-header {
        display: flex; align-items: flex-start; justify-content: space-between;
        margin-bottom: 24px; flex-wrap: wrap; gap: 16px;
    }
    .page-header-left h1 {
        font-size: 22px; font-weight: 800; letter-spacing: -0.04em; color: var(--text-1);
        margin-bottom: 5px; display: flex; align-items: center; gap: 12px;
    }
    .page-icon {
        width: 38px; height: 38px;
        background: linear-gradient(135deg, var(--accent), #4f46e5);
        border-radius: 10px; display: flex; align-items: center; justify-content: center; flex-shrink: 0;
        box-shadow: 0 8px 20px rgba(79, 70, 229, 0.15);
    }
    .page-icon svg { width: 20px; height: 20px; color: #fff; }
    .page-header-left p { font-size: 13px; color: var(--text-3); margin-left: 50px; }

    .btn-back {
        display: inline-flex; align-items: center; gap: 8px; padding: 10px 18px;
        border: 1px solid var(--border); border-radius: 10px; background: var(--panel);
        font-size: 13px; font-weight: 600; color: var(--text-2); text-decoration: none; transition: all 0.2s;
    }
    .btn-back:hover { border-color: var(--accent); color: var(--accent); transform: translateX(-3px); }

    /* ── Grid Layout ── */
    .detail-grid {
        display: grid; grid-template-columns: 1fr 340px; gap: 24px; align-items: start;
    }
    @media (max-width: 1024px) { .detail-grid { grid-template-columns: 1fr; } }

    .detail-main { display: flex; flex-direction: column; gap: 24px; }
    .detail-side { display: flex; flex-direction: column; gap: 24px; }

    /* ── Cards ── */
    .detail-card {
        background: var(--panel); border: 1px solid var(--border); border-radius: 16px;
        overflow: hidden; box-shadow: var(--shadow-sm);
    }
    .card-header {
        padding: 18px 22px; border-bottom: 1px solid var(--border);
        display: flex; align-items: center; justify-content: space-between;
        background: linear-gradient(to right, var(--surface), transparent);
    }
    .card-title { font-size: 14px; font-weight: 800; color: var(--text-1); display: flex; align-items: center; gap: 10px; }
    .card-title svg { width: 18px; height: 18px; color: var(--accent); }
    .card-body { padding: 22px; }

    /* ── Info Groups ── */
    .info-row { display: flex; justify-content: space-between; padding: 10px 0; border-bottom: 1px dashed var(--border); }
    .info-row:last-child { border-bottom: none; }
    .info-label { font-size: 12px; font-weight: 600; color: var(--text-3); }
    .info-value { font-size: 13px; font-weight: 700; color: var(--text-1); text-align: right; }

    /* ── Status Badge ── */
    .status-badge {
        display: inline-flex; align-items: center; gap: 6px; padding: 5px 14px; border-radius: 20px;
        font-size: 12px; font-weight: 700;
    }
    .status-badge::before { content: ''; width: 6px; height: 6px; border-radius: 50%; background: currentColor; }
    
    .status-pending    { background: var(--amber-dim); color: var(--amber); }
    .status-perlu_diproses { background: rgba(59,130,246,0.1); color: #3b82f6; }
    .status-processing { background: rgba(139,92,246,0.1); color: #8b5cf6; }
    .status-shipping   { background: var(--accent-dim); color: var(--accent); }
    .status-completed  { background: var(--green-dim); color: var(--green); }
    .status-cancelled  { background: var(--red-dim); color: var(--red); }

    /* ── Product Table ── */
    .product-list { display: flex; flex-direction: column; gap: 16px; }
    .product-item {
        display: flex; align-items: center; gap: 16px; padding: 12px;
        border: 1px solid var(--border); border-radius: 12px; transition: all 0.2s;
    }
    .product-item:hover { border-color: var(--accent); background: var(--accent-dim); }
    .product-img {
        width: 60px; height: 60px; border-radius: 8px; object-fit: cover;
        background: var(--surface-2); border: 1px solid var(--border);
    }
    .product-detail { flex: 1; }
    .product-name { font-size: 13.5px; font-weight: 700; color: var(--text-1); }
    .product-variant { font-size: 11px; color: var(--text-3); margin-top: 2px; }
    .product-price { font-size: 13px; font-weight: 800; color: var(--text-1); margin-top: 4px; }
    .product-qty { font-size: 12px; font-weight: 600; color: var(--text-4); }

    /* ── Timeline ── */
    .timeline { position: relative; padding-left: 32px; display: flex; flex-direction: column; gap: 24px; }
    .timeline::before {
        content: ''; position: absolute; left: 11px; top: 0; bottom: 0;
        width: 2px; background: var(--border);
    }
    .timeline-item { position: relative; }
    .timeline-dot {
        position: absolute; left: -32px; top: 3px; width: 24px; height: 24px;
        border-radius: 50%; background: var(--panel); border: 2px solid var(--border);
        display: flex; align-items: center; justify-content: center; z-index: 1;
    }
    .timeline-dot svg { width: 12px; height: 12px; color: var(--text-4); }
    .timeline-item.active .timeline-dot { border-color: var(--accent); background: var(--accent-dim); }
    .timeline-item.active .timeline-dot svg { color: var(--accent); }
    .timeline-content { }
    .timeline-status { font-size: 13px; font-weight: 800; color: var(--text-1); }
    .timeline-time { font-size: 11px; color: var(--text-3); margin-top: 2px; }
    .timeline-note { font-size: 12px; color: var(--text-2); margin-top: 6px; padding: 8px 12px; background: var(--surface); border-radius: 8px; }

    /* ── Action Box ── */
    .action-box {
        background: var(--accent); padding: 20px; border-radius: 16px; color: #fff;
        display: flex; align-items: center; justify-content: space-between; gap: 20px;
        box-shadow: 0 10px 25px rgba(79, 70, 229, 0.25);
    }
    .action-text h4 { font-size: 15px; font-weight: 800; margin-bottom: 4px; }
    .action-text p { font-size: 12px; opacity: 0.9; }
    .action-form { display: flex; gap: 10px; }
    
    .status-select {
        padding: 8px 14px; border-radius: 8px; border: none; font-family: var(--font);
        font-size: 13px; font-weight: 700; color: var(--text-1); background: #fff; cursor: pointer;
    }
    .btn-update {
        padding: 8px 18px; border-radius: 8px; border: 1.5px solid rgba(255,255,255,0.4);
        background: rgba(255,255,255,0.2); color: #fff; font-size: 13px; font-weight: 700;
        cursor: pointer; transition: all 0.2s;
    }
    .btn-update:hover { background: #fff; color: var(--accent); }

    .alert-banner {
        padding: 14px 18px; border-radius: 12px; margin-bottom: 24px;
        display: flex; align-items: center; gap: 12px; font-size: 13px; font-weight: 600;
    }
    .alert-success { background: var(--green-dim); color: var(--green); border: 1px solid rgba(22,163,74,0.2); }
    .alert-error { background: var(--red-dim); color: var(--red); border: 1px solid rgba(220,38,38,0.2); }
@endsection

@section('content')

    {{-- Page Header --}}
    <div class="page-header">
        <div class="page-header-left">
            <h1>
                <span class="page-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"/><rect x="8" y="2" width="8" height="4" rx="1" ry="1"/></svg>
                </span>
                Detail Pesanan
            </h1>
            <p>Informasi lengkap transaksi #{{ $order->order_number }}</p>
        </div>
        <a href="{{ route('orders.index') }}" class="btn-back">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/></svg>
            Kembali
        </a>
    </div>

    @if(session('success'))
        <div class="alert-banner alert-success">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
            {{ session('success') }}
        </div>
    @endif

    @if($errors->has('stock'))
        <div class="alert-banner alert-error">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
            {{ $errors->first('stock') }}
        </div>
    @endif

    {{-- Main Content --}}
    <div class="detail-grid">
        
        <div class="detail-main">
            
            @if(!in_array($order->status, ['completed', 'cancelled']))
                <div class="action-box">
                    @if($order->status === 'pending')
                        <div class="action-text">
                            <h4>Konfirmasi Pembayaran</h4>
                            <p>Gunakan tombol ini jika pelanggan sudah membayar secara manual atau notifikasi otomatis tertunda.</p>
                        </div>
                        <div style="display: flex; gap: 10px;">
                            <form action="{{ route('orders.update-status', $order) }}" method="POST">
                                @csrf
                                @method('PATCH')
                                <input type="hidden" name="status" value="perlu_diproses">
                                <button type="submit" class="btn-update" style="background:#fff; color:var(--amber); border:none; padding:12px 24px;">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="width:16px;height:16px;margin-right:8px;vertical-align:middle;"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
                                    Tandai Sudah Bayar
                                </button>
                            </form>
                            <form action="{{ route('orders.check-payment-status', $order) }}" method="POST">
                                @csrf
                                <button type="submit" class="btn-update" style="background:rgba(255,255,255,0.15); color:#fff; border:1px solid rgba(255,255,255,0.3); padding:12px 24px;">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="width:16px;height:16px;margin-right:8px;vertical-align:middle;"><polyline points="23 4 23 10 17 10"/><polyline points="1 20 1 14 7 14"/><path d="M3.51 9a9 9 0 0 1 14.85-3.36L23 10M1 14l4.64 4.36A9 9 0 0 0 20.49 15"/></svg>
                                    Cek Status Midtrans
                                </button>
                            </form>
                        </div>
                    @elseif($order->status === 'perlu_diproses')
                        <div class="action-text">
                            <h4>Konfirmasi Pesanan & Proses</h4>
                            <p>Pesanan ini telah dibayar. Klik konfirmasi untuk mulai menyiapkan barang (Dikemas).</p>
                        </div>
                        <form action="{{ route('orders.update-status', $order) }}" method="POST">
                            @csrf
                            @method('PATCH')
                            <input type="hidden" name="status" value="processing">
                            <button type="submit" class="btn-update" style="background:#fff; color:var(--accent); border:none; padding:12px 24px;">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="width:16px;height:16px;margin-right:8px;vertical-align:middle;"><polyline points="20 6 9 17 4 12"/></svg>
                                Konfirmasi & Mulai Kemas
                            </button>
                        </form>
                    @else
                        <div class="action-text">
                            <h4>Update Status Pengerjaan</h4>
                            <p>Ubah status pesanan ini sesuai dengan tahap progres saat ini.</p>
                        </div>
                        <form action="{{ route('orders.update-status', $order) }}" method="POST" class="action-form">
                            @csrf
                            @method('PATCH')
                            <select name="status" class="status-select">
                                <option value="pending" {{ $order->status === 'pending' ? 'selected' : '' }}>Belum Bayar</option>
                                <option value="perlu_diproses" {{ $order->status === 'perlu_diproses' ? 'selected' : '' }}>Perlu Diproses</option>
                                <option value="processing" {{ $order->status === 'processing' ? 'selected' : '' }}>Dikemas</option>
                                <option value="shipping" {{ $order->status === 'shipping' ? 'selected' : '' }}>Dikirim</option>
                                <option value="completed" {{ $order->status === 'completed' ? 'selected' : '' }}>Selesai</option>
                            </select>
                            <button type="submit" class="btn-update">Simpan Perubahan</button>
                        </form>
                    @endif
                </div>
            @endif
            
            {{-- Order Summary --}}
            <div class="detail-card">
                <div class="card-header">
                    <span class="card-title">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/><polyline points="3.27 6.96 12 12.01 20.73 6.96"/><line x1="12" y1="22.08" x2="12" y2="12"/></svg>
                        Rincian Pesanan
                    </span>
                    <span class="status-badge status-{{ $order->status }}">
                        {{ [
                            'pending' => 'Belum Bayar',
                            'perlu_diproses' => 'Perlu Diproses',
                            'processing' => 'Dikemas',
                            'shipping' => 'Dikirim',
                            'completed' => 'Selesai',
                            'cancelled' => 'Dibatalkan'
                        ][$order->status] ?? ucfirst($order->status) }}
                    </span>
                </div>
                <div class="card-body">
                    <div class="product-list">
                        @foreach($order->orderItems as $item)
                            <div class="product-item">
                                <img src="{{ $item->productVariant->product->image ? asset('storage/' . $item->productVariant->product->image) : asset('img/no-image.png') }}" class="product-img">
                                <div class="product-detail">
                                    <div class="product-name">{{ $item->productVariant->product->name }}</div>
                                    <div class="product-variant">Varian: {{ $item->productVariant->name }}</div>
                                    <div class="product-qty">{{ $item->quantity }} x Rp {{ number_format($item->price, 0, ',', '.') }}</div>
                                </div>
                                <div class="product-price">Rp {{ number_format($item->quantity * $item->price, 0, ',', '.') }}</div>
                            </div>
                        @endforeach
                    </div>

                    <div style="margin-top: 24px; padding-top: 20px; border-top: 2px solid var(--surface);">
                        <div class="info-row">
                            <span class="info-label">Subtotal Produk</span>
                            <span class="info-value">Rp {{ number_format($order->orderItems->sum(fn($i) => $i->price * $i->quantity), 0, ',', '.') }}</span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Biaya Pengiriman ({{ ucfirst($order->shipping_type) }})</span>
                            <span class="info-value">Rp {{ number_format($order->shipping_cost, 0, ',', '.') }}</span>
                        </div>
                        <div class="info-row" style="border-bottom: none; margin-top: 8px;">
                            <span class="info-label" style="font-size: 14px; color: var(--text-1);">Total Pembayaran</span>
                            <span class="info-value" style="font-size: 18px; color: var(--accent); letter-spacing: -0.02em;">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Tracking History --}}
            <div class="detail-card">
                <div class="card-header">
                    <span class="card-title">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                        Riwayat Pelacakan
                    </span>
                </div>
                <div class="card-body">
                    <div class="timeline">
                        @forelse($order->trackingHistories as $history)
                            <div class="timeline-item {{ $loop->first ? 'active' : '' }}">
                                <div class="timeline-dot">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg>
                                </div>
                                <div class="timeline-content">
                                    <div class="timeline-status">
                                        {{ [
                                            'pending' => 'Belum Bayar',
                                            'perlu_diproses' => 'Perlu Diproses',
                                            'processing' => 'Dikemas',
                                            'shipping' => 'Dikirim',
                                            'completed' => 'Selesai',
                                            'cancelled' => 'Dibatalkan'
                                        ][$history->status] ?? ucfirst($history->status) }}
                                    </div>
                                    <div class="timeline-time">{{ $history->created_at->format('d M Y, H:i') }} WIB &middot; Oleh {{ $history->admin->name ?? 'System' }}</div>
                                    @if($history->notes)
                                        <div class="timeline-note">{{ $history->notes }}</div>
                                    @endif
                                </div>
                            </div>
                        @empty
                            <p style="text-align: center; color: var(--text-4); font-size: 13px; padding: 20px 0;">Belum ada riwayat pelacakan.</p>
                        @endforelse
                    </div>
                </div>
            </div>

        </div>

        <div class="detail-side">
            
            {{-- Customer Card --}}
            <div class="detail-card">
                <div class="card-header">
                    <span class="card-title">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                        Informasi Pelanggan
                    </span>
                </div>
                <div class="card-body">
                    <div class="info-row">
                        <span class="info-label">Nama</span>
                        <span class="info-value">{{ $order->customer_name }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">WhatsApp</span>
                        <span class="info-value">{{ $order->customer_phone }}</span>
                    </div>
                    <div style="margin-top: 16px;">
                        <div class="info-label" style="margin-bottom: 8px;">Alamat Pengiriman</div>
                        <div style="font-size: 13px; color: var(--text-2); line-height: 1.6; background: var(--surface); padding: 12px; border-radius: 10px; border: 1px solid var(--border);">
                            {{ $order->shipping_address }}
                        </div>
                    </div>
                </div>
            </div>

            {{-- Store Card --}}
            <div class="detail-card">
                <div class="card-header">
                    <span class="card-title">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
                        Informasi Toko
                    </span>
                </div>
                <div class="card-body">
                    <div class="info-row">
                        <span class="info-label">Nama Toko</span>
                        <span class="info-value">{{ $order->store->name ?? 'Toko Pusat' }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Kontak</span>
                        <span class="info-value">{{ $order->store->phone ?? '-' }}</span>
                    </div>
                </div>
            </div>
            @if($order->status === 'cancelled')
                <div class="detail-card" style="border-color: var(--red);">
                    <div class="card-header" style="background: var(--red-dim);">
                        <span class="card-title" style="color: var(--red);">Alasan Pembatalan</span>
                    </div>
                    <div class="card-body">
                        <div style="font-size: 13px; color: var(--red); font-weight: 600; line-height: 1.6;">
                            {{ $order->cancel_reason ?? 'Tidak ada alasan spesifik.' }}
                        </div>
                    </div>
                </div>
            @endif

        </div>

    </div>

@endsection
