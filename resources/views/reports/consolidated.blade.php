@extends('layouts.app')

@section('title', 'Laporan Konsolidasi Penjualan')

@section('styles')
.report-card {
    background: var(--panel);
    border: 1px solid var(--border);
    border-radius: 16px;
    padding: 24px;
    box-shadow: var(--shadow-sm);
    margin-bottom: 24px;
}
.report-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-end;
    margin-bottom: 30px;
    padding-bottom: 20px;
    border-bottom: 1px solid var(--surface);
}
.report-title h1 { font-size: 24px; font-weight: 800; color: var(--text-1); margin-bottom: 4px; }
.report-title p { font-size: 14px; color: var(--text-3); }

.filter-bar {
    background: var(--surface);
    padding: 16px;
    border-radius: 12px;
    border: 1px solid var(--border-2);
    display: flex;
    gap: 16px;
    align-items: flex-end;
    margin-bottom: 30px;
}
.form-label { font-size: 11.5px; font-weight: 700; color: var(--text-4); margin-bottom: 6px; text-transform: uppercase; display: block; }
.form-input { 
    padding: 10px 14px; border: 1px solid var(--border); border-radius: 9px; 
    font-size: 13.5px; color: var(--text-1); background: #fff; outline: none;
}

.table-responsive { overflow-x: auto; margin: 0 -24px; border-top: 1px solid var(--border); }
table { width: 100%; border-collapse: collapse; }
th { background: var(--surface); padding: 16px 24px; text-align: left; font-size: 12px; font-weight: 700; color: var(--text-2); border-bottom: 1px solid var(--border); white-space: nowrap; }
td { padding: 18px 24px; border-bottom: 1px solid var(--surface); vertical-align: middle; font-size: 14px; }
tr:last-child td { border-bottom: none; }
.store-dot { width: 8px; height: 8px; border-radius: 50%; display: inline-block; margin-right: 8px; }

.btn-primary { 
    background: var(--accent); color: #fff; border: none; padding: 10px 20px; border-radius: 9px; 
    font-weight: 700; cursor: pointer; font-size: 13.5px;
}
.btn-outline {
    background: #fff; border: 1px solid var(--border); padding: 10px 20px; border-radius: 9px;
    font-weight: 700; color: var(--text-2); text-decoration: none; font-size: 13.5px;
}

.summary-row { background: var(--surface-2); font-weight: 800; color: var(--text-1); }
.summary-row td { border-top: 1px solid var(--border); }
@endsection

@section('content')
<div class="report-card">
    <div class="report-header">
        <div class="report-title">
            <h1>Laporan Konsolidasi</h1>
            <p>Perbandingan performa penjualan antar seluruh cabang toko</p>
        </div>
        <div style="display:flex; gap:10px;">
            <a href="{{ route('reports.export', ['type' => 'consolidated', 'start_date' => $startDate, 'end_date' => $endDate]) }}" target="_blank" class="btn-outline">
                <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2.5" fill="none" style="margin-right:6px;"><polyline points="6 9 6 2 18 2 18 9"></polyline><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path><rect x="6" y="14" width="12" height="8"></rect></svg>
                PDF / Print Laporan
            </a>
        </div>
    </div>

    <form action="{{ route('reports.consolidated') }}" method="GET" class="filter-bar">
        <div>
            <label class="form-label">Periode Mulai</label>
            <input type="date" name="start_date" class="form-input" value="{{ $startDate }}">
        </div>
        <div>
            <label class="form-label">Periode Selesai</label>
            <input type="date" name="end_date" class="form-input" value="{{ $endDate }}">
        </div>
        <button type="submit" class="btn-primary">Muat Data</button>
    </form>

    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th>Nama Cabang Toko</th>
                    <th style="text-align:right;">Total Pesanan</th>
                    <th style="text-align:right;">Berhasil / Selesai</th>
                    <th style="text-align:right;">Dibatalkan</th>
                    <th style="text-align:right;">Total Pendapatan (Gross)</th>
                </tr>
            </thead>
            <tbody>
                @php $colors = ['#4f46e5', '#10b981', '#f59e0b', '#ef4444']; @endphp
                @foreach($report as $idx => $r)
                <tr>
                    <td style="font-weight:700; color:var(--text-1);">
                        <span class="store-dot" style="background:{{ $colors[$idx % 4] }};"></span>
                        {{ $r['store_name'] }}
                    </td>
                    <td style="text-align:right; font-weight:600;">{{ number_format($r['total_orders']) }}</td>
                    <td style="text-align:right; color:var(--green); font-weight:700;">{{ number_format($r['completed_orders']) }}</td>
                    <td style="text-align:right; color:var(--red); font-weight:700;">{{ number_format($r['cancelled_orders']) }}</td>
                    <td style="text-align:right; font-weight:800; color:var(--text-1);">Rp {{ number_format($r['total_revenue'], 0, ',', '.') }}</td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr class="summary-row">
                    <td>TOTAL KONSOLIDASI</td>
                    <td style="text-align:right;">{{ number_format($totals['orders']) }}</td>
                    <td style="text-align:right;">-</td>
                    <td style="text-align:right;">-</td>
                    <td style="text-align:right; color:var(--accent);">Rp {{ number_format($totals['revenue'], 0, ',', '.') }}</td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>

<div class="report-card" style="background:var(--surface); border-style:dashed;">
    <h4 style="font-size:14px; font-weight:800; color:var(--text-1); margin-bottom:12px;">Catatan Laporan:</h4>
    <ul style="font-size:13px; color:var(--text-3); padding-left:20px; line-height:1.6;">
        <li>Data pesanan mencakup seluruh transaksi yang belum dibatalkan (Pending, Processing, Shipping, Completed).</li>
        <li>**Total Pendapatan (Gross)** dihitung berdasarkan akumulasi `total_amount` dari pesanan yang sah.</li>
        <li>Pastikan semua transaksi telah dikonfirmasi "Paid" di menu Transaksi agar data keuangan akurat 100%.</li>
    </ul>
</div>
@endsection
