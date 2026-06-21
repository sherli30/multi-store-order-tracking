@foreach($orders as $order)
<tr data-order-id="{{ $order->id }}">

    {{-- No --}}
    <td class="cell-no"></td>

    {{-- Nomor Pesanan --}}
    <td>
    <div class="product-info">
            <div class="product-thumb">
                @php
                    $firstItem = $order->orderItems->first();
                    $product = $firstItem?->product;
                @endphp
                @if($product && $product->image)
                    <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $order->order_number }}">
                @else
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path>
                        <polyline points="3.27 6.96 12 12.01 20.73 6.96"></polyline>
                        <line x1="12" y1="22.08" x2="12" y2="12"></line>
                    </svg>
                @endif
            </div>
            <div>
                <div class="product-name" style="font-size: 11px;">{{ $order->order_number }}</div>
                <div class="product-slug" style="font-family: inherit;">{{ ucfirst($order->shipping_type) }}</div>
            </div>
        </div>
    </td>

    {{-- Customer --}}
    <td>
        <div class="product-name">{{ $order->customer_name }}</div>
        <div class="product-slug" style="font-family: inherit;">{{ $order->customer_phone }}</div>
    </td>

    {{-- Toko --}}
    <td>
        <span class="store-badge">
            {{ $order->store->name ?? 'Toko Pusat' }}
        </span>
    </td>

    {{-- Tanggal Pesanan--}}
    <td>
        <div style="font-weight: 700; color: var(--text-1); font-size: 13px;">{{ $order->created_at->format('d M Y') }}</div>
        <div style="font-size: 11px; color: var(--text-3); margin-top: 2px;">{{ $order->created_at->format('H:i') }} WIB</div>
    </td>

    {{-- Total Bayar --}}
    <td>
        <div class="price-value">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</div>
    </td>

    {{-- Status --}}
    <td>
        <span class="badge badge-{{ $order->status }}">
            {{ $order->status_label }}
        </span>
    </td>

    {{-- Aksi --}}
    <td>
        <div class="actions-cell" style="justify-content: flex-end; gap: 8px;">
            {{-- 1. Tombol Aksi Cepat (Langsung Modal) --}}
            @if($order->status === 'menunggu_konfirmasi_admin')
                <button type="button" class="btn-action-primary green"
                        onclick="openConfirmModal('{{ $order->id }}', '{{ $order->order_number }}', 'perlu_diproses', 'Konfirmasi Pesanan', 'Terima pesanan dan teruskan ke toko untuk diproses?')">
                    Konfirmasi
                </button>
            @elseif($order->status === \App\Models\Order::STATUS_PERLU_DIPROSES)
                <button type="button" class="btn-action-primary blue"
                        onclick="openConfirmModal('{{ $order->id }}', '{{ $order->order_number }}', 'processing', 'Siapkan Pesanan', 'Konfirmasi untuk mulai mengemas produk dan mengurangi stok?')">
                    Proses
                </button>
            @elseif($order->status === \App\Models\Order::STATUS_PROCESSING)
                <button type="button" class="btn-action-primary purple"
                        onclick="openTrackingModalDirect('{{ $order->id }}', '{{ $order->order_number }}')">
                    Input Resi
                </button>
            @elseif($order->status === \App\Models\Order::STATUS_SHIPPING)
                <button type="button" class="btn-action-primary green"
                        onclick="openConfirmModal('{{ $order->id }}', '{{ $order->order_number }}', 'completed', 'Selesaikan Pesanan', 'Pastikan barang sudah sampai ke tangan customer?')">
                    Selesai
                </button>
            @elseif($order->status === \App\Models\Order::STATUS_COMPLETED && $order->return_status === 'requested')
                <button type="button" class="btn-action-primary amber"
                        onclick="openReturnModal('{{ $order->id }}', '{{ $order->order_number }}', '{{ addslashes($order->return_reason) }}')">
                    Tinjau Retur
                </button>
            @endif



            {{-- 3. Tombol Detail (View Only) --}}
            <a href="{{ route('orders.show', $order) }}" class="btn-icon-only" title="Lihat Detail Lengkap">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7z"/><circle cx="12" cy="12" r="3"/></svg>
            </a>
        </div>
    </td>
</tr>
@endforeach
