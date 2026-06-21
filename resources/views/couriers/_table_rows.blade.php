@foreach ($couriers as $courier)
    <tr>
        {{-- Nomor diisi otomatis oleh drawCallback DataTables --}}
        <td class="cell-no"></td>
        <td>
            <div class="courier-info">
                <div class="courier-logo">
                    {{ strtoupper(substr($courier->name, 0, 1)) }}
                </div>
                <div>
                    <div class="courier-name">{{ $courier->name }}</div>
                </div>
            </div>
        </td>
        <td style="font-family: var(--mono); font-size: 12px; color: var(--text-3);">{{ $courier->code }}</td>
        <td>
            <span class="service-pill">
                <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path></svg>
                {{ $courier->services_count }} Layanan
            </span>
        </td>
        <td>
            @if($courier->is_active)
                <span class="badge badge-active">Aktif</span>
            @else
                <span class="badge badge-inactive">Nonaktif</span>
            @endif
        </td>
        <td>
            <div class="actions-cell">
                <a href="{{ route('couriers.show', $courier->id) }}" class="btn-sm" title="Lihat">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                    Lihat
                </a>
                <button type="button" class="btn-sm" onclick="openCourierModal('{{ $courier->id }}', '{{ addslashes($courier->name) }}', '{{ $courier->code }}', '{{ addslashes($courier->contact_person) }}', '{{ $courier->phone_number }}', '{{ $courier->email }}', '{{ addslashes($courier->description) }}', '{{ $courier->is_active ? 1 : 0 }}')" title="Edit">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4L18.5 2.5z"></path></svg>
                    Edit
                </button>
                <button type="button" class="btn-sm danger" onclick="openDeleteModal('{{ $courier->id }}', '{{ addslashes($courier->name) }}')" title="Hapus">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
                    Hapus
                </button>
            </div>
        </td>
    </tr>
@endforeach
