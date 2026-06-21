@foreach ($cities as $city)
    <tr>
        <td class="cell-no"></td>
        <td>
            <div class="city-name">{{ $city->name }}</div>
            <div style="font-size:11.5px; color:var(--text-3); font-family:var(--mono);">{{ $city->code ?: '-' }}</div>
        </td>
        <td>
            <span class="province-pill">{{ $city->province->name ?? '-' }}</span>
        </td>
        <td>
            @if($city->type === 'Kota')
                <span class="status-pill blue">Kota</span>
            @else
                <span class="status-pill amber">Kabupaten</span>
            @endif
        </td>
        <td style="font-family: monospace; font-size: 12px; color: var(--text-2);">
            {{ $city->postal_code }}
        </td>
        <td>
            @if($city->is_active)
                <span class="badge badge-active" style="display: inline-flex; align-items: center; gap: 5px; padding: 4px 11px; border-radius: 20px; font-size: 11.5px; font-weight: 600; white-space: nowrap; background: var(--green-dim); color: var(--green); border: 1px solid rgba(22,163,74,0.2);"><span style="width: 6px; height: 6px; border-radius: 50%; background: var(--green);"></span>Aktif</span>
            @else
                <span class="badge badge-inactive" style="display: inline-flex; align-items: center; gap: 5px; padding: 4px 11px; border-radius: 20px; font-size: 11.5px; font-weight: 600; white-space: nowrap; background: var(--surface-2); color: var(--text-3); border: 1px solid var(--border);"><span style="width: 6px; height: 6px; border-radius: 50%; background: var(--text-4);"></span>Nonaktif</span>
            @endif
        </td>
        <td>
            <div class="actions-cell">
                <a href="{{ route('cities.show', $city->id) }}" class="btn-sm" title="Lihat">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                    Lihat
                </a>
                <button type="button" class="btn-sm" title="Edit Kota"
                    onclick="openCityModal('{{ $city->id }}', '{{ addslashes($city->name) }}', '{{ $city->code }}', '{{ $city->province_id }}', '{{ $city->type }}', '{{ $city->postal_code }}', '{{ $city->is_active ? 1 : 0 }}')">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                        <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4L18.5 2.5z"></path>
                    </svg>
                    Edit
                </button>
                <button type="button" class="btn-sm danger" title="Hapus Kota"
                    onclick="openDeleteModal({{ $city->id }}, '{{ addslashes($city->name) }}', '{{ $city->type }}')">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="3 6 5 6 21 6"></polyline>
                        <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                    </svg>
                    Hapus
                </button>
            </div>
        </td>
    </tr>
@endforeach
