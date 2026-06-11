@extends('layouts.app')

@section('title', 'Laporan Penjualan')

@section('styles')
<style>
    .rp-wrap {
        display: block;
        min-height: 0;
        position: relative;
    }

    /* ── Sticky Sidenav — fixed to viewport, clear of app sidebar ── */
    .rp-sidenav {
        display: none;
        /* hidden by default; shown only on very wide screens via media query below */
        position: fixed;
        top: 80px;
        /* 'left' is set dynamically via JS once we know the content offset */
        width: 190px;
        background: var(--panel);
        border: 1px solid var(--border);
        border-radius: 14px;
        padding: 16px 10px;
        box-shadow: var(--shadow-sm);
        z-index: 100;
        max-height: calc(100vh - 100px);
        overflow-y: auto;
    }

    @media (min-width: 1400px) {
        .rp-sidenav {
            display: block;
        }

        .rp-main-content {
            margin-left: 210px;
        }
    }

    .rp-nav-label {
        font-size: 10px;
        font-weight: 800;
        letter-spacing: 0.1em;
        text-transform: uppercase;
        color: var(--text-4);
        padding: 0 10px;
        margin-bottom: 8px;
    }

    .rp-nav-item {
        display: flex;
        align-items: center;
        gap: 9px;
        padding: 8px 10px;
        border-radius: 8px;
        font-size: 13px;
        font-weight: 600;
        color: var(--text-3);
        text-decoration: none;
        transition: background 0.15s, color 0.15s;
        cursor: pointer;
        white-space: nowrap;
    }

    .rp-nav-item:hover {
        background: var(--surface);
        color: var(--text-1);
    }

    .rp-nav-item.active {
        background: var(--accent-dim);
        color: var(--accent);
        font-weight: 700;
    }

    .rp-nav-item svg {
        width: 14px;
        height: 14px;
        flex-shrink: 0;
    }

    .rp-nav-div {
        height: 1px;
        background: var(--border);
        margin: 8px 2px;
    }

    /* ── Section structure ─────────────────────────────────── */
    .rp-section {
        scroll-margin-top: 80px;
        margin-bottom: 40px;
    }

    .rp-section-head {
        display: flex;
        align-items: flex-end;
        justify-content: space-between;
        margin-bottom: 18px;
        gap: 12px;
    }

    .rp-section-title {
        font-size: 16px;
        font-weight: 800;
        color: var(--text-1);
        letter-spacing: -0.02em;
        margin: 0 0 2px;
    }

    .rp-section-sub {
        font-size: 12.5px;
        color: var(--text-3);
        margin: 0;
    }

    .rp-sep {
        height: 1px;
        background: var(--border);
        margin: 4px 0 32px;
        border: none;
    }

    /* ── KPI grid ──────────────────────────────────────────── */
    .rp-kpi-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 14px;
        margin-bottom: 20px;
    }

    @media (max-width: 1200px) {
        .rp-kpi-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }

    @media (max-width: 520px) {
        .rp-kpi-grid {
            grid-template-columns: 1fr;
        }
    }

    .rp-kpi {
        background: var(--panel);
        border: 1px solid var(--border);
        border-radius: 12px;
        padding: 18px 20px;
        display: flex;
        flex-direction: column;
        box-shadow: var(--shadow-sm);
        position: relative;
        overflow: hidden;
        transition: transform 0.18s, box-shadow 0.18s;
    }

    .rp-kpi:hover {
        transform: translateY(-2px);
        box-shadow: var(--shadow-md);
    }

    .rp-kpi::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        height: 3px;
        background: var(--accent);
        opacity: 0;
        transition: opacity 0.18s;
    }

    .rp-kpi:hover::after {
        opacity: 1;
    }

    .rp-kpi-top {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 12px;
    }

    .rp-kpi-label {
        font-size: 11px;
        font-weight: 700;
        color: var(--text-3);
        text-transform: uppercase;
        letter-spacing: 0.06em;
    }

    .rp-kpi-icon {
        width: 38px;
        height: 38px;
        border-radius: 9px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    .rp-kpi-icon svg {
        width: 18px;
        height: 18px;
    }

    .rp-kpi-icon.blue {
        background: rgba(59, 130, 246, .1);
        color: #3b82f6;
    }

    .rp-kpi-icon.green {
        background: var(--green-dim);
        color: var(--green);
    }

    .rp-kpi-icon.purple {
        background: rgba(139, 92, 246, .1);
        color: #8b5cf6;
    }

    .rp-kpi-icon.amber {
        background: rgba(245, 158, 11, .1);
        color: #f59e0b;
    }

    .rp-kpi-icon.rose {
        background: rgba(244, 63, 94, .1);
        color: #f43f5e;
    }

    .rp-kpi-val {
        font-size: 22px;
        font-weight: 800;
        color: var(--text-1);
        letter-spacing: -0.03em;
        margin-bottom: 3px;
        line-height: 1.2;
    }

    .rp-kpi-sub {
        font-size: 11.5px;
        color: var(--text-4);
        font-weight: 500;
    }

    /* ── Chart cards ───────────────────────────────────────── */
    .rp-chart-row {
        display: grid;
        gap: 18px;
        margin-bottom: 18px;
    }

    .rp-col-2-1 {
        grid-template-columns: 2fr 1fr;
    }

    .rp-col-1-1 {
        grid-template-columns: 1fr 1fr;
    }

    .rp-col-3 {
        grid-template-columns: repeat(3, 1fr);
    }

    @media (max-width: 960px) {

        .rp-col-2-1,
        .rp-col-1-1,
        .rp-col-3 {
            grid-template-columns: 1fr;
        }
    }

    .rp-card {
        background: var(--panel);
        border: 1px solid var(--border);
        border-radius: 12px;
        padding: 20px 22px;
        box-shadow: var(--shadow-sm);
        min-width: 0;
    }

    .rp-card-head {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 16px;
    }

    .rp-card-title {
        font-size: 13.5px;
        font-weight: 800;
        color: var(--text-1);
    }

    .rp-badge {
        font-size: 11px;
        font-weight: 700;
        padding: 3px 9px;
        border-radius: 20px;
        background: var(--accent-dim);
        color: var(--accent);
    }

    /* ── Tables ────────────────────────────────────────────── */
    .rp-tbl {
        width: 100%;
        border-collapse: collapse;
    }

    .rp-tbl th {
        text-align: left;
        font-size: 10.5px;
        font-weight: 700;
        color: var(--text-4);
        text-transform: uppercase;
        letter-spacing: 0.05em;
        padding: 9px 0;
        border-bottom: 1px solid var(--border);
    }

    .rp-tbl td {
        padding: 10px 0;
        border-bottom: 1px solid var(--surface);
        vertical-align: middle;
        font-size: 13px;
    }

    .rp-tbl tbody tr:last-child td {
        border-bottom: none;
    }

    .rp-data-card {
        background: var(--panel);
        border: 1px solid var(--border);
        border-radius: 12px;
        overflow: hidden;
        box-shadow: var(--shadow-sm);
    }

    .rp-data-tbl {
        width: 100%;
        border-collapse: collapse;
    }

    .rp-data-tbl th {
        background: var(--surface);
        padding: 11px 16px;
        text-align: left;
        font-size: 10.5px;
        font-weight: 700;
        color: var(--text-3);
        border-bottom: 1px solid var(--border);
        text-transform: uppercase;
        letter-spacing: 0.05em;
        white-space: nowrap;
    }

    .rp-data-tbl td {
        padding: 13px 16px;
        border-bottom: 1px solid var(--border);
        font-size: 13px;
        transition: background 0.1s;
    }

    .rp-data-tbl tbody tr:last-child td {
        border-bottom: none;
    }

    .rp-data-tbl tbody tr:hover td {
        background: color-mix(in srgb, var(--accent) 3%, var(--surface));
    }

    .rp-data-tbl tfoot td {
        background: var(--surface);
        font-weight: 800;
        font-size: 13px;
        border-top: 2px solid var(--border);
    }

    .rp-dot {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        display: inline-block;
        margin-right: 7px;
        flex-shrink: 0;
    }

    /* ── Filter bar ────────────────────────────────────────── */
    .rp-filter {
        background: var(--surface);
        border: 1px solid var(--border);
        border-radius: 10px;
        padding: 16px 18px;
        margin-bottom: 16px;
    }

    .rp-filter-grid {
        display: grid;
        gap: 12px;
    }

    .rp-fg-3 {
        grid-template-columns: repeat(3, 1fr);
    }

    .rp-fg-2 {
        grid-template-columns: 1fr 1fr;
    }

    @media (max-width: 760px) {

        .rp-fg-3,
        .rp-fg-2 {
            grid-template-columns: 1fr;
        }
    }

    .rp-fg-actions {
        display: flex;
        justify-content: flex-end;
        gap: 8px;
        margin-top: 12px;
        padding-top: 12px;
        border-top: 1px solid var(--border);
    }

    .rp-form-group {
        display: flex;
        flex-direction: column;
        min-width: 0;
    }

    .rp-label {
        font-size: 10.5px;
        font-weight: 700;
        color: var(--text-3);
        margin-bottom: 4px;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }

    .rp-input {
        width: 100%;
        padding: 8px 11px;
        border: 1px solid var(--border);
        border-radius: 8px;
        font-family: var(--font);
        font-size: 13px;
        color: var(--text-1);
        background: var(--panel);
        outline: none;
        transition: border-color 0.15s, box-shadow 0.15s;
        box-sizing: border-box;
    }

    .rp-input:focus {
        border-color: var(--accent);
        box-shadow: 0 0 0 3px var(--accent-glow);
    }

    /* ── Buttons ───────────────────────────────────────────── */
    .rp-btn-primary {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: var(--accent);
        color: #fff;
        border: none;
        padding: 7px 15px;
        border-radius: 8px;
        font-family: var(--font);
        font-weight: 700;
        font-size: 12.5px;
        cursor: pointer;
        text-decoration: none;
        transition: all 0.15s;
        white-space: nowrap;
        height: 34px;
        box-sizing: border-box;
        box-shadow: 0 2px 8px color-mix(in srgb, var(--accent) 25%, transparent);
    }

    .rp-btn-primary:hover {
        opacity: 0.9;
        transform: translateY(-1px);
    }

    .rp-btn-primary svg {
        width: 13px;
        height: 13px;
    }

    .rp-btn-outline {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: var(--panel);
        color: var(--text-2);
        border: 1px solid var(--border);
        padding: 7px 13px;
        border-radius: 8px;
        font-family: var(--font);
        font-weight: 700;
        font-size: 12.5px;
        cursor: pointer;
        text-decoration: none;
        transition: all 0.15s;
        white-space: nowrap;
        height: 34px;
        box-sizing: border-box;
    }

    .rp-btn-outline:hover {
        border-color: var(--border-2);
        color: var(--text-1);
    }

    .rp-btn-outline svg {
        width: 13px;
        height: 13px;
    }

    .rp-range-select {
        padding: 7px 12px;
        border: 1px solid var(--border);
        border-radius: 8px;
        background: var(--panel);
        font-weight: 700;
        color: var(--text-2);
        font-family: var(--font);
        font-size: 13px;
        outline: none;
        cursor: pointer;
        height: 34px;
    }

    /* ── Store report cards ────────────────────────────────── */
    .rp-store-card {
        background: var(--panel);
        border: 1px solid var(--border);
        border-radius: 12px;
        padding: 22px;
        margin-bottom: 16px;
        box-shadow: var(--shadow-sm);
        position: relative;
        overflow: hidden;
        transition: transform 0.18s, box-shadow 0.18s;
    }

    .rp-store-card:hover {
        transform: translateY(-2px);
        box-shadow: var(--shadow-md);
    }

    .rp-store-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 3px;
        background: var(--accent);
        opacity: 0;
        transition: 0.18s;
    }

    .rp-store-card:hover::before {
        opacity: 1;
    }

    .rp-store-head {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 18px;
        padding-bottom: 14px;
        border-bottom: 1px solid var(--surface);
    }

    .rp-store-title {
        font-size: 16px;
        font-weight: 800;
        color: var(--text-1);
        letter-spacing: -0.02em;
    }

    .rp-stat-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 10px;
        margin-bottom: 16px;
    }

    @media (max-width: 800px) {
        .rp-stat-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }

    .rp-stat {
        background: var(--surface);
        padding: 14px 16px;
        border-radius: 9px;
        border: 1px solid var(--border);
        transition: background 0.15s;
    }

    .rp-stat:hover {
        background: color-mix(in srgb, var(--accent) 4%, var(--surface));
    }

    .rp-stat-lbl {
        font-size: 10.5px;
        font-weight: 700;
        color: var(--text-4);
        text-transform: uppercase;
        letter-spacing: 0.05em;
        margin-bottom: 5px;
    }

    .rp-stat-val {
        font-size: 19px;
        font-weight: 800;
        color: var(--text-1);
        letter-spacing: -0.02em;
    }

    /* ── Export card ───────────────────────────────────────── */
    .rp-export-card {
        background: var(--surface);
        border: 1px solid var(--border);
        border-radius: 10px;
        padding: 16px 18px;
        /* fade-in when section scrolls into view */
        animation: rpExCardIn 0.35s cubic-bezier(0.22, 1, 0.36, 1) both;
    }

    @keyframes rpExCardIn {
        from {
            opacity: 0;
            transform: translateY(10px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    /* ── Type selector ── */
    .rp-type-sel {
        display: flex;
        gap: 12px;
        margin-bottom: 16px;
    }

    @media (max-width:760px) {
        .rp-type-sel {
            flex-direction: column;
        }
    }

    .rp-type-opt {
        flex: 1;
        border: 1px solid var(--border);
        border-radius: 8px;
        padding: 12px 14px;
        cursor: pointer;
        background: var(--panel);
        display: flex;
        align-items: center;
        gap: 10px;
        transition: border-color 0.18s, background 0.18s, box-shadow 0.18s, transform 0.18s;
        position: relative;
        overflow: hidden;
    }

    /* ripple layer */
    .rp-type-opt::after {
        content: '';
        position: absolute;
        inset: 0;
        background: radial-gradient(circle at var(--rx, 50%) var(--ry, 50%), color-mix(in srgb, var(--accent) 12%, transparent) 0%, transparent 70%);
        opacity: 0;
        transition: opacity 0.3s;
        pointer-events: none;
    }

    .rp-type-opt:hover {
        border-color: var(--accent);
        transform: translateY(-1px);
        box-shadow: 0 3px 10px color-mix(in srgb, var(--accent) 10%, transparent);
    }

    .rp-type-opt:hover::after {
        opacity: 1;
    }

    .rp-type-opt:active {
        transform: translateY(0) scale(0.985);
        transition-duration: 0.08s;
    }

    .rp-type-opt.active {
        border-color: var(--accent);
        background: var(--accent-dim);
        box-shadow: 0 0 0 3px var(--accent-glow);
    }

    .rp-type-opt.active::after {
        opacity: 1;
    }

    /* checkmark badge shown on active option */
    .rp-type-check {
        margin-left: auto;
        width: 18px;
        height: 18px;
        border-radius: 50%;
        background: var(--accent);
        display: flex;
        align-items: center;
        justify-content: center;
        opacity: 0;
        transform: scale(0.5) rotate(-20deg);
        transition: opacity 0.2s, transform 0.25s cubic-bezier(0.34, 1.56, 0.64, 1);
        flex-shrink: 0;
    }

    .rp-type-check svg {
        width: 10px;
        height: 10px;
    }

    .rp-type-opt.active .rp-type-check {
        opacity: 1;
        transform: scale(1) rotate(0deg);
    }

    .rp-type-icon {
        width: 36px;
        height: 36px;
        border-radius: 8px;
        background: var(--surface);
        display: flex;
        align-items: center;
        justify-content: center;
        border: 1px solid var(--border);
        flex-shrink: 0;
        transition: background 0.18s, border-color 0.18s, transform 0.18s;
    }

    .rp-type-opt:hover .rp-type-icon {
        border-color: var(--accent);
        transform: scale(1.06);
    }

    .rp-type-opt.active .rp-type-icon {
        background: color-mix(in srgb, var(--accent) 12%, var(--surface));
        border-color: var(--accent);
    }

    .rp-type-icon svg {
        transition: transform 0.2s cubic-bezier(0.34, 1.56, 0.64, 1);
    }

    .rp-type-opt:hover .rp-type-icon svg {
        transform: scale(1.15) rotate(-4deg);
    }

    .rp-type-title {
        font-size: 13.5px;
        font-weight: 800;
        color: var(--text-1);
        margin-bottom: 1px;
        transition: color 0.15s;
    }

    .rp-type-desc {
        font-size: 11.5px;
        color: var(--text-3);
        font-weight: 500;
        transition: color 0.15s;
    }

    .rp-type-opt.active .rp-type-title {
        color: var(--accent);
    }

    /* ── Store selector slide-down ── */
    /* rp-export-grid replaced by rp-filter-grid rp-fg-2 in HTML */
    #rpStoreSelector {
        overflow: hidden;
        transition: max-height 0.28s cubic-bezier(0.4, 0, 0.2, 1), opacity 0.22s ease, margin-bottom 0.28s;
    }

    #rpStoreSelector.rp-sel-hidden {
        max-height: 0;
        opacity: 0;
        margin-bottom: 0 !important;
        pointer-events: none;
    }

    #rpStoreSelector.rp-sel-visible {
        max-height: 120px;
        opacity: 1;
        margin-bottom: 12px;
        pointer-events: auto;
    }

    /* ── Export button ── */
    .rp-btn-export {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: var(--accent);
        color: #fff;
        border: none;
        padding: 7px 15px;
        border-radius: 8px;
        font-family: var(--font);
        font-weight: 700;
        font-size: 12.5px;
        cursor: pointer;
        transition: opacity 0.15s, transform 0.18s, box-shadow 0.18s, background 0.18s;
        white-space: nowrap;
        height: 34px;
        box-sizing: border-box;
        box-shadow: 0 2px 8px color-mix(in srgb, var(--accent) 25%, transparent);
        position: relative;
        overflow: hidden;
    }

    /* shimmer sweep on idle */
    .rp-btn-export::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 60%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.18), transparent);
        transform: skewX(-20deg);
        animation: rpBtnShimmer 3.2s 1.5s infinite;
    }

    @keyframes rpBtnShimmer {

        0%,
        100% {
            left: -100%;
        }

        40%,
        60% {
            left: 160%;
        }
    }

    .rp-btn-export:hover {
        opacity: 0.92;
        transform: translateY(-1px);
        box-shadow: 0 5px 14px color-mix(in srgb, var(--accent) 35%, transparent);
    }

    .rp-btn-export:active {
        transform: translateY(0) scale(0.97);
        transition-duration: 0.08s;
    }

    /* loading state */
    .rp-btn-export.rp-loading {
        pointer-events: none;
        opacity: 0.8;
    }

    .rp-btn-export.rp-loading::before {
        display: none;
    }

    .rp-btn-export .rp-btn-spinner {
        display: none;
        width: 13px;
        height: 13px;
        border: 2px solid rgba(255, 255, 255, 0.35);
        border-top-color: #fff;
        border-radius: 50%;
        animation: rpSpin 0.6s linear infinite;
    }

    .rp-btn-export.rp-loading .rp-btn-icon {
        display: none;
    }

    .rp-btn-export.rp-loading .rp-btn-spinner {
        display: block;
    }

    @keyframes rpSpin {
        to {
            transform: rotate(360deg);
        }
    }

    /* ── Input focus polish (scoped to export card) ── */
    .rp-export-card .rp-input {
        transition: border-color 0.15s, box-shadow 0.15s, background 0.15s;
    }

    .rp-export-card .rp-input:hover:not(:focus) {
        border-color: color-mix(in srgb, var(--accent) 40%, var(--border));
    }

    /* ── Date field row fade-in stagger ── */
    .rp-export-card .rp-filter-grid .rp-form-group {
        animation: rpFgIn 0.3s cubic-bezier(0.22, 1, 0.36, 1) both;
    }

    .rp-export-card .rp-filter-grid .rp-form-group:nth-child(1) {
        animation-delay: 0.05s;
    }

    .rp-export-card .rp-filter-grid .rp-form-group:nth-child(2) {
        animation-delay: 0.10s;
    }

    @keyframes rpFgIn {
        from {
            opacity: 0;
            transform: translateY(6px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    /* ── Notes ─────────────────────────────────────────────── */
    .rp-notes {
        background: var(--surface);
        border: 1px dashed var(--border);
        border-radius: 10px;
        padding: 16px 20px;
        margin-top: 14px;
    }

    .rp-notes h4 {
        font-size: 12.5px;
        font-weight: 800;
        color: var(--text-1);
        margin: 0 0 8px;
    }

    .rp-notes ul {
        font-size: 12.5px;
        color: var(--text-3);
        padding-left: 18px;
        line-height: 1.7;
        margin: 0;
    }

    /* ── Status badges ─────────────────────────────────────── */
    .rp-status {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        font-size: 11px;
        font-weight: 700;
        padding: 3px 9px;
        border-radius: 20px;
    }

    .rp-status.completed {
        background: var(--green-dim);
        color: var(--green);
    }

    .rp-status.cancelled {
        background: rgba(244, 63, 94, .1);
        color: #f43f5e;
    }

    .rp-status.pending {
        background: rgba(245, 158, 11, .1);
        color: #f59e0b;
    }

    .rp-status.badge-pending { background: rgba(245, 158, 11, .1); color: #f59e0b; }
    .rp-status.badge-paid { background: var(--green-dim); color: var(--green); }
    .rp-status.badge-failed { background: rgba(244, 63, 94, .1); color: #f43f5e; }
    .rp-status.badge-refund { background: var(--surface-2); color: var(--text-3); }

    /* ── Product thumbnail ─────────────────────────────────── */
    .rp-prod-row {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .rp-prod-img {
        width: 28px;
        height: 28px;
        border-radius: 5px;
        object-fit: cover;
        background: var(--surface-2);
        flex-shrink: 0;
    }

    /* ── Trend indicator ───────────────────────────────────── */
    .rp-trend-up {
        color: var(--green);
        font-size: 11.5px;
        font-weight: 700;
    }

    .rp-trend-down {
        color: #f43f5e;
        font-size: 11.5px;
        font-weight: 700;
    }
</style>
@endsection

@section('content')

{{-- ── Page header (no duplicate of topbar) ──────────────── --}}
<div style="margin-bottom:24px;">
    <div style="font-size:22px;font-weight:800;color:var(--text-1);letter-spacing:-0.025em;">Laporan Penjualan</div>
    <div style="font-size:13px;color:var(--text-3);margin-top:2px;">Pantau performa bisnis dan analisis transaksi antar toko</div>
</div>

<div class="rp-wrap">

    {{-- ── Sticky sidenav ──────────────────────────────────── --}}
    <aside class="rp-sidenav">
        <div class="rp-nav-label">Laporan</div>
        <a href="#rp-trend" class="rp-nav-item active">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                <path d="M3 3v18h18" />
                <path d="M18.7 8l-5.1 5.2-2.8-2.7L7 14.3" />
            </svg>
            Tren & Analitik
        </a>
        <a href="#rp-toko" class="rp-nav-item">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z" />
                <polyline points="9 22 9 12 15 12 15 22" />
            </svg>
            Per Toko
        </a>
        <a href="#rp-konsolidasi" class="rp-nav-item">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                <rect x="2" y="3" width="20" height="14" rx="2" />
                <line x1="2" y1="10" x2="22" y2="10" />
            </svg>
            Konsolidasi
        </a>
        <div class="rp-nav-div"></div>
        <a href="#rp-produk" class="rp-nav-item">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                <path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z" />
                <line x1="7" y1="7" x2="7.01" y2="7" />
            </svg>
            Kinerja Produk
        </a>
        <a href="#rp-pending" class="rp-nav-item">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                <circle cx="12" cy="12" r="10" />
                <line x1="12" y1="8" x2="12" y2="12" />
                <line x1="12" y1="16" x2="12.01" y2="16" />
            </svg>
            Piutang / Pending
        </a>
        <a href="#rp-batal" class="rp-nav-item">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                <polygon points="7.86 2 16.14 2 22 7.86 22 16.14 16.14 22 7.86 22 2 16.14 2 7.86 7.86 2" />
                <line x1="15" y1="9" x2="9" y2="15" />
                <line x1="9" y1="9" x2="15" y2="15" />
            </svg>
            Analisis Batal
        </a>
        <div class="rp-nav-div"></div>
        <a href="#rp-ekspor" class="rp-nav-item">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4" />
                <polyline points="7 10 12 15 17 10" />
                <line x1="12" y1="15" x2="12" y2="3" />
            </svg>
            Ekspor PDF
        </a>
    </aside>

    {{-- ── Main ─────────────────────────────────────────────── --}}
    <div class="rp-main-content">

        {{-- ════════════════════════════════════════════════════
             §1  TREND & ANALYTICS
        ═════════════════════════════════════════════════════ --}}
        <section id="rp-trend" class="rp-section">
            <div class="rp-section-head">
                <div>
                    <div class="rp-section-title">Tren & Analitik</div>
                    <div class="rp-section-sub">Tren bulanan, breakdown status pesanan, dan pendapatan kumulatif ({{ $days }} hari terakhir)</div>
                </div>
                <form action="{{ route('reports.index') }}" method="GET" id="rangeForm" style="display:flex;gap:6px;align-items:center;">
                    {{-- Preserve other filters --}}
                    @foreach(request()->except('days') as $k => $v)
                    <input type="hidden" name="{{ $k }}" value="{{ $v }}">
                    @endforeach
                    <select name="days" class="rp-range-select" onchange="this.form.submit()">
                        <option value="7" {{ $days == 7  ? 'selected' : '' }}>7 Hari</option>
                        <option value="30" {{ $days == 30 ? 'selected' : '' }}>30 Hari</option>
                        <option value="90" {{ $days == 90 ? 'selected' : '' }}>90 Hari</option>
                        <option value="365" {{ $days == 365 ? 'selected' : '' }}>1 Tahun</option>
                    </select>
                </form>
            </div>

            {{-- Monthly Revenue Bar --}}
            <div class="rp-chart-row" style="grid-template-columns: 1fr;">
                <div class="rp-card">
                    <div class="rp-card-head">
                        <div class="rp-card-title">Pendapatan Bulanan (12 Bulan)</div>
                    </div>
                    <div style="height:260px;"><canvas id="chartMonthly"></canvas></div>
                </div>
            </div>

            {{-- Monthly Revenue Table --}}
            <div class="rp-data-card">
                <div style="padding:16px 18px;border-bottom:1px solid var(--border);display:flex;align-items:center;justify-content:space-between;">
                    <div style="font-size:13px;font-weight:800;color:var(--text-1);">Rincian Bulanan (12 Bulan Terakhir)</div>
                </div>
                <div style="overflow-x:auto;">
                    <table class="rp-data-tbl">
                        <thead>
                            <tr>
                                <th style="width:40px;">No</th>
                                <th>Bulan</th>
                                <th style="text-align:right;">Jumlah Transaksi</th>
                                <th style="text-align:right;">Total Pendapatan</th>
                                <th style="text-align:right;">Rata-rata/Trx</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($monthlyRevenue as $mIdx => $m)
                            <tr>
                                <td style="color:var(--text-4);font-size:12px;">{{ $mIdx + 1 }}</td>
                                <td style="font-weight:600;">
                                    {{ \Carbon\Carbon::createFromFormat('Y-m', $m->month)->translatedFormat('F Y') }}
                                </td>
                                <td style="text-align:right;">{{ number_format($m->count) }}</td>
                                <td style="text-align:right;font-weight:700;color:var(--text-1);">Rp {{ number_format($m->revenue, 0, ',', '.') }}</td>
                                <td style="text-align:right;color:var(--text-3);">Rp {{ $m->count > 0 ? number_format($m->revenue / $m->count, 0, ',', '.') : '0' }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" style="text-align:center;color:var(--text-4);padding:24px;">Tidak ada data</td>
                            </tr>
                            @endforelse
                        </tbody>
                        @if($monthlyRevenue->isNotEmpty())
                        <tfoot>
                            <tr>
                                <td></td>
                                <td>TOTAL</td>
                                <td style="text-align:right;">{{ number_format($monthlyRevenue->sum('count')) }}</td>
                                <td style="text-align:right;color:var(--accent);">Rp {{ number_format($monthlyRevenue->sum('revenue'), 0, ',', '.') }}</td>
                                <td style="text-align:right;color:var(--text-3);">—</td>
                            </tr>
                        </tfoot>
                        @endif
                    </table>
                </div>
            </div>
        </section>

        <hr class="rp-sep">

        {{-- ════════════════════════════════════════════════════
             §NEW  KINERJA PRODUK
        ═════════════════════════════════════════════════════ --}}
        <section id="rp-produk" class="rp-section">
            <div class="rp-section-head">
                <div>
                    <div class="rp-section-title">Laporan Performa Produk</div>
                    <div class="rp-section-sub">Analisis mendalam performa seluruh inventaris (Berdasarkan {{ $days }} hari terakhir)</div>
                </div>
            </div>
            <div class="rp-data-card">
                <div style="overflow-x:auto; max-height:450px; overflow-y:auto;">
                    <table class="rp-data-tbl">
                        <thead>
                            <tr>
                                <th style="width:40px;">No</th>
                                <th>Produk</th>
                                <th>Toko</th>
                                <th style="text-align:right;">Total Terjual</th>
                                <th style="text-align:right;">Total Penjualan</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($allProductsPerformance as $pIdx => $p)
                            <tr>
                                <td style="color:var(--text-4);font-size:12px;">{{ $pIdx + 1 }}</td>
                                <td>
                                    <div class="rp-prod-row">
                                        @if(!empty($p->image))
                                        <img src="{{ asset('storage/' . $p->image) }}" alt="{{ $p->name }}" class="rp-prod-img" onerror="this.onerror=null; this.src='{{ asset('img/no-image.png') }}';">
                                        @else
                                        <div class="rp-prod-img" style="display:flex;align-items:center;justify-content:center;border:1px solid var(--border);">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" style="width:14px;height:14px;color:var(--text-4);">
                                                <rect x="3" y="3" width="18" height="18" rx="2" />
                                                <circle cx="8.5" cy="8.5" r="1.5" />
                                                <polyline points="21 15 16 10 5 21" />
                                            </svg>
                                        </div>
                                        @endif
                                        <div style="font-weight:700;color:var(--text-1);">{{ $p->name }}</div>
                                    </div>
                                </td>
                                <td>{{ $p->store->name ?? '—' }}</td>
                                <td style="text-align:right; font-weight:800; color: {{ $p->total_sold > 0 ? 'var(--accent)' : 'var(--text-4)' }};">
                                    {{ number_format($p->total_sold) }}
                                </td>
                                <td style="text-align:right; font-weight:700;">Rp {{ number_format($p->total_revenue, 0, ',', '.') }}</td>
                                <td>
                                    @if($p->total_sold == 0)
                                    <span class="rp-status cancelled" style="font-size:10px;">Kurang Laku</span>
                                    @else
                                    <span class="rp-status completed" style="font-size:10px;">Aktif Terjual</span>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" style="text-align:center;padding:24px;color:var(--text-4);">Tidak ada data produk.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </section>

        <hr class="rp-sep">

        {{-- ════════════════════════════════════════════════════
             §NEW  TRANSAKSI TERTUNDA / PIUTANG
        ═════════════════════════════════════════════════════ --}}
        <section id="rp-pending" class="rp-section">
            <div class="rp-section-head">
                <div>
                    <div class="rp-section-title">Pembayaran Belum Selesai</div>
                    <div class="rp-section-sub">Daftar transaksi unpaid/failed yang perlu di-follow up atau dibatalkan</div>
                </div>
            </div>
            <div class="rp-data-card">
                <div style="overflow-x:auto;">
                    <table class="rp-data-tbl">
                        <thead>
                            <tr>
                                <th style="width:40px;">No</th>
                                <th>Nomor Pesanan</th>
                                <th>Pelanggan</th>
                                <th>Toko</th>
                                <th>Metode Bayar</th>
                                <th style="text-align:right;">Nominal</th>
                                <th>Status Pembayaran</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($pendingPayments as $trxIdx => $trx)
                            <tr>
                                <td style="color:var(--text-4);font-size:12px;">{{ $trxIdx + 1 }}</td>
                                <td style="font-family:monospace; font-weight:700;">{{ $trx->order?->order_number ?? '—' }}</td>
                                <td>{{ $trx->order->customer_name ?? '—' }}</td>
                                <td>{{ $trx->order->store->name ?? '—' }}</td>
                                <td style="text-transform:uppercase;">{{ $trx->payment_method }}</td>
                                <td style="text-align:right; font-weight:800;">Rp {{ number_format($trx->amount, 0, ',', '.') }}</td>
                                <td>
                                    <span class="rp-status {{ \App\Services\StatusService::getTransactionBadgeClass($trx->status ?? '') }}">
                                        {{ \App\Services\StatusService::getTransactionLabel($trx->status ?? '') }}
                                    </span>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" style="text-align:center;padding:24px;color:var(--text-4);">Semua transaksi lunas. Tidak ada piutang.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </section>

        <hr class="rp-sep">

        {{-- ════════════════════════════════════════════════════
             §NEW  ANALISIS PEMBATALAN
        ═════════════════════════════════════════════════════ --}}
        <section id="rp-batal" class="rp-section">
            <div class="rp-section-head">
                <div>
                    <div class="rp-section-title">Analisis Pembatalan</div>
                    <div class="rp-section-sub">Pemetaan alasan pembatalan untuk evaluasi operasional ({{ $days }} hari terakhir)</div>
                </div>
            </div>
            <div class="rp-chart-row rp-col-2-1">
                <div class="rp-data-card" style="height:100%;">
                    <table class="rp-data-tbl">
                        <thead>
                            <tr>
                                <th style="width:40px;">No</th>
                                <th>Alasan Pembatalan</th>
                                <th style="text-align:right;">Jumlah Kasus</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($cancellationAnalysis as $caIdx => $ca)
                            <tr>
                                <td style="color:var(--text-4);font-size:12px;">{{ $caIdx + 1 }}</td>
                                <td style="font-weight:600;">{{ $ca->cancel_reason ?: 'Tanpa Alasan Spesifik' }}</td>
                                <td style="text-align:right; font-weight:800; color:#f43f5e;">{{ number_format($ca->count) }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="3" style="text-align:center;padding:24px;color:var(--text-4);">Tidak ada pembatalan tercatat di periode ini. Hebat!</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="rp-card" style="display:flex; flex-direction:column; justify-content:center; align-items:center; text-align:center;">
                    <div style="width:54px; height:54px; background:rgba(244,63,94,.1); border-radius:14px; display:flex; align-items:center; justify-content:center; margin-bottom:14px; color:#f43f5e;">
                        <svg viewBox="0 0 24 24" width="28" height="28" stroke="currentColor" stroke-width="2.5" fill="none">
                            <circle cx="12" cy="12" r="10" />
                            <line x1="15" y1="9" x2="9" y2="15" />
                            <line x1="9" y1="9" x2="15" y2="15" />
                        </svg>
                    </div>
                    <div style="font-size:24px; font-weight:800; color:var(--text-1); margin-bottom:4px;">{{ number_format($cancellationAnalysis->sum('count')) }}</div>
                    <div style="font-size:12.5px; color:var(--text-3); font-weight:500;">Total Pembatalan<br>({{ $days }} hari)</div>
                </div>
            </div>
        </section>

        <hr class="rp-sep">

        {{-- ════════════════════════════════════════════════════
             §3  PER-STORE REPORT
        ═════════════════════════════════════════════════════ --}}
        <section id="rp-toko" class="rp-section">
            <div class="rp-section-head">
                <div>
                    <div class="rp-section-title">Laporan Per Toko</div>
                    <div class="rp-section-sub">Detail performa spesifik toko dalam rentang waktu tertentu</div>
                </div>
            </div>

            <div class="rp-filter">
                <form action="{{ route('reports.index') }}" method="GET">
                    {{-- Preserve dashboard range --}}
                    <input type="hidden" name="days" value="{{ $days }}">
                    <div class="rp-filter-grid rp-fg-3">
                        <div class="rp-form-group">
                            <label class="rp-label">Pilih Toko</label>
                            <select name="store_id" class="rp-input">
                                <option value="">Semua Toko</option>
                                @foreach($allStores as $s)
                                <option value="{{ $s->id }}" {{ request('store_id') == $s->id ? 'selected' : '' }}>{{ $s->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="rp-form-group">
                            <label class="rp-label">Tanggal Mulai</label>
                            <input type="date" name="store_start_date" class="rp-input" value="{{ $storeStartDate }}">
                        </div>
                        <div class="rp-form-group">
                            <label class="rp-label">Tanggal Selesai</label>
                            <input type="date" name="store_end_date" class="rp-input" value="{{ $storeEndDate }}">
                        </div>
                    </div>
                    <div class="rp-fg-actions">
                        <a href="{{ route('reports.index', ['days' => $days]) }}#rp-toko" class="rp-btn-outline">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                                <line x1="18" y1="6" x2="6" y2="18" />
                                <line x1="6" y1="6" x2="18" y2="18" />
                            </svg>
                            Reset
                        </a>
                        <button type="submit" class="rp-btn-primary">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                                <circle cx="11" cy="11" r="8" />
                                <line x1="21" y1="21" x2="16.65" y2="16.65" />
                            </svg>
                            Tampilkan
                        </button>
                    </div>
                </form>
            </div>

            @forelse($stores as $store)
            <div class="rp-store-card">
                <div class="rp-store-head">
                    <div style="display:flex;align-items:center;gap:10px;">
                        <div style="width:36px;height:36px;background:var(--accent-dim);border-radius:8px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                            <svg viewBox="0 0 24 24" width="17" height="17" stroke="var(--accent)" stroke-width="2.5" fill="none">
                                <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z" />
                                <polyline points="9 22 9 12 15 12 15 22" />
                            </svg>
                        </div>
                        <div class="rp-store-title">{{ $store->name }}</div>
                    </div>
                    <a href="{{ route('reports.export', ['type' => 'store', 'store_id' => $store->id, 'start_date' => $storeStartDate, 'end_date' => $storeEndDate]) }}" target="_blank" class="rp-btn-outline">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                            <polyline points="6 9 6 2 18 2 18 9" />
                            <path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2" />
                            <rect x="6" y="14" width="12" height="8" />
                        </svg>
                        Cetak PDF
                    </a>
                </div>

                <div class="rp-stat-grid">
                    <div class="rp-stat">
                        <div class="rp-stat-lbl">Total Pesanan</div>
                        <div class="rp-stat-val">{{ number_format($store->orders_count) }}</div>
                    </div>
                    <div class="rp-stat">
                        <div class="rp-stat-lbl">Estimasi Pendapatan</div>
                        <div class="rp-stat-val" style="color:var(--accent);font-size:16px;">Rp {{ number_format($store->revenue, 0, ',', '.') }}</div>
                    </div>
                    <div class="rp-stat">
                        <div class="rp-stat-lbl">Tingkat Keberhasilan</div>
                        @php
                        $cancelled = $store->cancelled_count;
                        $rate = $store->orders_count > 0 ? (($store->orders_count - $cancelled) / $store->orders_count) * 100 : 0;
                        @endphp
                        <div class="rp-stat-val" style="{{ $rate >= 80 ? 'color:var(--green)' : 'color:#f59e0b' }}">{{ number_format($rate, 1) }}%</div>
                    </div>
                    <div class="rp-stat">
                        <div class="rp-stat-lbl">Rata-rata Nilai Pesanan</div>
                        <div class="rp-stat-val" style="font-size:16px;">Rp {{ $store->orders_count > 0 ? number_format($store->revenue / $store->orders_count, 0, ',', '.') : '0' }}</div>
                    </div>
                </div>

                <div style="background:var(--surface);border-radius:8px;padding:13px 15px;border:1px solid var(--border);">
                    <div style="font-size:11px;font-weight:700;color:var(--text-3);text-transform:uppercase;letter-spacing:.05em;margin-bottom:5px;">Ringkasan Periode {{ $storeStartDate }} — {{ $storeEndDate }}</div>
                    <div style="font-size:13px;color:var(--text-3);line-height:1.6;">
                        Periode ini mencatat <strong style="color:var(--text-1);">{{ number_format($store->orders_count) }} transaksi</strong>
                        dengan total pendapatan bersih (non-cancelled)
                        <strong style="color:var(--accent);">Rp {{ number_format($store->revenue, 0, ',', '.') }}</strong>.
                        Klik <strong style="color:var(--text-2);">Cetak PDF</strong> untuk laporan lengkap per transaksi.
                    </div>
                </div>
            </div>
            @empty
            <div style="text-align:center;padding:52px 20px;background:var(--panel);border:1px dashed var(--border);border-radius:12px;">
                <div style="width:52px;height:52px;background:var(--surface);border-radius:12px;display:flex;align-items:center;justify-content:center;margin:0 auto 14px;">
                    <svg viewBox="0 0 24 24" width="26" height="26" stroke="var(--text-4)" stroke-width="1.5" fill="none">
                        <circle cx="11" cy="11" r="8" />
                        <line x1="21" y1="21" x2="16.65" y2="16.65" />
                    </svg>
                </div>
                <div style="font-size:15px;font-weight:800;color:var(--text-1);margin-bottom:5px;">Tidak Ada Data</div>
                <div style="color:var(--text-3);font-size:13px;max-width:340px;margin:0 auto;">Tidak ada penjualan ditemukan untuk kriteria atau rentang tanggal yang dipilih.</div>
            </div>
            @endforelse
        </section>

        <hr class="rp-sep">

        {{-- ════════════════════════════════════════════════════
             §4  CONSOLIDATED REPORT
        ═════════════════════════════════════════════════════ --}}
        <section id="rp-konsolidasi" class="rp-section">
            <div class="rp-section-head">
                <div>
                    <div class="rp-section-title">Laporan Konsolidasi</div>
                    <div class="rp-section-sub">Perbandingan performa seluruh toko dalam satu tabel</div>
                </div>
                <a href="{{ route('reports.export', ['type' => 'consolidated', 'start_date' => $consolidatedStartDate, 'end_date' => $consolidatedEndDate]) }}" target="_blank" class="rp-btn-outline">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                        <polyline points="6 9 6 2 18 2 18 9" />
                        <path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2" />
                        <rect x="6" y="14" width="12" height="8" />
                    </svg>
                    Cetak PDF
                </a>
            </div>

            <div class="rp-filter">
                <form action="{{ route('reports.index') }}" method="GET">
                    <input type="hidden" name="days" value="{{ $days }}">
                    <input type="hidden" name="store_start_date" value="{{ $storeStartDate }}">
                    <input type="hidden" name="store_end_date" value="{{ $storeEndDate }}">
                    <div class="rp-filter-grid rp-fg-2">
                        <div class="rp-form-group">
                            <label class="rp-label">Tanggal Mulai</label>
                            <input type="date" name="cons_start_date" class="rp-input" value="{{ $consolidatedStartDate }}">
                        </div>
                        <div class="rp-form-group">
                            <label class="rp-label">Tanggal Selesai</label>
                            <input type="date" name="cons_end_date" class="rp-input" value="{{ $consolidatedEndDate }}">
                        </div>
                    </div>
                    <div class="rp-fg-actions">
                        <a href="{{ route('reports.index', ['days' => $days]) }}#rp-konsolidasi" class="rp-btn-outline">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                                <line x1="18" y1="6" x2="6" y2="18" />
                                <line x1="6" y1="6" x2="18" y2="18" />
                            </svg>
                            Reset
                        </a>
                        <button type="submit" class="rp-btn-primary">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                                <circle cx="11" cy="11" r="8" />
                                <line x1="21" y1="21" x2="16.65" y2="16.65" />
                            </svg>
                            Tampilkan
                        </button>
                    </div>
                </form>
            </div>

            <div class="rp-data-card">
                <div style="overflow-x:auto;">
                    <table class="rp-data-tbl">
                        <thead>
                            <tr>
                                <th style="width:40px;">No</th>
                                <th>Nama Toko</th>
                                <th style="text-align:right;">Total Pesanan</th>
                                <th style="text-align:right;">Selesai</th>
                                <th style="text-align:right;">Dibatalkan</th>
                                <th style="text-align:right;">Tingkat Keberhasilan</th>
                                <th style="text-align:right;">Total Pendapatan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $dotColors = ['#4f46e5','#10b981','#f59e0b','#ef4444','#8b5cf6','#06b6d4']; @endphp
                            @foreach($report as $idx => $r)
                            <tr>
                                <td style="color:var(--text-4);font-size:12px;">{{ $idx + 1 }}</td>
                                <td style="font-weight:700;color:var(--text-1);">
                                    <span class="rp-dot" style="background:{{ $dotColors[$idx % count($dotColors)] }};"></span>
                                    {{ $r['store_name'] }}
                                </td>
                                <td style="text-align:right;font-weight:600;">{{ number_format($r['total_orders']) }}</td>
                                <td style="text-align:right;color:var(--green);font-weight:700;">{{ number_format($r['completed_orders']) }}</td>
                                <td style="text-align:right;color:#f43f5e;font-weight:700;">{{ number_format($r['cancelled_orders']) }}</td>
                                <td style="text-align:right;">
                                    @php $sr = $r['total_orders'] > 0 ? round((($r['total_orders'] - $r['cancelled_orders']) / $r['total_orders']) * 100, 1) : 0; @endphp
                                    <span style="font-weight:700;color:{{ $sr >= 80 ? 'var(--green)' : '#f59e0b' }};">{{ $sr }}%</span>
                                </td>
                                <td style="text-align:right;font-weight:800;color:var(--text-1);">Rp {{ number_format($r['total_revenue'], 0, ',', '.') }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <td></td>
                                <td style="font-weight:800;">TOTAL KONSOLIDASI</td>
                                <td style="text-align:right;font-weight:800;">{{ number_format($totals['orders']) }}</td>
                                <td style="text-align:right;">—</td>
                                <td style="text-align:right;">—</td>
                                <td style="text-align:right;">—</td>
                                <td style="text-align:right;font-weight:800;color:var(--accent);">Rp {{ number_format($totals['revenue'], 0, ',', '.') }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            <div class="rp-notes">
                <h4>Catatan Laporan</h4>
                <ul>
                    <li>Data Total Pesanan mencakup seluruh status transaksi (Termasuk Pending & Cancelled).</li>
                    <li>Total Pendapatan dihitung <strong>hanya</strong> dari pesanan yang pembayarannya telah berhasil (Paid / Settlement).</li>
                    <li>Pastikan semua transaksi dikonfirmasi "Paid" agar laporan keuangan akurat 100%.</li>
                </ul>
            </div>
        </section>

        <hr class="rp-sep">

        {{-- ════════════════════════════════════════════════════
             §5  EXPORT
        ═════════════════════════════════════════════════════ --}}
        <section id="rp-ekspor" class="rp-section">
            <div class="rp-section-head">
                <div>
                    <div class="rp-section-title">Ekspor Laporan</div>
                    <div class="rp-section-sub">Unduh atau cetak laporan penjualan dalam format PDF profesional</div>
                </div>
            </div>

            <div class="rp-export-card">
                <form action="{{ route('reports.export') }}" method="GET" target="_blank" id="rpExportForm">
                    {{-- Report type selector --}}
                    <div class="rp-type-sel" role="radiogroup" aria-label="Jenis laporan">
                        <div class="rp-type-opt active" data-type="consolidated" role="radio" aria-checked="true" tabindex="0">
                            <div class="rp-type-icon">
                                <svg viewBox="0 0 24 24" width="18" height="18" stroke="var(--accent)" stroke-width="2.5" fill="none">
                                    <rect x="2" y="3" width="20" height="14" rx="2" />
                                    <line x1="2" y1="10" x2="22" y2="10" />
                                </svg>
                            </div>
                            <div style="flex:1;min-width:0;">
                                <div class="rp-type-title">Konsolidasi</div>
                                <div class="rp-type-desc">Semua toko</div>
                            </div>
                            <div class="rp-type-check" aria-hidden="true">
                                <svg viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                                    <polyline points="20 6 9 17 4 12" />
                                </svg>
                            </div>
                            <input type="radio" name="type" value="consolidated" checked style="display:none;">
                        </div>
                        <div class="rp-type-opt" data-type="store" role="radio" aria-checked="false" tabindex="0">
                            <div class="rp-type-icon">
                                <svg viewBox="0 0 24 24" width="18" height="18" stroke="#64748b" stroke-width="2.5" fill="none">
                                    <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z" />
                                    <polyline points="9 22 9 12 15 12 15 22" />
                                </svg>
                            </div>
                            <div style="flex:1;min-width:0;">
                                <div class="rp-type-title">Per Toko</div>
                                <div class="rp-type-desc">Satu toko saja</div>
                            </div>
                            <div class="rp-type-check" aria-hidden="true">
                                <svg viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                                    <polyline points="20 6 9 17 4 12" />
                                </svg>
                            </div>
                            <input type="radio" name="type" value="store" style="display:none;">
                        </div>
                    </div>

                    {{-- Store selector (animated slide) --}}
                    <div id="rpStoreSelector" class="rp-sel-hidden">
                        <div class="rp-form-group" style="padding-bottom:12px;">
                            <label class="rp-label">Pilih Toko</label>
                            <select name="store_id" class="rp-input">
                                @foreach($allStores as $s)
                                <option value="{{ $s->id }}">{{ $s->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    {{-- Date range --}}
                    <div class="rp-filter-grid rp-fg-2">
                        <div class="rp-form-group">
                            <label class="rp-label">Tanggal Mulai</label>
                            <input type="date" name="start_date" class="rp-input" value="{{ $exportDefaultStart }}">
                        </div>
                        <div class="rp-form-group">
                            <label class="rp-label">Tanggal Selesai</label>
                            <input type="date" name="end_date" class="rp-input" value="{{ $exportDefaultEnd }}">
                        </div>
                    </div>

                    {{-- Actions --}}
                    <div class="rp-fg-actions">
                        <button type="submit" class="rp-btn-export" id="rpExportBtn">
                            <span class="rp-btn-spinner"></span>
                            <svg class="rp-btn-icon" viewBox="0 0 24 24" width="13" height="13" stroke="white" stroke-width="2.5" fill="none">
                                <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4" />
                                <polyline points="7 10 12 15 17 10" />
                                <line x1="12" y1="15" x2="12" y2="3" />
                            </svg>
                            <span class="rp-btn-label">Buat Laporan PDF</span>
                        </button>
                    </div>
                </form>
            </div>
        </section>

    </div>{{-- /main --}}
</div>{{-- /rp-wrap --}}

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    (function() {
        /* ── Colour helpers ────────────────────────────────────── */
        const ACCENT = '#4f46e5';
        const PALETTE = ['#4f46e5', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6', '#06b6d4', '#ec4899'];
        const toK = v => v >= 1e6 ? 'Rp ' + (v / 1e6).toFixed(1) + 'M' : v >= 1e3 ? 'Rp ' + (v / 1e3).toFixed(0) + 'K' : 'Rp ' + v;

        /* 4. Monthly Revenue Bar ─────────────────────────────── */
        const monthData = @json($monthlyRevenue);
        new Chart(document.getElementById('chartMonthly'), {
            type: 'bar',
            data: {
                labels: monthData.map(d => {
                    const [y, m] = d.month.split('-');
                    return new Date(y, m - 1).toLocaleDateString('id-ID', {
                        month: 'short',
                        year: '2-digit'
                    });
                }),
                datasets: [{
                    label: 'Pendapatan',
                    data: monthData.map(d => d.revenue),
                    backgroundColor: ACCENT + 'bb',
                    borderColor: ACCENT,
                    borderWidth: 1.5,
                    borderRadius: 5
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        callbacks: {
                            label: ctx => ' Rp ' + ctx.raw.toLocaleString('id-ID')
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: 'rgba(0,0,0,0.04)'
                        },
                        ticks: {
                            callback: toK
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        }
                    }
                }
            }
        });

        /* ── Sidenav auto-highlight on scroll ─────────────────── */
        const navLinks = document.querySelectorAll('.rp-nav-item');

        // Position fixed sidenav relative to actual content area left edge
        (function positionSidenav() {
            const sidenav = document.querySelector('.rp-sidenav');
            const wrap = document.querySelector('.rp-wrap');
            if (!sidenav || !wrap) return;
            const rect = wrap.getBoundingClientRect();
            sidenav.style.left = rect.left + 'px';
        })();
        window.addEventListener('resize', function() {
            const sidenav = document.querySelector('.rp-sidenav');
            const wrap = document.querySelector('.rp-wrap');
            if (!sidenav || !wrap) return;
            const rect = wrap.getBoundingClientRect();
            sidenav.style.left = rect.left + 'px';
        });

        const rpSections = document.querySelectorAll('.rp-section');
        const observer = new IntersectionObserver(entries => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const id = '#' + entry.target.id;
                    navLinks.forEach(l => l.classList.toggle('active', l.getAttribute('href') === id));
                }
            });
        }, {
            threshold: 0.2,
            rootMargin: '-80px 0px -60% 0px'
        });
        rpSections.forEach(s => observer.observe(s));

        /* ── Export type toggle ───────────────────────────────── */
        (function() {
            const opts = document.querySelectorAll('.rp-type-opt');
            const selector = document.getElementById('rpStoreSelector');
            const form = document.getElementById('rpExportForm');
            const btn = document.getElementById('rpExportBtn');
            const btnLabel = btn ? btn.querySelector('.rp-btn-label') : null;

            function activateOpt(target) {
                opts.forEach(o => {
                    const isTarget = o === target;
                    o.classList.toggle('active', isTarget);
                    o.setAttribute('aria-checked', isTarget ? 'true' : 'false');
                    const svg = o.querySelector('.rp-type-icon svg');
                    if (svg) svg.setAttribute('stroke', isTarget ? 'var(--accent)' : '#64748b');
                    o.querySelector('input[type=radio]').checked = isTarget;
                });
                const isStore = target.getAttribute('data-type') === 'store';
                if (isStore) {
                    selector.classList.remove('rp-sel-hidden');
                    selector.classList.add('rp-sel-visible');
                } else {
                    selector.classList.remove('rp-sel-visible');
                    selector.classList.add('rp-sel-hidden');
                }
            }

            opts.forEach(opt => {
                /* click */
                opt.addEventListener('click', function(e) {
                    /* radial ripple origin */
                    const rect = this.getBoundingClientRect();
                    const rx = ((e.clientX - rect.left) / rect.width * 100).toFixed(1) + '%';
                    const ry = ((e.clientY - rect.top) / rect.height * 100).toFixed(1) + '%';
                    this.style.setProperty('--rx', rx);
                    this.style.setProperty('--ry', ry);
                    activateOpt(this);
                });
                /* keyboard (Space / Enter) */
                opt.addEventListener('keydown', function(e) {
                    if (e.key === ' ' || e.key === 'Enter') {
                        e.preventDefault();
                        activateOpt(this);
                    }
                });
            });

            /* loading state on submit */
            if (form && btn && btnLabel) {
                form.addEventListener('submit', function() {
                    btn.classList.add('rp-loading');
                    btnLabel.textContent = 'Membuat PDF…';
                    /* auto-reset after tab opens (popup/new tab fires after ~800ms) */
                    setTimeout(() => {
                        btn.classList.remove('rp-loading');
                        btnLabel.textContent = 'Buat Laporan PDF';
                    }, 3500);
                });
            }
        })();
    })();
</script>
@endsection
