@extends('layouts.app')

@section('title', 'Dashboard')

@push('styles')
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,300;0,9..40,400;0,9..40,500;0,9..40,600;0,9..40,700;0,9..40,800;1,9..40,400&family=DM+Mono:wght@400;500&display=swap" rel="stylesheet">
@endpush

@section('content')
<style>
/* ── DB RESET ── */
.db-wrap *, .db-wrap *::before, .db-wrap *::after { box-sizing: border-box; }
.db-wrap { font-family: 'DM Sans', 'Plus Jakarta Sans', system-ui, sans-serif; display: flex; flex-direction: column; gap: 20px; padding-bottom: 32px; }
.db-wrap > * { animation: db-up 0.45s cubic-bezier(0.22,1,0.36,1) both; }
.db-wrap > *:nth-child(1){animation-delay:.04s}
.db-wrap > *:nth-child(2){animation-delay:.09s}
.db-wrap > *:nth-child(3){animation-delay:.14s}
.db-wrap > *:nth-child(4){animation-delay:.19s}
.db-wrap > *:nth-child(5){animation-delay:.24s}
.db-wrap > *:nth-child(6){animation-delay:.29s}
.db-wrap > *:nth-child(7){animation-delay:.34s}
.db-wrap > *:nth-child(8){animation-delay:.39s}
@keyframes db-up { from{opacity:0;transform:translateY(14px)} to{opacity:1;transform:translateY(0)} }

/* ── WELCOME ── */
.db-welcome {
    position: relative; overflow: hidden;
    border-radius: 16px;
    padding: 22px 26px;
    display: flex; align-items: center; gap: 16px;
    background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 60%, #3b82f6 100%);
    box-shadow: 0 8px 24px -6px rgba(99,102,241,0.35);
}
.db-welcome::before {
    content:''; position:absolute; top:-60px; right:2%; width:220px; height:220px;
    background: radial-gradient(circle, rgba(255,255,255,0.12) 0%, transparent 70%);
    border-radius:50%; pointer-events:none;
    animation: db-float 9s ease-in-out infinite;
}
.db-welcome::after {
    content:''; position:absolute; bottom:-50px; left:28%; width:160px; height:160px;
    background: radial-gradient(circle, rgba(255,255,255,0.07) 0%, transparent 70%);
    border-radius:50%; pointer-events:none;
    animation: db-float 13s ease-in-out infinite reverse;
}
@keyframes db-float { 0%,100%{transform:translate(0,0)} 50%{transform:translate(14px,-12px)} }
.db-welcome-dots {
    position:absolute; inset:0; pointer-events:none;
    background-image: radial-gradient(circle, rgba(255,255,255,0.14) 1px, transparent 1px);
    background-size: 22px 22px;
}
.db-welcome-av {
    position:relative; z-index:1; flex-shrink:0;
    width:46px; height:46px; border-radius:14px;
    background:rgba(255,255,255,0.18); backdrop-filter:blur(6px);
    border:1.5px solid rgba(255,255,255,0.3);
    display:flex; align-items:center; justify-content:center;
    font-size:22px;
}
.db-welcome-body { flex:1; z-index:1; }
.db-welcome-body h1 { margin:0 0 3px; font-size:18px; font-weight:800; color:#fff; letter-spacing:-0.3px; line-height:1.2; }
.db-welcome-body p  { margin:0; font-size:13px; color:rgba(255,255,255,0.76); }
.db-welcome-cta {
    z-index:1; flex-shrink:0;
    display:inline-flex; align-items:center; gap:7px;
    padding:9px 17px; border-radius:10px;
    background:rgba(255,255,255,0.18); backdrop-filter:blur(8px);
    border:1.5px solid rgba(255,255,255,0.3);
    color:#fff; text-decoration:none;
    font-size:12.5px; font-weight:700;
    transition: background .22s ease, transform .22s ease;
    white-space:nowrap;
}
.db-welcome-cta:hover { background:rgba(255,255,255,0.28); transform:translateY(-2px); }
.db-welcome-cta svg { width:14px; height:14px; flex-shrink:0; }

/* ── STATS ── */
.db-stats { display:grid; grid-template-columns:repeat(4,1fr); gap:14px; }
@media(max-width:1100px){ .db-stats{grid-template-columns:repeat(2,1fr)} }
@media(max-width:560px) { .db-stats{grid-template-columns:1fr} }

.db-sc {
    background:var(--panel,#fff); border:1px solid var(--border,rgba(0,0,0,.07));
    border-radius:14px; padding:18px 20px;
    display:flex; flex-direction:column; gap:14px;
    position:relative; overflow:hidden;
    box-shadow:0 1px 4px rgba(0,0,0,.05);
    transition: transform .28s cubic-bezier(.34,1.56,.64,1), box-shadow .28s ease, border-color .28s ease;
    cursor:default;
}
.db-sc::before {
    content:''; position:absolute; left:0; top:18%; bottom:18%; width:3px;
    border-radius:0 3px 3px 0;
    background:var(--_c, #6366f1); opacity:.6;
    transition: top .28s ease, bottom .28s ease, opacity .28s ease;
}
.db-sc::after {
    content:''; position:absolute; top:-32px; right:-32px;
    width:110px; height:110px; border-radius:50%;
    background:radial-gradient(circle, var(--_c,#6366f1) 0%, transparent 70%);
    opacity:.05; pointer-events:none;
    transition: opacity .3s ease, transform .3s ease;
}
.db-sc:hover { transform:translateY(-4px); box-shadow:0 12px 28px -8px rgba(0,0,0,.12); border-color:rgba(99,102,241,.18); }
.db-sc:hover::before { top:0; bottom:0; opacity:1; }
.db-sc:hover::after  { opacity:.09; transform:scale(1.12); }
.db-sc:hover .db-sc-icon { transform:scale(1.1) rotate(-4deg); }

.db-sc--blue   { --_c:#3b82f6 }
.db-sc--green  { --_c:#10b981 }
.db-sc--amber  { --_c:#f59e0b }
.db-sc--purple { --_c:#8b5cf6 }

.db-sc-top { display:flex; align-items:center; justify-content:space-between; }
.db-sc-icon {
    width:40px; height:40px; border-radius:10px; flex-shrink:0;
    display:flex; align-items:center; justify-content:center;
    background:color-mix(in srgb, var(--_c,#6366f1) 12%, transparent);
    color:var(--_c,#6366f1);
    transition:transform .28s cubic-bezier(.34,1.56,.64,1);
}
.db-sc-icon svg { width:18px; height:18px; display:block; flex-shrink:0; }

.db-sc-chip {
    display:inline-flex; align-items:center; gap:4px;
    font-size:11px; font-weight:700; padding:3px 8px; border-radius:20px;
    background:rgba(16,185,129,.10); color:#059669;
}
.db-sc-chip svg { width:10px; height:10px; flex-shrink:0; }

.db-sc-val   { font-size:22px; font-weight:800; letter-spacing:-0.5px; color:var(--text-1,#0f172a); line-height:1.1; }
.db-sc-label { font-size:11.5px; font-weight:600; color:var(--text-3,#94a3b8); text-transform:uppercase; letter-spacing:.5px; margin-top:2px; }

/* ── SHARED CARD ── */
.db-card {
    background:var(--panel,#fff); border:1px solid var(--border,rgba(0,0,0,.07));
    border-radius:14px; overflow:hidden; display:flex; flex-direction:column;
    box-shadow:0 1px 4px rgba(0,0,0,.05);
    transition:box-shadow .25s ease, border-color .25s ease;
}
.db-card:hover { box-shadow:0 6px 20px -6px rgba(0,0,0,.10); border-color:rgba(99,102,241,.15); }

.db-ch {
    padding:14px 20px; display:flex; align-items:center; justify-content:space-between;
    border-bottom:1px solid var(--border,rgba(0,0,0,.07));
    background:linear-gradient(180deg, var(--surface,#f7f9fc) 0%, var(--panel,#fff) 100%);
}
.db-ct {
    display:flex; align-items:center; gap:9px;
    font-size:13.5px; font-weight:800; color:var(--text-1,#0f172a); letter-spacing:-.15px;
}
.db-ct-i {
    width:28px; height:28px; border-radius:8px; flex-shrink:0;
    display:flex; align-items:center; justify-content:center;
    background:color-mix(in srgb,var(--accent,#6366f1) 11%,transparent);
    color:var(--accent,#6366f1);
}
.db-ct-i svg { width:13px; height:13px; display:block; flex-shrink:0; }

.db-ca {
    display:inline-flex; align-items:center; gap:5px;
    font-size:12px; font-weight:700; color:var(--accent,#6366f1); text-decoration:none;
    padding:5px 10px; border-radius:7px;
    background:color-mix(in srgb,var(--accent,#6366f1) 8%,transparent);
    transition:background .2s;
}
.db-ca:hover { background:color-mix(in srgb,var(--accent,#6366f1) 15%,transparent); }
.db-ca svg { width:12px; height:12px; flex-shrink:0; }

.db-cb { padding:18px 20px; flex:1; display:flex; flex-direction:column; }

/* ── ROWS ── */
.db-row  { display:grid; grid-template-columns:2fr 1fr; gap:16px; }
@media(max-width:1024px){ .db-row{ grid-template-columns:1fr } }

/* ── PERIOD CHIP ── */
.db-period {
    display:inline-flex; align-items:center; gap:5px;
    font-size:11px; font-weight:600; color:var(--text-3,#94a3b8);
    background:var(--surface,#f7f9fc); border:1px solid var(--border,rgba(0,0,0,.07));
    padding:4px 9px; border-radius:20px;
}
.db-period svg { width:11px; height:11px; flex-shrink:0; }

/* ── TABLE ── */
.db-tw { overflow-x:auto; -webkit-overflow-scrolling:touch; }
.db-t  { width:100%; border-collapse:collapse; font-size:13px; }
.db-t thead tr { background:var(--surface,#f7f9fc); }
.db-t th {
    padding:9px 16px; text-align:left;
    font-size:10.5px; font-weight:700; letter-spacing:.6px; text-transform:uppercase;
    color:var(--text-3,#94a3b8); border-bottom:1px solid var(--border,rgba(0,0,0,.07));
    white-space:nowrap;
}
.db-t td {
    padding:12px 16px; color:var(--text-1,#0f172a);
    border-bottom:1px solid var(--border,rgba(0,0,0,.07)); vertical-align:middle;
}
.db-t tbody tr:last-child td { border-bottom:none; }
.db-t tbody tr { transition:background .15s; }
.db-t tbody tr:hover td { background:color-mix(in srgb,var(--accent,#6366f1) 4%,transparent); }

/* avatar */
.db-av {
    width:30px; height:30px; border-radius:8px; flex-shrink:0;
    display:inline-flex; align-items:center; justify-content:center;
    background:color-mix(in srgb,var(--accent,#6366f1) 13%,transparent);
    color:var(--accent,#6366f1); font-weight:800; font-size:13px;
}

/* rank */
.db-rk { display:inline-flex; align-items:center; justify-content:center; width:22px; height:22px; border-radius:6px; font-weight:800; font-size:11px; }
.db-rk-g { background:rgba(245,158,11,.13); color:#b45309; }
.db-rk-s { background:rgba(148,163,184,.18); color:#64748b; }
.db-rk-b { background:rgba(180,100,48,.13); color:#92400e; }
.db-rk-n { background:var(--surface-2,#eef1f7); color:var(--text-3,#94a3b8); }

/* status badges */
.db-badge {
    display:inline-flex; align-items:center; gap:5px;
    padding:3px 9px; border-radius:20px;
    font-size:11px; font-weight:700; white-space:nowrap;
}
.db-badge::before { content:''; width:5px; height:5px; border-radius:50%; background:currentColor; opacity:.7; flex-shrink:0; }
.db-b-pending    { background:rgba(245,158,11,.12);  color:#b45309; }
.db-b-success    { background:rgba(16,185,129,.12);  color:#047857; }
.db-b-failed     { background:rgba(239,68,68,.12);   color:#b91c1c; }
.db-b-processing { background:rgba(59,130,246,.12);  color:#1d4ed8; }

/* ── TXN LIST ── */
.db-tl  { display:flex; flex-direction:column; gap:8px; }
.db-tli {
    display:flex; align-items:center; gap:12px;
    padding:11px 13px; border-radius:10px; border:1px solid transparent;
    transition:all .22s ease; cursor:default;
}
.db-tli:hover { background:var(--surface,#f7f9fc); border-color:var(--border,rgba(0,0,0,.07)); transform:translateX(2px); }
.db-tli-icon { width:36px; height:36px; border-radius:9px; display:flex; align-items:center; justify-content:center; flex-shrink:0; }
.db-tli-icon svg { width:16px; height:16px; display:block; flex-shrink:0; }
.db-tli-info { flex:1; }
.db-tli-title { font-size:13px; font-weight:700; color:var(--text-1,#0f172a); }
.db-tli-desc  { font-size:11.5px; color:var(--text-3,#94a3b8); margin-top:1px; }
.db-tli-num   { font-family:'DM Mono',monospace; font-size:16px; font-weight:500; color:var(--text-1,#0f172a); min-width:28px; text-align:right; }

/* divider */
.db-div { border:none; border-top:1px solid var(--border,rgba(0,0,0,.07)); margin:16px 0; }
.db-sub { font-size:10.5px; font-weight:700; color:var(--text-3,#94a3b8); text-transform:uppercase; letter-spacing:.7px; margin:0 0 12px; }

/* courier rows */
.db-cr { display:flex; justify-content:space-between; align-items:center; padding:8px 0; border-bottom:1px solid var(--border,rgba(0,0,0,.07)); }
.db-cr:last-child { border-bottom:none; }
.db-cr-name { font-size:12.5px; font-weight:700; color:var(--text-1,#0f172a); text-transform:uppercase; letter-spacing:.3px; display:flex; align-items:center; gap:8px; }
.db-cr-name::before { content:''; width:6px; height:6px; border-radius:50%; background:var(--accent,#6366f1); opacity:.5; flex-shrink:0; }
.db-cr-cnt { font-family:'DM Mono',monospace; font-size:12px; color:var(--text-2,#475569); }

/* ── ACTIVITY ── */
.db-al { display:flex; flex-direction:column; position:relative; }
.db-al::before {
    content:''; position:absolute; left:17px; top:20px; bottom:20px; width:1px;
    background:linear-gradient(180deg,var(--border,rgba(0,0,0,.07)) 0%,transparent 100%);
    pointer-events:none;
}
.db-ali { display:flex; gap:12px; padding:10px 12px; border-radius:10px; transition:background .18s; }
.db-ali:hover { background:var(--surface,#f7f9fc); }
.db-ali-dot { width:34px; height:34px; border-radius:9px; display:flex; align-items:center; justify-content:center; flex-shrink:0; position:relative; z-index:1; }
.db-ali-dot svg { width:15px; height:15px; display:block; flex-shrink:0; }
.db-ali-dot--order { background:rgba(59,130,246,.11); color:#3b82f6; }
.db-ali-dot--stock { background:rgba(139,92,246,.11); color:#8b5cf6; }
.db-ali-info { flex:1; }
.db-ali-text { font-size:12.5px; font-weight:600; color:var(--text-1,#0f172a); line-height:1.4; }
.db-ali-time { font-size:11px; color:var(--text-3,#94a3b8); margin-top:2px; }

/* ── EMPTY ── */
.db-empty { display:flex; flex-direction:column; align-items:center; justify-content:center; gap:8px; padding:28px 0; color:var(--text-3,#94a3b8); }
.db-empty svg { width:28px; height:28px; opacity:.3; display:block; }
.db-empty span { font-size:12.5px; font-weight:500; }

/* ── ORDER-ID CHIP ── */
.db-oid { font-family:'DM Mono',monospace; font-size:11.5px; font-weight:600; color:var(--text-2,#475569); background:color-mix(in srgb,var(--accent,#6366f1) 7%,transparent); padding:2px 7px; border-radius:5px; }

/* ── QUICK NAV ── */
.db-qnav { display:grid; grid-template-columns:repeat(4,1fr); gap:12px; }
@media(max-width:1100px){ .db-qnav{grid-template-columns:repeat(3,1fr)} }
@media(max-width:700px) { .db-qnav{grid-template-columns:repeat(2,1fr)} }
@media(max-width:400px) { .db-qnav{grid-template-columns:1fr} }

.db-qn {
    display:flex; align-items:center; gap:11px;
    padding:14px 16px; border-radius:12px;
    background:var(--panel,#fff); border:1px solid var(--border,rgba(0,0,0,.07));
    text-decoration:none; color:var(--text-1,#0f172a);
    box-shadow:0 1px 4px rgba(0,0,0,.04);
    transition:transform .22s cubic-bezier(.34,1.56,.64,1), box-shadow .22s ease, border-color .22s ease;
    position:relative; overflow:hidden;
}
.db-qn::after {
    content:''; position:absolute; inset:0;
    background:radial-gradient(circle at 50% 0%, color-mix(in srgb, var(--_qc,#6366f1) 8%, transparent) 0%, transparent 65%);
    opacity:0; transition:opacity .22s;
}
.db-qn:hover { transform:translateY(-3px); box-shadow:0 8px 22px -6px rgba(0,0,0,.13); border-color:color-mix(in srgb, var(--_qc,#6366f1) 30%, transparent); }
.db-qn:hover::after { opacity:1; }
.db-qn-icon {
    width:36px; height:36px; border-radius:9px; flex-shrink:0;
    display:flex; align-items:center; justify-content:center;
    background:color-mix(in srgb, var(--_qc,#6366f1) 12%, transparent);
    color:var(--_qc,#6366f1);
    transition:transform .22s cubic-bezier(.34,1.56,.64,1);
}
.db-qn:hover .db-qn-icon { transform:scale(1.12) rotate(-5deg); }
.db-qn-icon svg { width:16px; height:16px; display:block; flex-shrink:0; }
.db-qn-body { flex:1; min-width:0; }
.db-qn-title { font-size:12.5px; font-weight:800; color:var(--text-1,#0f172a); white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
.db-qn-sub   { font-size:11px; color:var(--text-3,#94a3b8); margin-top:1px; }
.db-qn-arr   { flex-shrink:0; color:var(--text-4,#cbd5e1); transition:transform .18s ease, color .18s; }
.db-qn:hover .db-qn-arr { transform:translateX(3px); color:var(--_qc,#6366f1); }
.db-qn-arr svg { width:13px; height:13px; display:block; }

/* ── MASTER DATA GRID ── */
.db-mgrid { display:grid; grid-template-columns:repeat(3,1fr); gap:14px; }
@media(max-width:900px){ .db-mgrid{grid-template-columns:repeat(2,1fr)} }
@media(max-width:520px){ .db-mgrid{grid-template-columns:1fr} }

.db-mc {
    background:var(--panel,#fff); border:1px solid var(--border,rgba(0,0,0,.07));
    border-radius:12px; padding:16px 18px;
    display:flex; align-items:center; gap:12px;
    text-decoration:none; color:var(--text-1,#0f172a);
    box-shadow:0 1px 4px rgba(0,0,0,.04);
    transition:transform .22s cubic-bezier(.34,1.56,.64,1), box-shadow .22s ease, border-color .22s ease;
}
.db-mc:hover { transform:translateY(-2px); box-shadow:0 6px 18px -6px rgba(0,0,0,.11); border-color:color-mix(in srgb, var(--_mc,#6366f1) 28%, transparent); }
.db-mc-icon {
    width:38px; height:38px; border-radius:9px; flex-shrink:0;
    display:flex; align-items:center; justify-content:center;
    background:color-mix(in srgb, var(--_mc,#6366f1) 11%, transparent);
    color:var(--_mc,#6366f1);
}
.db-mc-icon svg { width:17px; height:17px; display:block; }
.db-mc-body { flex:1; }
.db-mc-val   { font-size:20px; font-weight:800; letter-spacing:-.4px; color:var(--text-1,#0f172a); line-height:1.1; }
.db-mc-label { font-size:11px; font-weight:600; color:var(--text-3,#94a3b8); text-transform:uppercase; letter-spacing:.5px; margin-top:2px; }

/* ── REPORT SNAPSHOT ── */
.db-rsnap { display:grid; grid-template-columns:repeat(3,1fr); gap:12px; }
@media(max-width:900px){ .db-rsnap{grid-template-columns:repeat(2,1fr)} }
@media(max-width:520px){ .db-rsnap{grid-template-columns:1fr} }

.db-rs {
    background:var(--surface,#f7f9fc); border:1px solid var(--border,rgba(0,0,0,.07));
    border-radius:10px; padding:14px 16px;
    display:flex; align-items:center; gap:10px;
}
.db-rs-dot { width:8px; height:8px; border-radius:50%; flex-shrink:0; }
.db-rs-lbl { font-size:12px; font-weight:600; color:var(--text-2,#475569); flex:1; }
.db-rs-val { font-family:'DM Mono',monospace; font-size:14px; font-weight:700; color:var(--text-1,#0f172a); }

/* ── DELIVERY SECTION ── */
.db-drow { display:grid; grid-template-columns:repeat(3,1fr); gap:12px; margin-bottom:14px; }
@media(max-width:900px){ .db-drow{grid-template-columns:repeat(2,1fr)} }
@media(max-width:520px){ .db-drow{grid-template-columns:1fr} }

.db-ds {
    background:var(--surface,#f7f9fc); border:1px solid var(--border,rgba(0,0,0,.07));
    border-radius:10px; padding:14px 16px; text-align:center;
}
.db-ds-val   { font-size:22px; font-weight:800; letter-spacing:-.4px; color:var(--text-1,#0f172a); line-height:1.1; }
.db-ds-label { font-size:10.5px; font-weight:700; color:var(--text-3,#94a3b8); text-transform:uppercase; letter-spacing:.5px; margin-top:3px; }
</style>

<div class="db-wrap">

    {{-- ═══ WELCOME ═══ --}}
    <div class="db-welcome">
        <div class="db-welcome-dots"></div>
        <div class="db-welcome-av">👋</div>
        <div class="db-welcome-body">
            <h1>Selamat Datang, {{ Auth::user()->name }}!</h1>
            <p>Berikut ringkasan performa toko dan pesanan Anda hari ini.</p>
        </div>
        <a href="{{ route('orders.index') }}" class="db-welcome-cta">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/><path d="M16 10a4 4 0 0 1-8 0"/></svg>
            Kelola Pesanan
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="9 18 15 12 9 6"/></svg>
        </a>
    </div>

    {{-- ═══ STATS ═══ --}}
    <div class="db-stats">
        <div class="db-sc db-sc--blue">
            <div class="db-sc-top">
                <div class="db-sc-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect width="20" height="12" x="2" y="6" rx="2"/><circle cx="12" cy="12" r="2"/><path d="M6 12h.01M18 12h.01"/></svg>
                </div>
                <div class="db-sc-chip">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="23 6 13.5 15.5 8.5 10.5 1 18"/><polyline points="17 6 23 6 23 12"/></svg>
                    Aktif
                </div>
            </div>
            <div>
                <div class="db-sc-val">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</div>
                <div class="db-sc-label">Total Pendapatan</div>
            </div>
        </div>

        <div class="db-sc db-sc--green">
            <div class="db-sc-top">
                <div class="db-sc-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/><path d="M16 10a4 4 0 0 1-8 0"/></svg>
                </div>
                <div class="db-sc-chip">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="23 6 13.5 15.5 8.5 10.5 1 18"/><polyline points="17 6 23 6 23 12"/></svg>
                    Total
                </div>
            </div>
            <div>
                <div class="db-sc-val">{{ number_format($totalOrders, 0, ',', '.') }}</div>
                <div class="db-sc-label">Total Pesanan</div>
            </div>
        </div>

        <div class="db-sc db-sc--amber">
            <div class="db-sc-top">
                <div class="db-sc-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                </div>
                <div class="db-sc-chip">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="23 6 13.5 15.5 8.5 10.5 1 18"/><polyline points="17 6 23 6 23 12"/></svg>
                    Terdaftar
                </div>
            </div>
            <div>
                <div class="db-sc-val">{{ number_format($totalCustomers, 0, ',', '.') }}</div>
                <div class="db-sc-label">Total Customer</div>
            </div>
        </div>

        <div class="db-sc db-sc--purple">
            <div class="db-sc-top">
                <div class="db-sc-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z"/><line x1="7" y1="7" x2="7.01" y2="7"/></svg>
                </div>
                <div class="db-sc-chip">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="23 6 13.5 15.5 8.5 10.5 1 18"/><polyline points="17 6 23 6 23 12"/></svg>
                    Aktif
                </div>
            </div>
            <div>
                <div class="db-sc-val">{{ number_format($totalProducts, 0, ',', '.') }}</div>
                <div class="db-sc-label">Total Produk</div>
            </div>
        </div>
    </div>

    {{-- ═══ CHARTS ═══ --}}
    <div class="db-row">
        <div class="db-card">
            <div class="db-ch">
                <div class="db-ct">
                    <div class="db-ct-i">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg>
                    </div>
                    Tren Penjualan
                </div>
                <span class="db-period">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                    7 Hari Terakhir
                </span>
            </div>
            <div class="db-cb" style="padding:16px 20px;">
                <div style="position:relative; height:240px;">
                    <canvas id="salesChart"></canvas>
                </div>
            </div>
        </div>

        <div class="db-card">
            <div class="db-ch">
                <div class="db-ct">
                    <div class="db-ct-i">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M21.21 15.89A10 10 0 1 1 8 2.83"/><path d="M22 12A10 10 0 0 0 12 2v10z"/></svg>
                    </div>
                    Status Pesanan
                </div>
            </div>
            <div class="db-cb" style="padding:16px 20px; align-items:center; justify-content:center;">
                <div style="position:relative; height:240px; width:100%; display:flex; align-items:center; justify-content:center;">
                    <canvas id="orderStatusChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    {{-- ═══ DATA ROW 1 ═══ --}}
    <div class="db-row">
        <div class="db-card">
            <div class="db-ch">
                <div class="db-ct">
                    <div class="db-ct-i">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                    </div>
                    Pesanan Terbaru
                </div>
                <a href="{{ route('orders.index') }}" class="db-ca">
                    Lihat Semua
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="9 18 15 12 9 6"/></svg>
                </a>
            </div>
            <div class="db-tw">
                <table class="db-t">
                    <thead>
                        <tr>
                            <th>Nomor Pesanan</th>
                            <th>Customer</th>
                            <th>Tanggal</th>
                            <th>Total</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentOrders as $order)
                        <tr>
                            <td><span class="db-oid">{{ $order->order_number ?? '#'.$order->id }}</span></td>
                            <td>
                                <div style="display:flex;align-items:center;gap:9px;">
                                    {{-- Avatar: photo → onerror fallback to initials, mirrors customers.show --}}
                                    @if($order->customer && $order->customer->avatar)
                                        <img
                                            src="{{ Storage::url($order->customer->avatar) }}"
                                            alt="{{ $order->customer_name }}"
                                            style="width:30px;height:30px;border-radius:8px;object-fit:cover;flex-shrink:0;border:1.5px solid color-mix(in srgb,var(--accent,#6366f1) 20%,transparent);"
                                            onerror="this.style.display='none';this.nextElementSibling.style.display='inline-flex';">
                                        <div class="db-av" style="display:none;">{{ strtoupper(substr($order->customer_name,0,1)) }}</div>
                                    @else
                                        <div class="db-av">{{ strtoupper(substr($order->customer_name,0,1)) }}</div>
                                    @endif
                                    <span style="font-weight:700;font-size:13px;">{{ $order->customer_name }}</span>
                                </div>
                            </td>
                            <td style="color:var(--text-3,#94a3b8);font-size:12px;font-family:'DM Mono',monospace;">
                                {{ $order->created_at->format('d M Y') }}<br>
                                <span style="font-size:10.5px;">{{ $order->created_at->format('H:i') }}</span>
                            </td>
                            <td style="font-weight:800;color:#10b981;font-size:13px;">Rp {{ number_format($order->total_amount,0,',','.') }}</td>
                            <td>
                                @php
                                    $bgMap = [
                                        'badge-pending' => 'db-b-pending',
                                        'badge-perlu_diproses' => 'db-b-pending',
                                        'badge-processing' => 'db-b-processing',
                                        'badge-shipping' => 'db-b-processing',
                                        'badge-completed' => 'db-b-success',
                                        'badge-cancelled' => 'db-b-failed',
                                        'badge-refunded'  => 'db-b-failed',
                                    ];
                                    $statusClass = $bgMap[\App\Services\StatusService::getOrderBadgeClass($order->status ?? '')] ?? 'db-b-processing';
                                @endphp
                                <span class="db-badge {{ $statusClass }}">{{ \App\Services\StatusService::getOrderLabel($order->status ?? '') }}</span>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="5">
                            <div class="db-empty">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/></svg>
                                <span>Belum ada pesanan terbaru.</span>
                            </div>
                        </td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="db-card">
            <div class="db-ch">
                <div class="db-ct">
                    <div class="db-ct-i">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect x="1" y="4" width="22" height="16" rx="2" ry="2"/><line x1="1" y1="10" x2="23" y2="10"/></svg>
                    </div>
                    Ringkasan Transaksi
                </div>
            </div>
            <div class="db-cb" style="gap:0;">
                <div class="db-tl">
                    <div class="db-tli">
                        <div class="db-tli-icon" style="background:rgba(16,185,129,.11);color:#10b981;">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                        </div>
                        <div class="db-tli-info">
                            <div class="db-tli-title">Berhasil</div>
                            <div class="db-tli-desc">Pembayaran sukses</div>
                        </div>
                        <div class="db-tli-num">{{ $transactionSummaries['success'] ?? 0 }}</div>
                    </div>
                    <div class="db-tli">
                        <div class="db-tli-icon" style="background:rgba(245,158,11,.11);color:#f59e0b;">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                        </div>
                        <div class="db-tli-info">
                            <div class="db-tli-title">Tertunda</div>
                            <div class="db-tli-desc">Menunggu pembayaran</div>
                        </div>
                        <div class="db-tli-num">{{ $transactionSummaries['pending'] ?? 0 }}</div>
                    </div>
                    <div class="db-tli">
                        <div class="db-tli-icon" style="background:rgba(239,68,68,.11);color:#ef4444;">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>
                        </div>
                        <div class="db-tli-info">
                            <div class="db-tli-title">Gagal / Kadaluarsa</div>
                            <div class="db-tli-desc">Dibatalkan atau gagal</div>
                        </div>
                        <div class="db-tli-num">{{ $transactionSummaries['failed'] ?? 0 }}</div>
                    </div>
                    <div class="db-tli">
                        <div class="db-tli-icon" style="background:rgba(225,29,72,.11);color:#e11d48;">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="1 4 1 10 7 10"/><polyline points="23 20 23 14 17 14"/><path d="M20.49 9A9 9 0 0 0 5.64 5.64L1 10M3.51 15A9 9 0 0 0 18.36 18.36L23 14"/></svg>
                        </div>
                        <div class="db-tli-info">
                            <div class="db-tli-title">Dana Dikembalikan</div>
                            <div class="db-tli-desc">Transaksi di-refund</div>
                        </div>
                        <div class="db-tli-num">{{ $transactionSummaries['refund'] ?? 0 }}</div>
                    </div>
                </div>

                <hr class="db-div">
                <p class="db-sub">Kurir Terpopuler</p>
                <div>
                    @forelse($topCouriers as $courier)
                    <div class="db-cr">
                        <span class="db-cr-name">{{ $courier->shipping_courier }}</span>
                        <span class="db-cr-cnt">{{ $courier->count }} kiriman</span>
                    </div>
                    @empty
                    <div class="db-empty" style="padding:14px 0;">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="1" y="3" width="15" height="13"/><polygon points="16 8 20 8 23 11 23 16 16 16 16 8"/><circle cx="5.5" cy="18.5" r="2.5"/><circle cx="18.5" cy="18.5" r="2.5"/></svg>
                        <span>Belum ada data pengiriman.</span>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    {{-- ═══ DATA ROW 2 ═══ --}}
    <div class="db-row">
        <div class="db-card">
            <div class="db-ch">
                <div class="db-ct">
                    <div class="db-ct-i">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                    </div>
                    Produk Terlaris
                </div>
                <a href="{{ route('products.index') }}" class="db-ca">
                    Kelola Produk
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="9 18 15 12 9 6"/></svg>
                </a>
            </div>
            <div class="db-tw">
                <table class="db-t">
                    <thead>
                        <tr>
                            <th style="width:48px;text-align:center;">No.</th>
                            <th>Nama Produk</th>
                            <th style="text-align:right;">Terjual</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($topProducts as $index => $product)
                        <tr>
                            <td style="text-align:center;">
                                @php $rc = match($index){ 0=>'db-rk-g', 1=>'db-rk-s', 2=>'db-rk-b', default=>'db-rk-n' }; @endphp
                                <span class="db-rk {{ $rc }}">{{ $index+1 }}</span>
                            </td>
                            <td>
                                <div style="display:flex;align-items:center;gap:9px;">
                                    <div style="width:28px;height:28px;border-radius:7px;background:var(--surface-2,#eef1f7);display:flex;align-items:center;justify-content:center;color:var(--text-3,#94a3b8);flex-shrink:0;">
                                        <svg viewBox="0 0 24 24" width="13" height="13" fill="none" stroke="currentColor" stroke-width="2"><path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z"/><line x1="7" y1="7" x2="7.01" y2="7"/></svg>
                                    </div>
                                    <span style="font-weight:600;font-size:13px;">{{ $product->name }}</span>
                                </div>
                            </td>
                            <td style="text-align:right;">
                                <span style="font-family:'DM Mono',monospace;font-weight:600;font-size:13px;color:var(--accent,#6366f1);">{{ $product->total_sold }}</span>
                                <span style="font-size:11px;color:var(--text-3,#94a3b8);margin-left:2px;">unit</span>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="3">
                            <div class="db-empty">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                                <span>Belum ada data penjualan produk.</span>
                            </div>
                        </td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="db-card">
            <div class="db-ch">
                <div class="db-ct">
                    <div class="db-ct-i">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg>
                    </div>
                    Aktivitas Terbaru
                </div>
            </div>
            <div class="db-cb">
                <div class="db-al">
                    @forelse($recentActivities as $activity)
                    <div class="db-ali">
                        <div class="db-ali-dot db-ali-dot--{{ $activity['type'] }}">
                            @if($activity['type'] == 'order')
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/></svg>
                            @else
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/><polyline points="3.27 6.96 12 12.01 20.73 6.96"/><line x1="12" y1="22.08" x2="12" y2="12"/></svg>
                            @endif
                        </div>
                        <div class="db-ali-info">
                            <div class="db-ali-text">{{ $activity['description'] }}</div>
                            <div class="db-ali-time">{{ $activity['time'] }}</div>
                        </div>
                    </div>
                    @empty
                    <div class="db-empty">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg>
                        <span>Belum ada aktivitas.</span>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    {{-- ═══ NAVIGASI CEPAT ═══ --}}
    <div class="db-card">
        <div class="db-ch">
            <div class="db-ct">
                <div class="db-ct-i">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><polyline points="12 8 12 12 14 14"/></svg>
                </div>
                Navigasi Cepat
            </div>
            <span class="db-period">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>
                Semua Menu
            </span>
        </div>
        <div class="db-cb">
            <div class="db-qnav">
                {{-- Penjualan --}}
                <a href="{{ route('orders.index') }}" class="db-qn" style="--_qc:#6366f1">
                    <div class="db-qn-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"/><rect x="8" y="2" width="8" height="4" rx="1"/><path d="M12 11h4"/><path d="M12 16h4"/><path d="M8 11h.01"/><path d="M8 16h.01"/></svg>
                    </div>
                    <div class="db-qn-body">
                        <div class="db-qn-title">Kelola Pesanan</div>
                        <div class="db-qn-sub">Penjualan</div>
                    </div>
                    <div class="db-qn-arr"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="9 18 15 12 9 6"/></svg></div>
                </a>
                <a href="{{ route('transactions.index') }}" class="db-qn" style="--_qc:#8b5cf6">
                    <div class="db-qn-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="5" width="20" height="14" rx="2"/><line x1="2" y1="10" x2="22" y2="10"/></svg>
                    </div>
                    <div class="db-qn-body">
                        <div class="db-qn-title">Riwayat Transaksi</div>
                        <div class="db-qn-sub">Penjualan</div>
                    </div>
                    <div class="db-qn-arr"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="9 18 15 12 9 6"/></svg></div>
                </a>
                {{-- Logistik --}}
                <a href="{{ route('deliveries.index') }}" class="db-qn" style="--_qc:#06b6d4">
                    <div class="db-qn-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="1 6 1 22 8 18 16 22 23 18 23 2 16 6 8 2 1 6"/><line x1="8" y1="2" x2="8" y2="18"/><line x1="16" y1="6" x2="16" y2="22"/></svg>
                    </div>
                    <div class="db-qn-body">
                        <div class="db-qn-title">Monitoring Pengiriman</div>
                        <div class="db-qn-sub">Logistik</div>
                    </div>
                    <div class="db-qn-arr"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="9 18 15 12 9 6"/></svg></div>
                </a>
                <a href="{{ route('deliveries.scan') }}" class="db-qn" style="--_qc:#0ea5e9">
                    <div class="db-qn-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 7V4h3"/><path d="M4 17v3h3"/><path d="M20 7V4h-3"/><path d="M20 17v3h-3"/><rect x="9" y="8" width="6" height="8"/></svg>
                    </div>
                    <div class="db-qn-body">
                        <div class="db-qn-title">Cek Resi</div>
                        <div class="db-qn-sub">Logistik</div>
                    </div>
                    <div class="db-qn-arr"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="9 18 15 12 9 6"/></svg></div>
                </a>
                {{-- Data Logistik --}}
                <a href="{{ route('couriers.index') }}" class="db-qn" style="--_qc:#f59e0b">
                    <div class="db-qn-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="1" y="3" width="15" height="13"/><polygon points="16 8 20 8 23 11 23 16 16 16 16 8"/><circle cx="5.5" cy="18.5" r="2.5"/><circle cx="18.5" cy="18.5" r="2.5"/></svg>
                    </div>
                    <div class="db-qn-body">
                        <div class="db-qn-title">Kelola Kurir</div>
                        <div class="db-qn-sub">Data Logistik</div>
                    </div>
                    <div class="db-qn-arr"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="9 18 15 12 9 6"/></svg></div>
                </a>
                <a href="{{ route('shipping-services.index') }}" class="db-qn" style="--_qc:#f97316">
                    <div class="db-qn-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="12 2 3 6.92 12 12 21 6.92 12 2"/><polygon points="12 22.08 12 12 3 6.92 3 17.08 12 22.08"/><polygon points="12 22.08 12 12 21 6.92 21 17.08 12 22.08"/></svg>
                    </div>
                    <div class="db-qn-body">
                        <div class="db-qn-title">Kelola Layanan</div>
                        <div class="db-qn-sub">Data Logistik</div>
                    </div>
                    <div class="db-qn-arr"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="9 18 15 12 9 6"/></svg></div>
                </a>
                <a href="{{ route('provinces.index') }}" class="db-qn" style="--_qc:#10b981">
                    <div class="db-qn-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
                    </div>
                    <div class="db-qn-body">
                        <div class="db-qn-title">Kelola Wilayah</div>
                        <div class="db-qn-sub">Provinsi & Kota</div>
                    </div>
                    <div class="db-qn-arr"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="9 18 15 12 9 6"/></svg></div>
                </a>
                <a href="{{ route('shipping-rates.index') }}" class="db-qn" style="--_qc:#ef4444">
                    <div class="db-qn-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z"/><line x1="7" y1="7" x2="7.01" y2="7"/></svg>
                    </div>
                    <div class="db-qn-body">
                        <div class="db-qn-title">Kelola Ongkir</div>
                        <div class="db-qn-sub">Data Logistik</div>
                    </div>
                    <div class="db-qn-arr"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="9 18 15 12 9 6"/></svg></div>
                </a>
                {{-- Katalog --}}
                <a href="{{ route('products.index') }}" class="db-qn" style="--_qc:#8b5cf6">
                    <div class="db-qn-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/><path d="M16 10a4 4 0 0 1-8 0"/></svg>
                    </div>
                    <div class="db-qn-body">
                        <div class="db-qn-title">Kelola Produk</div>
                        <div class="db-qn-sub">Katalog</div>
                    </div>
                    <div class="db-qn-arr"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="9 18 15 12 9 6"/></svg></div>
                </a>
                <a href="{{ route('product-categories.index') }}" class="db-qn" style="--_qc:#a855f7">
                    <div class="db-qn-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="12 2 2 7 12 12 22 7 12 2"/><polygon points="2 17 12 22 22 17"/><polygon points="2 12 12 17 22 12"/></svg>
                    </div>
                    <div class="db-qn-body">
                        <div class="db-qn-title">Kategori Produk</div>
                        <div class="db-qn-sub">Katalog</div>
                    </div>
                    <div class="db-qn-arr"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="9 18 15 12 9 6"/></svg></div>
                </a>
                <a href="{{ route('stores.index') }}" class="db-qn" style="--_qc:#3b82f6">
                    <div class="db-qn-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
                    </div>
                    <div class="db-qn-body">
                        <div class="db-qn-title">Kelola Toko</div>
                        <div class="db-qn-sub">Katalog</div>
                    </div>
                    <div class="db-qn-arr"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="9 18 15 12 9 6"/></svg></div>
                </a>
                {{-- Analisis & Lainnya --}}
                <a href="{{ route('reports.index') }}" class="db-qn" style="--_qc:#6366f1">
                    <div class="db-qn-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/><line x1="6" y1="20" x2="6" y2="14"/></svg>
                    </div>
                    <div class="db-qn-body">
                        <div class="db-qn-title">Laporan Penjualan</div>
                        <div class="db-qn-sub">Analisis</div>
                    </div>
                    <div class="db-qn-arr"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="9 18 15 12 9 6"/></svg></div>
                </a>
                <a href="{{ route('customers.index') }}" class="db-qn" style="--_qc:#f59e0b">
                    <div class="db-qn-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                    </div>
                    <div class="db-qn-body">
                        <div class="db-qn-title">Kelola Customer</div>
                        <div class="db-qn-sub">Lainnya</div>
                    </div>
                    <div class="db-qn-arr"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="9 18 15 12 9 6"/></svg></div>
                </a>
            </div>
        </div>
    </div>

    {{-- ═══ MONITORING PENGIRIMAN + DATA LOGISTIK ═══ --}}
    <div class="db-row">
        {{-- Monitoring Pengiriman --}}
        <div class="db-card">
            <div class="db-ch">
                <div class="db-ct">
                    <div class="db-ct-i">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polygon points="1 6 1 22 8 18 16 22 23 18 23 2 16 6 8 2 1 6"/><line x1="8" y1="2" x2="8" y2="18"/><line x1="16" y1="6" x2="16" y2="22"/></svg>
                    </div>
                    Monitoring Pengiriman
                </div>
                <a href="{{ route('deliveries.index') }}" class="db-ca">
                    Lihat Semua
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="9 18 15 12 9 6"/></svg>
                </a>
            </div>
            <div class="db-cb">
                <div class="db-drow">
                    <div class="db-ds">
                        <div class="db-ds-val" style="color:#f59e0b;">{{ number_format($deliverySummary['pending'] ?? 0) }}</div>
                        <div class="db-ds-label">Diproses</div>
                    </div>
                    <div class="db-ds">
                        <div class="db-ds-val" style="color:#3b82f6;">{{ number_format($deliverySummary['shipping'] ?? 0) }}</div>
                        <div class="db-ds-label">Dikirim</div>
                    </div>
                    <div class="db-ds">
                        <div class="db-ds-val" style="color:#10b981;">{{ number_format($deliverySummary['delivered'] ?? 0) }}</div>
                        <div class="db-ds-label">Terkirim</div>
                    </div>
                </div>
                <hr class="db-div">
                <p class="db-sub">Kurir Aktif</p>
                <div>
                    @forelse($topCouriers as $courier)
                    <div class="db-cr">
                        <span class="db-cr-name">{{ $courier->shipping_courier }}</span>
                        <span class="db-cr-cnt">{{ $courier->count }} kiriman</span>
                    </div>
                    @empty
                    <div class="db-empty" style="padding:14px 0;">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="1" y="3" width="15" height="13"/><polygon points="16 8 20 8 23 11 23 16 16 16 16 8"/><circle cx="5.5" cy="18.5" r="2.5"/><circle cx="18.5" cy="18.5" r="2.5"/></svg>
                        <span>Belum ada data pengiriman.</span>
                    </div>
                    @endforelse
                </div>
                <hr class="db-div">
                <a href="{{ route('deliveries.scan') }}" class="db-ca" style="align-self:flex-start;">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 7V4h3"/><path d="M4 17v3h3"/><path d="M20 7V4h-3"/><path d="M20 17v3h-3"/><rect x="9" y="8" width="6" height="8"/></svg>
                    Cek Resi / Scan
                </a>
            </div>
        </div>

        {{-- Ringkasan Data --}}
        <div class="db-card">
            <div class="db-ch">
                <div class="db-ct">
                    <div class="db-ct-i">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect x="3" y="3" width="18" height="18" rx="2"/><path d="M3 9h18"/><path d="M9 21V9"/></svg>
                    </div>
                    Ringkasan Data
                </div>
            </div>
            <div class="db-cb">
                <div class="db-mgrid">
                    <a href="{{ route('stores.index') }}" class="db-mc" style="--_mc:#3b82f6">
                        <div class="db-mc-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
                        </div>
                        <div class="db-mc-body">
                            <div class="db-mc-val">{{ number_format($totalStores ?? 0) }}</div>
                            <div class="db-mc-label">Toko Aktif</div>
                        </div>
                    </a>
                    <a href="{{ route('product-categories.index') }}" class="db-mc" style="--_mc:#a855f7">
                        <div class="db-mc-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="12 2 2 7 12 12 22 7 12 2"/><polygon points="2 17 12 22 22 17"/><polygon points="2 12 12 17 22 12"/></svg>
                        </div>
                        <div class="db-mc-body">
                            <div class="db-mc-val">{{ number_format($totalCategories ?? 0) }}</div>
                            <div class="db-mc-label">Kategori</div>
                        </div>
                    </a>
                    <a href="{{ route('couriers.index') }}" class="db-mc" style="--_mc:#f59e0b">
                        <div class="db-mc-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="1" y="3" width="15" height="13"/><polygon points="16 8 20 8 23 11 23 16 16 16 16 8"/><circle cx="5.5" cy="18.5" r="2.5"/><circle cx="18.5" cy="18.5" r="2.5"/></svg>
                        </div>
                        <div class="db-mc-body">
                            <div class="db-mc-val">{{ number_format($totalCouriers ?? 0) }}</div>
                            <div class="db-mc-label">Kurir</div>
                        </div>
                    </a>
                    <a href="{{ route('shipping-services.index') }}" class="db-mc" style="--_mc:#f97316">
                        <div class="db-mc-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="12 2 3 6.92 12 12 21 6.92 12 2"/><polygon points="12 22.08 12 12 3 6.92 3 17.08 12 22.08"/><polygon points="12 22.08 12 12 21 6.92 21 17.08 12 22.08"/></svg>
                        </div>
                        <div class="db-mc-body">
                            <div class="db-mc-val">{{ number_format($totalShippingServices ?? 0) }}</div>
                            <div class="db-mc-label">Layanan</div>
                        </div>
                    </a>
                    <a href="{{ route('provinces.index') }}" class="db-mc" style="--_mc:#10b981">
                        <div class="db-mc-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
                        </div>
                        <div class="db-mc-body">
                            <div class="db-mc-val">{{ number_format($totalProvinces ?? 0) }}</div>
                            <div class="db-mc-label">Provinsi</div>
                        </div>
                    </a>
                    <a href="{{ route('shipping-rates.index') }}" class="db-mc" style="--_mc:#ef4444">
                        <div class="db-mc-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z"/><line x1="7" y1="7" x2="7.01" y2="7"/></svg>
                        </div>
                        <div class="db-mc-body">
                            <div class="db-mc-val">{{ number_format($totalShippingRates ?? 0) }}</div>
                            <div class="db-mc-label">Tarif Ongkir</div>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- ═══ LAPORAN SNAPSHOT ═══ --}}
    <div class="db-card">
        <div class="db-ch">
            <div class="db-ct">
                <div class="db-ct-i">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/><line x1="6" y1="20" x2="6" y2="14"/></svg>
                </div>
                Laporan Penjualan — Bulan Ini
            </div>
            <a href="{{ route('reports.index') }}" class="db-ca">
                Laporan Lengkap
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="9 18 15 12 9 6"/></svg>
            </a>
        </div>
        <div class="db-cb">
            <div class="db-rsnap">
                <div class="db-rs">
                    <div class="db-rs-dot" style="background:#10b981;"></div>
                    <div class="db-rs-lbl">Pesanan Selesai</div>
                    <div class="db-rs-val">{{ number_format($reportSnapshot['completed'] ?? 0) }}</div>
                </div>
                <div class="db-rs">
                    <div class="db-rs-dot" style="background:#f59e0b;"></div>
                    <div class="db-rs-lbl">Dalam Proses</div>
                    <div class="db-rs-val">{{ number_format($reportSnapshot['processing'] ?? 0) }}</div>
                </div>
                <div class="db-rs">
                    <div class="db-rs-dot" style="background:#ef4444;"></div>
                    <div class="db-rs-lbl">Dibatalkan</div>
                    <div class="db-rs-val">{{ number_format($reportSnapshot['cancelled'] ?? 0) }}</div>
                </div>
                <div class="db-rs">
                    <div class="db-rs-dot" style="background:#b91c1c;"></div>
                    <div class="db-rs-lbl">Pengembalian</div>
                    <div class="db-rs-val">{{ number_format($reportSnapshot['refunded'] ?? 0) }}</div>
                </div>
                <div class="db-rs">
                    <div class="db-rs-dot" style="background:#6366f1;"></div>
                    <div class="db-rs-lbl">Pendapatan Bulan Ini</div>
                    <div class="db-rs-val" style="color:var(--accent,#6366f1);">Rp {{ number_format($reportSnapshot['revenue'] ?? 0, 0, ',', '.') }}</div>
                </div>
                <div class="db-rs">
                    <div class="db-rs-dot" style="background:#3b82f6;"></div>
                    <div class="db-rs-lbl">Total Customer</div>
                    <div class="db-rs-val">{{ number_format($totalCustomers, 0, ',', '.') }}</div>
                </div>
                <div class="db-rs">
                    <div class="db-rs-dot" style="background:#8b5cf6;"></div>
                    <div class="db-rs-lbl">Produk Aktif</div>
                    <div class="db-rs-val">{{ number_format($totalProducts, 0, ',', '.') }}</div>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const accent    = '#6366f1';
    const textMuted = '#94a3b8';
    const gridLine  = 'rgba(0,0,0,0.06)';
    const tooltipBg = '#0f172a';

    /* ── SALES LINE CHART ── */
    const salesCtx = document.getElementById('salesChart').getContext('2d');
    const gradFill = salesCtx.createLinearGradient(0, 0, 0, 240);
    gradFill.addColorStop(0,   'rgba(99,102,241,0.20)');
    gradFill.addColorStop(0.6, 'rgba(99,102,241,0.05)');
    gradFill.addColorStop(1,   'rgba(99,102,241,0.00)');

    new Chart(salesCtx, {
        type: 'line',
        data: {
            labels: {!! json_encode($last7DaysLabels) !!},
            datasets: [{
                label: 'Pendapatan',
                data: {!! json_encode($salesChartData) !!},
                borderColor: accent,
                backgroundColor: gradFill,
                borderWidth: 2,
                pointBackgroundColor: '#fff',
                pointBorderColor: accent,
                pointBorderWidth: 2,
                pointRadius: 3.5,
                pointHoverRadius: 6,
                pointHoverBackgroundColor: accent,
                pointHoverBorderColor: '#fff',
                pointHoverBorderWidth: 2,
                fill: true,
                tension: 0.42,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: tooltipBg,
                    titleColor: 'rgba(255,255,255,0.5)',
                    bodyColor: '#fff',
                    padding: 11,
                    cornerRadius: 8,
                    displayColors: false,
                    titleFont: { size: 10, family: "'DM Sans', sans-serif", weight: '600' },
                    bodyFont:  { size: 14, family: "'DM Sans', sans-serif", weight: '800' },
                    callbacks: { label: ctx => 'Rp ' + ctx.parsed.y.toLocaleString('id-ID') }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    border: { display: false },
                    grid: { color: gridLine },
                    ticks: {
                        color: textMuted,
                        font: { family: "'DM Sans', sans-serif", size: 10 },
                        padding: 6,
                        callback: v => {
                            if (v >= 1_000_000) return 'Rp '+(v/1_000_000).toFixed(1)+'jt';
                            if (v >= 1_000)     return 'Rp '+(v/1_000).toFixed(0)+'rb';
                            return v;
                        }
                    }
                },
                x: {
                    border: { display: false },
                    grid: { display: false },
                    ticks: { color: textMuted, font: { family: "'DM Sans', sans-serif", size: 10 }, padding: 6 }
                }
            },
            interaction: { intersect: false, mode: 'index' },
        }
    });

    /* ── ORDER STATUS DOUGHNUT ── */
    const statusCtx  = document.getElementById('orderStatusChart').getContext('2d');
    const statusData = {!! json_encode($ordersByStatus) !!};
    const backendLabels = {!! json_encode(\App\Services\StatusService::ORDER_LABELS) !!};
    const statusMap  = {
        pending:        { color: '#f59e0b' },
        perlu_diproses: { color: '#6366f1' },
        processing:     { color: '#3b82f6' },
        shipping:       { color: '#8b5cf6' },
        completed:      { color: '#10b981' },
        cancelled:      { color: '#ef4444' },
        refunded:       { color: '#b91c1c' },
    };

    let labels = [], values = [], colors = [];
    for (const [k, v] of Object.entries(statusData)) {
        const color = statusMap[k]?.color ?? '#94a3b8';
        const label = backendLabels[k] ?? k.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase());
        labels.push(label); values.push(v); colors.push(color);
    }
    if (!values.length) { labels=['Belum Ada Data']; values=[1]; colors=['#e2e8f0']; }

    new Chart(statusCtx, {
        type: 'doughnut',
        data: {
            labels,
            datasets: [{
                data: values,
                backgroundColor: colors,
                borderWidth: 3,
                borderColor: 'transparent',
                hoverBorderColor: '#fff',
                hoverBorderWidth: 3,
                hoverOffset: 5,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '72%',
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        usePointStyle: true, pointStyle: 'circle',
                        padding: 14, color: textMuted,
                        font: { family: "'DM Sans', sans-serif", size: 11, weight: '600' }
                    }
                },
                tooltip: {
                    backgroundColor: tooltipBg, padding: 11, cornerRadius: 8,
                    bodyFont: { size: 13, weight: '700', family: "'DM Sans', sans-serif" },
                    callbacks: {
                        label: ctx => labels[0]==='Belum Ada Data' ? ' 0 Pesanan' : ` ${ctx.label}: ${ctx.parsed} pesanan`
                    }
                }
            }
        }
    });

    /* ── REALTIME DASHBOARD SYNC ── */
    document.addEventListener('realtime-notification', function(e) {
        fetch(window.location.href)
            .then(res => res.text())
            .then(html => {
                const parser = new DOMParser();
                const doc = parser.parseFromString(html, 'text/html');
                
                // Update Top Stats
                const stats = document.querySelector('.db-stats');
                const newStats = doc.querySelector('.db-stats');
                if(stats && newStats) stats.innerHTML = newStats.innerHTML;
                
                // Update Tables (Recent Orders & Top Products)
                const tws = document.querySelectorAll('.db-tw');
                const newTws = doc.querySelectorAll('.db-tw');
                tws.forEach((tw, i) => { if(newTws[i]) tw.innerHTML = newTws[i].innerHTML; });

                // Update Transaction Summaries
                const tl = document.querySelector('.db-tl');
                const newTl = doc.querySelector('.db-tl');
                if(tl && newTl) tl.innerHTML = newTl.innerHTML;

                // Update Recent Activities
                const al = document.querySelector('.db-al');
                const newAl = doc.querySelector('.db-al');
                if(al && newAl) al.innerHTML = newAl.innerHTML;

                // Update Master Data Grid
                const mgrid = document.querySelector('.db-mgrid');
                const newMgrid = doc.querySelector('.db-mgrid');
                if(mgrid && newMgrid) mgrid.innerHTML = newMgrid.innerHTML;

                // Update Report Snapshot
                const rsnap = document.querySelector('.db-rsnap');
                const newRsnap = doc.querySelector('.db-rsnap');
                if(rsnap && newRsnap) rsnap.innerHTML = newRsnap.innerHTML;

                // Update Delivery Section
                const drow = document.querySelector('.db-drow');
                const newDrow = doc.querySelector('.db-drow');
                if(drow && newDrow) drow.innerHTML = newDrow.innerHTML;
            })
            .catch(err => console.error('Dashboard sync error:', err));
    });
});
</script>
@endpush
