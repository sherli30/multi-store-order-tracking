@if($orders->isEmpty())
    <div class="empty-state">
        <div class="empty-icon">
            <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" style="color:var(--text-4);">
                <line x1="16.5" y1="9.4" x2="7.5" y2="4.21"/>
                <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/>
                <polyline points="3.27 6.96 12 12.01 20.73 6.96"/>
                <line x1="12" y1="22.08" x2="12" y2="12"/>
            </svg>
        </div>
        <div class="empty-title">Tidak ada pesanan ditemukan</div>
        <div class="empty-desc">Coba ubah filter atau kata kunci pencarian Anda.</div>
    </div>

@else
    <div style="overflow-x: auto;">
        <table class="order-table">
            <thead>
                <tr>
                    <th style="width:48px; text-align:center;">#</th>
                    <th>ID Pesanan</th>
                    <th>Customer</th>
                    <th>Tanggal</th>
                    <th>Pengiriman</th>
                    <th style="text-align:right; padding-right:20px;">Total</th>
                    <th>Status</th>
                    <th style="width:56px; text-align:center;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $startIndex = ($orders->currentPage() - 1) * $orders->perPage() + 1;
                    $statusLabel = [
                        'pending'    => 'Menunggu',
                        'processing' => 'Dikemas',
                        'shipping'   => 'Dikirim',
                        'completed'  => 'Selesai',
                        'cancelled'  => 'Dibatalkan',
                    ];
                @endphp

                @foreach($orders as $order)
                <tr>
                    {{-- No --}}
                    <td style="text-align:center; color:var(--text-4); font-size:12px; font-weight:600;">
                        {{ $startIndex + $loop->index }}
                    </td>

                    {{-- ID Pesanan --}}
                    <td>
                        <div class="order-id">{{ $order->order_number }}</div>
                        <div class="order-store">{{ $order->store->name ?? '—' }}</div>
                    </td>

                    {{-- Customer --}}
                    <td>
                        <div class="customer-name">{{ $order->customer_name }}</div>
                        <div class="customer-sub">
                            {{ $order->orderItems->count() }} produk &middot; {{ $order->orderItems->sum('quantity') }} unit
                        </div>
                    </td>

                    {{-- Tanggal --}}
                    <td style="white-space:nowrap;">
                        <div style="font-size:13px; font-weight:500; color:var(--text-1);">
                            {{ $order->created_at->format('d M Y') }}
                        </div>
                        <div style="font-size:11.5px; color:var(--text-3);">
                            {{ $order->created_at->format('H:i') }} WIB
                        </div>
                    </td>

                    {{-- Pengiriman --}}
                    <td>
                        <span class="shipping-badge shipping-{{ $order->shipping_type }}">
                            {{ ucfirst($order->shipping_type) }}
                        </span>
                    </td>

                    {{-- Total --}}
                    <td style="text-align:right; padding-right:20px;">
                        <div class="amount-total">
                            Rp {{ number_format($order->total_amount, 0, ',', '.') }}
                        </div>
                    </td>

                    {{-- Status --}}
                    <td>
                        <span class="status {{ $order->status }}">
                            {{ $statusLabel[$order->status] ?? ucfirst($order->status) }}
                        </span>
                    </td>

                    {{-- Aksi --}}
                    <td style="text-align:center;">
                        <a href="{{ route('orders.show', $order) }}" class="action-btn" title="Lihat Detail">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                                <circle cx="12" cy="12" r="3"/>
                            </svg>
                        </a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    <div class="pagination-wrap">
        <div class="pagination-info">
            @if($orders->hasPages())
                Menampilkan
                <strong>{{ $orders->firstItem() }}</strong>–<strong>{{ $orders->lastItem() }}</strong>
                dari <strong>{{ $orders->total() }}</strong> pesanan
            @else
                <strong>{{ $orders->total() }}</strong> pesanan ditemukan
            @endif
        </div>

        @if($orders->hasPages())
        <div class="pagination-links">
            {{-- Prev --}}
            @if($orders->onFirstPage())
                <span class="disabled">‹ Prev</span>
            @else
                <a href="{{ $orders->previousPageUrl() }}">‹ Prev</a>
            @endif

            {{-- Numbered pages --}}
            @php
                $current = $orders->currentPage();
                $last    = $orders->lastPage();
                $start   = max(1, $current - 2);
                $end     = min($last, $current + 2);
            @endphp

            @if($start > 1)
                <a href="{{ $orders->url(1) }}">1</a>
                @if($start > 2)
                    <span style="border:none; background:none; color:var(--text-3); cursor:default;">…</span>
                @endif
            @endif

            @for($p = $start; $p <= $end; $p++)
                @if($p === $current)
                    <span class="active-page">{{ $p }}</span>
                @else
                    <a href="{{ $orders->url($p) }}">{{ $p }}</a>
                @endif
            @endfor

            @if($end < $last)
                @if($end < $last - 1)
                    <span style="border:none; background:none; color:var(--text-3); cursor:default;">…</span>
                @endif
                <a href="{{ $orders->url($last) }}">{{ $last }}</a>
            @endif

            {{-- Next --}}
            @if($orders->hasMorePages())
                <a href="{{ $orders->nextPageUrl() }}">Next ›</a>
            @else
                <span class="disabled">Next ›</span>
            @endif
        </div>
        @endif
    </div>

@endif
