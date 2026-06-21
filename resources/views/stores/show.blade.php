@extends('layouts.app')

@section('title', 'Detail Toko - ' . $store->name)

@section('styles')
    .store-detail-wrap { display: flex; flex-direction: column; gap: 24px; }
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
    .logo-img { width: 100px; height: 100px; object-fit: cover; border-radius: 8px; border: 1px solid var(--border); }
    .badge { padding: 4px 10px; border-radius: 20px; font-size: 12px; font-weight: 700; text-transform: uppercase; }
    .badge-active { background: var(--green-dim); color: var(--green); }
    .badge-inactive { background: var(--red-dim); color: var(--red); }
    .btn-back { display: inline-flex; align-items: center; gap: 8px; padding: 10px 16px; background: var(--surface); border: 1px solid var(--border); border-radius: 8px; color: var(--text-2); text-decoration: none; font-weight: 600; transition: all 0.2s; }
    .btn-back:hover { background: var(--panel); color: var(--text-1); border-color: var(--border-2); }
@endsection

@section('content')
<div class="store-detail-wrap">
    <div class="page-header">
        <div class="page-header-left">
            <h1>Detail Toko</h1>
            <p>Informasi lengkap mengenai entitas toko.</p>
        </div>
        <a href="{{ route('stores.index') }}" class="btn-back">Kembali</a>
    </div>

    <div class="card">
        <div class="card-title">Informasi Dasar</div>
        <div class="detail-grid">
            <div class="detail-item" style="grid-column: span 2;">
                <div class="detail-label">Logo Toko</div>
                <div class="detail-value">
                    @if($store->logo)
                        <img src="{{ asset('storage/' . $store->logo) }}" alt="Logo" class="logo-img">
                    @else
                        <span class="empty-state">Logo belum tersedia.</span>
                    @endif
                </div>
            </div>
            <div class="detail-item">
                <div class="detail-label">Nama Toko</div>
                <div class="detail-value">
                    @if($store->name)
                        {{ $store->name }}
                    @else
                        <span class="empty-state">Nama belum tersedia.</span>
                    @endif
                </div>
            </div>
            <div class="detail-item">
                <div class="detail-label">Status</div>
                <div class="detail-value">
                    @if($store->is_active)
                        <span class="badge badge-active">Aktif</span>
                    @else
                        <span class="badge badge-inactive">Nonaktif</span>
                    @endif
                </div>
            </div>
            <div class="detail-item">
                <div class="detail-label">Nomor Telepon</div>
                <div class="detail-value">
                    @if($store->phone)
                        {{ $store->phone }}
                    @else
                        <span class="empty-state">Nomor telepon belum diisi.</span>
                    @endif
                </div>
            </div>
            <div class="detail-item">
                <div class="detail-label">Jam Operasional</div>
                <div class="detail-value">
                    @if($store->operational_hours)
                        {{ $store->operational_hours }}
                    @else
                        <span class="empty-state">Jam operasional belum diatur.</span>
                    @endif
                </div>
            </div>
            <div class="detail-item">
                <div class="detail-label">Provinsi & Kota</div>
                <div class="detail-value">
                    @if(isset($store->city) && isset($store->province))
                        {{ $store->city->full_name }}, {{ $store->province->name }}
                    @else
                        <span class="empty-state">Provinsi & Kota belum diisi lengkap.</span>
                    @endif
                </div>
            </div>
            <div class="detail-item">
                <div class="detail-label">Alamat Lengkap</div>
                <div class="detail-value">
                    @if($store->address)
                        {{ $store->address }}
                    @else
                        <span class="empty-state">Alamat belum diisi.</span>
                    @endif
                </div>
            </div>
            <div class="detail-item" style="grid-column: span 2;">
                <div class="detail-label">Deskripsi Toko</div>
                <div class="detail-value">
                    @if($store->description)
                        {{ $store->description }}
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
                <div class="detail-value">{{ number_format($store->products_count) }}</div>
            </div>
            <div class="detail-item">
                <div class="detail-label">Total Kategori</div>
                <div class="detail-value">{{ number_format($store->product_categories_count) }}</div>
            </div>
            <div class="detail-item">
                <div class="detail-label">Total Pesanan</div>
                <div class="detail-value">{{ number_format($store->orders_count) }}</div>
            </div>
            <div class="detail-item">
                <div class="detail-label">Tanggal Dibuat</div>
                <div class="detail-value">
                    @if($store->created_at)
                        {{ $store->created_at->format('d M Y H:i') }}
                    @else
                        <span class="empty-state">Data tidak tersedia.</span>
                    @endif
                </div>
            </div>
            <div class="detail-item">
                <div class="detail-label">Terakhir Diperbarui</div>
                <div class="detail-value">
                    @if($store->updated_at)
                        {{ $store->updated_at->format('d M Y H:i') }}
                    @else
                        <span class="empty-state">Data tidak tersedia.</span>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
