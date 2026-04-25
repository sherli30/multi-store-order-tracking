@extends('layouts.app')

@section('title', 'Laporan Per Toko')

@section('styles')
.filter-card {
    background: var(--panel);
    border: 1px solid var(--border);
    border-radius: 16px;
    padding: 24px;
    margin-bottom: 24px;
    box-shadow: var(--shadow-sm);
}
.filter-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 16px;
    align-items: flex-end;
}
.form-label { font-size: 12px; font-weight: 700; color: var(--text-3); margin-bottom: 6px; text-transform: uppercase; display: block; }
.form-input { 
    width: 100%; padding: 10px 14px; border: 1px solid var(--border); border-radius: 10px; 
    font-size: 14px; color: var(--text-1); background: #fff; outline: none; transition: 0.2s;
}
.form-input:focus { border-color: var(--accent); box-shadow: 0 0 0 3px var(--accent-glow); }

.store-report-card {
    background: var(--panel);
    border: 1px solid var(--border);
    border-radius: 16px;
    padding: 24px;
    margin-bottom: 24px;
    box-shadow: var(--shadow-sm);
}
.store-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
    padding-bottom: 15px;
    border-bottom: 1px solid var(--surface);
}
.store-title { font-size: 18px; font-weight: 800; color: var(--text-1); }

.stats-mini-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
    gap: 20px;
    margin-bottom: 30px;
}
.stat-item {
    background: var(--surface);
    padding: 20px;
    border-radius: 12px;
    border: 1px solid var(--border-2);
}
.stat-label { font-size: 11px; font-weight: 700; color: var(--text-4); text-transform: uppercase; margin-bottom: 4px; }
.stat-value { font-size: 20px; font-weight: 800; color: var(--text-1); }

.btn-primary { 
    background: var(--accent); color: #fff; border: none; padding: 12px 24px; border-radius: 10px; 
    font-weight: 700; cursor: pointer; display: inline-flex; align-items: center; gap: 8px; text-decoration: none;
}
.btn-outline {
    background: #fff; border: 1px solid var(--border); padding: 10px 20px; border-radius: 10px;
    font-weight: 700; color: var(--text-2); text-decoration: none; display: inline-flex; align-items: center; gap: 8px;
}
@endsection

@section('content')
<div style="margin-bottom:24px;">
    <h1 style="font-size:24px; font-weight:800; color:var(--text-1); letter-spacing:-0.02em;">Laporan Per Toko</h1>
    <p style="color:var(--text-3); font-size:14px;">Detail performa spesifik cabang/toko dalam rentang waktu tertentu</p>
</div>

<div class="filter-card">
    <form action="{{ route('reports.stores') }}" method="GET">
        <div class="filter-grid">
            <div>
                <label class="form-label">Pilih Toko</label>
                <select name="store_id" class="form-input">
                    <option value="">Semua Toko</option>
                    @foreach($allStores as $s)
                        <option value="{{ $s->id }}" {{ request('store_id') == $s->id ? 'selected' : '' }}>{{ $s->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="form-label">Tanggal Mulai</label>
                <input type="date" name="start_date" class="form-input" value="{{ $startDate }}">
            </div>
            <div>
                <label class="form-label">Tanggal Selesai</label>
                <input type="date" name="end_date" class="form-input" value="{{ $endDate }}">
            </div>
            <div style="display:flex; gap:10px;">
                <button type="submit" class="btn-primary" style="height:42px; border-radius:10px; font-size:14px;">Tampilkan</button>
                <a href="{{ route('reports.stores') }}" class="btn-outline" style="height:42px; padding:0 20px; border-radius:10px; font-size:14px; display:flex; align-items:center;">Reset</a>
            </div>
        </div>
    </form>
</div>

@forelse($stores as $store)
<div class="store-report-card">
    <div class="store-header">
        <div style="display:flex; align-items:center; gap:12px;">
            <div style="width:40px; height:40px; background:var(--accent-dim); border-radius:10px; display:flex; align-items:center; justify-content:center;">
                <svg viewBox="0 0 24 24" width="20" height="20" stroke="var(--accent)" stroke-width="2.5" fill="none"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path><polyline points="9 22 9 12 15 12 15 22"></polyline></svg>
            </div>
            <div class="store-title">{{ $store->name }}</div>
        </div>
        <a href="{{ route('reports.export', ['type' => 'store', 'store_id' => $store->id, 'start_date' => $startDate, 'end_date' => $endDate]) }}" target="_blank" class="btn-outline" style="font-size:13px; padding:8px 16px;">
            <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2.5" fill="none"><polyline points="6 9 6 2 18 2 18 9"></polyline><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path><rect x="6" y="14" width="12" height="8"></rect></svg>
            Excel / Print
        </a>
    </div>

    <div class="stats-mini-grid">
        <div class="stat-item">
            <div class="stat-label">Total Pesanan</div>
            <div class="stat-value">{{ number_format($store->orders_count) }} Unit</div>
        </div>
        <div class="stat-item">
            <div class="stat-label">Estimasi Revenue</div>
            <div class="stat-value" style="color:var(--accent);">Rp {{ number_format($store->revenue, 0, ',', '.') }}</div>
        </div>
        <div class="stat-item">
            <div class="stat-label">Success Rate</div>
            @php
                $cancelled = $store->orders->where('status', 'cancelled')->count();
                $rate = $store->orders_count > 0 ? (($store->orders_count - $cancelled) / $store->orders_count) * 100 : 0;
            @endphp
            <div class="stat-value">{{ number_format($rate, 1) }}%</div>
        </div>
        <div class="stat-item">
            <div class="stat-label">Rata-rata Keranjang</div>
            <div class="stat-value">Rp {{ $store->orders_count > 0 ? number_format($store->revenue / $store->orders_count, 0, ',', '.') : 0 }}</div>
        </div>
    </div>

    <div style="background:var(--surface); border-radius:12px; padding:20px; border:1px solid var(--border-2);">
        <h4 style="font-size:13px; font-weight:700; color:var(--text-2); margin-bottom:15px; text-transform:uppercase;">Ringkasan Item Terjual</h4>
        <div style="font-size:14px; line-height:1.6; color:var(--text-3);">
            Laporan detail mencakup seluruh daftar item yang dibeli melalui cabang ini dalam rentang waktu terpilih. Untuk rincian lengkap tiap transaksi, silakan tekan tombol **Excel / Print** di atas.
        </div>
    </div>
</div>
@empty
<div style="text-align:center; padding:60px; background:var(--panel); border:1px solid var(--border); border-radius:16px;">
    <p style="color:var(--text-3);">Tidak ada data penjualan ditemukan untuk rentang tanggal ini.</p>
</div>
@endforelse
@endsection
