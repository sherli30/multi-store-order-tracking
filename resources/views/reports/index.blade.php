@extends('layouts.app')

@section('title', 'Sales Dashboard')

@section('styles')
.report-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
    gap: 20px;
    margin-bottom: 30px;
}
.kpi-card {
    background: var(--panel);
    border: 1px solid var(--border);
    border-radius: 16px;
    padding: 24px;
    display: flex;
    flex-direction: column;
    box-shadow: var(--shadow-sm);
}
.kpi-label { font-size: 13px; font-weight: 700; color: var(--text-3); text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 8px; }
.kpi-value { font-size: 28px; font-weight: 800; color: var(--text-1); line-height: 1.2; }
.kpi-sub { font-size: 12px; color: var(--text-4); margin-top: 4px; }

.chart-grid {
    display: grid;
    grid-template-columns: 2fr 1fr;
    gap: 24px;
    margin-bottom: 30px;
}
@media (max-width: 1024px) { .chart-grid { grid-template-columns: 1fr; } }

.chart-card {
    background: var(--panel);
    border: 1px solid var(--border);
    border-radius: 16px;
    padding: 24px;
    box-shadow: var(--shadow-sm);
}
.chart-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; }
.chart-title { font-size: 16px; font-weight: 800; color: var(--text-1); }

.top-table { width: 100%; border-collapse: collapse; }
.top-table th { text-align: left; font-size: 11px; font-weight: 700; color: var(--text-4); text-transform: uppercase; padding: 10px 0; border-bottom: 1px solid var(--border); }
.top-table td { padding: 12px 0; border-bottom: 1px solid var(--surface); vertical-align: middle; }
.product-info { display: flex; align-items: center; gap: 10px; }
.product-img { width: 32px; height: 32px; border-radius: 6px; object-fit: cover; background: var(--surface-2); }
@endsection

@section('content')
<div style="display:flex; justify-content:space-between; align-items:flex-end; margin-bottom:24px;">
    <div>
        <h1 style="font-size:24px; font-weight:800; color:var(--text-1); letter-spacing:-0.02em;">Sales Dashboard</h1>
        <p style="color:var(--text-3); font-size:14px;">Ringkasan performa bisnis {{ $days }} hari terakhir</p>
    </div>
    <div style="display:flex; gap:10px;">
        <form action="{{ route('reports.index') }}" method="GET" id="rangeForm">
            <select name="days" onchange="this.form.submit()" style="padding:10px 16px; border:1px solid var(--border); border-radius:10px; background:var(--panel); font-weight:700; color:var(--text-2); outline:none; cursor:pointer;">
                <option value="7" {{ $days == 7 ? 'selected' : '' }}>7 Hari Terakhir</option>
                <option value="30" {{ $days == 30 ? 'selected' : '' }}>30 Hari Terakhir</option>
                <option value="90" {{ $days == 90 ? 'selected' : '' }}>90 Hari Terakhir</option>
            </select>
        </form>
    </div>
</div>

<div class="report-grid">
    <div class="kpi-card">
        <div class="kpi-label">Total Revenue</div>
        <div class="kpi-value" style="color:var(--accent);">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</div>
        <div class="kpi-sub">Pendapatan dari transaksi lunas</div>
    </div>
    <div class="kpi-card">
        <div class="kpi-label">Total Pesanan</div>
        <div class="kpi-value">{{ number_format($totalOrders) }}</div>
        <div class="kpi-sub">Pesanan masuk (exclude cancelled)</div>
    </div>
    <div class="kpi-card">
        <div class="kpi-label">Avg. Order Value</div>
        <div class="kpi-value">Rp {{ number_format($avgOrderValue, 0, ',', '.') }}</div>
        <div class="kpi-sub">Rata-rata belanja per transaksi</div>
    </div>
    <div class="kpi-card">
        <div class="kpi-label">Active Customers</div>
        <div class="kpi-value">{{ number_format($activeCustomers) }}</div>
        <div class="kpi-sub">Pelanggan unik yang berbelanja</div>
    </div>
</div>

<div class="chart-grid">
    <!-- Trend Penjualan -->
    <div class="chart-card">
        <div class="chart-header">
            <div class="chart-title">Tren Pendapatan Harian</div>
        </div>
        <div style="height:350px;">
            <canvas id="salesTrendChart"></canvas>
        </div>
    </div>

    <!-- Payment Methods -->
    <div class="chart-card">
        <div class="chart-header">
            <div class="chart-title">Metode Pembayaran</div>
        </div>
        <div style="height:250px; display:flex; justify-content:center;">
            <canvas id="paymentMethodChart"></canvas>
        </div>
        <div style="margin-top:20px;">
            @foreach($paymentMethods as $pm)
                <div style="display:flex; justify-content:space-between; font-size:13px; margin-bottom:8px; padding-bottom:8px; border-bottom:1px solid var(--surface);">
                    <span style="font-weight:600; color:var(--text-2);">{{ strtoupper($pm->payment_method) }}</span>
                    <span style="font-weight:800; color:var(--text-1);">{{ $pm->count }} trx</span>
                </div>
            @endforeach
        </div>
    </div>
</div>

<div class="chart-grid" style="grid-template-columns: 1fr 1fr;">
    <!-- Top Products -->
    <div class="chart-card">
        <div class="chart-header">
            <div class="chart-title">Top 5 Produk Terlaris</div>
        </div>
        <table class="top-table">
            <thead>
                <tr>
                    <th>Produk</th>
                    <th style="text-align:right;">Terjual</th>
                </tr>
            </thead>
            <tbody>
                @foreach($topProducts as $item)
                <tr>
                    <td>
                        <div class="product-info">
                            <img src="{{ $item->product->image_url }}" alt="" class="product-img">
                            <div>
                                <div style="font-size:14px; font-weight:700; color:var(--text-1);">{{ $item->product->name }}</div>
                                <div style="font-size:12px; color:var(--text-4);">{{ $item->product->store->name }}</div>
                            </div>
                        </div>
                    </td>
                    <td style="text-align:right; font-weight:800; color:var(--accent);">{{ number_format($item->total_qty) }} Unit</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Placeholder / Other Stat -->
    <div class="chart-card" style="display:flex; flex-direction:column; justify-content:center; align-items:center; background:linear-gradient(135deg, var(--accent) 0%, #4338ca 100%); border:none;">
        <div style="width:64px; height:64px; background:rgba(255,255,255,0.2); border-radius:16px; display:flex; align-items:center; justify-content:center; margin-bottom:20px;">
            <svg viewBox="0 0 24 24" width="32" height="32" stroke="white" stroke-width="2.5" fill="none"><path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"></path></svg>
        </div>
        <h3 style="color:white; font-weight:800; font-size:20px; text-align:center;">Laporan Lengkap Tersedia</h3>
        <p style="color:rgba(255,255,255,0.7); font-size:13px; text-align:center; max-width:240px; margin-top:8px;">Gunakan menu sidebar untuk melihat laporan spesifik per toko atau konsolidasi antar cabang.</p>
        <a href="{{ route('reports.consolidated') }}" style="margin-top:24px; padding:12px 24px; background:white; color:var(--accent); border-radius:10px; font-weight:800; text-decoration:none; font-size:14px; box-shadow:0 10px 20px rgba(0,0,0,0.1);">Pusat Konsolidasi</a>
    </div>
</div>

<!-- Chart.js Library -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    // 1. Sales Trend Chart
    const trendCtx = document.getElementById('salesTrendChart').getContext('2d');
    const salesTrendData = @json($salesTrend);
    
    new Chart(trendCtx, {
        type: 'line',
        data: {
            labels: salesTrendData.map(d => {
                const date = new Date(d.date);
                return date.toLocaleDateString('id-ID', { day: 'numeric', month: 'short' });
            }),
            datasets: [{
                label: 'Revenue',
                data: salesTrendData.map(d => d.revenue),
                borderColor: '#4f46e5',
                backgroundColor: 'rgba(79, 70, 229, 0.1)',
                borderWidth: 3,
                fill: true,
                tension: 0.4,
                pointBackgroundColor: '#4f46e5',
                pointRadius: 4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: { color: 'rgba(0,0,0,0.03)' },
                    ticks: {
                        callback: function(value) {
                            if (value >= 1000000) return 'Rp ' + (value/1000000).toFixed(1) + 'M';
                            if (value >= 1000) return 'Rp ' + (value/1000) + 'K';
                            return 'Rp ' + value;
                        }
                    }
                },
                x: {
                    grid: { display: false }
                }
            }
        }
    });

    // 2. Payment Method Chart
    const payCtx = document.getElementById('paymentMethodChart').getContext('2d');
    const paymentData = @json($paymentMethods);
    
    new Chart(payCtx, {
        type: 'doughnut',
        data: {
            labels: paymentData.map(d => d.payment_method.toUpperCase()),
            datasets: [{
                data: paymentData.map(d => d.count),
                backgroundColor: ['#4f46e5', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6'],
                borderWidth: 0,
                hoverOffset: 10
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { position: 'bottom', labels: { usePointStyle: true, padding: 20 } }
            },
            cutout: '70%'
        }
    });
</script>
@endsection
