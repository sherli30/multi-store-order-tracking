@foreach($products as $product)
{{--
    Determine availability state for this product.
    A product is "available" only when the full chain is active:
    product.is_active = true AND category.is_active = true AND store.is_active = true
    Used to gray-out rows and disable actions on unavailable products.
--}}
@php
    $categoryActive = $product->category?->is_active ?? false;
    $storeActive    = $product->store?->is_active ?? false;
    $fullyActive    = $product->is_active && $categoryActive && $storeActive;
    $stockClass     = $product->stock === 0
                        ? 'stock-empty'
                        : ($product->stock <= 10 ? 'stock-low' : 'stock-ok');
    $isLowStock     = $product->stock <= 10 && $product->stock > 0;
    $isOutOfStock   = $product->stock === 0;
@endphp

<tr class="{{ !$fullyActive ? 'row-unavailable' : '' }}" data-product-id="{{ $product->id }}">

    {{-- No --}}
    <td class="cell-no"></td>

    {{-- Produk --}}
    <td>
        <div class="product-info">
            <div class="product-thumb">
                @if($product->image)
                    <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}">
                @else
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="3" y="3" width="18" height="18" rx="2"/>
                        <circle cx="8.5" cy="8.5" r="1.5"/>
                        <polyline points="21 15 16 10 5 21"/>
                    </svg>
                @endif
            </div>
            <div>
                <div class="product-name">{{ $product->name }}</div>
            </div>
        </div>
    </td>

    {{-- Kategori — warn if inactive --}}
    <td>
        @if($product->category)
            <a href="{{ route('products.index', ['category_id' => $product->category_id]) }}"
               class="category-pill {{ !$categoryActive ? 'pill-inactive' : '' }}">
                {{ $product->category->name }}
                @if(!$categoryActive)
                    <span class="inactive-dot" title="Kategori nonaktif">●</span>
                @endif
            </a>
        @else
            <span class="category-none">—</span>
        @endif
    </td>

    {{-- Toko — warn if inactive --}}
    <td>
        <span class="store-badge {{ !$storeActive ? 'store-badge-inactive' : '' }}">
            {{ $product->store->name ?? '—' }}
            @if(!$storeActive)
                <span title="Toko nonaktif"> ⚠</span>
            @endif
        </span>
    </td>

    {{-- Harga --}}
    <td>
        <div class="price-value">{{ $product->formatted_price }}</div>
    </td>

    {{-- Stok — with visual indicator + quick-stock buttons --}}
    <td>
        <div class="stock-cell">
            @php
                $variantCount = $product->variants()->where('name', '!=', 'Default')->count();
            @endphp
            <div class="stock-value {{ $stockClass }}">{{ number_format($product->stock) }}</div>
            <div class="stock-label">
                @if($variantCount > 0)
                    <span class="stock-tag" style="background:var(--surface-2); color:var(--text-3); margin-right:4px;">{{ $variantCount }} Varian</span>
                @endif

                @if($isOutOfStock)
                    <span class="stock-tag stock-tag-empty">Habis</span>
                @elseif($isLowStock)
                    <span class="stock-tag stock-tag-low">Menipis</span>
                @else
                    <span class="stock-tag stock-tag-ok">Tersedia</span>
                @endif
            </div>

            {{-- Quick Stock Buttons (only for active products and single variants) --}}
            @if($fullyActive)
                <div class="quick-stock-actions">
                    @if($variantCount > 0)
                        {{-- Redirect to detail view for multi-variant stock management --}}
                        <a href="{{ route('products.stock.index', $product) }}" class="btn-stock-quick" title="Kelola Stok Varian" style="width: auto; padding: 0 8px; font-size: 10px; text-decoration: none;">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="margin-right: 4px;">
                                <polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/>
                            </svg>
                            Kelola Stok Varian
                        </a>
                    @else
                        {{-- Add/Deduct for single variant --}}
                        <button type="button" class="btn-stock-quick btn-add-stock"
                                onclick="openStockModal('add', '{{ $product->slug }}', '{{ addslashes($product->name) }}', {{ $product->stock }})"
                                title="Tambah Stok">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                <line x1="12" y1="5" x2="12" y2="19"/>
                                <line x1="5" y1="12" x2="19" y2="12"/>
                            </svg>
                        </button>
                        <button type="button" class="btn-stock-quick btn-deduct-stock {{ $isOutOfStock ? 'btn-disabled' : '' }}"
                                onclick="openStockModal('deduct', '{{ $product->slug }}', '{{ addslashes($product->name) }}', {{ $product->stock }})"
                                title="{{ $isOutOfStock ? 'Stok sudah habis' : 'Kurangi Stok' }}"
                                {{ $isOutOfStock ? 'disabled' : '' }}>
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                <line x1="5" y1="12" x2="19" y2="12"/>
                            </svg>
                        </button>
                    @endif
                </div>
            @endif
        </div>
    </td>

    {{-- Status — multi-level badge --}}
    <td>
        @if(!$product->is_active)
            <span class="badge badge-inactive">Nonaktif</span>
        @elseif(!$categoryActive)
            <span class="badge badge-warning" title="Kategori nonaktif menyebabkan produk tidak terlihat">Kat. Nonaktif</span>
        @elseif(!$storeActive)
            <span class="badge badge-warning" title="Toko nonaktif menyebabkan produk tidak terlihat">Toko Nonaktif</span>
        @else
            <span class="badge badge-active">Aktif</span>
        @endif
    </td>

    {{-- Aksi --}}
    <td>
        <div class="actions-cell">
            <a href="{{ route('products.edit', $product) }}" class="btn-sm" title="Edit Produk">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                    <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                </svg>
            </a>
            <a href="{{ route('products.stock.index', $product) }}" class="btn-sm" title="Riwayat Stok">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/>
                </svg>
            </a>
            <button class="btn-sm danger"
                    onclick="openDeleteModal('{{ $product->slug }}', '{{ addslashes($product->name) }}')"
                    title="Hapus Produk">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="3 6 5 6 21 6"/>
                    <path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/>
                    <path d="M10 11v6M14 11v6"/>
                    <path d="M9 6V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2"/>
                </svg>
            </button>
        </div>
    </td>

</tr>
@endforeach
