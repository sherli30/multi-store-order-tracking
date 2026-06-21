@extends('layouts.app')

@section('title', 'Detail Produk - ' . $product->name)

@section('styles')
    .product-detail-wrap { display: flex; flex-direction: column; gap: 24px; }
    .page-header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 24px; }
    .page-header-left h1 { font-size: 24px; font-weight: 800; color: var(--text-1); margin-bottom: 4px; }
    .page-header-left p { font-size: 14px; color: var(--text-3); }
    .card { background: var(--panel); border: 1px solid var(--border); border-radius: 12px; padding: 24px; margin-bottom: 24px; }
    .card-title { font-size: 18px; font-weight: 700; color: var(--text-1); margin-bottom: 16px; border-bottom: 1px solid var(--border); padding-bottom: 12px; }
    .detail-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
    .detail-item { display: flex; flex-direction: column; gap: 6px; }
    .detail-label { font-size: 12px; font-weight: 600; color: var(--text-3); text-transform: uppercase; }
    .detail-value { font-size: 14px; color: var(--text-1); font-weight: 500; }
    .empty-state { font-style: italic; color: var(--text-4); }
    .logo-img { width: 100px; height: 100px; object-fit: cover; border-radius: 8px; border: 1px solid var(--border); margin-right: 10px;}
    .badge { padding: 4px 10px; border-radius: 20px; font-size: 12px; font-weight: 700; text-transform: uppercase; }
    .badge-active { background: var(--green-dim); color: var(--green); }
    .badge-inactive { background: var(--red-dim); color: var(--red); }
    .btn-back { display: inline-flex; align-items: center; gap: 8px; padding: 10px 16px; background: var(--surface); border: 1px solid var(--border); border-radius: 8px; color: var(--text-2); text-decoration: none; font-weight: 600; transition: all 0.2s; }
    .btn-back:hover { background: var(--panel); color: var(--text-1); border-color: var(--border-2); }
@endsection

@section('content')
<div class="product-detail-wrap">
    <div class="page-header">
        <div class="page-header-left">
            <h1>Detail Produk</h1>
            <p>Informasi lengkap mengenai produk.</p>
        </div>
        <a href="{{ route('products.index') }}" class="btn-back">Kembali</a>
    </div>

    <div class="card">
        <div class="card-title">Informasi Dasar</div>
        <div class="detail-grid">
            <div class="detail-item" style="grid-column: span 2;">
                <div class="detail-label">Gambar Produk</div>
                <div class="detail-value" style="display: flex; gap: 10px; overflow-x: auto;">
                    @if($product->images && $product->images->count() > 0)
                        @foreach($product->images as $image)
                            <img src="{{ asset('storage/' . $image->image_path) }}" alt="Gambar" class="logo-img {{ $image->is_primary ? 'primary-img' : '' }}" style="{{ $image->is_primary ? 'border: 2px solid var(--accent);' : '' }}">
                        @endforeach
                    @else
                        <span class="empty-state">Gambar produk belum tersedia.</span>
                    @endif
                </div>
            </div>
            <div class="detail-item">
                <div class="detail-label">Nama Produk</div>
                <div class="detail-value">
                    @if($product->name)
                        {{ $product->name }}
                    @else
                        <span class="empty-state">Nama produk belum tersedia.</span>
                    @endif
                </div>
            </div>
            <div class="detail-item">
                <div class="detail-label">Status</div>
                <div class="detail-value">
                    @if($product->is_active)
                        <span class="badge badge-active">Aktif</span>
                    @else
                        <span class="badge badge-inactive">Nonaktif</span>
                    @endif
                </div>
            </div>
            <div class="detail-item">
                <div class="detail-label">Toko</div>
                <div class="detail-value">
                    @if($product->store)
                        {{ $product->store->name }}
                    @else
                        <span class="empty-state">Data produk belum lengkap.</span>
                    @endif
                </div>
            </div>
            <div class="detail-item">
                <div class="detail-label">Kategori</div>
                <div class="detail-value">
                    @if($product->category)
                        {{ $product->category->name }}
                    @else
                        <span class="empty-state">Kategori belum tersedia.</span>
                    @endif
                </div>
            </div>
            <div class="detail-item">
                <div class="detail-label">SKU</div>
                <div class="detail-value">
                    @if($product->sku)
                        {{ $product->sku }}
                    @else
                        <span class="empty-state">Data produk belum lengkap.</span>
                    @endif
                </div>
            </div>
            <div class="detail-item">
                <div class="detail-label">Harga</div>
                <div class="detail-value">
                    @if($product->price)
                        {{ $product->formatted_price }}
                    @else
                        <span class="empty-state">Harga belum tersedia.</span>
                    @endif
                </div>
            </div>
            <div class="detail-item">
                <div class="detail-label">Stok</div>
                <div class="detail-value">
                    @if($product->stock !== null)
                        {{ number_format($product->stock) }}
                    @else
                        <span class="empty-state">Stok belum tersedia.</span>
                    @endif
                </div>
            </div>
            <div class="detail-item">
                <div class="detail-label">Berat</div>
                <div class="detail-value">
                    @if($product->weight)
                        {{ $product->weight }} gr
                    @else
                        <span class="empty-state">Berat produk belum tersedia.</span>
                    @endif
                </div>
            </div>
            
            <div class="detail-item" style="grid-column: span 2;">
                <div class="detail-label">Deskripsi Produk</div>
                <div class="detail-value">
                    @if($product->descriptions && $product->descriptions->count() > 0)
                        @foreach($product->descriptions as $desc)
                            <div style="margin-bottom: 10px;">
                                <strong>{{ $desc->title }}</strong><br>
                                {{ $desc->content }}
                            </div>
                        @endforeach
                    @else
                        <span class="empty-state">Deskripsi produk belum tersedia.</span>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-title">Statistik & Riwayat</div>
        <div class="detail-grid">
            <div class="detail-item">
                <div class="detail-label">Total Terjual</div>
                <div class="detail-value">{{ number_format($product->sold_count) }}</div>
            </div>
            <div class="detail-item">
                <div class="detail-label">Tanggal Dibuat</div>
                <div class="detail-value">
                    @if($product->created_at)
                        {{ $product->created_at->format('d M Y H:i') }}
                    @else
                        <span class="empty-state">Data tidak tersedia.</span>
                    @endif
                </div>
            </div>
            <div class="detail-item">
                <div class="detail-label">Terakhir Diperbarui</div>
                <div class="detail-value">
                    @if($product->updated_at)
                        {{ $product->updated_at->format('d M Y H:i') }}
                    @else
                        <span class="empty-state">Data tidak tersedia.</span>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
