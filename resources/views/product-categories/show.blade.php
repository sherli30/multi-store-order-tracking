@extends('layouts.app')

@section('title', 'Detail Kategori Produk')

@section('styles')
    .category-detail-wrap { display: flex; flex-direction: column; gap: 24px; }
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
    .badge { padding: 4px 10px; border-radius: 20px; font-size: 12px; font-weight: 700; text-transform: uppercase; }
    .badge-active { background: var(--green-dim); color: var(--green); }
    .badge-inactive { background: var(--red-dim); color: var(--red); }
    .btn-back { display: inline-flex; align-items: center; gap: 8px; padding: 10px 16px; background: var(--surface); border: 1px solid var(--border); border-radius: 8px; color: var(--text-2); text-decoration: none; font-weight: 600; transition: all 0.2s; }
    .btn-back:hover { background: var(--panel); color: var(--text-1); border-color: var(--border-2); }
@endsection

@section('content')
<div class="category-detail-wrap">
    <div class="page-header">
        <div class="page-header-left">
            <h1>Detail Kategori</h1>
            <p>Informasi lengkap mengenai kategori produk.</p>
        </div>
        <a href="{{ route('product-categories.index') }}" class="btn-back">Kembali</a>
    </div>

    <div class="card">
        <div class="card-title">Informasi Dasar</div>
        <div class="detail-grid">
            <div class="detail-item">
                <div class="detail-label">Nama Kategori</div>
                <div class="detail-value">
                    @if($productCategory->name)
                        {{ $productCategory->name }}
                    @else
                        <span class="empty-state">Data kategori belum lengkap.</span>
                    @endif
                </div>
            </div>
            <div class="detail-item">
                <div class="detail-label">Status</div>
                <div class="detail-value">
                    @if($productCategory->is_active)
                        <span class="badge badge-active">Aktif</span>
                    @else
                        <span class="badge badge-inactive">Nonaktif</span>
                    @endif
                </div>
            </div>
            <div class="detail-item">
                <div class="detail-label">Toko</div>
                <div class="detail-value">
                    @if($productCategory->store)
                        {{ $productCategory->store->name }}
                    @else
                        <span class="empty-state">Data kategori belum lengkap.</span>
                    @endif
                </div>
            </div>
            <div class="detail-item" style="grid-column: span 2;">
                <div class="detail-label">Deskripsi Kategori</div>
                <div class="detail-value">
                    @if($productCategory->description)
                        {{ $productCategory->description }}
                    @else
                        <span class="empty-state">Deskripsi belum tersedia.</span>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-title">Statistik & Riwayat</div>
        <div class="detail-grid">
            <div class="detail-item">
                <div class="detail-label">Total Produk</div>
                <div class="detail-value">
                    @if($productCategory->products_count > 0)
                        {{ number_format($productCategory->products_count) }} Produk
                    @else
                        <span class="empty-state">Tidak ada produk dalam kategori ini.</span>
                    @endif
                </div>
            </div>
            <div class="detail-item">
                <div class="detail-label">Tanggal Dibuat</div>
                <div class="detail-value">
                    @if($productCategory->created_at)
                        {{ $productCategory->created_at->format('d M Y H:i') }}
                    @else
                        <span class="empty-state">Data tidak tersedia.</span>
                    @endif
                </div>
            </div>
            <div class="detail-item">
                <div class="detail-label">Terakhir Diperbarui</div>
                <div class="detail-value">
                    @if($productCategory->updated_at)
                        {{ $productCategory->updated_at->format('d M Y H:i') }}
                    @else
                        <span class="empty-state">Data tidak tersedia.</span>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
