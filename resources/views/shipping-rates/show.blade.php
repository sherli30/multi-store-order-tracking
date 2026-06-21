@extends('layouts.app')

@section('title', 'Detail Tarif Pengiriman')

@section('styles')
    /* ── Page Header ─────────────────────────────── */
    .page-header {
        display: flex; align-items: flex-start; justify-content: space-between;
        margin-bottom: 28px; flex-wrap: wrap; gap: 16px;
    }
    .page-header-left h1 {
        font-size: 22px; font-weight: 800; letter-spacing: -0.04em; color: var(--text-1);
        margin-bottom: 5px; display: flex; align-items: center; gap: 10px;
    }
    .page-icon {
        width: 36px; height: 36px;
        background: linear-gradient(135deg, var(--accent), color-mix(in srgb, var(--accent) 70%, #7c3aed));
        border-radius: 10px; display: flex; align-items: center; justify-content: center; flex-shrink: 0;
    }
    .page-icon svg { width: 18px; height: 18px; color: #fff; }
    .page-header-left p { font-size: 13px; color: var(--text-3); margin-left: 46px; }

    /* ── Buttons ─────────────────────────────────── */
    .btn-outline {
        display: inline-flex; align-items: center; gap: 7px;
        background: var(--surface); color: var(--text-2); border: 1px solid var(--border);
        padding: 9px 18px; border-radius: 9px; font-family: var(--font); font-weight: 700; font-size: 13px;
        cursor: pointer; text-decoration: none; transition: all 0.15s; height: 38px; box-sizing: border-box;
    }
    .btn-outline:hover { border-color: var(--accent); color: var(--accent); background: color-mix(in srgb, var(--accent) 5%, var(--surface)); }

    /* ── Detail Cards ────────────────────────────── */
    .detail-container { display: grid; grid-template-columns: 1fr 320px; gap: 24px; align-items: start; }
    @media (max-width: 900px) { .detail-container { grid-template-columns: 1fr; } }
    
    .detail-card {
        background: var(--panel); border: 1px solid var(--border); border-radius: 14px;
        padding: 24px; box-shadow: var(--shadow-sm); margin-bottom: 24px;
    }
    .detail-card-title { font-size: 15px; font-weight: 800; color: var(--text-1); margin-bottom: 20px; display: flex; align-items: center; gap: 8px; border-bottom: 1px solid var(--border); padding-bottom: 12px; }
    .detail-card-title svg { width: 16px; height: 16px; color: var(--accent); }

    .info-list { display: flex; flex-direction: column; gap: 16px; }
    .info-item { display: flex; flex-direction: column; gap: 4px; }
    .info-label { font-size: 11.5px; font-weight: 700; color: var(--text-3); text-transform: uppercase; letter-spacing: 0.05em; }
    .info-value { font-size: 14px; color: var(--text-1); font-weight: 500; }
    .info-value.empty { color: var(--text-4); font-style: italic; }

    .route-display {
        display: flex; align-items: center; justify-content: space-between; gap: 16px;
        background: var(--surface); padding: 16px; border-radius: 12px; border: 1px solid var(--border);
        margin-bottom: 20px;
    }
    .route-node { display: flex; flex-direction: column; gap: 4px; flex: 1; }
    .route-node-title { font-size: 11px; font-weight: 700; color: var(--text-3); text-transform: uppercase; }
    .route-node-value { font-size: 14px; font-weight: 700; color: var(--text-1); }
    .route-node-sub { font-size: 12px; color: var(--text-3); }
    .route-arrow { color: var(--text-4); }

    .badge { display: inline-flex; align-items: center; gap: 5px; padding: 4px 11px; border-radius: 20px; font-size: 11.5px; font-weight: 600; white-space: nowrap; width: fit-content; }
    .badge-active { background: var(--green-dim); color: var(--green); border: 1px solid rgba(22,163,74,0.2); }
    .badge-inactive { background: var(--surface-2); color: var(--text-3); border: 1px solid var(--border); }
@endsection

@section('content')
    <div class="page-header">
        <div class="page-header-left">
            <h1>
                <span class="page-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z"></path>
                        <line x1="7" y1="7" x2="7.01" y2="7"></line>
                    </svg>
                </span>
                Detail Tarif Pengiriman
            </h1>
            <p>Informasi rincian biaya pengiriman rute, wilayah, dan layanan kurir.</p>
        </div>
        <a href="{{ route('shipping-rates.index') }}" class="btn-outline">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="width:14px;height:14px;">
                <line x1="19" y1="12" x2="5" y2="12"></line>
                <polyline points="12 19 5 12 12 5"></polyline>
            </svg>
            Kembali
        </a>
    </div>

    <div class="detail-container">
        <div class="detail-main">
            <div class="detail-card">
                <div class="detail-card-title">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path>
                        <circle cx="12" cy="10" r="3"></circle>
                    </svg>
                    Rute Pengiriman
                </div>

                <div class="route-display">
                    <div class="route-node">
                        <div class="route-node-title">Wilayah Asal</div>
                        <div class="route-node-value">{{ $shippingRate->originCity ? $shippingRate->originCity->name : 'Data tarif pengiriman belum lengkap.' }}</div>
                        <div class="route-node-sub">{{ $shippingRate->originProvince ? $shippingRate->originProvince->name : 'Informasi wilayah belum tersedia.' }}</div>
                    </div>
                    <div class="route-arrow">
                        <svg viewBox="0 0 24 24" width="24" height="24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <line x1="5" y1="12" x2="19" y2="12"></line>
                            <polyline points="12 5 19 12 12 19"></polyline>
                        </svg>
                    </div>
                    <div class="route-node" style="text-align: right;">
                        <div class="route-node-title">Wilayah Tujuan</div>
                        <div class="route-node-value">{{ $shippingRate->destinationCity ? $shippingRate->destinationCity->name : 'Data tarif pengiriman belum lengkap.' }}</div>
                        <div class="route-node-sub">{{ $shippingRate->destinationProvince ? $shippingRate->destinationProvince->name : 'Informasi wilayah belum tersedia.' }}</div>
                    </div>
                </div>

                <div class="info-list" style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                    <div class="info-item">
                        <div class="info-label">Biaya Pengiriman</div>
                        <div class="info-value" style="font-size: 18px; font-weight: 800; color: var(--accent);">Rp {{ number_format($shippingRate->cost_per_kg, 0, ',', '.') }} <span style="font-size: 12px; color: var(--text-3); font-weight: 500;">/ kg</span></div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Estimasi Waktu Tiba (ETD)</div>
                        <div class="info-value">{{ $shippingRate->etd_min ? $shippingRate->etd_min . ' - ' . $shippingRate->etd_max . ' Hari' : 'Estimasi pengiriman belum tersedia.' }}</div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Berat Minimum</div>
                        <div class="info-value">{{ $shippingRate->min_weight ? number_format($shippingRate->min_weight, 2, ',', '.') . ' kg' : 'Data tarif pengiriman belum lengkap.' }}</div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Berat Maksimum</div>
                        <div class="info-value">{{ $shippingRate->max_weight ? number_format($shippingRate->max_weight, 2, ',', '.') . ' kg' : 'Tanpa batas maksimum' }}</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="detail-sidebar">
            <div class="detail-card">
                <div class="detail-card-title">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="2" y="3" width="20" height="14" rx="2" ry="2"></rect>
                        <line x1="8" y1="21" x2="16" y2="21"></line>
                        <line x1="12" y1="17" x2="12" y2="21"></line>
                    </svg>
                    Informasi Layanan
                </div>
                <div class="info-list">
                    <div class="info-item">
                        <div class="info-label">Nama Kurir Ekspedisi</div>
                        <div class="info-value">{{ $shippingRate->service && $shippingRate->service->courier ? $shippingRate->service->courier->name : 'Data tarif pengiriman belum lengkap.' }}</div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Layanan Pengiriman</div>
                        <div class="info-value">{{ $shippingRate->service ? $shippingRate->service->service_name : 'Data tarif pengiriman belum lengkap.' }}</div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Status Layanan Induk</div>
                        <div>
                            @if($shippingRate->service && $shippingRate->service->is_active && $shippingRate->service->courier && $shippingRate->service->courier->is_active)
                                <span class="badge badge-active">Layanan Tersedia</span>
                            @else
                                <span class="badge badge-inactive">Layanan Tidak Tersedia</span>
                            @endif
                        </div>
                    </div>
                    <div class="info-item" style="margin-top: 8px;">
                        <div class="info-label">Status Tarif (Rute)</div>
                        <div>
                            @if($shippingRate->is_active)
                                <span class="badge badge-active"><span style="width: 6px; height: 6px; border-radius: 50%; background: var(--green); display:inline-block; margin-right:4px;"></span>Aktif Digunakan</span>
                            @else
                                <span class="badge badge-inactive"><span style="width: 6px; height: 6px; border-radius: 50%; background: var(--text-4); display:inline-block; margin-right:4px;"></span>Dinonaktifkan</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <div class="detail-card">
                <div class="detail-card-title">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10"></circle>
                        <polyline points="12 6 12 12 16 14"></polyline>
                    </svg>
                    Riwayat Sistem
                </div>
                <div class="info-list">
                    <div class="info-item">
                        <div class="info-label">Ditambahkan Pada</div>
                        <div class="info-value">{{ $shippingRate->created_at->format('d M Y, H:i') }}</div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Terakhir Diperbarui</div>
                        <div class="info-value">{{ $shippingRate->updated_at->format('d M Y, H:i') }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
