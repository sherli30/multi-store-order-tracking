@extends('layouts.app')

@section('title', 'Detail Provinsi - ' . $province->name)

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
    .detail-card-title { font-size: 15px; font-weight: 800; color: var(--text-1); margin-bottom: 20px; display: flex; align-items: center; gap: 8px; }
    .detail-card-title svg { width: 16px; height: 16px; color: var(--accent); }

    .info-list { display: flex; flex-direction: column; gap: 16px; }
    .info-item { display: flex; flex-direction: column; gap: 4px; }
    .info-label { font-size: 11.5px; font-weight: 700; color: var(--text-3); text-transform: uppercase; letter-spacing: 0.05em; }
    .info-value { font-size: 14px; color: var(--text-1); font-weight: 500; }
    .info-value.empty { color: var(--text-4); font-style: italic; }
    .info-value-mono { font-family: var(--mono); font-size: 13px; }

    .badge { display: inline-flex; align-items: center; gap: 5px; padding: 4px 11px; border-radius: 20px; font-size: 11.5px; font-weight: 600; white-space: nowrap; }
    .badge-active { background: var(--green-dim); color: var(--green); border: 1px solid rgba(22,163,74,0.2); }
    .badge-inactive { background: var(--surface-2); color: var(--text-3); border: 1px solid var(--border); }

    .stat-box { background: var(--surface); border: 1px solid var(--border); border-radius: 12px; padding: 16px; text-align: center; }
    .stat-box-value { font-size: 24px; font-weight: 800; color: var(--text-1); margin-bottom: 4px; }
    .stat-box-label { font-size: 12px; color: var(--text-3); font-weight: 500; }
@endsection

@section('content')
    <div class="page-header">
        <div class="page-header-left">
            <h1>
                <span class="page-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path>
                        <circle cx="12" cy="10" r="3"></circle>
                    </svg>
                </span>
                Detail Provinsi: {{ $province->name }}
            </h1>
            <p>Informasi lengkap terkait provinsi wilayah pengiriman ini.</p>
        </div>
        <a href="{{ route('provinces.index') }}" class="btn-outline">
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
                        <circle cx="12" cy="12" r="10"></circle>
                        <line x1="12" y1="16" x2="12" y2="12"></line>
                        <line x1="12" y1="8" x2="12.01" y2="8"></line>
                    </svg>
                    Informasi Provinsi
                </div>
                <div class="info-list">
                    <div class="info-item">
                        <div class="info-label">Nama Provinsi</div>
                        <div class="info-value {{ $province->name ? '' : 'empty' }}">{{ $province->name ?: 'Data provinsi belum lengkap.' }}</div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Kode Provinsi</div>
                        <div class="info-value info-value-mono {{ $province->code ? '' : 'empty' }}">{{ $province->code ?: 'Data provinsi belum lengkap.' }}</div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Status Layanan</div>
                        <div>
                            @if($province->is_active)
                                <span class="badge badge-active"><span style="width: 6px; height: 6px; border-radius: 50%; background: var(--green); display:inline-block; margin-right:4px;"></span>Aktif</span>
                            @else
                                <span class="badge badge-inactive"><span style="width: 6px; height: 6px; border-radius: 50%; background: var(--text-4); display:inline-block; margin-right:4px;"></span>Nonaktif</span>
                            @endif
                        </div>
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
                    Statistik
                </div>
                <div class="stat-box">
                    <div class="stat-box-value">{{ $province->cities_count ?? 0 }}</div>
                    <div class="stat-box-label">{{ $province->cities_count > 0 ? 'Total Kota / Kabupaten' : 'Belum ada kota dalam provinsi ini.' }}</div>
                </div>
            </div>

            <div class="detail-card">
                <div class="detail-card-title">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10"></circle>
                        <polyline points="12 6 12 12 16 14"></polyline>
                    </svg>
                    Riwayat Data
                </div>
                <div class="info-list">
                    <div class="info-item">
                        <div class="info-label">Dibuat Pada</div>
                        <div class="info-value">{{ $province->created_at->format('d M Y, H:i') }}</div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Terakhir Diperbarui</div>
                        <div class="info-value">{{ $province->updated_at->format('d M Y, H:i') }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
