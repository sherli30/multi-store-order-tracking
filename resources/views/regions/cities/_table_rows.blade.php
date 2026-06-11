@foreach ($cities as $city)
    <tr>
        <td class="cell-no"></td>
        <td>
            <div class="city-name">{{ $city->name }}</div>
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
            <div class="actions-cell">
                <button type="button" class="btn-sm" title="Edit Kota"
                    onclick="openCityModal({{ $city->id }}, '{{ addslashes($city->name) }}', '{{ $city->province_id }}', '{{ $city->type }}', '{{ $city->postal_code }}')">
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
