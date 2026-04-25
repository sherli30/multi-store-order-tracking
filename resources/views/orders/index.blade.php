@extends('layouts.app')

@section('title', 'Manajemen Pesanan')

@section('styles')

/* ── Page Header ── */
.page-header {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    margin-bottom: 28px;
    gap: 16px;
}
.page-header-left h1 {
    font-size: 18px;
    font-weight: 800;
    letter-spacing: -0.025em;
    color: var(--text-1);
    margin-bottom: 4px;
    display: flex;
    align-items: center;
    gap: 8px;
}
.page-header-left p {
    font-size: 13px;
    color: var(--text-3);
    line-height: 1.5;
}

/* ── Status Tabs ── */
.tabs-wrap {
    display: flex;
    gap: 2px;
    border-bottom: 1px solid var(--border);
    margin-bottom: 20px;
    overflow-x: auto;
    scrollbar-width: none;
}
.tabs-wrap::-webkit-scrollbar { display: none; }

.tab-btn {
    padding: 10px 16px;
    font-size: 13px;
    font-weight: 600;
    color: var(--text-3);
    border-bottom: 2px solid transparent;
    cursor: pointer;
    transition: all 0.2s;
    text-decoration: none;
    white-space: nowrap;
    display: inline-flex;
    align-items: center;
    gap: 7px;
    border-radius: 6px 6px 0 0;
}
.tab-btn:hover { color: var(--text-1); background: var(--surface); }
.tab-btn.active { color: var(--accent); border-bottom-color: var(--accent); background: transparent; }

.tab-count {
    font-size: 10.5px;
    font-weight: 700;
    padding: 1px 6px;
    border-radius: 10px;
    background: var(--surface-2);
    color: var(--text-3);
    min-width: 18px;
    text-align: center;
}
.tab-btn.active .tab-count { background: var(--accent-dim); color: var(--accent); }

/* ── Filter Card ─────────────────────────────── */
.filter-card {
    background: var(--panel);
    border: 1px solid var(--border);
    border-radius: 14px;
    padding: 20px 22px;
    box-shadow: var(--shadow-sm);
    margin-bottom: 20px;
}
.filter-card-top { display: flex; align-items: center; gap: 8px; margin-bottom: 16px; }
.filter-card-title { font-size: 13px; font-weight: 700; color: var(--text-1); }
.filter-grid {
    display: grid;
    grid-template-columns: repeat(6, 1fr);
    gap: 15px;
    align-items: flex-end;
}
@media (max-width: 1200px) {
    .filter-grid { grid-template-columns: repeat(3, 1fr); }
}
@media (max-width: 700px) {
    .filter-grid { grid-template-columns: repeat(2, 1fr); }
}
@media (max-width: 480px) {
    .filter-grid { grid-template-columns: 1fr; }
}

.form-group {
    display: flex;
    flex-direction: column;
    min-width: 0;
}
.form-label { display: block; font-size: 11.5px; font-weight: 700; color: var(--text-3); margin-bottom: 6px; text-transform: uppercase; letter-spacing: 0.05em; }
.form-input {
    width: 100%; padding: 9px 13px; border: 1px solid var(--border); border-radius: 9px;
    font-family: var(--font); font-size: 13px; color: var(--text-1); background: var(--surface);
    outline: none; transition: border-color 0.15s, box-shadow 0.15s; box-sizing: border-box;
}
.form-input:focus { border-color: var(--accent); box-shadow: 0 0 0 3px var(--accent-glow); }
.form-input::placeholder { color: var(--text-4); }

/* ── Filter Actions ── */
.filter-actions {
    display: flex;
    justify-content: flex-end;
    align-items: center;
    gap: 12px;
    margin-top: 16px;
    padding-top: 16px;
    border-top: 1px solid var(--border);
    min-height: 45px;
}
@media (max-width: 600px) {
    .filter-actions {
        flex-direction: column-reverse;
        align-items: stretch;
        gap: 10px;
        min-height: auto;
    }
    .filter-actions .btn-outline-reset {
        justify-content: center;
        width: 100%;
        padding: 11px;
    }
}
.btn-outline-reset {
    display: inline-flex;
    align-items: center;
    gap: 7px;
    background: var(--red-dim);
    color: var(--red);
    border: 1px solid rgba(220, 38, 38, 0.2);
    padding: 9px 16px;
    border-radius: 9px;
    font-family: var(--font);
    font-weight: 700;
    font-size: 13px;
    cursor: pointer;
    text-decoration: none;
    transition: all 0.15s;
}
.btn-outline-reset:hover {
    border-color: rgba(220, 38, 38, 0.4);
    background: color-mix(in srgb, var(--red-dim) 80%, var(--red));
    color: var(--red);
}
.btn-outline-reset svg { width: 13px; height: 13px; }

/* ── Table ── */
.table-card {
    background: var(--panel);
    border: 1px solid var(--border);
    border-radius: 14px;
    overflow: hidden;
    box-shadow: var(--shadow-sm);
    animation: rise 0.35s ease both;
    position: relative;
    min-height: 220px;
}

.order-table { width: 100%; border-collapse: collapse; }

.order-table th {
    font-size: 10.5px;
    font-weight: 700;
    letter-spacing: 0.08em;
    text-transform: uppercase;
    color: var(--text-3);
    padding: 10px 18px;
    text-align: left;
    border-bottom: 1px solid var(--border);
    background: var(--surface);
    white-space: nowrap;
}

.order-table td {
    padding: 13px 18px;
    border-bottom: 1px solid var(--border);
    vertical-align: middle;
    font-size: 13px;
    color: var(--text-2);
}
.order-table tr:last-child td { border-bottom: none; }
.order-table tbody tr { transition: background 0.12s; }
.order-table tbody tr:hover td { background: color-mix(in srgb, var(--accent) 3%, var(--surface)); }

/* ── Cell content ── */
.order-id { font-size: 13px; font-weight: 700; color: var(--text-1); font-variant-numeric: tabular-nums; }
.order-store { font-size: 11px; color: var(--text-3); margin-top: 2px; }

.customer-name { font-size: 13px; font-weight: 600; color: var(--text-1); }
.customer-sub { font-size: 11.5px; color: var(--text-3); margin-top: 2px; }

.shipping-badge {
    display: inline-block;
    font-size: 10.5px;
    font-weight: 700;
    text-transform: uppercase;
    padding: 3px 8px;
    border-radius: 6px;
    background: var(--surface-2);
    color: var(--text-2);
    letter-spacing: 0.03em;
}
.shipping-reguler { border-left: 3px solid var(--accent); }
.shipping-cargo   { border-left: 3px solid var(--amber); }

/* ── Status badges ── */
.status {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    font-size: 11.5px;
    font-weight: 600;
    padding: 4px 10px;
    border-radius: 20px;
    white-space: nowrap;
}
.status::before {
    content: '';
    display: block;
    width: 6px;
    height: 6px;
    border-radius: 50%;
    flex-shrink: 0;
}
.status.pending    { background: var(--amber-dim); color: var(--amber); border: 1px solid rgba(245,158,11,0.2); }
.status.pending::before    { background: var(--amber); }
.status.processing { background: rgba(59,130,246,0.08); color: #3b82f6; border: 1px solid rgba(59,130,246,0.2); }
.status.processing::before { background: #3b82f6; }
.status.shipping   { background: var(--accent-dim); color: var(--accent); border: 1px solid rgba(99,102,241,0.2); }
.status.shipping::before   { background: var(--accent); animation: pulse-dot 1.5s ease infinite; }
.status.completed  { background: var(--green-dim); color: var(--green); border: 1px solid rgba(22,163,74,0.2); }
.status.completed::before  { background: var(--green); }
.status.cancelled  { background: var(--red-dim); color: var(--red); border: 1px solid rgba(220,38,38,0.2); }
.status.cancelled::before  { background: var(--red); }

@keyframes pulse-dot {
    0%, 100% { opacity: 1; transform: scale(1); }
    50%       { opacity: 0.5; transform: scale(0.75); }
}

.amount-total { font-size: 13px; font-weight: 700; color: var(--text-1); font-variant-numeric: tabular-nums; }

/* ── Action buttons ── */
.action-btn {
    width: 30px;
    height: 30px;
    border-radius: 7px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    border: 1px solid var(--border);
    background: var(--surface);
    color: var(--text-2);
    cursor: pointer;
    transition: all 0.15s;
    text-decoration: none;
}
.action-btn:hover { border-color: var(--accent); color: var(--accent); background: var(--accent-dim); }
.action-btn svg { width: 13px; height: 13px; }

/* ── Pagination ── */
.pagination-wrap {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 12px 18px;
    border-top: 1px solid var(--border);
    background: var(--surface);
    flex-wrap: wrap;
    gap: 10px;
}
.pagination-info {
    font-size: 12.5px;
    color: var(--text-3);
}
.pagination-info strong { color: var(--text-1); font-weight: 700; }

.pagination-links {
    display: flex;
    align-items: center;
    gap: 4px;
}
.pagination-links a,
.pagination-links span {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-width: 30px;
    height: 30px;
    padding: 0 8px;
    border-radius: 7px;
    font-size: 12.5px;
    font-weight: 600;
    border: 1px solid var(--border);
    background: var(--panel);
    color: var(--text-2);
    text-decoration: none;
    transition: all 0.15s;
    cursor: pointer;
}
.pagination-links a:hover { border-color: var(--accent); color: var(--accent); background: var(--accent-dim); }
.pagination-links span.active-page { background: var(--accent); color: #fff; border-color: var(--accent); }
.pagination-links span.disabled { opacity: 0.4; cursor: not-allowed; }

/* ── Empty State ─────────────────────────────── */
.empty-state {
    padding: 72px 20px; text-align: center;
    display: flex; flex-direction: column; align-items: center; justify-content: center;
    width: 100%; max-width: 480px; margin: 0 auto;
}
.empty-icon {
    width: 88px; height: 88px; margin-bottom: 20px; border-radius: 24px;
    background: linear-gradient(135deg, color-mix(in srgb, var(--accent) 10%, transparent), color-mix(in srgb, var(--accent) 4%, transparent));
    display: flex; align-items: center; justify-content: center;
    border: 1px solid color-mix(in srgb, var(--accent) 15%, transparent);
}
.empty-icon svg { width: 38px; height: 38px; color: var(--accent); opacity: 0.9; }
.empty-title { font-size: 18px; font-weight: 800; color: var(--text-1); letter-spacing: -0.02em; margin-bottom: 6px; }
.empty-desc { font-size: 13px; color: var(--text-3); }



/* ── Loader ── */
@keyframes spin { 100% { transform: rotate(360deg); } }
#table-loader {
    display: none;
    position: absolute;
    inset: 0;
    background: rgba(255,255,255,0.65);
    backdrop-filter: blur(2px);
    z-index: 10;
    align-items: center;
    justify-content: center;
    border-radius: 14px;
}
.loader-spinner {
    border: 3px solid var(--border);
    border-top: 3px solid var(--accent);
    border-radius: 50%;
    width: 30px;
    height: 30px;
    animation: spin 0.8s linear infinite;
}

@endsection

@section('content')



{{-- Header --}}
<div class="page-header">
    <div class="page-header-left">
        <h1>
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="color:var(--accent)">
                <line x1="16.5" y1="9.4" x2="7.5" y2="4.21"/>
                <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/>
                <polyline points="3.27 6.96 12 12.01 20.73 6.96"/>
                <line x1="12" y1="22.08" x2="12" y2="12"/>
            </svg>
            Manajemen Pesanan
        </h1>
        <p>Lihat dan proses seluruh pesanan masuk dari semua toko.</p>
    </div>
</div>

{{-- Status Tabs --}}
<div class="tabs-wrap">
    @php
        function appendTab($baseUrl, $tabName) {
            $query = request()->query();
            $query['tab'] = $tabName;
            return $baseUrl . '?' . http_build_query($query);
        }
    @endphp
    <a href="{{ appendTab(url()->current(), 'all') }}" class="tab-btn {{ $tab === 'all' ? 'active' : '' }}">
        Semua
        <span class="tab-count">{{ $tabCounts['all'] ?? 0 }}</span>
    </a>
    <a href="{{ appendTab(url()->current(), 'pending') }}" class="tab-btn {{ $tab === 'pending' ? 'active' : '' }}">
        Menunggu
        <span class="tab-count">{{ $tabCounts['pending'] ?? 0 }}</span>
    </a>
    <a href="{{ appendTab(url()->current(), 'processing') }}" class="tab-btn {{ $tab === 'processing' ? 'active' : '' }}">
        Dikemas
        <span class="tab-count">{{ $tabCounts['processing'] ?? 0 }}</span>
    </a>
    <a href="{{ appendTab(url()->current(), 'shipping') }}" class="tab-btn {{ $tab === 'shipping' ? 'active' : '' }}">
        Dikirim
        <span class="tab-count">{{ $tabCounts['shipping'] ?? 0 }}</span>
    </a>
    <a href="{{ appendTab(url()->current(), 'completed') }}" class="tab-btn {{ $tab === 'completed' ? 'active' : '' }}">
        Selesai
        <span class="tab-count">{{ $tabCounts['completed'] ?? 0 }}</span>
    </a>
    <a href="{{ appendTab(url()->current(), 'cancelled') }}" class="tab-btn {{ $tab === 'cancelled' ? 'active' : '' }}">
        Dibatalkan
        <span class="tab-count">{{ $tabCounts['cancelled'] ?? 0 }}</span>
    </a>
</div>

{{-- Filters --}}
<form method="GET" action="{{ route('orders.index') }}" id="filter-form" onsubmit="event.preventDefault(); fetchOrders();">
    <input type="hidden" name="tab" id="filter-tab" value="{{ $tab }}">

    <div class="filter-card">
        <div class="filter-card-top">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="color:var(--text-3)">
                <polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"/>
            </svg>
            <span class="filter-card-title">Filter & Pencarian</span>
        </div>
        <div class="filter-grid">
            <div class="form-group">
                <label class="form-label">Cari Pesanan</label>
                <input type="text" name="search" id="filter-search" class="form-input" placeholder="ID, Customer..." value="{{ request('search') }}" autocomplete="off">
            </div>
            <div class="form-group">
                <label class="form-label">Toko</label>
                <select name="store_id" id="filter-store" class="form-input">
                    <option value="">Semua Toko</option>
                    @foreach($stores as $s)
                        <option value="{{ $s->id }}" {{ request('store_id') == $s->id ? 'selected' : '' }}>{{ $s->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">Tanggal</label>
                <input type="date" name="date" id="filter-date" class="form-input" value="{{ request('date') }}">
            </div>
            <div class="form-group">
                <label class="form-label">Urutkan</label>
                <select name="sort" id="filter-sort" class="form-input">
                    <option value="created_at"   {{ request('sort','created_at') == 'created_at'   ? 'selected' : '' }}>Tanggal</option>
                    <option value="total_amount" {{ request('sort') == 'total_amount'              ? 'selected' : '' }}>Total</option>
                    <option value="status"       {{ request('sort') == 'status'                    ? 'selected' : '' }}>Status</option>
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">Arah</label>
                <select name="dir" id="filter-dir" class="form-input">
                    <option value="desc" {{ request('dir','desc') == 'desc' ? 'selected' : '' }}>↓ Desc</option>
                    <option value="asc"  {{ request('dir','desc') == 'asc'  ? 'selected' : '' }}>↑ Asc</option>
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">Tampil</label>
                <select name="per_page" id="filter-perpage" class="form-input">
                    <option value="10"  {{ request('per_page', 10) == '10'  ? 'selected' : '' }}>10 baris</option>
                    <option value="25"  {{ request('per_page', 10) == '25'  ? 'selected' : '' }}>25 baris</option>
                    <option value="50"  {{ request('per_page', 10) == '50'  ? 'selected' : '' }}>50 baris</option>
                </select>
            </div>
        </div>

        <div class="filter-actions">
            <button type="button" id="resetFilterBtn" onclick="resetFilters()" class="btn-outline-reset" style="display: {{ (request('search') || request('store_id') || request('date')) ? 'inline-flex' : 'none' }};">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/>
                </svg>
                Reset Filter
            </button>
        </div>
    </div>
</form>

{{-- Table Card --}}
<div class="table-card">
    <div id="table-loader"><div class="loader-spinner"></div></div>
    <div id="table-container">
        @include('orders.partials.table', ['orders' => $orders])
    </div>
</div>

@endsection

@push('scripts')
<script>
    let fetchTimeout = null;

    function fetchOrders(url = null) {
        const loader   = document.getElementById('table-loader');
        const container = document.getElementById('table-container');

        loader.style.display = 'flex';

        const baseUrl = url || '{{ route('orders.index') }}';
        let finalUrl  = url;

        if (!url) {
            const params = new URLSearchParams();
            params.append('tab',      document.getElementById('filter-tab').value);
            params.append('search',   document.getElementById('filter-search').value);
            params.append('store_id', document.getElementById('filter-store').value);
            params.append('date',     document.getElementById('filter-date').value);
            params.append('per_page', document.getElementById('filter-perpage').value);
            params.append('sort',     document.getElementById('filter-sort').value);
            params.append('dir',      document.getElementById('filter-dir').value);
            finalUrl = baseUrl + '?' + params.toString();
            window.history.replaceState(null, '', finalUrl);

            const resetBtn = document.getElementById('resetFilterBtn');
            if(resetBtn) {
                if (params.get('search') || params.get('store_id') || params.get('date')) {
                    resetBtn.style.display = 'inline-flex';
                } else {
                    resetBtn.style.display = 'none';
                }
            }
        }

        fetch(finalUrl, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
            .then(res => res.text())
            .then(html => {
                container.innerHTML = html;
                attachPaginationListeners();
            })
            .finally(() => { loader.style.display = 'none'; });
    }

    function attachPaginationListeners() {
        document.querySelectorAll('.pagination-links a').forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                fetchOrders(this.href);
            });
        });
    }

    function resetFilters() {
        document.getElementById('filter-search').value  = '';
        document.getElementById('filter-store').value   = '';
        document.getElementById('filter-date').value    = '';
        document.getElementById('filter-perpage').value = '10';
        document.getElementById('filter-sort').value    = 'created_at';
        document.getElementById('filter-dir').value     = 'desc';
        fetchOrders();
    }

    function debounceFetch() {
        clearTimeout(fetchTimeout);
        fetchTimeout = setTimeout(fetchOrders, 350);
    }

    document.addEventListener('DOMContentLoaded', () => {
        attachPaginationListeners();
        document.getElementById('filter-search').addEventListener('input', debounceFetch);
        ['filter-store','filter-date','filter-perpage','filter-sort','filter-dir'].forEach(id => {
            document.getElementById(id).addEventListener('change', () => fetchOrders());
        });
    });
</script>
@endpush
