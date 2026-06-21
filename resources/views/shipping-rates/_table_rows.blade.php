@foreach($rates as $rate)
<tr>
    <td class="cell-no" style="text-align: center; font-weight: 600; color: var(--text-4); font-size: 12.5px;"></td>
    <td>
        <span class="service-info">{{ $rate->service->courier->name }}</span>
        <span class="service-badge">{{ $rate->service->service_name }}</span>
        @if(!$rate->service->is_active || ($rate->service->courier && !$rate->service->courier->is_active))
            <span class="service-badge-inactive" style="display: inline-flex; align-items: center; gap: 4px; padding: 2px 8px; border-radius: 6px; font-size: 11px; font-weight: 700; background: rgba(220, 38, 38, 0.1); color: var(--red); border: 1px solid rgba(220, 38, 38, 0.2); margin-top: 4px;">
                <svg viewBox="0 0 24 24" width="11" height="11" fill="none" stroke="currentColor" stroke-width="2.5" style="flex-shrink:0;"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path><line x1="12" y1="9" x2="12" y2="13"></line><line x1="12" y1="17" x2="12.01" y2="17"></line></svg>
                Non-aktif
            </span>
        @endif
    </td>
    <td>
        <div class="route-info">
            <div class="origin-dot route-dot"></div>
            <span style="font-weight:600; color: var(--text-1);">{{ $rate->originCity->full_name }}</span>
        </div>
        <div class="route-info" style="margin-top:6px;">
            <div class="dest-dot route-dot"></div>
            <span style="font-weight:600; color: var(--text-1);">{{ $rate->destinationCity->full_name }}</span>
        </div>
    </td>
    <td>
        <div class="cost-value">Rp {{ number_format($rate->cost_per_kg, 0, ',', '.') }}</div>
        <div class="cost-unit">per kg</div>
        <div style="font-size: 11px; color: var(--text-3); font-weight: 600; margin-top: 4px;">{{ number_format($rate->min_weight, 2, ',', '.') }} kg - {{ $rate->max_weight ? number_format($rate->max_weight, 2, ',', '.') . ' kg' : '∞' }}</div>
    </td>
    <td>
        <div class="etd-badge">
            <svg viewBox="0 0 24 24" width="12" height="12" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
            {{ $rate->etd_min }}-{{ $rate->etd_max }} Hari
        </div>
    </td>
    <td>
        @if($rate->is_active)
            <span class="badge badge-active" style="display: inline-flex; align-items: center; gap: 5px; padding: 4px 11px; border-radius: 20px; font-size: 11.5px; font-weight: 600; white-space: nowrap; background: var(--green-dim); color: var(--green); border: 1px solid rgba(22,163,74,0.2);"><span style="width: 6px; height: 6px; border-radius: 50%; background: var(--green);"></span>Aktif</span>
        @else
            <span class="badge badge-inactive" style="display: inline-flex; align-items: center; gap: 5px; padding: 4px 11px; border-radius: 20px; font-size: 11.5px; font-weight: 600; white-space: nowrap; background: var(--surface-2); color: var(--text-3); border: 1px solid var(--border);"><span style="width: 6px; height: 6px; border-radius: 50%; background: var(--text-4);"></span>Nonaktif</span>
        @endif
    </td>
    <td>
        <div class="actions-cell">
            <a href="{{ route('shipping-rates.show', $rate->id) }}" class="btn-sm" title="Lihat">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                Lihat
            </a>
            <button class="btn-sm" onclick="openModal('{{ $rate->id }}', '{{ $rate->shipping_service_id }}', '{{ $rate->origin_province_id }}', '{{ $rate->origin_city_id }}', '{{ $rate->destination_province_id }}', '{{ $rate->destination_city_id }}', '{{ $rate->min_weight }}', '{{ $rate->max_weight }}', '{{ (int)$rate->cost_per_kg }}', '{{ $rate->etd_min }}', '{{ $rate->etd_max }}', '{{ $rate->is_active ? 1 : 0 }}')">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4L18.5 2.5z"></path></svg>
                Edit
            </button>
            <button type="button" class="btn-sm danger" onclick="openDeleteModal('{{ $rate->id }}', '{{ $rate->service->courier->name }} - {{ $rate->service->service_name }}', '{{ $rate->originCity->full_name }} → {{ $rate->destinationCity->full_name }}')">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
                Hapus
            </button>
        </div>
    </td>
</tr>
@endforeach
