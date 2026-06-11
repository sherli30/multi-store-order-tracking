@foreach($categories as $index => $category)
    <tr class="fade-in-animated">
        <td class="cell-no">{{ $index + 1 }}</td>
        <td>
            <div class="cat-name">{{ $category->name }}</div>
        </td>
        <td>
            <div style="font-size:12.5px; font-weight:600; color:var(--text-1);">{{ $category->store->name }}</div>
        </td>
        <td>
            @if($category->description)
                <div class="cat-desc">{{ $category->description }}</div>
            @else
                <div class="cat-desc empty">Tanpa deskripsi</div>
            @endif
        </td>
        <td class="center">
            <span class="product-count">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width:14px;height:14px;">
                    <path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"/>
                </svg>
                {{ number_format($category->products_count) }}
            </span>
        </td>
        <td>
            <div style="font-size:12px; color:var(--text-2); font-weight:500;">
                {{ $category->created_at->format('d M Y') }}
            </div>
            <div style="font-size:10.5px; color:var(--text-4);">
                {{ $category->created_at->format('H:i') }}
            </div>
        </td>
        <td>
            @if(!$category->store->is_active)
                <span class="badge badge-inactive" style="background: rgba(239, 68, 68, 0.08); color: var(--red); border: 1px solid rgba(239, 68, 68, 0.15);" title="Toko ini sedang dinonaktifkan di Manajemen Toko">Non-aktif (Toko Non-aktif)</span>
            @elseif($category->is_active)
                <span class="badge badge-active">Aktif</span>
            @else
                <span class="badge badge-inactive">Non-aktif</span>
            @endif
        </td>
        <td>
            <div class="actions-cell">
                <a href="{{ route('product-categories.edit', $category->id) }}" class="btn-sm" title="Edit Kategori">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                        <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                    </svg>
                </a>
                <button type="button" class="btn-sm danger"
                        onclick="openDeleteModal({{ $category->id }}, '{{ addslashes($category->name) }}', {{ $category->products_count ?? 0 }})"
                        title="Hapus Kategori">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="3 6 5 6 21 6"/>
                        <path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/>
                        <path d="M10 11v6"/>
                        <path d="M14 11v6"/>
                        <path d="M9 6V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2"/>
                    </svg>
                </button>
            </div>
        </td>
    </tr>
@endforeach
