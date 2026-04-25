@foreach ($stores as $store)
    <tr id="row-store-{{ $store->id }}">
        {{-- REVISI 1: Biarkan kosong, nomor akan diisi otomatis oleh drawCallback DataTables --}}
        <td class="cell-no"></td>

        <td>
            <div class="store-info-cell">
                @if($store->logo)
                    <img src="{{ asset('storage/' . $store->logo) }}" alt="{{ $store->name }}" class="store-logo-thumb">
                @else
                    <div class="store-logo-placeholder">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z" />
                            <polyline points="9 22 9 12 15 12 15 22" />
                        </svg>
                    </div>
                @endif
                <div>
                    <div class="store-name">{{ $store->name }}</div>
                    <div class="store-slug">{{ $store->slug }}</div>
                </div>
            </div>
        </td>
        <td>
            @if ($store->is_active)
                <span class="badge badge-active">Aktif</span>
            @else
                <span class="badge badge-inactive">Nonaktif</span>
            @endif
        </td>
        <td class="center" data-products="{{ $store->products_count }}">
            <span class="product-count">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                    stroke-linecap="round" stroke-linejoin="round"
                    style="width:12px;height:12px;">
                    <polyline points="21 8 21 21 3 21 3 8" />
                    <rect x="1" y="3" width="22" height="5" />
                </svg>
                {{ number_format($store->products_count) }}
            </span>
        </td>
        <td style="font-size:12px; color:var(--text-3); white-space:nowrap;">
            {{ $store->created_at->format('d M Y') }}
        </td>
        <td>
            <div class="actions-cell">
                <a href="{{ route('stores.edit', $store) }}" class="btn-sm">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7" />
                        <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z" />
                    </svg>
                </a>
                <button class="btn-sm danger"
                    onclick="openDeleteModal({{ $store->id }}, '{{ addslashes($store->name) }}', {{ $store->products_count }})">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="3 6 5 6 21 6" />
                        <path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6" />
                        <path d="M10 11v6M14 11v6" />
                        <path d="M9 6V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2" />
                    </svg>
                    Hapus
                </button>
            </div>
        </td>
    </tr>
@endforeach
