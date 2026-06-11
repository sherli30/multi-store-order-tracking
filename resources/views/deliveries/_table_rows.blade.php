@foreach($orders as $order)
<tr data-order-id="{{ $order->id }}">

    {{-- No --}}
    <td class="cell-no"></td>

    {{-- Pesanan & Toko --}}
    <td>
        <a href="{{ route('orders.show', $order) }}" class="order-id" style="text-decoration:none; color:inherit;" title="Lihat Detail Pesanan">{{ $order->order_number }}</a>
        <div class="store-tag">{{ $order->store->name ?? '-' }}</div>
    </td>

    {{-- Logistik & Kurir --}}
    <td>
        <div style="display: flex; align-items: center; gap: 10px;">
            <div style="width: 28px; height: 28px; background: var(--surface-2); border-radius: 8px; display: flex; align-items: center; justify-content: center; color: var(--text-3); border: 1px solid var(--border); flex-shrink: 0;">
                <svg viewBox="0 0 24 24" width="14" fill="none" stroke="currentColor" stroke-width="2.5"><rect x="1" y="3" width="15" height="13"/><polygon points="16 8 20 8 23 11 23 16 16 16 16 8"/><circle cx="5.5" cy="18.5" r="2.5"/><circle cx="18.5" cy="18.5" r="2.5"/></svg>
            </div>
            <div>
                <div style="font-weight: 700; font-size: 13px; color: var(--text-1);">{{ strtoupper($order->shipping_courier ?? 'Manual') }}</div>
                <div style="font-size: 11px; color: var(--text-4); margin-top: 1px;">{{ ucfirst($order->shipping_type) }}</div>
            </div>
        </div>
    </td>

    {{-- Nomor Resi --}}
    <td>
        @if($order->tracking_number)
            <span class="resi-val">{{ $order->tracking_number }}</span>
        @else
            <span style="color: var(--text-4); font-size: 11.5px; font-style: italic;">Belum diinput</span>
        @endif
    </td>

    {{-- Update Terakhir --}}
    <td>
        @php $lastH = $order->trackingHistories->first(); @endphp
        <div class="last-update">
            {{ $lastH ? $lastH->created_at->translatedFormat('d M Y, H:i') : $order->updated_at->translatedFormat('d M Y, H:i') }}
        </div>
        <div class="update-admin">Oleh: {{ $lastH->admin->name ?? 'Sistem' }}</div>
    </td>

    {{-- Status --}}
    <td>
        <span class="badge badge-{{ $order->status }}">
            {{ \App\Services\StatusService::getOrderLabel($order->status ?? '') }}
        </span>
    </td>

    {{-- Aksi --}}
    <td class="center">
        <div class="actions-cell">
            <button
                type="button"
                class="btn-icon-only"
                title="Riwayat Tracking"
                onclick="openTrackingModal('{{ route('deliveries.tracking-modal', $order) }}')"
            >
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
            </button>
            <a href="{{ route('orders.show', $order) }}" class="btn-icon-only" title="Detail Pesanan">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/><polyline points="15 3 21 3 21 9"/><line x1="10" y1="14" x2="21" y2="3"/></svg>
            </a>
        </div>
    </td>

</tr>
@endforeach
