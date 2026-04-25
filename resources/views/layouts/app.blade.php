<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Dashboard') — SiPesan Admin</title>

    <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=JetBrains+Mono:wght@400;500&display=swap"
        rel="stylesheet">

    <style>
        :root {
            --bg: #f1f4f9;
            --sidebar: #ffffff;
            --panel: #ffffff;
            --surface: #f7f9fc;
            --surface-2: #eef1f7;
            --surface-3: #e4e9f2;
            --border: rgba(0, 0, 0, 0.07);
            --border-2: rgba(0, 0, 0, 0.13);
            --accent: #6366f1;
            --accent-dim: rgba(99, 102, 241, 0.09);
            --accent-glow: rgba(99, 102, 241, 0.20);
            --blue: #3b82f6;
            --blue-dim: rgba(59, 130, 246, 0.09);
            --green: #16a34a;
            --green-dim: rgba(22, 163, 74, 0.09);
            --amber: #d97706;
            --amber-dim: rgba(217, 119, 6, 0.09);
            --red: #dc2626;
            --red-dim: rgba(220, 38, 38, 0.09);
            --cyan: #0891b2;
            --cyan-dim: rgba(8, 145, 178, 0.09);
            --text-1: #0f172a;
            --text-2: #475569;
            --text-3: #94a3b8;
            --text-4: #cbd5e1;
            --font: 'Plus Jakarta Sans', sans-serif;
            --mono: 'JetBrains Mono', monospace;
            --sidebar-w: 248px;
            --topbar-h: 60px;
            --shadow-sm: 0 1px 3px rgba(0, 0, 0, 0.06), 0 1px 2px rgba(0, 0, 0, 0.04);
            --shadow-md: 0 4px 12px rgba(0, 0, 0, 0.07), 0 2px 4px rgba(0, 0, 0, 0.04);
            --shadow-lg: 0 10px 30px rgba(0, 0, 0, 0.10), 0 4px 8px rgba(0, 0, 0, 0.05);
        }

        *,
        *::before,
        *::after {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        html {
            height: 100%;
        }

        body {
            background: var(--bg);
            color: var(--text-1);
            font-family: var(--font);
            font-size: 14px;
            line-height: 1.5;
            min-height: 100vh;
            display: flex;
            -webkit-font-smoothing: antialiased;
        }

        /* ═══════════════════════════════════════
           SIDEBAR
        ═══════════════════════════════════════ */
        .sidebar {
            width: 248px;
            flex-shrink: 0;
            background: var(--sidebar);
            border-right: 1px solid var(--border);
            display: flex;
            flex-direction: column;
            position: fixed;
            top: 0;
            left: 0;
            bottom: 0;
            z-index: 50;
            overflow-y: auto;
            overflow-x: hidden;
            box-shadow: var(--shadow-sm);
            transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .sidebar-overlay {
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.5);
            backdrop-filter: blur(2px);
            z-index: 45;
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s ease;
        }

        .sidebar-overlay.open {
            opacity: 1;
            visibility: visible;
        }

        .sidebar::-webkit-scrollbar {
            width: 4px;
        }

        .sidebar::-webkit-scrollbar-track {
            background: transparent;
        }

        .sidebar::-webkit-scrollbar-thumb {
            background: var(--surface-3);
            border-radius: 4px;
        }

        .sidebar-brand {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 0 20px;
            height: var(--topbar-h);
            border-bottom: 1px solid var(--border);
            text-decoration: none;
            flex-shrink: 0;
        }

        .brand-icon {
            width: 40px;
            height: 40px;
            background: rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(5px);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            border: 1px solid rgba(255, 255, 255, 0.3);
        }

        .logo-icon {
            width: 40px;
            height: 40px;
            background: rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(5px);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            border: 1px solid rgba(255, 255, 255, 0.3);
        }

        .brand-name {
            font-size: 15.5px;
            font-weight: 800;
            letter-spacing: -0.03em;
            color: var(--text-1);
        }

        .brand-name span {
            color: var(--accent);
        }

        .nav-section {
            padding: 20px 12px 8px;
            flex-shrink: 0;
        }

        .nav-section-label {
            font-size: 10px;
            font-weight: 700;
            letter-spacing: 0.1em;
            text-transform: uppercase;
            color: var(--text-4);
            padding: 0 8px;
            margin-bottom: 6px;
        }

        .nav-item {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 9px 10px;
            border-radius: 8px;
            text-decoration: none;
            color: var(--text-2);
            font-size: 13.5px;
            font-weight: 500;
            transition: all 0.15s;
            margin-bottom: 2px;
            position: relative;
        }

        .nav-item:hover {
            color: var(--text-1);
            background: var(--surface);
        }

        .nav-item.active {
            color: var(--accent);
            background: var(--accent-dim);
            font-weight: 600;
        }

        .nav-item.active::before {
            content: '';
            position: absolute;
            left: 0;
            top: 20%;
            bottom: 20%;
            width: 3px;
            background: var(--accent);
            border-radius: 0 3px 3px 0;
        }

        .nav-icon {
            width: 16px;
            height: 16px;
            flex-shrink: 0;
            opacity: 0.5;
        }

        .nav-item.active .nav-icon {
            opacity: 1;
        }

        .nav-item:hover .nav-icon {
            opacity: 0.75;
        }

        .nav-badge {
            margin-left: auto;
            background: var(--accent);
            color: #fff;
            font-size: 10px;
            font-weight: 700;
            padding: 1px 6px;
            border-radius: 100px;
            min-width: 18px;
            text-align: center;
        }

        .nav-badge.amber {
            background: var(--amber);
            color: #fff;
        }

        .nav-badge.red {
            background: var(--red);
            color: #fff;
        }

        .sidebar-bottom {
            margin-top: auto;
            padding: 12px;
            border-top: 1px solid var(--border);
            flex-shrink: 0;
        }

        .user-card {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px;
            border-radius: 8px;
            background: var(--surface);
            border: 1px solid var(--border);
            cursor: pointer;
            transition: border-color 0.2s, box-shadow 0.2s;
        }

        .user-card:hover {
            border-color: var(--border-2);
            box-shadow: var(--shadow-sm);
        }

        .user-avatar {
            width: 32px;
            height: 32px;
            background: linear-gradient(135deg, var(--accent), var(--blue));
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 13px;
            font-weight: 700;
            color: #fff;
            flex-shrink: 0;
        }

        .user-info {
            flex: 1;
            min-width: 0;
        }

        .user-name {
            font-size: 12.5px;
            font-weight: 700;
            color: var(--text-1);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .user-role {
            font-size: 11px;
            color: var(--text-3);
            font-weight: 500;
        }

        .user-chevron {
            width: 14px;
            height: 14px;
            color: var(--text-3);
            flex-shrink: 0;
        }

        /* ═══════════════════════════════════════
           MAIN AREA
        ═══════════════════════════════════════ */
        .main-wrap {
            margin-left: var(--sidebar-w);
            flex: 1;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        /* Topbar */
        .topbar {
            height: var(--topbar-h);
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border-bottom: 1px solid var(--border);
            display: flex;
            align-items: center;
            padding: 0 28px;
            gap: 16px;
            position: sticky;
            top: 0;
            z-index: 40;
            box-shadow: var(--shadow-sm);
        }

        .topbar-title {
            font-size: 15px;
            font-weight: 700;
            letter-spacing: -0.025em;
            color: var(--text-1);
            flex: 1;
        }

        .topbar-title span {
            color: var(--text-3);
            font-weight: 400;
            font-size: 13px;
        }

        .topbar-actions {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .topbar-btn {
            width: 34px;
            height: 34px;
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.15s;
            color: var(--text-2);
            position: relative;
        }

        .topbar-btn:hover {
            background: var(--surface-2);
            border-color: var(--border-2);
            color: var(--text-1);
        }

        .topbar-btn svg {
            width: 15px;
            height: 15px;
        }

        .notif-dot {
            position: absolute;
            top: 6px;
            right: 6px;
            width: 7px;
            height: 7px;
            background: var(--red);
            border-radius: 50%;
            border: 1.5px solid #fff;
        }

        .topbar-date {
            font-family: var(--mono);
            font-size: 11.5px;
            color: var(--text-3);
            padding: 6px 12px;
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 7px;
        }

        /* ── Profile Dropdown ── */
        .profile-dropdown-wrap {
            position: relative;
        }

        .profile-btn {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 5px 10px 5px 5px;
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 10px;
            cursor: pointer;
            transition: all 0.15s;
            user-select: none;
        }

        .profile-btn:hover {
            border-color: var(--border-2);
            box-shadow: var(--shadow-sm);
        }

        .profile-avatar {
            width: 28px;
            height: 28px;
            background: linear-gradient(135deg, var(--accent), var(--blue));
            border-radius: 7px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
            font-weight: 700;
            color: #fff;
            flex-shrink: 0;
        }

        .profile-name {
            font-size: 12.5px;
            font-weight: 600;
            color: var(--text-1);
            max-width: 100px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .profile-chevron {
            width: 13px;
            height: 13px;
            color: var(--text-3);
            transition: transform 0.2s;
        }

        .profile-dropdown-wrap.open .profile-chevron {
            transform: rotate(180deg);
        }

        .profile-menu {
            position: absolute;
            top: calc(100% + 8px);
            right: 0;
            width: 220px;
            background: #fff;
            border: 1px solid var(--border);
            border-radius: 12px;
            box-shadow: var(--shadow-lg);
            overflow: hidden;
            opacity: 0;
            visibility: hidden;
            transform: translateY(-6px);
            transition: all 0.18s cubic-bezier(0.4, 0, 0.2, 1);
            z-index: 100;
        }

        .profile-dropdown-wrap.open .profile-menu {
            opacity: 1;
            visibility: visible;
            transform: translateY(0);
        }

        .profile-menu-header {
            padding: 14px 16px 12px;
            border-bottom: 1px solid var(--border);
        }

        .profile-menu-name {
            font-size: 13px;
            font-weight: 700;
            color: var(--text-1);
        }

        .profile-menu-email {
            font-size: 11.5px;
            color: var(--text-3);
            margin-top: 2px;
        }

        .profile-menu-role {
            display: inline-block;
            font-size: 10px;
            font-weight: 700;
            padding: 2px 7px;
            border-radius: 5px;
            background: var(--accent-dim);
            color: var(--accent);
            margin-top: 6px;
        }

        .profile-menu-body {
            padding: 6px;
        }

        .profile-menu-item {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 9px 10px;
            border-radius: 7px;
            text-decoration: none;
            color: var(--text-2);
            font-size: 13px;
            font-weight: 500;
            transition: all 0.12s;
            cursor: pointer;
            border: none;
            background: none;
            width: 100%;
            font-family: var(--font);
        }

        .profile-menu-item:hover {
            background: var(--surface);
            color: var(--text-1);
        }

        .profile-menu-item svg {
            width: 15px;
            height: 15px;
            flex-shrink: 0;
            opacity: 0.6;
        }

        .profile-menu-item:hover svg {
            opacity: 1;
        }

        .profile-menu-divider {
            height: 1px;
            background: var(--border);
            margin: 4px 6px;
        }

        .profile-menu-item.danger {
            color: var(--red);
        }

        .profile-menu-item.danger:hover {
            background: var(--red-dim);
            color: var(--red);
        }

        .profile-menu-item.danger svg {
            opacity: 0.7;
        }

        /* ═══════════════════════════════════════
           PAGE CONTENT
        ═══════════════════════════════════════ */
        .page {
            padding: 28px;
            flex: 1;
        }

        /* ── Stat cards ── */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 16px;
            margin-bottom: 24px;
        }

        .stat-card {
            background: var(--panel);
            border: 1px solid var(--border);
            border-radius: 14px;
            padding: 20px 20px 18px;
            position: relative;
            overflow: hidden;
            transition: border-color 0.2s, transform 0.2s, box-shadow 0.2s;
            animation: rise 0.4s ease both;
            box-shadow: var(--shadow-sm);
        }

        .stat-card:nth-child(1) {
            animation-delay: 0.05s;
        }

        .stat-card:nth-child(2) {
            animation-delay: 0.10s;
        }

        .stat-card:nth-child(3) {
            animation-delay: 0.15s;
        }

        .stat-card:nth-child(4) {
            animation-delay: 0.20s;
        }

        .stat-card:hover {
            border-color: var(--border-2);
            transform: translateY(-2px);
            box-shadow: var(--shadow-md);
        }

        .stat-card::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            height: 3px;
            border-radius: 0 0 14px 14px;
            opacity: 0;
            transition: opacity 0.2s;
        }

        .stat-card:hover::after {
            opacity: 1;
        }

        .stat-card.c-blue::after {
            background: var(--blue);
        }

        .stat-card.c-green::after {
            background: var(--green);
        }

        .stat-card.c-amber::after {
            background: var(--amber);
        }

        .stat-card.c-purple::after {
            background: var(--accent);
        }

        .stat-header {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            margin-bottom: 14px;
        }

        .stat-icon {
            width: 38px;
            height: 38px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 17px;
        }

        .si-blue {
            background: var(--blue-dim);
            border: 1px solid rgba(59, 130, 246, 0.2);
        }

        .si-green {
            background: var(--green-dim);
            border: 1px solid rgba(22, 163, 74, 0.2);
        }

        .si-amber {
            background: var(--amber-dim);
            border: 1px solid rgba(217, 119, 6, 0.2);
        }

        .si-purple {
            background: var(--accent-dim);
            border: 1px solid rgba(99, 102, 241, 0.2);
        }

        .stat-trend {
            display: flex;
            align-items: center;
            gap: 4px;
            font-size: 11.5px;
            font-weight: 600;
            padding: 3px 8px;
            border-radius: 6px;
        }

        .trend-up {
            color: var(--green);
            background: var(--green-dim);
        }

        .trend-down {
            color: var(--red);
            background: var(--red-dim);
        }

        .stat-num {
            font-size: 28px;
            font-weight: 800;
            letter-spacing: -0.04em;
            color: var(--text-1);
            margin-bottom: 4px;
            font-variant-numeric: tabular-nums;
        }

        .stat-label {
            font-size: 12px;
            color: var(--text-3);
            font-weight: 500;
        }

        .spark {
            display: flex;
            align-items: flex-end;
            gap: 3px;
            height: 28px;
            margin-top: 14px;
        }

        .spark-bar {
            flex: 1;
            border-radius: 3px 3px 0 0;
            opacity: 0.25;
            transition: opacity 0.2s;
            animation: bar-grow 0.6s ease both;
        }

        .stat-card:hover .spark-bar {
            opacity: 0.5;
        }

        .spark-bar.active {
            opacity: 0.8 !important;
        }

        @keyframes bar-grow {
            from {
                transform: scaleY(0);
                transform-origin: bottom;
            }

            to {
                transform: scaleY(1);
                transform-origin: bottom;
            }
        }

        .spark-bar.b-blue {
            background: var(--blue);
        }

        .spark-bar.b-green {
            background: var(--green);
        }

        .spark-bar.b-amber {
            background: var(--amber);
        }

        .spark-bar.b-purple {
            background: var(--accent);
        }

        /* ── Main grid ── */
        .main-grid {
            display: grid;
            grid-template-columns: 1fr 340px;
            gap: 20px;
            margin-bottom: 24px;
        }

        /* ── Card base ── */
        .card {
            background: var(--panel);
            border: 1px solid var(--border);
            border-radius: 14px;
            overflow: hidden;
            animation: rise 0.4s 0.2s ease both;
            box-shadow: var(--shadow-sm);
        }

        .card-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 18px 20px 16px;
            border-bottom: 1px solid var(--border);
        }

        .card-title {
            font-size: 14px;
            font-weight: 700;
            letter-spacing: -0.02em;
            color: var(--text-1);
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .card-title-icon {
            font-size: 16px;
        }

        .card-action {
            font-size: 12px;
            color: var(--accent);
            font-weight: 600;
            text-decoration: none;
            padding: 5px 10px;
            border-radius: 6px;
            transition: background 0.15s;
        }

        .card-action:hover {
            background: var(--accent-dim);
        }

        /* ── Orders table ── */
        .orders-table {
            width: 100%;
            border-collapse: collapse;
        }

        .orders-table th {
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 0.07em;
            text-transform: uppercase;
            color: var(--text-3);
            padding: 10px 20px;
            text-align: left;
            border-bottom: 1px solid var(--border);
            background: var(--surface);
        }

        .orders-table td {
            padding: 13px 20px;
            border-bottom: 1px solid var(--border);
            vertical-align: middle;
        }

        .orders-table tr:last-child td {
            border-bottom: none;
        }

        .orders-table tr {
            transition: background 0.15s;
        }

        .orders-table tbody tr:hover td {
            background: var(--surface);
        }

        .order-id {
            font-family: var(--mono);
            font-size: 12px;
            color: var(--text-3);
            font-weight: 500;
        }

        .order-customer {
            font-size: 13px;
            font-weight: 600;
            color: var(--text-1);
        }

        .order-store {
            font-size: 11.5px;
            color: var(--text-3);
            margin-top: 2px;
        }

        .order-amount {
            font-size: 13.5px;
            font-weight: 700;
            color: var(--text-1);
        }

        .order-type {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            font-size: 11.5px;
            font-weight: 600;
            padding: 3px 9px;
            border-radius: 6px;
        }

        .type-reguler {
            color: var(--blue);
            background: var(--blue-dim);
            border: 1px solid rgba(59, 130, 246, 0.2);
        }

        .type-cargo {
            color: var(--amber);
            background: var(--amber-dim);
            border: 1px solid rgba(217, 119, 6, 0.2);
        }

        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            font-size: 11.5px;
            font-weight: 600;
            padding: 3px 9px;
            border-radius: 6px;
            white-space: nowrap;
        }

        .status-dot-sm {
            width: 5px;
            height: 5px;
            border-radius: 50%;
            flex-shrink: 0;
        }

        .s-confirmed {
            color: var(--blue);
            background: var(--blue-dim);
            border: 1px solid rgba(59, 130, 246, 0.2);
        }

        .s-packing {
            color: var(--amber);
            background: var(--amber-dim);
            border: 1px solid rgba(217, 119, 6, 0.2);
        }

        .s-shipping {
            color: var(--accent);
            background: var(--accent-dim);
            border: 1px solid rgba(99, 102, 241, 0.2);
        }

        .s-arrived {
            color: var(--green);
            background: var(--green-dim);
            border: 1px solid rgba(22, 163, 74, 0.2);
        }

        .s-waiting {
            color: var(--text-3);
            background: var(--surface-2);
            border: 1px solid var(--border);
        }

        .s-confirmed .status-dot-sm {
            background: var(--blue);
        }

        .s-packing .status-dot-sm {
            background: var(--amber);
        }

        .s-shipping .status-dot-sm {
            background: var(--accent);
        }

        .s-arrived .status-dot-sm {
            background: var(--green);
        }

        .s-waiting .status-dot-sm {
            background: var(--text-3);
        }

        /* ── Right column ── */
        .right-col {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        .tracking-card {
            padding: 20px;
        }

        .track-step {
            display: flex;
            align-items: flex-start;
            gap: 12px;
            padding: 10px 0;
            position: relative;
        }

        .track-step:not(:last-child)::after {
            content: '';
            position: absolute;
            left: 15px;
            top: 40px;
            width: 2px;
            height: calc(100% - 20px);
            background: var(--border);
        }

        .track-indicator {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 13px;
            flex-shrink: 0;
            position: relative;
            z-index: 1;
            border: 2px solid var(--border);
            background: var(--surface);
        }

        .track-indicator.done {
            border-color: var(--green);
            background: var(--green-dim);
        }

        .track-indicator.active {
            border-color: var(--accent);
            background: var(--accent-dim);
            box-shadow: 0 0 12px var(--accent-glow);
            animation: pulse-ring 2s ease infinite;
        }

        @keyframes pulse-ring {

            0%,
            100% {
                box-shadow: 0 0 6px var(--accent-glow);
            }

            50% {
                box-shadow: 0 0 16px var(--accent-glow);
            }
        }

        .track-body {
            flex: 1;
            padding-top: 4px;
        }

        .track-title {
            font-size: 13px;
            font-weight: 700;
            color: var(--text-1);
        }

        .track-meta {
            font-size: 11.5px;
            color: var(--text-3);
            margin-top: 2px;
        }

        .track-count {
            font-size: 12px;
            font-weight: 700;
            padding: 2px 8px;
            border-radius: 5px;
            margin-top: 4px;
            display: inline-block;
        }

        .store-list {
            padding: 0 20px 16px;
        }

        .store-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 0;
            border-bottom: 1px solid var(--border);
        }

        .store-item:last-child {
            border-bottom: none;
        }

        .store-name {
            font-size: 13px;
            font-weight: 600;
            color: var(--text-1);
            flex: 1;
        }

        .store-sub {
            font-size: 11.5px;
            color: var(--text-3);
        }

        .store-revenue {
            font-size: 13px;
            font-weight: 700;
            color: var(--text-1);
            text-align: right;
        }

        .store-growth {
            font-size: 11px;
            font-weight: 600;
            text-align: right;
            margin-top: 2px;
        }

        .growth-up {
            color: var(--green);
        }

        .growth-down {
            color: var(--red);
        }

        .store-bar-wrap {
            width: 100%;
            height: 3px;
            background: var(--surface-2);
            border-radius: 2px;
            margin-top: 6px;
        }

        .store-bar-fill {
            height: 100%;
            border-radius: 2px;
            animation: bar-width 0.8s ease both;
        }

        @keyframes bar-width {
            from {
                width: 0 !important;
            }
        }

        /* ── Alert strip ── */
        .alert-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 14px;
            margin-bottom: 24px;
            animation: rise 0.4s 0.3s ease both;
        }

        .alert-item {
            background: var(--panel);
            border: 1px solid var(--border);
            border-radius: 12px;
            padding: 16px 18px;
            display: flex;
            align-items: center;
            gap: 14px;
            transition: border-color 0.2s, transform 0.2s, box-shadow 0.2s;
            box-shadow: var(--shadow-sm);
        }

        .alert-item:hover {
            border-color: var(--border-2);
            transform: translateY(-1px);
            box-shadow: var(--shadow-md);
        }

        .alert-item.alert-warning {
            border-color: rgba(217, 119, 6, 0.2);
        }

        .alert-item.alert-info {
            border-color: rgba(59, 130, 246, 0.2);
        }

        .alert-item.alert-danger {
            border-color: rgba(220, 38, 38, 0.2);
        }

        .si-red {
            background: var(--red-dim);
            border: 1px solid rgba(220, 38, 38, 0.2);
        }

        .alert-icon {
            width: 38px;
            height: 38px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 17px;
            flex-shrink: 0;
        }

        .alert-body {
            flex: 1;
        }

        .alert-num {
            font-size: 22px;
            font-weight: 800;
            letter-spacing: -0.04em;
            color: var(--text-1);
        }

        .alert-desc {
            font-size: 12px;
            color: var(--text-3);
            font-weight: 500;
            margin-top: 1px;
        }

        .alert-action {
            font-size: 11.5px;
            color: var(--text-2);
            text-decoration: none;
            font-weight: 600;
            padding: 5px 10px;
            border-radius: 6px;
            background: var(--surface);
            border: 1px solid var(--border);
            white-space: nowrap;
            transition: all 0.15s;
        }

        .alert-action:hover {
            color: var(--accent);
            border-color: rgba(99, 102, 241, 0.3);
            background: var(--accent-dim);
        }

        /* Welcome strip */
        .welcome-strip {
            background: linear-gradient(135deg, #eef0ff 0%, #e8f0fe 50%, #f0fdf4 100%);
            border: 1px solid rgba(99, 102, 241, 0.15);
            border-radius: 14px;
            padding: 20px 24px;
            display: flex;
            align-items: center;
            gap: 16px;
            margin-bottom: 28px;
            position: relative;
            overflow: hidden;
            animation: rise 0.4s ease both;
            box-shadow: var(--shadow-sm);
        }

        .welcome-strip::before {
            content: '';
            position: absolute;
            right: -40px;
            top: -40px;
            width: 200px;
            height: 200px;
            background: radial-gradient(circle, rgba(99, 102, 241, 0.08) 0%, transparent 65%);
            pointer-events: none;
        }

        .welcome-emoji {
            font-size: 28px;
            flex-shrink: 0;
        }

        .welcome-body {
            flex: 1;
        }

        .welcome-title {
            font-size: 17px;
            font-weight: 800;
            letter-spacing: -0.03em;
            color: var(--text-1);
        }

        .welcome-sub {
            font-size: 13px;
            color: var(--text-2);
            margin-top: 2px;
        }

        .welcome-cta {
            flex-shrink: 0;
            background: var(--accent);
            color: #fff;
            border: none;
            border-radius: 8px;
            font-family: var(--font);
            font-size: 13px;
            font-weight: 600;
            padding: 9px 18px;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 7px;
            text-decoration: none;
            transition: background 0.2s, transform 0.15s, box-shadow 0.2s;
            box-shadow: 0 2px 10px var(--accent-glow);
        }

        .welcome-cta:hover {
            background: #4f51e8;
            transform: translateY(-1px);
            box-shadow: 0 4px 16px var(--accent-glow);
        }

        .welcome-cta svg {
            width: 14px;
            height: 14px;
        }

        @keyframes rise {
            from {
                opacity: 0;
                transform: translateY(14px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        ::-webkit-scrollbar {
            width: 5px;
            height: 5px;
        }

        ::-webkit-scrollbar-track {
            background: transparent;
        }

        ::-webkit-scrollbar-thumb {
            background: var(--surface-3);
            border-radius: 5px;
        }

        @media (max-width: 1200px) {
            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
            }

            .main-grid {
                grid-template-columns: 1fr;
            }

            .right-col {
                display: grid;
                grid-template-columns: 1fr 1fr;
            }
        }

        @media (max-width: 900px) {
            :root {
                --sidebar-w: 0px;
            }

            .sidebar {
                transform: translateX(-100%);
            }

            .sidebar.mobile-open {
                transform: translateX(0);
            }

            .alert-grid {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 640px) {
            .stats-grid {
                grid-template-columns: 1fr 1fr;
            }

            .right-col {
                grid-template-columns: 1fr;
            }

            .page {
                padding: 16px;
            }
        }

        /* ── Toast Notification (Revisi Lengkap) ──────────────────────────────── */
        #toastContainer {
            position: fixed;
            top: 24px;
            /* Jarak dari atas */
            right: 24px;
            /* Jarak dari kanan */
            z-index: 9999;
            display: flex;
            flex-direction: column;
            gap: 12px;
            pointer-events: none;
        }

        .toast {
            pointer-events: auto;
            min-width: 320px;
            max-width: 420px;
            background: #ffffff;
            border-radius: 12px;
            padding: 16px;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 8px 10px -6px rgba(0, 0, 0, 0.1);
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-left: 5px solid #ddd;
            /* Animasi masuk dari kanan */
            animation: slideInRight 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            transition: all 0.3s ease;
        }

        /* Warna sesuai tipe */
        .toast.success {
            border-left-color: #22c55e;
        }

        .toast.error {
            border-left-color: #ef4444;
        }

        .toast.info {
            border-left-color: #3b82f6;
        }

        .toast.warning {
            border-left-color: #f59e0b;
        }

        .toast-content {
            display: flex;
            align-items: flex-start;
            gap: 12px;
            font-size: 14px;
            font-weight: 500;
            color: #1f2937;
            padding-top: 2px;
        }

        .toast-content svg {
            margin-top: 2px;
            flex-shrink: 0;
        }

        /* Tombol Close sesuai kode asli yang Anda inginkan */
        .toast-close {
            background: none;
            border: none;
            color: #9ca3af;
            cursor: pointer;
            padding: 4px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s;
            margin-left: 12px;
        }

        .toast-close:hover {
            color: #4b5563;
            background: #f3f4f6;
            border-radius: 6px;
        }

        @keyframes slideInRight {
            from {
                transform: translateX(120%);
                opacity: 0;
            }

            to {
                transform: translateX(0);
                opacity: 1;
            }
        }

        @keyframes fadeOut {
            from {
                transform: translateX(0);
                opacity: 1;
            }

            to {
                transform: translateX(20px);
                opacity: 0;
            }
        }

        @yield('styles')
    </style>
</head>

<body>

    <div id="toastContainer" class="toast-container"></div>

    <div class="sidebar-overlay" id="sidebarOverlay" onclick="toggleSidebar()"></div>

    {{-- Sidebar --}}
    @include('layouts.sidebar')

    {{-- Main Wrapper --}}
    <div class="main-wrap">

        {{-- Header --}}
        @include('layouts.header')

        {{-- Page Content --}}
        <main class="page">
            @yield('content')
        </main>

    </div>

    <script>
        // Live date/time (if present)
        function updateDate() {
            const display = document.getElementById('dateDisplay');
            if (!display) return;
            const now = new Date();
            const opts = {
                weekday: 'short',
                day: 'numeric',
                month: 'short',
                year: 'numeric'
            };
            display.textContent = now.toLocaleDateString('id-ID', opts);
        }
        updateDate();

        // Profile dropdown toggle
        function toggleProfileDropdown() {
            const wrap = document.getElementById('profileDropdown');
            if (wrap) wrap.classList.toggle('open');
        }

        // Sidebar mobile toggle
        function toggleSidebar() {
            const sidebar = document.querySelector('.sidebar');
            const overlay = document.getElementById('sidebarOverlay');
            if (sidebar) sidebar.classList.toggle('mobile-open');
            if (overlay) overlay.classList.toggle('open');
        }

        // Close dropdowns when clicking outside
        document.addEventListener('click', function(e) {
            const profileWrap = document.getElementById('profileDropdown');
            if (profileWrap && !profileWrap.contains(e.target) && !e.target.closest('.profile-btn')) {
                profileWrap.classList.remove('open');
            }
        });

        // ── Toast Notification (Versi Lengkap & Anti [object Object]) ──────────
        function showToast(message, type = 'success') {
            const container = document.getElementById('toastContainer');
            if (!container) return;

            let finalMessage = message;
            let titleHtml = '';

            // Handle Object (bisa berisi title, message, atau list)
            if (typeof message === 'object' && message !== null) {
                if (message.title) {
                    titleHtml = `<div style="font-weight: 700; margin-bottom: 4px; color: var(--text-1); font-size: 14px;">${message.title}</div>`;
                }

                if (Array.isArray(message.list)) {
                    finalMessage = `<ul style="margin-left: 18px; margin-top: 4px; margin-bottom: 0; padding-left: 0; color: var(--text-2); font-size: 13.5px; line-height: 1.6;">` +
                        message.list.map(item => `<li style="margin-bottom:2px;">${item}</li>`).join('') +
                        `</ul>`;
                } else {
                    finalMessage = `<div style="color: var(--text-2); font-size: 13.5px;">${message.message || message.error || JSON.stringify(message)}</div>`;
                }
                
                if (message.type) type = message.type;
            } else {
                finalMessage = `<div style="color: var(--text-2); font-size: 13.5px;">${message}</div>`;
            }

            const toast = document.createElement('div');
            toast.className = `toast ${type}`;

            const icons = {
                success: '<svg width="22" height="22" fill="#22c55e" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>',
                error: '<svg width="22" height="22" fill="#ef4444" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path></svg>',
                info: '<svg width="22" height="22" fill="#3b82f6" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zm-1 4a1 1 0 00-1 1v3a1 1 0 102 0v-3a1 1 0 00-1-1z" clip-rule="evenodd"></path></svg>',
                warning: '<svg width="22" height="22" fill="#f59e0b" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path></svg>',
            };

            const icon = icons[type] || icons.info;

            toast.innerHTML = `
        <div class="toast-content" style="align-items: flex-start; gap: 12px;">
            <div style="margin-top: 2px;">${icon}</div>
            <div style="flex: 1;">
                ${titleHtml}
                ${finalMessage}
            </div>
        </div>
        <button class="toast-close" onclick="this.parentElement.remove()" style="background:none; border:none; cursor:pointer; color:#999; padding:4px;">
            <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                <path d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </button>
    `;

            container.appendChild(toast);

            // Auto remove setelah 5 detik
            setTimeout(() => {
                if (toast.parentElement) {
                    toast.style.animation = 'fadeOut 0.3s ease forwards';
                    setTimeout(() => toast.remove(), 300);
                }
            }, 5000);
        }
    </script>

    @stack('scripts')

    <!-- 🔥 TRIGGER (LARAVEL) -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Mencegah input type="number" berubah secara tidak sengaja saat halaman discroll dengan mouse
            document.addEventListener("wheel", function() {
                if (document.activeElement.type === "number") {
                    document.activeElement.blur();
                }
            });
            // ✅ SUCCESS BIASA
            @if (session('success'))
                showToast(@json(session('success')), 'success');
            @endif

            // ✅ INFO
            @if (session('info'))
                showToast(@json(session('info')), 'info');
            @endif

            // ✅ SUCCESS PASSWORD
            @if (session('password_success'))
                showToast(@json(session('password_success')), 'success');
            @endif

            // ❌ ERROR SESSION
            @if (session('error'))
                showToast(@json(session('error')), 'error');
            @endif

            // ❌ ERROR PASSWORD (KHUSUS)
            @if ($errors->updatePassword->any())
                @php $pwErrors = $errors->updatePassword->all(); @endphp
                @if (count($pwErrors) > 1)
                    let pwMsg = '<ul style="margin: 0; padding-left: 16px; list-style-type: disc;">';
                    @foreach ($pwErrors as $error)
                        pwMsg += '<li>' + @json($error) + '</li>';
                    @endforeach
                    pwMsg += '</ul>';
                    showToast(pwMsg, 'error');
                @else
                    showToast(@json($pwErrors[0]), 'error');
                @endif
            @endif

            // ❌ ERROR UMUM
            @if ($errors->any())
                @php $genErrors = $errors->all(); @endphp
                @if (count($genErrors) > 1)
                    let genMsg = '<ul style="margin: 0; padding-left: 16px; list-style-type: disc;">';
                    @foreach ($genErrors as $error)
                        genMsg += '<li>' + @json($error) + '</li>';
                    @endforeach
                    genMsg += '</ul>';
                    showToast(genMsg, 'error');
                @else
                    showToast(@json($genErrors[0]), 'error');
                @endif
            @endif

        });
    </script>
    <!-- 🔥 LOADING BUTTON -->
    <script>
        document.querySelectorAll('form').forEach(form => {
            form.addEventListener('submit', function() {
                const btn = this.querySelector('button[type="submit"]');
                if (btn) {
                    btn.innerHTML = 'Menyimpan...';
                    btn.disabled = true;
                }
            });
        });
    </script>
</body>

</html>
