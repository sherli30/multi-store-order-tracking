<div class="modal-header" style="padding: 20px 24px; border-bottom: 1px solid var(--border); display: flex; justify-content: space-between; align-items: flex-start; background: linear-gradient(to bottom, var(--surface), var(--panel));">
    <div>
        <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 4px;">
            <span style="font-size: 11px; font-weight: 800; color: var(--accent); background: var(--accent-dim); padding: 2px 8px; border-radius: 4px; text-transform: uppercase; border: 1px solid color-mix(in srgb, var(--accent) 20%, transparent);">Nomor Pesanan</span>
            <h3 style="margin: 0; font-size: 16px; font-weight: 900; color: var(--text-1); letter-spacing: -0.02em;">{{ $order->order_number }}</h3>
        </div>
        <p style="margin: 0; font-size: 13px; color: var(--text-3); font-weight: 500;">
            {{ $order->store->name ?? 'Toko Utama' }} &bull; {{ $order->customer_name }}
        </p>
    </div>
    <button onclick="closeTrackingModal()" style="background: var(--surface-2); border: 1px solid var(--border); cursor: pointer; color: var(--text-3); width: 32px; height: 32px; border-radius: 8px; display: flex; align-items: center; justify-content: center; transition: all 0.2s; flex-shrink: 0;" onmouseover="this.style.borderColor='var(--red)';this.style.color='var(--red)';" onmouseout="this.style.borderColor='var(--border)';this.style.color='var(--text-3)';">
        <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
    </button>
</div>

<div class="modal-body" style="padding: 0; max-height: 70vh; overflow-y: auto;">

    {{-- Courier Summary Card --}}
    <div style="padding: 20px 24px; border-bottom: 1px solid var(--border); background: var(--surface);">
        <div style="background: var(--panel); border: 1px solid var(--border); border-radius: 14px; padding: 16px 18px; display: flex; align-items: center; justify-content: space-between; box-shadow: var(--shadow-sm); gap: 16px; flex-wrap: wrap;">
            <div style="display: flex; align-items: center; gap: 14px;">
                <div style="width: 44px; height: 44px; background: linear-gradient(135deg, var(--accent), color-mix(in srgb, var(--accent) 70%, #7c3aed)); color: #fff; border-radius: 12px; display: flex; align-items: center; justify-content: center; box-shadow: 0 4px 12px var(--accent-glow); flex-shrink: 0;">
                    <svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="2.5"><rect x="1" y="3" width="15" height="13"/><polygon points="16 8 20 8 23 11 23 16 16 16 16 8"/><circle cx="5.5" cy="18.5" r="2.5"/><circle cx="18.5" cy="18.5" r="2.5"/></svg>
                </div>
                <div>
                    <div style="font-size: 10.5px; font-weight: 800; color: var(--text-4); text-transform: uppercase; letter-spacing: 0.06em; margin-bottom: 3px;">Layanan Kurir</div>
                    <div style="font-size: 15px; font-weight: 900; color: var(--text-1);">{{ strtoupper($order->shipping_courier ?? 'Reguler') }}</div>
                </div>
            </div>
            @if($order->tracking_number)
                <div style="text-align: right;">
                    <div style="font-size: 10.5px; font-weight: 800; color: var(--text-4); text-transform: uppercase; letter-spacing: 0.06em; margin-bottom: 3px;">Nomor Resi</div>
                    <div style="font-size: 13px; font-weight: 800; color: var(--accent); font-family: var(--mono); background: var(--accent-dim); padding: 4px 10px; border-radius: 8px; border: 1px solid color-mix(in srgb, var(--accent) 20%, transparent);">{{ $order->tracking_number }}</div>
                </div>
            @else
                <div style="text-align: right;">
                    <span style="font-size: 11.5px; color: var(--text-4); font-style: italic; font-weight: 600;">Resi belum diinput</span>
                </div>
            @endif
        </div>
    </div>

    {{-- Timeline --}}
    <div style="padding: 24px;">
        <div style="font-size: 11px; font-weight: 800; color: var(--text-3); text-transform: uppercase; letter-spacing: 0.08em; margin-bottom: 20px;">Riwayat Status Logistik</div>

        <div style="position: relative; padding-left: 44px;">
            {{-- Vertical line --}}
            <div style="position: absolute; left: 20px; top: 10px; bottom: 10px; width: 2px; background: var(--border); z-index: 1;"></div>

            @php
                $timelineStatuses = ['perlu_diproses', 'processing', 'shipping', 'completed', 'cancelled', 'refunded', 'pending'];
                $histories = $order->trackingHistories()
                    ->whereIn('status', $timelineStatuses)
                    ->latest()
                    ->get();
            @endphp

            @forelse($histories as $index => $h)
                @php $isLatest = $index === 0; @endphp
                <div style="position: relative; margin-bottom: {{ $isLatest ? '0' : '28px' }}; animation: slideIn 0.3s ease forwards; animation-delay: {{ $index * 0.07 }}s; opacity: 0;">

                    {{-- Dot --}}
                    <div style="position: absolute; left: -34px; top: 2px; width: 22px; height: 22px; border-radius: 50%; background: var(--panel); border: 2px solid {{ $isLatest ? 'var(--accent)' : 'var(--border-2)' }}; z-index: 2; display: flex; align-items: center; justify-content: center; box-shadow: 0 0 0 3px var(--panel);">
                        @if($isLatest)
                            <div style="width: 9px; height: 9px; border-radius: 50%; background: var(--accent);"></div>
                        @else
                            <div style="width: 6px; height: 6px; border-radius: 50%; background: var(--border-2);"></div>
                        @endif
                    </div>

                    {{-- Content --}}
                    <div style="background: {{ $isLatest ? 'var(--panel)' : 'var(--surface)' }}; border: 1px solid {{ $isLatest ? 'var(--accent)' : 'var(--border)' }}; border-radius: 12px; padding: 14px 16px; {{ $isLatest ? 'box-shadow: var(--shadow-sm);' : '' }}">
                        <div style="display: flex; align-items: flex-start; justify-content: space-between; gap: 10px; flex-wrap: wrap; margin-bottom: 6px;">
                            <div style="font-size: 13px; font-weight: {{ $isLatest ? '800' : '700' }}; color: {{ $isLatest ? 'var(--text-1)' : 'var(--text-2)' }};">
                                {{ \App\Services\StatusService::getOrderLabel($h->status ?? '') }}
                            </div>
                            <div style="font-size: 11px; font-weight: 600; color: var(--text-4); background: var(--surface-2); padding: 2px 8px; border-radius: 5px; white-space: nowrap; flex-shrink: 0;">
                                {{ $h->created_at->translatedFormat('d M Y, H:i') }}
                            </div>
                        </div>

                        <div style="font-size: 12.5px; line-height: 1.6; color: {{ $isLatest ? 'var(--text-2)' : 'var(--text-3)' }}; font-weight: 500; margin-bottom: 8px;">
                            @if($h->notes)
                                {{ $h->notes }}
                            @else
                                {{ match($h->status) {
                                    'pending'        => 'Pesanan berhasil dibuat oleh customer.',
                                    'perlu_diproses' => 'Pesanan menunggu untuk diproses oleh administrator.',
                                    'processing'     => 'Barang sedang dipersiapkan dan dikemas untuk pengiriman.',
                                    'shipping'       => 'Pesanan telah diserahkan ke kurir untuk dikirim ke alamat tujuan.',
                                    'completed'      => 'Pesanan telah sampai dan diterima oleh customer.',
                                    'cancelled'      => 'Pesanan dibatalkan.',
                                    'refunded'       => 'Pesanan dikembalikan dan dana direfund.',
                                    default          => 'Status diperbarui oleh sistem.'
                                } }}
                            @endif
                        </div>

                        <div style="display: flex; align-items: center; gap: 6px;">
                            <div style="width: 18px; height: 18px; border-radius: 50%; background: var(--surface-2); border: 1px solid var(--border); display: flex; align-items: center; justify-content: center; font-size: 9px; font-weight: 800; color: var(--text-3); flex-shrink: 0;">
                                {{ substr($h->admin->name ?? 'S', 0, 1) }}
                            </div>
                            <span style="font-size: 11px; color: var(--text-4); font-weight: 600;">{{ $h->admin->name ?? 'Sistem Otomatis' }}</span>
                        </div>
                    </div>
                </div>
            @empty
                <div style="text-align: center; padding: 40px 0;">
                    <svg viewBox="0 0 24 24" width="40" height="40" stroke="currentColor" stroke-width="1.5" fill="none" style="color: var(--text-4); opacity: 0.4; margin-bottom: 12px; display: block; margin-left: auto; margin-right: auto;"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                    <p style="color: var(--text-4); font-size: 13px; font-style: italic; margin: 0;">Belum ada riwayat aktivitas yang tercatat.</p>
                </div>
            @endforelse
        </div>
    </div>
</div>

<div class="modal-footer" style="padding: 16px 24px; border-top: 1px solid var(--border); background: var(--surface); display: flex; justify-content: space-between; align-items: center; gap: 12px; flex-wrap: wrap;">
    <div style="font-size: 11.5px; color: var(--text-4); font-weight: 600; display: flex; align-items: center; gap: 5px;">
        <svg viewBox="0 0 24 24" width="12" fill="none" stroke="currentColor" stroke-width="2.5" style="display: inline-block;"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
        Waktu lokal server
    </div>
    <div style="display: flex; gap: 10px;">
        <button type="button" onclick="closeTrackingModal()" style="padding: 9px 18px; border-radius: 9px; border: 1px solid var(--border); background: var(--panel); color: var(--text-2); font-size: 13px; font-weight: 700; cursor: pointer; transition: all 0.15s; font-family: var(--font);">Tutup</button>
        <a href="{{ route('orders.show', $order) }}" style="display: inline-flex; align-items: center; gap: 7px; padding: 9px 18px; border-radius: 9px; background: var(--accent); color: #fff; font-size: 13px; font-weight: 700; text-decoration: none; transition: all 0.15s; box-shadow: 0 2px 8px color-mix(in srgb, var(--accent) 30%, transparent);">
            <svg viewBox="0 0 24 24" width="13" height="13" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/><polyline points="15 3 21 3 21 9"/><line x1="10" y1="14" x2="21" y2="3"/></svg>
            Detail Penuh
        </a>
    </div>
</div>

<style>
    @keyframes slideIn {
        from { transform: translateX(8px); opacity: 0; }
        to   { transform: translateX(0);   opacity: 1; }
    }
</style>
