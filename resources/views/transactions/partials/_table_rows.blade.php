@foreach($transactions as $trx)
<tr data-trx-id="{{ $trx->id }}">
    {{-- No --}}
    <td class="cell-no"></td>

    {{-- ID Transaction --}}
    <td>
        <div class="trx-id">{{ $trx->transaction_id }}</div>
        <div class="trx-date">{{ $trx->created_at->setTimezone('Asia/Jakarta')->format('d M Y, H:i') }} WIB</div>
        @if($trx->payment_method)
        <div class="payment-method">Method: {{ strtoupper($trx->payment_method) }}</div>
        @endif
    </td>

    {{-- Nomor Pesanan --}}
    <td>
        @if($trx->invoice)
            <div class="trx-id">{{ $trx->invoice->invoice_number ?? $trx->invoice->midtrans_order_id }}</div>
            <div class="trx-date">{{ $trx->invoice->created_at->format('d M Y') }}</div>
            <div style="margin-top:4px;">
            @foreach($trx->invoice->orders as $invOrder)
                <a href="{{ route('orders.show', $invOrder) }}" class="order-link" style="display:block;">
                    {{ $invOrder->order_number }}
                </a>
            @endforeach
            </div>
        @elseif($trx->order)
            <a href="{{ route('orders.show', $trx->order) }}" class="order-link">
                {{ $trx->order->order_number }}
            </a>
            <div class="trx-date">{{ $trx->order->created_at->format('d M Y') }}</div>
        @else
            <span style="color:var(--text-4); font-size:11px; font-style:italic;">[Pesanan Dihapus]</span>
        @endif
    </td>

    {{-- Customer / Toko --}}
    <td>
        @if($trx->invoice)
            <div class="customer-name">{{ $trx->invoice->user?->name ?? '—' }}</div>
            <div class="store-info">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="width:10px;height:10px;">
                    <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z" />
                    <polyline points="9 22 9 12 15 12 15 22" />
                </svg>
                Multi-Toko ({{ $trx->invoice->orders->count() }} Pesanan)
            </div>
        @elseif($trx->order)
            <div class="customer-name">{{ $trx->order->customer_name ?? '—' }}</div>
            <div class="store-info">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="width:10px;height:10px;">
                    <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z" />
                    <polyline points="9 22 9 12 15 12 15 22" />
                </svg>
                {{ $trx->order->store->name ?? '—' }}
            </div>
        @else
            <div class="customer-name">—</div>
        @endif
    </td>

    {{-- Total Bayar --}}
    <td>
        <div class="amount-val">Rp {{ number_format($trx->amount, 0, ',', '.') }}</div>
        <div class="trx-date">Total Tagihan</div>
    </td>

    {{-- Status --}}
    <td>
        <span class="badge badge-{{ $trx->status }}">
            {{ $trx->status_label }}
        </span>
        @if($trx->payment_date)
        <div class="trx-date" style="font-weight: 600; color: {{ $trx->status === 'paid' ? 'var(--green)' : 'var(--text-3)' }};">
            Dibayar: {{ $trx->payment_date->setTimezone('Asia/Jakarta')->format('d/m H:i') }}
        </div>
        @elseif($trx->refunded_at)
        <div class="trx-date" style="font-weight: 600; color: var(--red);">
            {{ $trx->status === 'refund' ? 'Dana Dikembalikan' : 'Gagal' }}: {{ $trx->refunded_at->setTimezone('Asia/Jakarta')->format('d/m H:i') }}
        </div>
        @endif
    </td>

    {{-- Aksi --}}
    <td>
        <div class="actions-cell">
            {{-- Lihat Detail --}}
            <a href="{{ route('transactions.show', $trx) }}" class="btn-sm" title="Lihat Detail">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z" />
                    <circle cx="12" cy="12" r="3" />
                </svg>
                Detail
            </a>

        </div>
    </td>
</tr>
@endforeach
