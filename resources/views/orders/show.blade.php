@extends('layouts.app')

@section('title', 'Detail Pesanan #' . $order->order_number)

@section('styles')
<style>
    /* ── Root Extras ── */
    :root {
        --card-radius: 18px;
        --section-gap: 24px;
    }

    /* ── Page Header ── */
    .page-header {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        margin-bottom: 20px;
        flex-wrap: wrap;
        gap: 16px;
    }

    .page-header-left { display: flex; align-items: center; gap: 14px; }

    .page-icon {
        width: 44px; height: 44px; flex-shrink: 0;
        background: linear-gradient(135deg, var(--accent), #7c3aed);
        border-radius: 12px;
        display: flex; align-items: center; justify-content: center;
        box-shadow: 0 8px 20px rgba(79,70,229,0.2);
    }
    .page-icon svg { width: 22px; height: 22px; color: #fff; }

    .page-header-text h1 {
        font-size: 21px; font-weight: 900; letter-spacing: -0.04em;
        color: var(--text-1); margin: 0 0 4px 0;
    }
    .page-header-text p { font-size: 12.5px; color: var(--text-3); margin: 0; }

    .header-actions { display: flex; gap: 10px; align-items: center; flex-wrap: wrap; }

    /* ── Order Metadata Strip ── */
    .meta-strip {
        display: flex; gap: 0; align-items: stretch;
        background: var(--panel); border: 1px solid var(--border);
        border-radius: 14px; overflow: hidden;
        margin-bottom: var(--section-gap);
        box-shadow: var(--shadow-sm);
    }
    .meta-chip {
        flex: 1; padding: 14px 20px;
        border-right: 1px solid var(--border);
        display: flex; flex-direction: column; gap: 4px;
        min-width: 0;
    }
    .meta-chip:last-child { border-right: none; }
    .meta-chip-label {
        font-size: 10px; font-weight: 800; color: var(--text-4);
        text-transform: uppercase; letter-spacing: 0.06em;
    }
    .meta-chip-value {
        font-size: 13px; font-weight: 800; color: var(--text-1);
        white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
    }

    /* ── Progress Tracker ── */
    .progress-wrap {
        background: var(--panel); border: 1px solid var(--border);
        border-radius: var(--card-radius); padding: 28px 32px;
        margin-bottom: var(--section-gap); box-shadow: var(--shadow-sm);
        position: relative;
    }
    .progress-steps {
        display: flex; justify-content: space-between;
        position: relative; z-index: 2;
    }
    .progress-line-track {
        position: absolute; top: 42px; left: 8%; right: 8%;
        height: 3px; background: var(--border); border-radius: 4px; z-index: 1;
    }
    .progress-line-fill {
        height: 100%; background: linear-gradient(90deg, var(--accent), #7c3aed);
        border-radius: 4px; transition: width 0.6s ease;
    }
    .step {
        display: flex; flex-direction: column; align-items: center; gap: 10px;
        flex: 1; position: relative; z-index: 2;
    }
    .step-circle {
        width: 36px; height: 36px; border-radius: 50%;
        background: var(--surface); border: 2px solid var(--border);
        display: flex; align-items: center; justify-content: center;
        color: var(--text-4); transition: all 0.3s;
    }
    .step-circle svg { width: 16px; height: 16px; }
    .step-label { font-size: 10.5px; font-weight: 700; color: var(--text-4); text-align: center; }
    .step.completed .step-circle { background: var(--green); border-color: var(--green); color: #fff; }
    .step.completed .step-label { color: var(--green); }
    .step.active .step-circle {
        background: var(--accent); border-color: var(--accent); color: #fff;
        box-shadow: 0 0 0 5px rgba(79,70,229,0.12);
        transform: scale(1.1);
    }
    .step.active .step-label { color: var(--accent); font-weight: 900; }
    .cancelled-banner {
        display: flex; align-items: center; gap: 12px; padding: 16px 20px;
        background: var(--red-dim); border-radius: 12px; border: 1px solid rgba(220,38,38,0.2);
    }
    .cancelled-banner svg { width: 20px; height: 20px; color: var(--red); flex-shrink: 0; }
    .cancelled-banner-text { font-size: 13px; font-weight: 700; color: var(--red); }
    .cancelled-banner-reason { font-size: 12px; color: var(--red); opacity: 0.8; margin-top: 2px; }

    /* ── Action Box ── */
    .action-box {
        background: linear-gradient(135deg, var(--accent) 0%, #7c3aed 100%);
        padding: 20px 24px; border-radius: var(--card-radius); color: #fff;
        display: flex; align-items: center; justify-content: space-between;
        gap: 20px; flex-wrap: wrap;
        box-shadow: 0 10px 30px rgba(79,70,229,0.25);
        margin-bottom: var(--section-gap);
    }
    .action-text h4 { font-size: 15px; font-weight: 900; margin: 0 0 4px 0; }
    .action-text p  { font-size: 12px; opacity: 0.88; margin: 0; }
    .action-controls { display: flex; gap: 10px; flex-wrap: wrap; align-items: center; }
    .status-select {
        padding: 9px 14px; border-radius: 9px; border: none;
        font-family: var(--font); font-size: 13px; font-weight: 700;
        color: var(--text-1); background: #fff; cursor: pointer;
    }
    .btn-action {
        padding: 9px 20px; border-radius: 9px;
        border: 1.5px solid rgba(255,255,255,0.4);
        background: rgba(255,255,255,0.18); color: #fff;
        font-family: var(--font); font-size: 13px; font-weight: 700;
        cursor: pointer; transition: all 0.2s;
        display: inline-flex; align-items: center; gap: 8px;
    }
    .btn-action:hover { background: #fff; color: var(--accent); border-color: transparent; }
    .btn-action.solid  { background: #fff; color: var(--accent); border-color: transparent; }
    .btn-action.solid:hover { background: rgba(255,255,255,0.88); }
    .btn-action.green-solid { background: #fff; color: var(--green); border-color: transparent; }
    .btn-action.green-solid:hover { opacity: 0.9; }
    .action-field-error {
        width: 100%; font-size: 11.5px;
        color: #fde68a; font-weight: 600; margin-top: 2px;
    }

    /* ── Alert Banners ── */
    .alert-banner {
        padding: 13px 18px; border-radius: 12px; margin-bottom: var(--section-gap);
        display: flex; align-items: center; gap: 12px;
        font-size: 13px; font-weight: 600;
    }
    .alert-banner svg { width: 18px; height: 18px; flex-shrink: 0; }
    .alert-success { background: var(--green-dim); color: var(--green); border: 1px solid rgba(22,163,74,0.2); }
    .alert-error   { background: var(--red-dim);   color: var(--red);   border: 1px solid rgba(220,38,38,0.2); }

    /* ── Main Grid ── */
    .detail-grid {
        display: grid;
        grid-template-columns: 1fr 330px;
        gap: var(--section-gap);
        align-items: start;
    }
    @media (max-width: 1060px) {
        .detail-grid { grid-template-columns: 1fr; }
        .sidebar-priority { order: -1; }
    }
    .detail-main { display: flex; flex-direction: column; gap: var(--section-gap); }
    .detail-side { display: flex; flex-direction: column; gap: var(--section-gap); }

    /* ── Cards ── */
    .detail-card {
        background: var(--panel); border: 1px solid var(--border);
        border-radius: var(--card-radius); overflow: hidden;
        box-shadow: var(--shadow-sm);
    }
    .card-header {
        padding: 16px 22px; border-bottom: 1px solid var(--border);
        display: flex; align-items: center; justify-content: space-between;
        background: linear-gradient(to right, var(--surface), transparent);
    }
    .card-title {
        font-size: 13.5px; font-weight: 900; color: var(--text-1);
        display: flex; align-items: center; gap: 9px;
    }
    .card-title svg { width: 16px; height: 16px; color: var(--accent); }
    .card-body { padding: 20px 22px; }

    /* ── Status Badge ── */
    .status-badge {
        display: inline-flex; align-items: center; gap: 6px;
        padding: 4px 12px; border-radius: 20px;
        font-size: 11px; font-weight: 800;
    }
    .status-badge::before {
        content: ''; width: 6px; height: 6px;
        border-radius: 50%; background: currentColor;
    }
    .status-pending       { background: var(--amber-dim);             color: var(--amber); }
    .status-perlu_diproses{ background: rgba(59,130,246,0.1);         color: #3b82f6; }
    .status-processing    { background: rgba(139,92,246,0.1);         color: #8b5cf6; }
    .status-shipping      { background: var(--accent-dim);            color: var(--accent); }
    .status-completed     { background: var(--green-dim);             color: var(--green); }
    .status-cancelled     { background: var(--red-dim);               color: var(--red); }

    /* ── Product List ── */
    .product-list { display: flex; flex-direction: column; gap: 12px; }
    .product-item {
        display: flex; align-items: center; gap: 14px; padding: 12px;
        border: 1px solid var(--border); border-radius: 12px; transition: all 0.2s;
    }
    .product-item:hover { border-color: var(--accent); background: var(--accent-dim); }
    .product-img {
        width: 58px; height: 58px; border-radius: 9px; object-fit: cover;
        background: var(--surface); border: 1px solid var(--border); flex-shrink: 0;
    }
    .product-detail { flex: 1; min-width: 0; }
    .product-name { font-size: 13.5px; font-weight: 800; color: var(--text-1); }
    .product-variant { font-size: 11px; color: var(--text-3); margin-top: 2px; }
    .product-qty  { font-size: 12px; font-weight: 600; color: var(--text-4); margin-top: 4px; }
    .product-price { font-size: 13.5px; font-weight: 900; color: var(--text-1); flex-shrink: 0; }

    /* ── Receipt ── */
    .receipt {
        margin-top: 20px; padding-top: 18px;
        border-top: 2px dashed var(--border);
    }
    .receipt-row {
        display: flex; justify-content: space-between;
        padding: 7px 0; font-size: 13px; font-weight: 600;
        color: var(--text-3); border-bottom: 1px dashed transparent;
    }
    .receipt-row.divider { border-bottom-color: var(--border); }
    .receipt-total {
        display: flex; justify-content: space-between; align-items: baseline;
        padding-top: 14px; margin-top: 6px;
        border-top: 2px solid var(--border);
    }
    .receipt-total-label { font-size: 14px; font-weight: 900; color: var(--text-1); }
    .receipt-total-value { font-size: 20px; font-weight: 900; color: var(--accent); letter-spacing: -0.03em; }

    /* ── Info Rows ── */
    .info-row {
        display: flex; justify-content: space-between; align-items: flex-start;
        padding: 9px 0; border-bottom: 1px dashed var(--border);
        gap: 12px;
    }
    .info-row:last-child { border-bottom: none; }
    .info-label { font-size: 11.5px; font-weight: 700; color: var(--text-3); white-space: nowrap; }
    .info-value { font-size: 13px; font-weight: 800; color: var(--text-1); text-align: right; }

    /* ── Payment Card Grid ── */
    .payment-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(170px, 1fr));
        gap: 16px;
    }
    .payment-cell {}
    .payment-cell-label {
        font-size: 10px; font-weight: 800; color: var(--text-4);
        text-transform: uppercase; letter-spacing: 0.06em; margin-bottom: 5px;
    }
    .payment-cell-value { font-size: 13px; font-weight: 800; color: var(--text-1); }
    .payment-cell-value.mono { font-family: var(--mono); font-size: 12px; color: var(--text-2); }
    .payment-cell-value.accent { color: var(--accent); }
    .payment-status-paid    { color: var(--green); }
    .payment-status-pending { color: var(--amber); }
    .payment-status-failed  { color: var(--red); }

    /* ── Logistics Card ── */
    .address-box {
        background: var(--surface); border: 1px solid var(--border);
        border-radius: 10px; padding: 13px 14px;
        font-size: 13px; color: var(--text-2); line-height: 1.65;
    }
    .address-box strong { color: var(--text-1); }
    .tracking-chip {
        display: inline-flex; align-items: center; gap: 6px;
        font-family: var(--mono); font-size: 12px; font-weight: 800;
        color: var(--accent); background: var(--accent-dim);
        padding: 3px 10px; border-radius: 6px;
    }
    .notes-box {
        margin-top: 14px; padding: 12px 14px;
        background: var(--amber-dim); border: 1px solid rgba(217,119,6,0.2);
        border-radius: 10px; border-left: 3px solid var(--amber);
    }
    .notes-box-label {
        font-size: 10px; font-weight: 900; color: var(--amber);
        text-transform: uppercase; margin-bottom: 4px;
    }
    .notes-box-text { font-size: 12.5px; color: var(--text-2); line-height: 1.5; font-style: italic; }

    /* ── Activity Log Table ── */
    .log-table { width: 100%; border-collapse: collapse; text-align: left; min-width: 560px; }
    .log-table thead tr { background: var(--surface); }
    .log-table th {
        padding: 11px 18px; font-size: 10.5px; font-weight: 800;
        color: var(--text-4); text-transform: uppercase; letter-spacing: 0.05em;
        border-bottom: 1px solid var(--border);
    }
    .log-table td {
        padding: 13px 18px; font-size: 12.5px; font-weight: 600;
        color: var(--text-2); border-bottom: 1px solid var(--border);
        vertical-align: middle;
    }
    .log-table tbody tr:last-child td { border-bottom: none; }
    .log-table tbody tr:hover td { background: var(--surface); }
    .admin-avatar {
        display: inline-flex; width: 24px; height: 24px; border-radius: 50%;
        background: var(--accent); color: #fff; font-size: 10px; font-weight: 900;
        align-items: center; justify-content: center; margin-right: 7px; flex-shrink: 0;
    }
    .admin-cell { display: flex; align-items: center; }

    /* ── Sidebar Customer Card ── */
    .whatsapp-link {
        display: inline-flex; align-items: center; gap: 6px;
        color: #16a34a; font-weight: 800; font-size: 13px;
        text-decoration: none; margin-top: 4px;
    }
    .whatsapp-link:hover { text-decoration: underline; }
    .whatsapp-link svg { width: 14px; height: 14px; }

    /* ── Utility Buttons ── */
    .btn-back {
        display: inline-flex; align-items: center; gap: 8px;
        padding: 9px 16px; border: 1px solid var(--border);
        border-radius: 10px; background: var(--panel);
        font-family: var(--font); font-size: 13px; font-weight: 700;
        color: var(--text-2); text-decoration: none; cursor: pointer; transition: all 0.2s;
    }
    .btn-back:hover { border-color: var(--accent); color: var(--accent); transform: translateX(-2px); }
    .btn-back svg { width: 14px; height: 14px; }

    .btn-secondary {
        display: inline-flex; align-items: center; gap: 8px;
        padding: 9px 16px; border: 1px solid var(--border);
        border-radius: 10px; background: var(--panel);
        font-family: var(--font); font-size: 13px; font-weight: 700;
        color: var(--text-2); cursor: pointer; transition: all 0.2s;
    }
    .btn-secondary:hover { border-color: var(--border-2); color: var(--text-1); }
    .btn-secondary.danger { color: var(--red); border-color: rgba(220,38,38,0.25); }
    .btn-secondary.danger:hover { background: var(--red-dim); border-color: var(--red); }

    .btn-print {
        display: inline-flex; align-items: center; gap: 9px;
        padding: 9px 18px; border-radius: 10px; border: none;
        background: linear-gradient(135deg, var(--accent), #7c3aed);
        color: #fff; font-family: var(--font); font-size: 13px; font-weight: 800;
        text-decoration: none; cursor: pointer; transition: all 0.2s;
        box-shadow: 0 4px 15px rgba(79,70,229,0.25);
    }
    .btn-print:hover { transform: translateY(-2px); box-shadow: 0 8px 22px rgba(79,70,229,0.35); filter: brightness(1.08); }
    .btn-print svg { width: 15px; height: 15px; }

    /* ── Modals ── */
    .modal-overlay {
        position: fixed; inset: 0;
        background: rgba(15,23,42,0.45); backdrop-filter: blur(4px);
        z-index: 1000; display: flex; align-items: center; justify-content: center;
        opacity: 0; visibility: hidden; transition: all 0.2s;
    }
    .modal-overlay.open { opacity: 1; visibility: visible; }
    .modal-box {
        background: var(--panel); border-radius: 16px; padding: 28px;
        width: 430px; max-width: 92vw; box-shadow: var(--shadow-lg);
        transform: scale(0.95) translateY(10px); transition: transform 0.2s;
    }
    .modal-overlay.open .modal-box { transform: scale(1) translateY(0); }
    .modal-icon {
        width: 48px; height: 48px; border-radius: 12px;
        display: flex; align-items: center; justify-content: center; margin-bottom: 14px;
    }
    .modal-icon svg { width: 22px; height: 22px; }
    .modal-icon.red    { background: var(--red-dim);              color: var(--red); }
    .modal-icon.purple { background: rgba(139,92,246,0.1);        color: #8b5cf6; }
    .modal-title { font-size: 16px; font-weight: 900; color: var(--text-1); margin-bottom: 6px; }
    .modal-desc  { font-size: 13px; color: var(--text-2); line-height: 1.6; margin-bottom: 20px; }
    .modal-form-group { margin-bottom: 14px; }
    .modal-label {
        display: block; font-size: 11.5px; font-weight: 800;
        color: var(--text-3); margin-bottom: 6px; text-transform: uppercase;
    }
    .modal-input {
        width: 100%; padding: 9px 13px;
        border: 1.5px solid var(--border); border-radius: 9px;
        background: var(--surface); color: var(--text-1);
        font-family: var(--font); font-size: 13px; font-weight: 600;
        box-sizing: border-box; transition: border-color 0.2s;
    }
    .modal-input:focus { outline: none; border-color: var(--accent); }
    .modal-actions { display: flex; gap: 10px; justify-content: flex-end; margin-top: 22px; }
    .btn-modal-cancel {
        padding: 9px 18px; border: 1px solid var(--border); border-radius: 8px;
        font-family: var(--font); font-size: 13px; font-weight: 700;
        background: var(--surface); color: var(--text-2); cursor: pointer;
        transition: all 0.15s;
    }
    .btn-modal-cancel:hover { color: var(--text-1); border-color: var(--border-2); }
    .btn-modal-confirm {
        padding: 9px 20px; border: none; border-radius: 8px;
        font-family: var(--font); font-size: 13px; font-weight: 800;
        background: var(--accent); color: #fff; cursor: pointer;
        transition: all 0.15s; box-shadow: 0 2px 8px rgba(79,70,229,0.25);
    }
    .btn-modal-confirm:hover { filter: brightness(1.08); transform: translateY(-1px); }
    .btn-modal-danger {
        padding: 9px 20px; border: none; border-radius: 8px;
        font-family: var(--font); font-size: 13px; font-weight: 800;
        background: var(--red); color: #fff; cursor: pointer;
        transition: all 0.15s; box-shadow: 0 2px 8px rgba(220,38,38,0.25);
    }
    .btn-modal-danger:hover { background: #b91c1c; transform: translateY(-1px); }
    .field-error { font-size: 11.5px; color: var(--red); font-weight: 600; margin-top: 5px; }

    /* ── Responsive ── */
    @media (max-width: 700px) {
        .meta-strip { flex-wrap: wrap; }
        .meta-chip  { flex: 1 1 45%; border-right: none; border-bottom: 1px solid var(--border); }
        .meta-chip:nth-child(odd) { border-right: 1px solid var(--border); }
        .meta-chip:last-child, .meta-chip:nth-last-child(2):nth-child(odd) { border-bottom: none; }
        .progress-wrap { padding: 22px 14px; }
        .step-label { font-size: 9px; }
        .action-box { flex-direction: column; align-items: flex-start; }
        .action-controls { width: 100%; }
        .action-controls .status-select,
        .action-controls input[type="text"] { width: 100% !important; }
        .payment-grid { grid-template-columns: 1fr 1fr; }
        .header-actions { width: 100%; }
    }
    @media (max-width: 480px) {
        .step-label { display: none; }
        .payment-grid { grid-template-columns: 1fr; }
    }
</style>
@endsection

@section('content')

<div class="breadcrumb" style="margin-bottom: 20px;">
    <a href="{{ route('dashboard') }}" style="color: var(--text-3); text-decoration: none;">Dashboard</a> &raquo;
    <a href="{{ route('orders.index') }}" style="color: var(--text-3); text-decoration: none;">Manajemen Pesanan</a> &raquo;
    <span style="color: var(--text-1); font-weight: 600;">{{ $order->order_number }}</span>
</div>

{{-- ─────────────────────────────────────────────
     Page Header
────────────────────────────────────────────── --}}
<div class="page-header">
    <div class="page-header-left">
        <div class="page-icon">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                <path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"/>
                <rect x="8" y="2" width="8" height="4" rx="1" ry="1"/>
            </svg>
        </div>
        <div class="page-header-text">
            <h1>Detail Pesanan</h1>
            <p>
                #{{ $order->order_number }} &bull;
                {{ $order->created_at->translatedFormat('d M Y, H:i') }} WIB &bull;
                {{ $order->store->name ?? 'Toko Pusat' }}
            </p>
        </div>
    </div>

    <div class="header-actions">
        {{-- Print Resi --}}
        @if(!in_array($order->status, ['pending', 'cancelled']))
            <a href="{{ route('orders.print', $order) }}" target="_blank" class="btn-print">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="6 9 6 2 18 2 18 9"/>
                    <path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/>
                    <rect x="6" y="14" width="12" height="8"/>
                </svg>
                Cetak Resi
            </a>
        @endif

        {{-- Cancel --}}
        @if(in_array($order->status, ['pending', 'perlu_diproses']))
            <button type="button" class="btn-secondary danger" onclick="document.getElementById('cancelModal').classList.add('open')">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="width:14px;height:14px;">
                    <circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/>
                </svg>
                Batalkan
            </button>
        @endif

        <a href="{{ route('orders.index') }}" class="btn-back">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                <line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/>
            </svg>
            Kembali
        </a>
    </div>
</div>

{{-- ─────────────────────────────────────────────
     Alert Banners
────────────────────────────────────────────── --}}
@if(session('success'))
    <div class="alert-banner alert-success">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
        {{ session('success') }}
    </div>
@endif
@if($errors->has('stock'))
    <div class="alert-banner alert-error">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
        {{ $errors->first('stock') }}
    </div>
@endif

{{-- ─────────────────────────────────────────────
     Order Metadata Strip
────────────────────────────────────────────── --}}
<div class="meta-strip">
    <div class="meta-chip">
        <span class="meta-chip-label">Nomor Pesanan</span>
        <span class="meta-chip-value" style="font-family: var(--mono);">#{{ $order->order_number }}</span>
    </div>
    <div class="meta-chip">
        <span class="meta-chip-label">Status Pesanan</span>
        <span class="meta-chip-value">
            <span class="status-badge status-{{ $order->status }}">
                {{ \App\Services\StatusService::getOrderLabel($order->status) }}
            </span>
        </span>
    </div>
    <div class="meta-chip">
        <span class="meta-chip-label">Status Pembayaran</span>
        @php
            $mappedStatus = \App\Services\StatusService::midtransToTransactionStatus($order->payment_status ?? 'pending');
        @endphp
        <span class="meta-chip-value payment-status-{{ $mappedStatus }}">
            {{ strtoupper(\App\Services\StatusService::getTransactionLabel($mappedStatus)) }}
        </span>
    </div>
    <div class="meta-chip">
        <span class="meta-chip-label">Total Pembayaran</span>
        <span class="meta-chip-value" style="color: var(--accent);">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</span>
    </div>
    <div class="meta-chip">
        <span class="meta-chip-label">Toko</span>
        <span class="meta-chip-value">{{ $order->store->name ?? 'Toko Pusat' }}</span>
    </div>
</div>

{{-- ─────────────────────────────────────────────
     Progress Tracker
────────────────────────────────────────────── --}}
@php
    $steps = [
        'pending'        => ['label' => 'Pesanan Dibuat',    'icon' => 'credit-card'],
        'perlu_diproses' => ['label' => 'Perlu Diproses',    'icon' => 'check-circle'],
        'processing'     => ['label' => 'Dikemas',           'icon' => 'package'],
        'shipping'       => ['label' => 'Dalam Pengiriman',  'icon' => 'truck'],
        'completed'      => ['label' => 'Selesai',           'icon' => 'flag'],
    ];
    $stepKeys = array_keys($steps);
    $currentIdx = array_search($order->status, $stepKeys);
    if ($currentIdx === false) $currentIdx = -1;
    $fillWidth = ($currentIdx >= 0 && count($stepKeys) > 1)
        ? ($currentIdx / (count($stepKeys) - 1)) * 100
        : 0;
@endphp

<div class="progress-wrap">
    @if($order->status === 'cancelled')
        <div class="cancelled-banner">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/>
            </svg>
            <div>
                <div class="cancelled-banner-text">Pesanan ini telah dibatalkan</div>
                @if($order->cancel_reason)
                    <div class="cancelled-banner-reason">Alasan: {{ $order->cancel_reason }}</div>
                @endif
            </div>
        </div>
    @else
        <div class="progress-line-track">
            <div class="progress-line-fill" style="width: {{ $fillWidth }}%;"></div>
        </div>
        <div class="progress-steps">
            @foreach($steps as $key => $s)
                @php
                    $sIdx = array_search($key, $stepKeys);
                    $cls  = $currentIdx > $sIdx ? 'completed' : ($currentIdx === $sIdx ? 'active' : '');
                @endphp
                <div class="step {{ $cls }}">
                    <div class="step-circle">
                        @if($s['icon'] === 'credit-card')
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect x="1" y="4" width="22" height="16" rx="2" ry="2"/><line x1="1" y1="10" x2="23" y2="10"/></svg>
                        @elseif($s['icon'] === 'check-circle')
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                        @elseif($s['icon'] === 'package')
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/><polyline points="3.27 6.96 12 12.01 20.73 6.96"/><line x1="12" y1="22.08" x2="12" y2="12"/></svg>
                        @elseif($s['icon'] === 'truck')
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect x="1" y="3" width="15" height="13"/><polygon points="16 8 20 8 23 11 23 16 16 16 16 8"/><circle cx="5.5" cy="18.5" r="2.5"/><circle cx="18.5" cy="18.5" r="2.5"/></svg>
                        @elseif($s['icon'] === 'flag')
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M4 15s1-1 4-1 5 2 8 2 4-1 4-1V3s-1 1-4 1-5-2-8-2-4 1-4 1z"/><line x1="4" y1="22" x2="4" y2="15"/></svg>
                        @endif
                    </div>
                    <div class="step-label">{{ $s['label'] }}</div>
                </div>
            @endforeach
        </div>
    @endif
</div>

{{-- ─────────────────────────────────────────────
     Action Box (Next Step Required Action)
────────────────────────────────────────────── --}}
@if(!in_array($order->status, ['completed', 'cancelled']))
    <div class="action-box">
        @if($order->status === 'pending')
            <div class="action-text">
                <h4>Konfirmasi Pembayaran</h4>
                <p>Gunakan tombol ini jika pelanggan sudah membayar secara manual atau notifikasi otomatis tertunda.</p>
            </div>
            <div class="action-controls">
                <form action="{{ route('orders.update-status', $order) }}" method="POST" style="display:inline;">
                    @csrf @method('PATCH')
                    <input type="hidden" name="status" value="perlu_diproses">
                    <button type="submit" class="btn-action solid">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="width:15px;height:15px;"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
                        Tandai Sudah Bayar
                    </button>
                </form>
                <form action="{{ route('orders.check-payment-status', $order) }}" method="POST" style="display:inline;">
                    @csrf
                    <button type="submit" class="btn-action">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="width:15px;height:15px;"><polyline points="23 4 23 10 17 10"/><polyline points="1 20 1 14 7 14"/><path d="M3.51 9a9 9 0 0 1 14.85-3.36L23 10M1 14l4.64 4.36A9 9 0 0 0 20.49 15"/></svg>
                        Cek Status Midtrans
                    </button>
                </form>
            </div>

        @elseif($order->status === 'perlu_diproses')
            <div class="action-text">
                <h4>Mulai Proses Pengemasan</h4>
                <p>Pesanan ini telah dibayar. Klik konfirmasi untuk mulai menyiapkan dan mengemas barang.</p>
            </div>
            <div class="action-controls">
                <form action="{{ route('orders.update-status', $order) }}" method="POST">
                    @csrf @method('PATCH')
                    <input type="hidden" name="status" value="processing">
                    <button type="submit" class="btn-action solid">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="width:15px;height:15px;"><polyline points="20 6 9 17 4 12"/></svg>
                        Konfirmasi & Mulai Kemas
                    </button>
                </form>
            </div>

        @elseif($order->status === 'processing')
            <div class="action-text">
                <h4>Kirim Pesanan</h4>
                <p>Masukkan kurir dan nomor resi untuk mengubah status menjadi Dikirim.</p>
            </div>
            <div class="action-controls" style="flex-direction: column; align-items: flex-start; gap: 8px;">
                <div style="display: flex; gap: 10px; flex-wrap: wrap; width: 100%;">
                    <button type="button" class="btn-action solid" onclick="document.getElementById('shipModal').classList.add('open')">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="width:15px;height:15px;"><rect x="1" y="3" width="15" height="13"/><polygon points="16 8 20 8 23 11 23 16 16 16 16 8"/><circle cx="5.5" cy="18.5" r="2.5"/><circle cx="18.5" cy="18.5" r="2.5"/></svg>
                        Input Resi & Kirim
                    </button>
                </div>
            </div>

        @elseif($order->status === 'shipping')
            <div class="action-text">
                <h4>Selesaikan Pesanan</h4>
                <p>Tandai pesanan ini sebagai selesai jika barang telah diterima oleh pelanggan.</p>
            </div>
            <div class="action-controls">
                <form action="{{ route('orders.update-status', $order) }}" method="POST">
                    @csrf @method('PATCH')
                    <input type="hidden" name="status" value="completed">
                    <button type="submit" class="btn-action green-solid">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="width:15px;height:15px;"><polyline points="20 6 9 17 4 12"/></svg>
                        Tandai Selesai
                    </button>
                </form>
            </div>
        @endif
    </div>
@endif

{{-- ─────────────────────────────────────────────
     Main Grid
────────────────────────────────────────────── --}}
<div class="detail-grid">

    {{-- ── Left Column ── --}}
    <div class="detail-main">

        {{-- Product List --}}
        <div class="detail-card">
            <div class="card-header">
                <span class="card-title">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4Z"/><line x1="3" y1="6" x2="21" y2="6"/></svg>
                    Daftar Produk
                </span>
                <span class="status-badge status-{{ $order->status }}">
                    {{ \App\Services\StatusService::getOrderLabel($order->status) }}
                </span>
            </div>
            <div class="card-body">
                <div class="product-list">
                    @foreach($order->orderItems as $item)
                        <div class="product-item">
                            <img src="{{ $item->product?->image ? asset('storage/' . $item->product->image) : asset('img/no-image.png') }}" class="product-img" alt="{{ $item->product?->name }}">
                            <div class="product-detail">
                                <div class="product-name">{{ $item->product?->name ?? 'Produk Telah Dihapus' }}</div>
                                @if($item->packing)
                                    <div class="product-variant">Packing: {{ $item->packing }}</div>
                                @endif
                                <div class="product-qty">{{ $item->quantity }} x Rp {{ number_format($item->price, 0, ',', '.') }}</div>
                            </div>
                            <div class="product-price">Rp {{ number_format($item->quantity * $item->price, 0, ',', '.') }}</div>
                        </div>
                    @endforeach
                </div>

                {{-- Receipt --}}
                <div class="receipt">
                    <div class="receipt-row divider">
                        <span>Subtotal Produk</span>
                        <span style="color:var(--text-1); font-weight:800;">Rp {{ number_format($order->orderItems->sum(fn($i) => $i->price * $i->quantity), 0, ',', '.') }}</span>
                    </div>
                    @if(isset($order->packing_cost) && $order->packing_cost > 0)
                        <div class="receipt-row divider">
                            <span>Biaya Packing</span>
                            <span style="color:var(--text-1); font-weight:800;">Rp {{ number_format($order->packing_cost, 0, ',', '.') }}</span>
                        </div>
                    @endif
                    <div class="receipt-row">
                        <span>Ongkos Kirim ({{ strtoupper($order->shipping_courier ?? 'Kurir') }} — {{ strtoupper($order->shipping_type ?? '') }})</span>
                        <span style="color:var(--text-1); font-weight:800;">Rp {{ number_format($order->shipping_cost, 0, ',', '.') }}</span>
                    </div>
                    <div class="receipt-total">
                        <span class="receipt-total-label">Total Pembayaran</span>
                        <span class="receipt-total-value">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Payment Information --}}
        <div class="detail-card">
            <div class="card-header">
                <span class="card-title">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="1" y="4" width="22" height="16" rx="2" ry="2"/><line x1="1" y1="10" x2="23" y2="10"/></svg>
                    Informasi Pembayaran
                </span>
            </div>
            <div class="card-body">
                <div class="payment-grid">
                    <div class="payment-cell">
                        <div class="payment-cell-label">Metode Pembayaran</div>
                        <div class="payment-cell-value">{{ $order->payment_type ?? 'Online' }}</div>
                    </div>
                    <div class="payment-cell">
                        <div class="payment-cell-label">Status Pembayaran</div>
                        @php
                            $mappedStatus = \App\Services\StatusService::midtransToTransactionStatus($order->payment_status ?? 'pending');
                        @endphp
                        <div class="payment-cell-value payment-status-{{ $mappedStatus }}">
                            {{ strtoupper(\App\Services\StatusService::getTransactionLabel($mappedStatus)) }}
                        </div>
                    </div>
                    <div class="payment-cell">
                        <div class="payment-cell-label">ID Transaksi</div>
                        <div class="payment-cell-value">
                            @if($order->transaction && $order->transaction->transaction_id)
                                <span class="mono" style="font-family:var(--mono); font-size:11px; color:var(--text-2); font-weight:700;">{{ $order->transaction->transaction_id }}</span>
                            @else
                                <form action="{{ route('orders.check-payment-status', $order) }}" method="POST" style="display:flex; gap:6px; align-items:center;" novalidate>
                                    @csrf
                                    <input type="text" name="manual_id" placeholder="Masukkan ID..." required
                                        style="flex:1; font-size:11px; padding:5px 9px; border:1.5px solid var(--border); border-radius:7px; background:var(--surface); color:var(--text-1); font-family:var(--mono);">
                                    <button type="submit" style="background:var(--accent); color:#fff; border:none; padding:5px 12px; border-radius:7px; font-size:11px; font-weight:800; cursor:pointer; font-family:var(--font);">Sync</button>
                                </form>
                            @endif
                        </div>
                    </div>
                    <div class="payment-cell">
                        <div class="payment-cell-label">Waktu Pembayaran</div>
                        <div class="payment-cell-value" style="font-size:12px;">
                            @php $paymentDate = $order->transaction?->payment_date; @endphp
                            @if($paymentDate)
                                {{ \Carbon\Carbon::parse($paymentDate)->setTimezone('Asia/Jakarta')->format('d/m/Y H:i') }} WIB
                            @else
                                <span style="color:var(--text-4); font-style:italic;">—</span>
                            @endif
                        </div>
                    </div>
                    <div class="payment-cell">
                        <div class="payment-cell-label">ID Midtrans</div>
                        <div class="payment-cell-value mono" style="font-size:11px;">{{ $order->midtrans_order_id ?? '—' }}</div>
                    </div>

                    {{-- Virtual Account / Channel Info --}}
                    @if($order->transaction && is_array($order->transaction->payment_details))
                        @php
                            $details = $order->transaction->payment_details;
                            $channelLabel = null; $channelInfo = null;
                            if (isset($details['va_numbers'][0])) {
                                $channelLabel = 'Virtual Account (' . strtoupper($details['va_numbers'][0]['bank']) . ')';
                                $channelInfo  = $details['va_numbers'][0]['va_number'];
                            } elseif (isset($details['bca_va_number'])) {
                                $channelLabel = 'Virtual Account (BCA)';
                                $channelInfo  = $details['bca_va_number'];
                            } elseif (isset($details['permata_va_number'])) {
                                $channelLabel = 'Virtual Account (Permata)';
                                $channelInfo  = $details['permata_va_number'];
                            } elseif (isset($details['bill_key'])) {
                                $channelLabel = 'Mandiri Bill Key (Kode: ' . ($details['biller_code'] ?? '-') . ')';
                                $channelInfo  = $details['bill_key'];
                            } elseif (isset($details['bank'])) {
                                $channelLabel = 'Acquiring Bank';
                                $channelInfo  = strtoupper($details['bank']);
                            }
                        @endphp
                        @if($channelInfo)
                            <div class="payment-cell">
                                <div class="payment-cell-label">{{ $channelLabel }}</div>
                                <div class="payment-cell-value accent" style="font-family:var(--mono); font-size:13px;">{{ $channelInfo }}</div>
                            </div>
                        @endif
                    @endif
                </div>
            </div>
        </div>

        {{-- Activity Log --}}
        <div class="detail-card">
            <div class="card-header">
                <span class="card-title">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
                    Log Aktivitas Pesanan
                </span>
            </div>
            <div style="overflow-x: auto;">
                <table class="log-table">
                    <thead>
                        <tr>
                            <th>Waktu</th>
                            <th>Status</th>
                            <th>Admin / Sistem</th>
                            <th>Catatan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($order->trackingHistories()->latest()->get() as $history)
                            <tr>
                                <td style="font-size:12px; white-space:nowrap;">
                                    {{ $history->created_at->translatedFormat('d M Y') }}<br>
                                    <span style="color:var(--text-4);">{{ $history->created_at->format('H:i') }} WIB</span>
                                </td>
                                <td>
                                    <span class="status-badge status-{{ $history->status }}" style="font-size:10px;">
                                        {{ strtoupper(\App\Services\StatusService::getOrderLabel($history->status)) }}
                                    </span>
                                </td>
                                <td>
                                    <div class="admin-cell">
                                        <span class="admin-avatar">{{ substr($history->admin->name ?? 'S', 0, 1) }}</span>
                                        {{ $history->admin->name ?? 'System' }}
                                    </div>
                                </td>
                                <td style="color:var(--text-3);">{{ $history->notes ?? '—' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" style="padding:36px; text-align:center; color:var(--text-4); font-style:italic;">
                                    Belum ada riwayat aktivitas.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>{{-- /detail-main --}}

    {{-- ── Right Sidebar ── --}}
    <div class="detail-side">

        {{-- Customer Card — gets CSS order:-1 on mobile via .sidebar-priority --}}
        <div class="detail-card sidebar-priority">
            <div class="card-header">
                <span class="card-title">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                    Informasi Pelanggan
                </span>
            </div>
            <div class="card-body">
                <div class="info-row">
                    <span class="info-label">Nama</span>
                    <span class="info-value">{{ $order->customer_name }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">WhatsApp</span>
                    <span class="info-value">
                        <a href="https://wa.me/{{ preg_replace('/\D/', '', $order->customer_phone) }}" target="_blank" class="whatsapp-link">
                            <svg viewBox="0 0 24 24" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 0 1-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 0 1-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 0 1 2.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0 0 12.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 0 0 5.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 0 0-3.48-8.413Z"/></svg>
                            {{ $order->customer_phone }}
                        </a>
                    </span>
                </div>
                <div style="margin-top: 14px;">
                    <div class="info-label" style="margin-bottom: 8px;">Alamat Pengiriman</div>
                    <div class="address-box">
                        <strong>{{ $order->customer_name }}</strong><br>
                        {{ $order->shipping_address }}
                        @if(isset($order->city) && $order->city)
                            <br>{{ $order->city }}@if(isset($order->province) && $order->province), {{ $order->province }}@endif
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- Logistics Card --}}
        <div class="detail-card">
            <div class="card-header">
                <span class="card-title">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="1" y="3" width="15" height="13"/><polygon points="16 8 20 8 23 11 23 16 16 16 16 8"/><circle cx="5.5" cy="18.5" r="2.5"/><circle cx="18.5" cy="18.5" r="2.5"/></svg>
                    Logistik & Pengiriman
                </span>
            </div>
            <div class="card-body">
                <div class="info-row">
                    <span class="info-label">Kurir</span>
                    <span class="info-value">{{ strtoupper($order->shipping_courier ?? 'Manual') }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Layanan</span>
                    <span class="info-value">{{ strtoupper($order->shipping_type ?? '—') }}</span>
                </div>
                @if(isset($order->total_weight) && $order->total_weight)
                    <div class="info-row">
                        <span class="info-label">Berat Total</span>
                        <span class="info-value">{{ number_format($order->total_weight / 1000, 2, ',', '.') }} kg</span>
                    </div>
                @endif
                <div class="info-row">
                    <span class="info-label">Nomor Resi</span>
                    <span class="info-value">
                        @if($order->tracking_number)
                            <span class="tracking-chip">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="width:12px;height:12px;"><polyline points="9 11 12 14 22 4"/><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/></svg>
                                {{ $order->tracking_number }}
                            </span>
                        @else
                            <span style="color:var(--text-4); font-style:italic; font-size:12px;">Belum ada resi</span>
                        @endif
                    </span>
                </div>

                @if($order->notes)
                    <div class="notes-box">
                        <div class="notes-box-label">Catatan Pengiriman</div>
                        <div class="notes-box-text">"{{ $order->notes }}"</div>
                    </div>
                @endif
            </div>
        </div>

        {{-- Store Card --}}
        <div class="detail-card">
            <div class="card-header">
                <span class="card-title">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
                    Informasi Toko
                </span>
            </div>
            <div class="card-body">
                <div class="info-row">
                    <span class="info-label">Nama Toko</span>
                    <span class="info-value" style="color:var(--accent);">{{ $order->store->name ?? '—' }}</span>
                </div>
                @if(isset($order->store->address) && $order->store->address)
                    <div class="info-row">
                        <span class="info-label">Alamat</span>
                        <span class="info-value" style="font-size:12px; font-weight:600;">{{ $order->store->address }}</span>
                    </div>
                @endif
                <div class="info-row">
                    <span class="info-label">WhatsApp</span>
                    <span class="info-value">{{ $order->store->phone ?? '—' }}</span>
                </div>
            </div>
        </div>

        {{-- Cancellation Reason (sidebar detail when cancelled) --}}
        @if($order->status === 'cancelled' && $order->cancel_reason)
            <div class="detail-card" style="border-color: var(--red);">
                <div class="card-header" style="background: var(--red-dim);">
                    <span class="card-title" style="color: var(--red);">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="color:var(--red);"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                        Alasan Pembatalan
                    </span>
                </div>
                <div class="card-body">
                    <div style="font-size:13px; color:var(--red); font-weight:600; line-height:1.6;">
                        {{ $order->cancel_reason }}
                    </div>
                </div>
            </div>
        @endif

    </div>{{-- /detail-side --}}

</div>{{-- /detail-grid --}}


{{-- ─────────────────────────────────────────────
     Modal: Kirim Pesanan (Ship)
────────────────────────────────────────────── --}}
<div id="shipModal" class="modal-overlay">
    <div class="modal-box">
        <div class="modal-icon purple">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect x="1" y="3" width="15" height="13"/><polygon points="16 8 20 8 23 11 23 16 16 16 16 8"/><circle cx="5.5" cy="18.5" r="2.5"/><circle cx="18.5" cy="18.5" r="2.5"/></svg>
        </div>
        <div class="modal-title">Konfirmasi Pengiriman</div>
        <div class="modal-desc">Masukkan informasi kurir dan nomor resi untuk pesanan <strong>{{ $order->order_number }}</strong>.</div>

        <form action="{{ route('orders.update-tracking-number', $order) }}" method="POST" novalidate>
            @csrf @method('PATCH')
            <input type="hidden" name="source" value="show">

            <div class="modal-form-group">
                <label class="modal-label">Pilih Kurir <span style="color:var(--red);">*</span></label>
                <select name="shipping_courier" class="modal-input" required>
                    <option value="">— Pilih Kurir —</option>
                    @php
                        $courierNames    = $couriers->pluck('name')->toArray();
                        $currentCourier  = $order->shipping_courier;
                    @endphp
                    @if($currentCourier && !in_array($currentCourier, $courierNames))
                        <option value="{{ $currentCourier }}" selected>{{ $currentCourier }} (Tidak Aktif)</option>
                    @endif
                    @foreach($couriers as $courier)
                        <option value="{{ $courier->name }}" {{ $currentCourier == $courier->name ? 'selected' : '' }}>
                            {{ $courier->name }}
                        </option>
                    @endforeach
                </select>
                @error('shipping_courier')
                    <div class="field-error">{{ $message }}</div>
                @enderror
            </div>

            <div class="modal-form-group">
                <label class="modal-label">Nomor Resi <span style="color:var(--red);">*</span></label>
                <input type="text" name="tracking_number" class="modal-input"
                    placeholder="Contoh: JT123456789" required
                    value="{{ old('tracking_number', $order->tracking_number) }}"
                    style="font-family:var(--mono);">
                @error('tracking_number')
                    <div class="field-error">{{ $message }}</div>
                @enderror
            </div>

            <div class="modal-actions">
                <button type="button" class="btn-modal-cancel" onclick="document.getElementById('shipModal').classList.remove('open')">Batal</button>
                <button type="submit" class="btn-modal-confirm">Konfirmasi & Kirim</button>
            </div>
        </form>
    </div>
</div>

{{-- ─────────────────────────────────────────────
     Modal: Batalkan Pesanan (Cancel)
────────────────────────────────────────────── --}}
<div id="cancelModal" class="modal-overlay">
    <div class="modal-box">
        <div class="modal-icon red">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>
        </div>
        <div class="modal-title">Batalkan Pesanan</div>
        <div class="modal-desc">Apakah Anda yakin ingin membatalkan pesanan <strong>{{ $order->order_number }}</strong>? Stok produk akan dikembalikan otomatis.</div>

        <form action="{{ route('orders.cancel', $order) }}" method="POST" novalidate>
            @csrf @method('PATCH')
            <div class="modal-form-group">
                <label class="modal-label">Alasan Pembatalan <span style="color:var(--red);">*</span></label>
                <textarea name="cancel_reason" class="modal-input" rows="3"
                    placeholder="Masukkan alasan pembatalan...">{{ old('cancel_reason') }}</textarea>
                @error('cancel_reason')
                    <div class="field-error">{{ $message }}</div>
                @enderror
            </div>

            <div class="modal-actions">
                <button type="button" class="btn-modal-cancel" onclick="document.getElementById('cancelModal').classList.remove('open')">Batal</button>
                <button type="submit" class="btn-modal-danger">Ya, Batalkan Pesanan</button>
            </div>
        </form>
    </div>
</div>

@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Close modals on Escape key
        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') {
                document.querySelectorAll('.modal-overlay.open')
                    .forEach(function (m) { m.classList.remove('open'); });
            }
        });

        // Close modals on backdrop click
        document.querySelectorAll('.modal-overlay').forEach(function (overlay) {
            overlay.addEventListener('click', function (e) {
                if (e.target === overlay) overlay.classList.remove('open');
            });
        });

        // Auto-open ship modal if there were validation errors on tracking fields
        @if($errors->has('shipping_courier') || $errors->has('tracking_number'))
            document.getElementById('shipModal').classList.add('open');
        @endif

        // Auto-open cancel modal if there were validation errors on cancel_reason
        @if($errors->has('cancel_reason'))
            document.getElementById('cancelModal').classList.add('open');
        @endif
    });
</script>
@endpush
