@foreach($services as $service)
    <tr>
        {{-- Nomor diisi otomatis oleh drawCallback DataTables --}}
        <td class="cell-no"></td>
        <td>
            <div class="courier-cell">
                <div class="courier-logo-sm">{{ strtoupper(substr($service->courier->name, 0, 1)) }}</div>
                <span style="font-weight:600; color: var(--text-1);">{{ $service->courier->name }}</span>
            </div>
        </td>
        <td class="service-name">{{ $service->service_name }}</td>
        <td style="font-family: var(--mono); color: var(--text-2); font-weight: 600;">{{ (float)$service->min_weight }} kg</td>
        <td>
            <span class="type-pill">{{ $service->min_weight >= 10 ? 'CARGO' : 'REGULER' }}</span>
        </td>
        <td>
            @if(!$service->courier->is_active)
                <span class="badge badge-inactive" style="background: rgba(239, 68, 68, 0.08); color: var(--red); border: 1px solid rgba(239, 68, 68, 0.15);" title="Kurir ini sedang dinonaktifkan di Data Kurir">Nonaktif (Kurir Nonaktif)</span>
            @elseif($service->is_active)
                <span class="badge badge-active">Aktif</span>
            @else
                <span class="badge badge-inactive">Nonaktif</span>
            @endif
        </td>
        <td>
            <div class="actions-cell">
                <a href="{{ route('shipping-services.show', $service->id) }}" class="btn-sm" title="Lihat">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                    Lihat
                </a>
                <button type="button" class="btn-sm" onclick="openModal('{{ $service->id }}', '{{ addslashes($service->service_name) }}', '{{ $service->service_code }}', '{{ $service->courier_id }}', '{{ (float)$service->min_weight }}', '{{ $service->estimated_delivery }}', '{{ addslashes($service->description) }}', '{{ $service->is_active ? 1 : 0 }}')" title="Edit">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4L18.5 2.5z"></path></svg>
                    Edit
                </button>
                <button type="button" class="btn-sm danger" onclick="openDeleteModal('{{ $service->id }}', '{{ addslashes($service->service_name) }}')" title="Hapus">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
                    Hapus
                </button>
            </div>
        </td>
    </tr>
@endforeach
