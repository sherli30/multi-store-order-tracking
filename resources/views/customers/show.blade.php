@extends('layouts.app')

@section('title', 'Detail Pelanggan — ' . $customer->name)

@section('styles')
    /* =============================================
    CUSTOMER SHOW — Professional Detail Page v2
    ============================================= */

    /* ── Breadcrumb ──────────────────────────────── */
    .breadcrumb {
    display: flex; align-items: center; gap: 8px;
    margin-bottom: 24px; font-size: 12.5px; color: var(--text-3); flex-wrap: wrap;
    }
    .breadcrumb a { color: var(--text-3); text-decoration: none; transition: color 0.15s; }
    .breadcrumb a:hover { color: var(--accent); }
    .breadcrumb svg { width: 12px; height: 12px; color: var(--text-4); flex-shrink: 0; }
    .breadcrumb .current { color: var(--text-1); font-weight: 600; }

    /* ── Page Header ─────────────────────────────── */
    .cust-page-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 28px;
    flex-wrap: wrap;
    gap: 12px;
    }
    .cust-page-header-left {
    display: flex;
    align-items: center;
    gap: 14px;
    }
    .cust-page-icon {
    width: 40px;
    height: 40px;
    background: linear-gradient(135deg, var(--accent), color-mix(in srgb, var(--accent) 70%, #7c3aed));
    border-radius: 11px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    box-shadow: 0 4px 12px var(--accent-glow);
    }
    .cust-page-icon svg {
    width: 19px;
    height: 19px;
    color: #fff;
    }
    .cust-page-header-text h1 {
    font-size: 20px;
    font-weight: 800;
    letter-spacing: -0.03em;
    color: var(--text-1);
    margin-bottom: 3px;
    }
    .cust-page-header-text p {
    font-size: 13px;
    color: var(--text-3);
    }
    .cust-btn-back {
    display: inline-flex;
    align-items: center;
    gap: 7px;
    padding: 9px 18px;
    border: 1px solid var(--border);
    border-radius: 9px;
    font-family: var(--font);
    font-size: 13px;
    font-weight: 600;
    color: var(--text-2);
    background: var(--panel);
    cursor: pointer;
    text-decoration: none;
    transition: all 0.15s;
    }
    .cust-btn-back:hover {
    border-color: var(--border-2);
    color: var(--text-1);
    background: var(--surface);
    }
    .cust-btn-back svg {
    width: 14px;
    height: 14px;
    }

    /* ── Layout Grid ─────────────────────────────── */
    .detail-grid {
    display: grid;
    grid-template-columns: 320px 1fr;
    gap: 20px;
    align-items: start;
    }
    @media (max-width: 960px) { .detail-grid { grid-template-columns: 1fr; } }

    /* ── Card Base ───────────────────────────────── */
    .detail-card {
    background: var(--panel);
    border: 1px solid var(--border);
    border-radius: 14px;
    overflow: hidden;
    box-shadow: var(--shadow-sm);
    animation: rise 0.3s ease both;
    }
    .detail-card + .detail-card { margin-top: 20px; }

    .card-header {
    display: flex; align-items: center; justify-content: space-between;
    padding: 16px 22px;
    border-bottom: 1px solid var(--border);
    background: var(--surface);
    }
    .card-header-left { display: flex; align-items: center; gap: 10px; }
    .card-header-icon {
    width: 32px; height: 32px; border-radius: 8px;
    display: flex; align-items: center; justify-content: center;
    }
    .card-header-icon svg { width: 15px; height: 15px; }
    .card-header-icon.blue { background: rgba(59,130,246,0.1); color: #3b82f6; }
    .card-header-icon.green { background: var(--green-dim); color: var(--green); }
    .card-header-icon.purple { background: rgba(139,92,246,0.1); color: #8b5cf6; }
    .card-header-icon.amber { background: var(--amber-dim); color: var(--amber); }
    .card-header-icon.red { background: var(--red-dim); color: var(--red); }
    .card-title { font-size: 13.5px; font-weight: 700; color: var(--text-1); }
    .card-subtitle { font-size: 11.5px; color: var(--text-3); margin-top: 1px; }
    .card-badge {
    font-size: 12px; font-weight: 700; color: var(--accent);
    background: color-mix(in srgb, var(--accent) 10%, transparent);
    padding: 4px 12px; border-radius: 20px;
    }
    .card-body { padding: 22px; }

    /* ── Profile Section ─────────────────────────── */
    .profile-section {
    text-align: center;
    padding: 32px 22px 24px;
    border-bottom: 1px solid var(--border);
    position: relative;

    /* TAMBAHAN FIX */
    display: flex;
    flex-direction: column;
    align-items: center; /* biar semua isi center horizontal */
    }
    .profile-avatar-wrap {
    width: 84px;
    height: 84px;
    border-radius: 50%;
    margin: 0 auto 16px;
    position: relative;
    }
    .profile-avatar-wrap img {
    width: 84px;
    height: 84px;
    border-radius: 50%;
    object-fit: cover;
    border: 2.5px solid color-mix(in srgb, var(--accent) 22%, transparent);
    }
    .profile-section .profile-avatar-initials {
    width: 84px;
    height: 84px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 800;
    font-size: 32px;
    background: linear-gradient(135deg,
    color-mix(in srgb, var(--accent) 18%, transparent),
    color-mix(in srgb, var(--accent) 8%, transparent));
    color: var(--accent);
    border: 2.5px solid color-mix(in srgb, var(--accent) 22%, transparent);
    }
    .profile-section .profile-name {
    font-size: 17px;
    font-weight: 800;
    color: var(--text-1);
    margin-bottom: 4px;

    text-align: center;

    /* FIX 1 BARIS */
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    max-width: 100%;
    }
    .profile-email { font-size: 13px; font-weight: 600; color: var(--accent); margin-bottom: 14px; word-break: break-all; }
    .profile-badge {
    display: inline-flex; align-items: center; gap: 6px;
    padding: 6px 16px; border-radius: 24px;
    font-size: 12.5px; font-weight: 700;
    }
    .profile-badge::before { content: ''; width: 7px; height: 7px; border-radius: 50%; }
    .profile-badge.active { background: var(--green-dim); color: var(--green); border: 1.5px solid rgba(22,163,74,0.25); }
    .profile-badge.active::before { background: var(--green); animation: pulse-dot 1.8s ease-in-out infinite; }
    .profile-badge.blocked { background: var(--red-dim); color: var(--red); border: 1.5px solid rgba(220,38,38,0.25); }
    .profile-badge.blocked::before { background: var(--red); }

    @keyframes pulse-dot {
    0%,100% { box-shadow: 0 0 0 2px rgba(22,163,74,0.2); }
    50% { box-shadow: 0 0 0 5px rgba(22,163,74,0.06); }
    }

    /* ── Mini Stats ──────────────────────────────── */
    .mini-stats {
    display: grid; grid-template-columns: 1fr 1fr;
    gap: 10px; padding: 18px 22px;
    border-bottom: 1px solid var(--border);
    }
    .mini-stat {
    background: var(--surface); border: 1px solid var(--border);
    border-radius: 10px; padding: 14px; text-align: center;
    transition: box-shadow 0.15s, transform 0.15s;
    }
    .mini-stat:hover { box-shadow: var(--shadow-sm); transform: translateY(-1px); }
    .mini-stat-value { font-size: 18px; font-weight: 800; color: var(--text-1); letter-spacing: -0.02em; }
    .mini-stat-value.accent { color: var(--accent); }
    .mini-stat-value.green { color: var(--green); }
    .mini-stat-label {
    font-size: 10.5px; font-weight: 600; color: var(--text-3);
    text-transform: uppercase; letter-spacing: 0.05em; margin-top: 3px;
    }

    /* ── Info List ───────────────────────────────── */
    .info-list { padding: 8px 22px 18px; }
    .info-row { padding: 12px 0; border-bottom: 1px solid var(--border); }
    .info-row:last-child { border-bottom: none; }
    .info-label {
    font-size: 10.5px; font-weight: 700; color: var(--text-3);
    text-transform: uppercase; letter-spacing: 0.07em; margin-bottom: 5px;
    display: flex; align-items: center; gap: 5px;
    }
    .info-label svg { width: 11px; height: 11px; }
    .info-value { font-size: 13.5px; font-weight: 600; color: var(--text-1); line-height: 1.5; }
    .info-value.muted { color: var(--text-3); font-weight: 500; font-style: italic; }
    .info-value.mono { font-family: 'Courier New', monospace; font-size: 12.5px; }

    /* ── Admin Controls ──────────────────────────── */
    .admin-control {
    padding: 18px 22px;
    border-top: 1px solid var(--border);
    }
    .control-section-title {
    font-size: 10.5px; font-weight: 700; color: var(--text-3);
    text-transform: uppercase; letter-spacing: 0.08em;
    margin-bottom: 14px; display: flex; align-items: center; gap: 6px;
    }
    .control-section-title svg { width: 12px; height: 12px; }

    .action-btn {
    display: flex; align-items: center; justify-content: center; gap: 8px;
    width: 100%; padding: 11px 18px; border-radius: 10px;
    font-family: var(--font); font-size: 13px; font-weight: 700;
    cursor: pointer; transition: all 0.15s; border: none; text-align: center;
    margin-bottom: 10px;
    }
    .action-btn:last-child { margin-bottom: 0; }
    .action-btn svg { width: 15px; height: 15px; }
    .action-btn.toggle-active { background: var(--red-dim); color: var(--red); border: 1.5px solid rgba(220,38,38,0.25); }
    .action-btn.toggle-active:hover { background: rgba(220,38,38,0.15); }
    .action-btn.toggle-inactive { background: var(--green-dim); color: var(--green); border: 1.5px solid
    rgba(22,163,74,0.25); }
    .action-btn.toggle-inactive:hover { background: rgba(22,163,74,0.15); }
    .action-btn.delete { background: var(--surface); color: var(--text-3); border: 1.5px solid var(--border); }
    .action-btn.delete:hover { background: var(--red-dim); color: var(--red); border-color: rgba(220,38,38,0.3); }

    .control-note {
    font-size: 11px; color: var(--text-4); text-align: center;
    margin-top: 7px; line-height: 1.5;
    }
    .control-divider { height: 1px; background: var(--border); margin: 16px 0; }

    /* ── Order Table ─────────────────────────────── */
    .order-table { width: 100%; border-collapse: collapse; }
    .order-table th {
    background: var(--surface); padding: 12px 20px;
    text-align: left; font-size: 10.5px; font-weight: 700;
    color: var(--text-3); border-bottom: 1px solid var(--border);
    white-space: nowrap; text-transform: uppercase; letter-spacing: 0.08em;
    }
    .order-table td { padding: 14px 20px; border-bottom: 1px solid var(--border); vertical-align: middle; }
    .order-table tr:last-child td { border-bottom: none; }
    .order-table tbody tr { transition: background 0.12s; }
    .order-table tbody tr:hover td { background: color-mix(in srgb, var(--accent) 3%, var(--surface)); }

    .order-num {
    font-family: 'Courier New', monospace; font-size: 12.5px;
    font-weight: 700; color: var(--accent); text-decoration: none;
    }
    .order-num:hover { text-decoration: underline; }
    .order-date { font-size: 11px; color: var(--text-3); margin-top: 3px; }
    .store-name { font-size: 13px; font-weight: 600; color: var(--text-1); }
    .amount-val { font-size: 13.5px; font-weight: 800; color: var(--text-1); }

    .order-status-pill {
    display: inline-flex; align-items: center; gap: 5px;
    font-size: 11px; font-weight: 700;
    padding: 4px 10px; border-radius: 20px; white-space: nowrap;
    }
    .order-status-pill::before { content: ''; width: 6px; height: 6px; border-radius: 50%; flex-shrink: 0; }
    .status-pending { background: var(--amber-dim); color: var(--amber); }
    .status-pending::before { background: var(--amber); }
    .status-perlu_diproses { background: rgba(14,165,233,0.1); color: #0ea5e9; }
    .status-perlu_diproses::before { background: #0ea5e9; }
    .status-processing { background: rgba(59,130,246,0.1); color: #3b82f6; }
    .status-processing::before { background: #3b82f6; }
    .status-shipping { background: rgba(139,92,246,0.1); color: #8b5cf6; }
    .status-shipping::before { background: #8b5cf6; }
    .status-completed { background: var(--green-dim); color: var(--green); }
    .status-completed::before { background: var(--green); }
    .status-cancelled { background: var(--surface-2); color: var(--text-3); }
    .status-cancelled::before { background: var(--text-4); }

    /* ── Empty State ─────────────────────────────── */
    .empty-orders { padding: 64px 20px; text-align: center; }
    .empty-orders-icon {
    width: 72px; height: 72px; background: var(--surface-2); border-radius: 18px;
    display: flex; align-items: center; justify-content: center; margin: 0 auto 18px;
    }
    .empty-orders-icon svg { width: 32px; height: 32px; color: var(--text-4); }
    .empty-orders-title { font-size: 14px; font-weight: 700; color: var(--text-2); margin-bottom: 5px; }
    .empty-orders-desc { font-size: 12.5px; color: var(--text-3); }

    /* ── Pagination ──────────────────────────────── */
    .pagination-wrap {
    display: flex; align-items: center; justify-content: space-between;
    padding: 14px 20px; border-top: 1px solid var(--border);
    background: var(--surface); gap: 12px; flex-wrap: wrap;
    }
    .pagination-info { font-size: 12px; color: var(--text-3); }

    /* ── Summary Stats ───────────────────────────── */
    .summary-grid {
    display: grid; grid-template-columns: repeat(3, 1fr);
    gap: 14px; margin-bottom: 20px;
    }
    .summary-stat {
    background: var(--surface); border: 1px solid var(--border);
    border-radius: 10px; padding: 16px; text-align: center;
    transition: box-shadow 0.15s, transform 0.15s;
    }
    .summary-stat:hover { box-shadow: var(--shadow-sm); transform: translateY(-1px); }
    .summary-stat-value { font-size: 20px; font-weight: 800; letter-spacing: -0.03em; }
    .summary-stat-label {
    font-size: 10.5px; font-weight: 600; color: var(--text-3);
    text-transform: uppercase; letter-spacing: 0.05em; margin-top: 4px;
    }

    /* ── Status Bar Chart ────────────────────────── */
    .status-bar-wrap { display: flex; flex-direction: column; gap: 10px; }
    .status-bar-row { display: flex; align-items: center; gap: 10px; }
    .status-bar-dot { width: 8px; height: 8px; border-radius: 50%; flex-shrink: 0; }
    .status-bar-name { font-size: 12.5px; font-weight: 600; color: var(--text-2); min-width: 80px; }
    .status-bar-track {
    flex: 1; background: var(--surface-2);
    border-radius: 4px; height: 7px; overflow: hidden;
    }
    .status-bar-fill {
    height: 100%; border-radius: 4px;
    transition: width 0.6s cubic-bezier(0.34, 1.56, 0.64, 1);
    }
    .status-bar-count {
    font-size: 12px; font-weight: 700; color: var(--text-1);
    min-width: 24px; text-align: right;
    }

    @media (max-width: 600px) { .summary-grid { grid-template-columns: 1fr 1fr; } }

    /* ── Modal Overlay ───────────────────────────── */
    .modal-overlay {
    position: fixed; top: 0; left: 0; width: 100%; height: 100%;
    background: rgba(0, 0, 0, 0.5); backdrop-filter: blur(4px);
    display: flex; align-items: center; justify-content: center;
    z-index: 9999; opacity: 0; visibility: hidden;
    transition: all 0.25s ease; padding: 20px;
    }
    .modal-overlay.open { opacity: 1; visibility: visible; }

    /* ── Modal Box ───────────────────────────────── */
    .modal-box {
    background: #fff; width: 100%; max-width: 400px;
    border-radius: 20px; padding: 32px; text-align: center;
    transform: translateY(20px); transition: all 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
    box-shadow: 0 20px 25px -5px rgba(0,0,0,0.1), 0 10px 10px -5px rgba(0,0,0,0.04);
    }
    .modal-overlay.open .modal-box { transform: translateY(0); }

    .modal-icon {
    width: 64px; height: 64px; background: rgba(239, 68, 68, 0.1);
    color: #ef4444; border-radius: 50%; display: flex;
    align-items: center; justify-content: center; margin: 0 auto 20px;
    }
    .modal-icon svg { width: 32px; height: 32px; }

    .modal-title { font-size: 20px; font-weight: 800; color: var(--text-1); margin-bottom: 12px; }
    .modal-desc { font-size: 14px; color: var(--text-3); line-height: 1.6; margin-bottom: 28px; }

    /* ── Modal Actions ───────────────────────────── */
    .modal-actions { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; }

    .btn-cancel, .btn-danger {
    padding: 12px; border-radius: 12px; font-size: 14px; font-weight: 600;
    cursor: pointer; transition: all 0.2s; border: none;
    }
    .btn-cancel { background: var(--bg-2); color: var(--text-2); }
    .btn-cancel:hover { background: var(--bg-3); }

    .btn-danger { background: #ef4444; color: #fff; }
    .btn-danger:hover { background: #dc2626; transform: translateY(-2px); box-shadow: 0 4px 12px rgba(239, 68, 68, 0.3); }

    #btn-confirm-submit {
    transition: background-color 0.2s ease, transform 0.2s ease;
    }

    .modal-icon {
    transition: background-color 0.2s ease, color 0.2s ease;
    }
@endsection

@section('content')

    {{-- Breadcrumb --}}
    <div class="breadcrumb">
        <a href="{{ route('dashboard') }}">Dashboard</a>
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"
            stroke-linejoin="round">
            <polyline points="9 18 15 12 9 6" />
        </svg>
        <a href="{{ route('customers.index') }}">Manajemen Pelanggan</a>
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"
            stroke-linejoin="round">
            <polyline points="9 18 15 12 9 6" />
        </svg>
        <span class="current">{{ $customer->name }}</span>
    </div>

    {{-- Page Header --}}
    <div class="cust-page-header">
        <div class="cust-page-header-left">
            <div class="cust-page-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                    stroke-linejoin="round">
                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2" />
                    <circle cx="12" cy="7" r="4" />
                </svg>
            </div>
            <div class="cust-page-header-text">
                <h1>Profil Pelanggan</h1>
                <p>Terdaftar {{ $customer->created_at->diffForHumans() }} · {{ $customer->created_at->format('d M Y') }}</p>
            </div>
        </div>
        <div>
            <a href="{{ route('customers.index') }}" class="cust-btn-back">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                    stroke-linejoin="round">
                    <line x1="19" y1="12" x2="5" y2="12" />
                    <polyline points="12 19 5 12 12 5" />
                </svg>
                Kembali ke Daftar
            </a>
        </div>
    </div>

    {{-- Main Grid --}}
    <div class="detail-grid">

        {{-- ===== LEFT COLUMN ===== --}}
        <div>
            <div class="detail-card">

                {{-- Profile Header --}}
                <div class="profile-section">
                    {{-- Avatar: tampilkan foto jika ada, fallback ke inisial --}}
                    <div class="profile-avatar-wrap">
                        @if($customer->avatar)
                            <img src="{{ Storage::url($customer->avatar) }}" alt="{{ $customer->name }}"
                                onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                            <div class="profile-avatar-initials" style="display:none;">
                                {{ strtoupper(substr($customer->name, 0, 1)) }}
                            </div>
                        @else
                            <div class="profile-avatar-initials">
                                {{ strtoupper(substr($customer->name, 0, 1)) }}
                            </div>
                        @endif
                    </div>

                    <div class="profile-name">{{ $customer->name }}</div>
                    <div class="profile-email">{{ $customer->email }}</div>
                    <span class="profile-badge {{ $customer->is_active ? 'active' : 'blocked' }}">
                        {{ $customer->is_active ? 'Akun Aktif' : 'Akun Diblokir' }}
                    </span>
                </div>

                {{-- Mini Stats --}}
                <div class="mini-stats">
                    <div class="mini-stat">
                        <div class="mini-stat-value accent">{{ number_format($totalOrders) }}</div>
                        <div class="mini-stat-label">Total Pesanan</div>
                    </div>
                    <div class="mini-stat">
                        <div class="mini-stat-value green" style="font-size:15px;">
                            Rp {{ number_format($totalSpent, 0, ',', '.') }}
                        </div>
                        <div class="mini-stat-label">Total Belanja</div>
                    </div>
                </div>

                {{-- Info List --}}
                <div class="info-list">
                    <div class="info-row">
                        <div class="info-label">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                stroke-linecap="round" stroke-linejoin="round">
                                <rect x="2" y="3" width="20" height="18" rx="3" />
                                <line x1="8" y1="9" x2="16" y2="9" />
                                <line x1="8" y1="13" x2="14" y2="13" />
                            </svg>
                            ID Pelanggan
                        </div>
                        <div class="info-value mono">#{{ $customer->id }}</div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                stroke-linecap="round" stroke-linejoin="round">
                                <path
                                    d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07A19.5 19.5 0 0 1 4.69 12 19.79 19.79 0 0 1 1.61 3.38 2 2 0 0 1 3.58 1h3a2 2 0 0 1 2 1.72c.13.96.36 1.9.7 2.81a2 2 0 0 1-.45 2.11L7.91 8.54a16 16 0 0 0 6 6l.92-.92a2 2 0 0 1 2.11-.45c.91.34 1.85.57 2.81.7A2 2 0 0 1 22 16.92z" />
                            </svg>
                            WhatsApp / No. HP
                        </div>
                        <div class="info-value {{ $customer->phone ? '' : 'muted' }}">
                            {{ $customer->phone ?: 'Belum diisi' }}
                        </div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M21 10c0 7-9 13-9 13S3 17 3 10a9 9 0 0 1 18 0z" />
                                <circle cx="12" cy="10" r="3" />
                            </svg>
                            Alamat
                        </div>
                        <div class="info-value {{ $customer->address ? '' : 'muted' }}">
                            {{ $customer->address ?: 'Belum diisi.' }}
                        </div>
                    </div>

                    @if($customer->city)
                    <div class="info-row">
                        <div class="info-label">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M3 21h18M3 7v14M21 7v14M9 21V11h6v10M2 7l10-4 10 4" />
                            </svg>
                            Kota
                        </div>
                        <div class="info-value">{{ $customer->city }}</div>
                    </div>
                    @endif

                    @if($customer->province)
                    <div class="info-row">
                        <div class="info-label">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <polygon points="1 6 1 22 8 18 16 22 23 18 23 2 16 6 8 2 1 6" />
                                <line x1="8" y1="2" x2="8" y2="18" />
                                <line x1="16" y1="6" x2="16" y2="22" />
                            </svg>
                            Provinsi
                        </div>
                        <div class="info-value">{{ $customer->province }}</div>
                    </div>
                    @endif

                    @if($customer->postal_code)
                    <div class="info-row">
                        <div class="info-label">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z" />
                                <polyline points="22,6 12,13 2,6" />
                            </svg>
                            Kode Pos
                        </div>
                        <div class="info-value">{{ $customer->postal_code }}</div>
                    </div>
                    @endif
                    <div class="info-row">
                        <div class="info-label">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                stroke-linecap="round" stroke-linejoin="round">
                                <rect x="3" y="4" width="18" height="18" rx="2" />
                                <line x1="16" y1="2" x2="16" y2="6" />
                                <line x1="8" y1="2" x2="8" y2="6" />
                                <line x1="3" y1="10" x2="21" y2="10" />
                            </svg>
                            Tanggal Mendaftar
                        </div>
                        <div class="info-value">{{ $customer->created_at->translatedFormat('d F Y, H:i') }} WIB</div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="12" cy="12" r="10" />
                                <polyline points="12 6 12 12 16 14" />
                            </svg>
                            Terakhir Diperbarui
                        </div>
                        <div class="info-value">{{ $customer->updated_at->translatedFormat('d F Y, H:i') }} WIB</div>
                    </div>
                </div>

                {{-- Admin Controls --}}
                <div class="admin-control">
                    <div class="control-section-title">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                            stroke-linejoin="round">
                            <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z" />
                        </svg>
                        Kontrol Admin
                    </div>

                    @if($customer->is_active)
                        {{-- Tombol Blokir / Nonaktifkan --}}
                        <button type="button" class="action-btn toggle-active"
                            onclick="openConfirmModal('block', '{{ $customer->id }}', '{{ addslashes($customer->name) }}')">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round">
                                <circle cx="12" cy="12" r="10" />
                                <line x1="4.93" y1="4.93" x2="19.07" y2="19.07" />
                            </svg>
                            Blokir / Nonaktifkan Akun
                        </button>
                    @else
                        {{-- Tombol Aktifkan Kembali --}}
                        <button type="button" class="action-btn toggle-inactive"
                            onclick="openConfirmModal('activate', '{{ $customer->id }}', '{{ addslashes($customer->name) }}')">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round">
                                <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14" />
                                <polyline points="22 4 12 14.01 9 11.01" />
                            </svg>
                            Aktifkan Kembali Akun
                        </button>
                    @endif

                    <p class="control-note">
                        {{ $customer->is_active ? 'Pelanggan yang diblokir tidak dapat login ke aplikasi.' : 'Mengaktifkan kembali akses pelanggan ke aplikasi.' }}
                    </p>

                    <div class="control-divider"></div>

                    {{-- Tombol Hapus Permanen --}}
                    <button type="button" class="action-btn delete"
                        onclick="openConfirmModal('delete', '{{ $customer->id }}', '{{ addslashes($customer->name) }}')">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                            stroke-linejoin="round">
                            <polyline points="3 6 5 6 21 6" />
                            <path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6" />
                            <path d="M10 11v6" />
                            <path d="M14 11v6" />
                            <path d="M9 6V4h6v2" />
                        </svg>
                        Hapus Permanen Akun Ini
                    </button>
                </div>

                {{-- Hidden Form untuk eksekusi --}}
                {{-- Letakkan ini di bawah bagian admin-control --}}
                <form id="statusForm" method="POST" style="display: none;">
                    @csrf
                    <input type="hidden" id="methodField" name="_method" value="PATCH">
                    {{-- Atribut name HARUS is_active --}}
                    <input type="hidden" id="statusInput" name="is_active" value="">
                </form>
            </div>
        </div>
        {{-- ===== END LEFT COLUMN ===== --}}

        {{-- ===== RIGHT COLUMN ===== --}}
        <div>

            {{-- Riwayat Pesanan --}}
            <div class="detail-card">
                <div class="card-header">
                    <div class="card-header-left">
                        <div class="card-header-icon purple">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                stroke-linecap="round" stroke-linejoin="round">
                                <path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z" />
                                <line x1="3" y1="6" x2="21" y2="6" />
                                <path d="M16 10a4 4 0 0 1-8 0" />
                            </svg>
                        </div>
                        <div>
                            <div class="card-title">Riwayat Pesanan</div>
                            <div class="card-subtitle">Semua transaksi yang pernah dilakukan pelanggan ini</div>
                        </div>
                    </div>
                    <span class="card-badge">{{ $totalOrders }} Pesanan</span>
                </div>

                @if($orders->isEmpty())
                    <div class="empty-orders">
                        <div class="empty-orders-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                                stroke-linejoin="round">
                                <path
                                    d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z" />
                                <polyline points="3.27 6.96 12 12.01 20.73 6.96" />
                                <line x1="12" y1="22.08" x2="12" y2="12" />
                            </svg>
                        </div>
                        <div class="empty-orders-title">Belum Ada Riwayat Pesanan</div>
                        <div class="empty-orders-desc">Pelanggan ini belum pernah melakukan transaksi.</div>
                    </div>
                @else
                    <div style="overflow-x: auto;">
                        <table class="order-table">
                            <thead>
                                <tr>
                                    <th>Nomor Pesanan</th>
                                    <th>Toko / Vendor</th>
                                    <th>Total Bayar</th>
                                    <th>Status</th>
                                    <th>Waktu Checkout</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($orders as $order)
                                    <tr>
                                        <td>
                                            <a href="{{ route('orders.show', $order->id) }}" class="order-num" style="font-size: 11px;">
                                                {{ $order->order_number }}
                                            </a>
                                            <div class="order-date">{{ $order->created_at->format('d M Y') }}</div>
                                        </td>
                                        <td>
                                            <div class="store-name">{{ $order->store->name ?? '—' }}</div>
                                        </td>
                                        <td>
                                            <div class="amount-val">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</div>
                                        </td>
                                        <td>
                                            <span class="order-status-pill status-{{ $order->status }}">
                                                {{ \App\Services\StatusService::getOrderLabel($order->status) }}
                                            </span>
                                        </td>
                                        <td style="font-size: 12px; color: var(--text-3); white-space: nowrap;">
                                            {{ $order->created_at->format('d/m/Y H:i') }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="pagination-wrap">
                        <div class="pagination-info">
                            Menampilkan <strong>{{ $orders->firstItem() }}–{{ $orders->lastItem() }}</strong>
                            dari <strong>{{ $orders->total() }}</strong> pesanan
                        </div>
                        @if($orders->hasPages())
                            <div>{{ $orders->links('vendor.pagination.custom') }}</div>
                        @endif
                    </div>
                @endif
            </div>

            {{-- Ringkasan Belanja --}}
            <div class="detail-card" style="margin-top: 20px;">
                <div class="card-header">
                    <div class="card-header-left">
                        <div class="card-header-icon green">
                            <span style="
                                font-size: 16px;
                                font-weight: 800;
                                color: currentColor;
                                line-height: 1;
                            ">
                                Rp
                            </span>
                        </div>
                        <div>
                            <div class="card-title">Ringkasan Belanja</div>
                            <div class="card-subtitle">Statistik transaksi keseluruhan pelanggan</div>
                        </div>
                    </div>
                </div>
                <div class="card-body">

                    {{-- Summary Stats --}}
                    <div class="summary-grid">
                        <div class="summary-stat">
                            <div class="summary-stat-value" style="color: var(--text-1);">
                                {{ number_format($totalOrders) }}
                            </div>
                            <div class="summary-stat-label">Total Pesanan</div>
                        </div>
                        <div class="summary-stat">
                            <div class="summary-stat-value" style="color: var(--green); font-size:17px;">
                                Rp {{ number_format($totalSpent, 0, ',', '.') }}
                            </div>
                            <div class="summary-stat-label">Total Belanja</div>
                        </div>
                        <div class="summary-stat">
                            <div class="summary-stat-value" style="color: var(--accent); font-size:17px;">
                                Rp
                                {{ $totalOrders > 0 ? number_format($totalSpent / $totalOrders, 0, ',', '.') : '0' }}
                            </div>
                            <div class="summary-stat-label">Rata-rata/Pesanan</div>
                        </div>
                    </div>

                    {{-- Status Breakdown --}}
                    @php
                        $statusBreakdown = $orders->getCollection()->groupBy('status');
                        $allStatuses = [];
                        
                        $colors = [
                            'pending' => 'var(--amber)',
                            'perlu_diproses' => '#0ea5e9',
                            'processing' => '#3b82f6',
                            'shipping' => '#8b5cf6',
                            'completed' => 'var(--green)',
                            'cancelled' => 'var(--text-3)'
                        ];
                        
                        foreach(\App\Services\StatusService::getOrderStatuses() as $st) {
                            $allStatuses[$st] = [
                                'label' => \App\Services\StatusService::getOrderLabel($st),
                                'color' => $colors[$st] ?? 'var(--text-3)'
                            ];
                        }
                    @endphp

                    <div style="padding-top: 18px; border-top: 1px solid var(--border);">
                        <div
                            style="font-size: 11px; font-weight: 700; color: var(--text-3); text-transform: uppercase; letter-spacing: 0.07em; margin-bottom: 14px;">
                            Distribusi Status Pesanan
                        </div>
                        <div class="status-bar-wrap">
                            @foreach($allStatuses as $key => $meta)
                                @php
                                    $count = $statusBreakdown->get($key, collect())->count();
                                    $pct = $totalOrders > 0 ? round($count / $totalOrders * 100) : 0;
                                @endphp
                                @if($count > 0)
                                    <div class="status-bar-row">
                                        <div class="status-bar-dot" style="background: {{ $meta['color'] }};"></div>
                                        <div class="status-bar-name">{{ $meta['label'] }}</div>
                                        <div class="status-bar-track">
                                            <div class="status-bar-fill"
                                                style="width: {{ $pct }}%; background: {{ $meta['color'] }};"
                                                data-width="{{ $pct }}">
                                            </div>
                                        </div>
                                        <div class="status-bar-count">{{ $count }}</div>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    </div>

                </div>
            </div>

        </div>
        {{-- ===== END RIGHT COLUMN ===== --}}

    </div>
    {{-- Modal Konfirmasi --}}
    <div id="confirmModal" class="modal-overlay">
        <div class="modal-box">
            <div id="modal-icon-bg" class="modal-icon">
                <div id="modal-icon-content"></div>
            </div>
            <h3 id="modal-title" class="modal-title">Konfirmasi</h3>
            <p id="modal-desc" class="modal-desc"></p>

            <div class="modal-actions">
                <button type="button" class="btn-cancel" onclick="closeConfirmModal()">Batal</button>
                <button type="button" id="btn-confirm-submit" class="btn-danger" onclick="submitStatusForm()">Ya,
                    Lanjutkan</button>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
    <script>
        // Flash alert auto-hide
        function openConfirmModal(type, id, name) {
            const modal = document.getElementById('confirmModal');
            const title = document.getElementById('modal-title');
            const desc = document.getElementById('modal-desc');
            const iconBg = document.getElementById('modal-icon-bg');
            const iconContent = document.getElementById('modal-icon-content');
            const btnSubmit = document.getElementById('btn-confirm-submit');
            const form = document.getElementById('statusForm');
            const statusInput = document.getElementById('statusInput');
            const methodField = document.getElementById('methodField');

            // 1. RESET DEFAULT ke mode Merah (Blokir/Hapus)
            btnSubmit.innerText = "Ya, Lanjutkan";
            btnSubmit.style.backgroundColor = "#ef4444";
            btnSubmit.style.color = "#ffffff";
            iconBg.style.backgroundColor = "rgba(239, 68, 68, 0.1)";
            iconBg.style.color = "#ef4444";
            methodField.value = "PATCH";

            if (type === 'block') {
                title.innerText = "Blokir Akun";
                desc.innerHTML = `Apakah Anda yakin ingin memblokir <strong>${name}</strong>?`;
                iconContent.innerHTML = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><line x1="4.93" y1="4.93" x2="19.07" y2="19.07"></line></svg>';

                form.action = "{{ url('customers') }}/" + id + "/status";
                statusInput.value = "0"; // Kirim 0 ke Controller
            }
            else if (type === 'activate') {
                title.innerText = "Aktifkan Akun";
                desc.innerHTML = `Aktifkan kembali akses untuk <strong>${name}</strong>?`;
                iconContent.innerHTML = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>';

                // 2. UBAH KE HIJAU khusus untuk aktivasi
                iconBg.style.backgroundColor = "rgba(16, 185, 129, 0.1)";
                iconBg.style.color = "#10b981";
                btnSubmit.style.backgroundColor = "#10b981";
                btnSubmit.innerText = "Ya, Aktifkan";

                form.action = "{{ url('customers') }}/" + id + "/status";
                statusInput.value = "1"; // Kirim 1 ke Controller
            }
            else if (type === 'delete') {
                title.innerText = "Hapus Akun";
                desc.innerHTML = `Hapus <strong>${name}</strong> permanen? Data tidak bisa dikembalikan.`;
                iconContent.innerHTML = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"></path></svg>';

                methodField.value = "DELETE";
                form.action = "{{ url('customers') }}/" + id;
            }

            modal.classList.add('open');
        }
        function closeConfirmModal() {
            document.getElementById('confirmModal').classList.remove('open');
        }

        function submitStatusForm() {
            document.getElementById('statusForm').submit();
        }

        // Close modal when clicking outside
        document.getElementById('confirmModal').addEventListener('click', function (e) {
            if (e.target === this) closeConfirmModal();
        });

        // Animate status bars on load
        document.addEventListener('DOMContentLoaded', () => {
            document.querySelectorAll('.status-bar-fill').forEach(el => {
                const target = el.dataset.width + '%';
                el.style.width = '0%';
                requestAnimationFrame(() => {
                    setTimeout(() => { el.style.width = target; }, 120);
                });
            });
        });
    </script>
@endpush
