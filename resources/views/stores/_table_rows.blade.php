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
                </div>
            </div>
        </td>
        <td>
            <div class="store-meta-info">
                <div class="meta-item">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path><circle cx="12" cy="10" r="3"></circle>
                    </svg>
                    <div>
                        <div style="font-weight:700; color:var(--text-1); font-size:13px;">{{ $store->city->full_name ?? 'N/A' }}</div>
                        <div style="font-size:11px; color:var(--text-4);">{{ $store->province->name ?? '' }}</div>
                    </div>
                </div>
                <div class="meta-item">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path>
                    </svg>
                    <span style="font-size:12px; font-weight:600;">{{ $store->phone ?? 'Belum Ada No. Telp' }}</span>
                </div>
            </div>
        </td>
        <td>
            @if($store->operational_hours)
                <div class="hours-pill">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline>
                    </svg>
                    {{ $store->operational_hours }}
                </div>
            @else
                <span style="color:var(--text-4); font-size:11px; font-style:italic;">Belum diatur</span>
            @endif
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
                <a href="{{ route('stores.edit', $store) }}" class="btn-sm" title="Edit Toko">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7" />
                        <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z" />
                    </svg>
                    Edit
                </a>
                <button type="button" class="btn-sm danger" title="Hapus Toko"
                    onclick="openDeleteModal({{ $store->id }}, '{{ addslashes($store->name) }}', {{ $store->products_count }})">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
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
