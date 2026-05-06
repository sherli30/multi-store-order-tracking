@foreach($orders as $order)
<tr data-order-id="{{ $order->id }}">

    {{-- No --}}
    <td class="cell-no"></td>

    {{-- ID Pesanan --}}
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
                <div class="product-name">#{{ $order->order_number }}</div>
                <div class="product-slug" style="font-family: inherit;">{{ ucfirst($order->shipping_type) }}</div>
            </div>
        </div>
    </td>

    {{-- Pelanggan --}}
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

    {{-- Waktu --}}
    <td>
        <div style="font-weight: 700; color: var(--text-1); font-size: 13px;">{{ $order->created_at->format('d M Y') }}</div>
        <div style="font-size: 11px; color: var(--text-3); margin-top: 2px;">{{ $order->created_at->format('H:i') }} WIB</div>
    </td>

    {{-- Total Harga --}}
    <td>
        <div class="price-value">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</div>
    </td>

    {{-- Status --}}
    <td>
        <span class="badge badge-{{ $order->status }}">
            {{ [
                'pending' => 'Belum Bayar',
                'perlu_diproses' => 'Perlu Diproses',
                'processing' => 'Dikemas',
                'shipping' => 'Dikirim',
                'completed' => 'Selesai',
                'cancelled' => 'Dibatalkan'
            ][$order->status] ?? ucfirst($order->status) }}
        </span>
    </td>

    {{-- Aksi --}}
    <td>
        <div class="actions-cell">
            {{-- Tahap 1: Konfirmasi Pembayaran (Hanya untuk status 'pending') --}}
            @if($order->status === 'pending')
                <form action="{{ route('orders.update-status', $order) }}" method="POST" style="display: inline;">
                    @csrf
                    @method('PATCH')
                    <input type="hidden" name="status" value="perlu_diproses">
                    <button type="submit" class="btn-sm" style="background: var(--amber); color: #fff; border: none;" title="Tandai Sudah Bayar">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <line x1="12" y1="1" x2="12" y2="23"></line>
                            <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path>
                        </svg>
                    </button>
                </form>
            @endif

            {{-- Tahap 2: Konfirmasi Pesanan & Proses (Hanya untuk status 'perlu_diproses') --}}
            @if($order->status === 'perlu_diproses')
                <form action="{{ route('orders.update-status', $order) }}" method="POST" style="display: inline;">
                    @csrf
                    @method('PATCH')
                    <input type="hidden" name="status" value="processing">
                    <button type="submit" class="btn-sm" style="background: var(--accent); color: #fff; border: none;" title="Konfirmasi & Proses">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="20 6 9 17 4 12"></polyline>
                        </svg>
                    </button>
                </form>
            @endif

            {{-- Cek Status Midtrans (Hanya untuk status 'pending') --}}
            @if($order->status === 'pending')
                <form action="{{ route('orders.check-payment-status', $order) }}" method="POST" style="display: inline;">
                    @csrf
                    <button type="submit" class="btn-sm" style="background: var(--surface-2); color: var(--text-2); border: 1px solid var(--border);" title="Cek Status Midtrans">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="23 4 23 10 17 10"></polyline>
                            <polyline points="1 20 1 14 7 14"></polyline>
                            <path d="M3.51 9a9 9 0 0 1 14.85-3.36L23 10M1 14l4.64 4.36A9 9 0 0 0 20.49 15"></path>
                        </svg>
                    </button>
                </form>
            @endif

            {{-- View Detail --}}
            <a href="{{ route('orders.show', $order) }}" class="btn-sm" title="Detail Pesanan">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7z"></path>
                    <circle cx="12" cy="12" r="3"></circle>
                </svg>
            </a>

            {{-- Update Status (Modal Trigger) --}}
            @if(!in_array($order->status, ['completed', 'cancelled']))
                <button type="button" class="btn-sm" title="Update Status" 
                        onclick="openStatusModal('{{ $order->id }}', '{{ $order->order_number }}', '{{ $order->status }}')">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M12 2v4M12 18v4M4.93 4.93l2.83 2.83M16.24 16.24l2.83 2.83M2 12h4M18 12h4M4.93 19.07l2.83-2.83M16.24 7.76l2.83-2.83"></path>
                    </svg>
                </button>
            @endif

            {{-- Cancel Order (Modal Trigger) --}}
            @if(!in_array($order->status, ['completed', 'cancelled']))
                <button type="button" class="btn-sm danger" title="Batalkan Pesanan" 
                        onclick="openCancelModal('{{ $order->id }}', '{{ $order->order_number }}')">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10"></circle>
                        <line x1="15" y1="9" x2="9" y2="15"></line>
                        <line x1="9" y1="9" x2="15" y2="15"></line>
                    </svg>
                </button>
            @endif
        </div>
    </td>
</tr>
@endforeach
