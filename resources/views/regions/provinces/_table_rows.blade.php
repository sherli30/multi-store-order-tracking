@foreach ($provinces as $province)
    <tr>
        {{-- Nomor diisi otomatis oleh drawCallback DataTables --}}
        <td class="cell-no"></td>
        <td class="province-name">{{ $province->name }}</td>
        <td>
            <span class="city-count-badge">
                <svg viewBox="0 0 24 24" width="12" height="12" fill="none" stroke="currentColor" stroke-width="3" style="margin-right:5px;"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path><circle cx="12" cy="10" r="3"></circle></svg>
                {{ $province->cities_count }} Kota
            </span>
        </td>
        <td>
            <div class="actions-cell">
                <button type="button" class="btn-sm" onclick="openProvinceModal('{{ $province->id }}', '{{ addslashes($province->name) }}')" title="Edit">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4L18.5 2.5z"></path></svg>
                    Edit
                </button>
                <button type="button" class="btn-sm danger" onclick="openDeleteModal('{{ $province->id }}', '{{ addslashes($province->name) }}')" title="Hapus">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
                    Hapus
                </button>
            </div>
        </td>
    </tr>
@endforeach
