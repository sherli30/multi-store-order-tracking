<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title }} | Sistem Multi-Store</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap');

        body {
            font-family: 'Inter', sans-serif;
            color: #1e293b;
            background: #fff;
            padding: 40px;
            margin: 0;
            line-height: 1.5;
        }

        .no-print-btn {
            position: fixed;
            top: 20px;
            right: 20px;
            background: #4f46e5;
            color: #fff;
            border: none;
            padding: 12px 24px;
            border-radius: 8px;
            font-weight: 700;
            cursor: pointer;
            box-shadow: 0 4px 12px rgba(79, 70, 229, 0.3);
            text-transform: uppercase;
            font-size: 13px;
        }

        .no-print-btn:hover {
            background: #4338ca;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 40px;
            border-bottom: 3px solid #f1f5f9;
            padding-bottom: 20px;
        }

        .company-name {
            font-size: 24px;
            font-weight: 800;
            color: #0f172a;
            margin-bottom: 5px;
        }

        .report-title {
            font-size: 18px;
            font-weight: 700;
            color: #4b5563;
        }

        .period-info {
            font-size: 14px;
            font-weight: 600;
            color: #64748b;
            margin-top: 10px;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
            margin-bottom: 40px;
        }

        .stat-box {
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 20px;
        }

        .stat-label {
            font-size: 11px;
            font-weight: 700;
            color: #94a3b8;
            text-transform: uppercase;
            margin-bottom: 6px;
        }

        .stat-value {
            font-size: 20px;
            font-weight: 800;
            color: #0f172a;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 40px;
        }

        th {
            text-align: left;
            background: #f8fafc;
            padding: 12px 16px;
            font-size: 11px;
            font-weight: 800;
            text-transform: uppercase;
            border-bottom: 2px solid #e2e8f0;
        }

        td {
            padding: 16px;
            border-bottom: 1px solid #f1f5f9;
            font-size: 13px;
        }

        .totals-row {
            background: #f8fafc;
            font-weight: 800;
        }

        .footer {
            margin-top: 40px;
            text-align: center;
            font-size: 12px;
            color: #94a3b8;
            border-top: 1px solid #f1f5f9;
            padding-top: 20px;
        }

        @media print {
            body {
                padding: 0;
            }

            .no-print-btn {
                display: none !important;
            }

            .header {
                border-bottom-color: #000;
            }
        }
    </style>
</head>

<body>

    <button onclick="window.print()" class="no-print-btn">🖨️ Cetak / Simpan PDF Laporan</button>

    <div class="header">
        <div>
            <div class="company-name">Sistem Penjualan Multi-Store</div>
            <div class="report-title">{{ $title }}</div>
            <div class="period-info">Periode: {{ date('d M Y', strtotime($startDate)) }} s/d {{ date('d M Y', strtotime($endDate)) }}</div>
        </div>
        <div style="text-align:right;">
            <div style="font-size:12px; font-weight:700; color:#64748b;">Dokumen Resmi Dashboard Admin</div>
            <div style="font-size:12px; color:#94a3b8; margin-top:4px;">Dicetak: {{ now()->translatedFormat('d F Y H:i:s') }}</div>
        </div>
    </div>

    @if($type === 'consolidated')
    <div class="stats-grid">
        <div class="stat-box">
            <div class="stat-label">Total Pesanan</div>
            <div class="stat-value">{{ number_format($data->sum('count')) }} TRX</div>
        </div>
        <div class="stat-box">
            <div class="stat-label">Total Pendapatan</div>
            <div class="stat-value">Rp {{ number_format($data->sum('revenue'), 0, ',', '.') }}</div>
        </div>
        <div class="stat-box">
            <div class="stat-label">Jumlah Toko</div>
            <div class="stat-value">{{ $data->count() }} Toko</div>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>Nama Toko</th>
                <th style="text-align:right;">Total Pesanan</th>
                <th style="text-align:right;">Total Pendapatan (Rp)</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data as $row)
            <tr>
                <td style="font-weight:700;">{{ $row['name'] }}</td>
                <td style="text-align:right;">{{ number_format($row['count']) }}</td>
                <td style="text-align:right;">{{ number_format($row['revenue'], 0, ',', '.') }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr class="totals-row">
                <td>TOTAL KESELURUHAN</td>
                <td style="text-align:right;">{{ number_format($data->sum('count')) }}</td>
                <td style="text-align:right;">Rp {{ number_format($data->sum('revenue'), 0, ',', '.') }}</td>
            </tr>
        </tfoot>
    </table>
    @else
    <div class="stats-grid">
        <div class="stat-box">
            <div class="stat-label">Nama Toko</div>
            <div class="stat-value">{{ $data['store']->name }}</div>
        </div>
        <div class="stat-box">
            <div class="stat-label">Total Pendapatan</div>
            <div class="stat-value">Rp {{ number_format($data['revenue'], 0, ',', '.') }}</div>
        </div>
        <div class="stat-box">
            <div class="stat-label">Jumlah Data Pesanan</div>
            <div class="stat-value">{{ count($data['orders']) }} Pesanan</div>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>No. Order</th>
                <th>Pelanggan</th>
                <th>Status</th>
                <th style="text-align:right;">Total (Rp)</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data['orders'] as $order)
            <tr>
                <td style="font-family:monospace; font-weight:700;">{{ $order->order_number }}</td>
                <td>{{ $order->customer_name }}</td>
                <td style="text-transform:uppercase; font-size:11px; font-weight:700;">
                    {{ \App\Services\StatusService::getOrderLabel($order->status ?? '') }}
                </td>
                <td style="text-align:right;">{{ number_format($order->total_amount, 0, ',', '.') }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr class="totals-row">
                <td colspan="3">TOTAL PENDAPATAN CABANG</td>
                <td style="text-align:right;">Rp {{ number_format($data['revenue'], 0, ',', '.') }}</td>
            </tr>
        </tfoot>
    </table>
    @endif

    <div class="footer">
        Laporan ini digenerasi secara otomatis oleh sistem pusat pemantauan multi-store.<br>
        &copy; {{ date('Y') }} Dashboard Admin - Sipesan Logistik
    </div>

</body>

</html>
