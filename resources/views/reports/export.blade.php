<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title }} | Sistem Multi-Store</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap');

        @page {
            size: A4 landscape;
            margin: 12mm 10mm;
        }

        * { box-sizing: border-box; }

        body {
            font-family: 'Inter', sans-serif;
            color: #1e293b;
            background: #fff;
            padding: 40px;
            margin: 0;
            line-height: 1.5;
            max-width: 100%;
            overflow-x: hidden;
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
            width: 100%;
            margin-bottom: 30px;
            border-bottom: 3px solid #f1f5f9;
            padding-bottom: 20px;
        }

        .header::after, .stats-grid::after {
            content: "";
            clear: both;
            display: table;
        }

        .header-left {
            float: left;
            width: 65%;
        }

        .header-right {
            float: right;
            width: 30%;
            text-align: right;
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

        .section-title {
            font-size: 16px;
            font-weight: 800;
            color: #0f172a;
            margin-bottom: 15px;
            margin-top: 35px;
            border-left: 4px solid #4f46e5;
            padding-left: 10px;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .stats-grid {
            width: 100%;
            margin-bottom: 30px;
        }

        .stat-box {
            float: left;
            width: 22%;
            margin-right: 3%;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 15px 20px;
            background: #f8fafc;
            box-sizing: border-box;
        }
        .stat-box:last-child {
            margin-right: 0;
        }

        .stat-label {
            font-size: 10px;
            font-weight: 800;
            color: #64748b;
            text-transform: uppercase;
            margin-bottom: 6px;
            letter-spacing: 0.05em;
        }

        .stat-value {
            font-size: 18px;
            font-weight: 800;
            color: #0f172a;
        }

        table {
            width: 100%;
            max-width: 100%;
            table-layout: fixed;
            border-collapse: collapse;
            margin-bottom: 30px;
            font-size: 11.5px;
        }

        th {
            text-align: left;
            background: #f1f5f9;
            padding: 9px 10px;
            font-size: 9.5px;
            font-weight: 800;
            text-transform: uppercase;
            color: #475569;
            border-bottom: 2px solid #cbd5e1;
            overflow-wrap: break-word;
            word-break: break-word;
        }

        td {
            padding: 9px 10px;
            border-bottom: 1px solid #e2e8f0;
            color: #334155;
            vertical-align: middle;
            overflow-wrap: break-word;
            word-break: break-word;
        }

        table.table-compact {
            font-size: 9.5px;
        }
        table.table-compact th,
        table.table-compact td {
            padding: 6px 7px;
        }

        tr:nth-child(even) td {
            background-color: #fafafa;
        }

        .totals-row td {
            background: #f1f5f9;
            font-weight: 800;
            color: #0f172a;
            border-top: 2px solid #cbd5e1;
            border-bottom: none;
        }

        .text-right {
            text-align: right;
        }

        .footer {
            margin-top: 40px;
            text-align: center;
            font-size: 11px;
            color: #94a3b8;
            border-top: 1px solid #e2e8f0;
            padding-top: 20px;
        }

        .badge {
            display: inline-block;
            padding: 3px 7px;
            border-radius: 12px;
            font-size: 9px;
            font-weight: 700;
            text-transform: uppercase;
            white-space: normal;
            line-height: 1.3;
        }
        .badge-success { background: #dcfce7; color: #16a34a; }
        .badge-warning { background: #fef3c7; color: #d97706; }
        .badge-danger { background: #fee2e2; color: #dc2626; }
        .badge-neutral { background: #f1f5f9; color: #64748b; }

        @media print {
            body { padding: 0; overflow-x: visible; }
            .no-print-btn { display: none !important; }
            .header { border-bottom-color: #000; }
            table { page-break-inside: auto; }
            tr { page-break-inside: avoid; page-break-after: auto; }
            thead { display: table-header-group; }
            tfoot { display: table-footer-group; }
            .section-title { page-break-after: avoid; }
            .stats-grid { page-break-inside: avoid; }
            .stat-box { page-break-inside: avoid; }

            /* Background colors for printing */
            .stat-box { -webkit-print-color-adjust: exact; print-color-adjust: exact; background: #f8fafc !important; }
            th { -webkit-print-color-adjust: exact; print-color-adjust: exact; background: #f1f5f9 !important; }
            .totals-row td { -webkit-print-color-adjust: exact; print-color-adjust: exact; background: #f1f5f9 !important; }
            .badge-success { -webkit-print-color-adjust: exact; print-color-adjust: exact; background: #dcfce7 !important; }
            .badge-warning { -webkit-print-color-adjust: exact; print-color-adjust: exact; background: #fef3c7 !important; }
            .badge-danger { -webkit-print-color-adjust: exact; print-color-adjust: exact; background: #fee2e2 !important; }
        }
    </style>
</head>

<body>

    {{-- Company Header & Report Period --}}
    <div class="header">
        <div class="header-left">
            <div class="company-name">Sistem Penjualan Multi-Store</div>
            <div class="report-title">{{ $title }}</div>
            <div class="period-info">Periode: {{ date('d M Y', strtotime($startDate)) }} s/d {{ date('d M Y', strtotime($endDate)) }}</div>
            <div style="font-size: 12px; color: #64748b; margin-top: 5px;">
                Filter:
                {{ $storeId && $consolidatedReport->first() ? 'Toko: '.$consolidatedReport->first()->store_name : 'Semua Toko' }} |
                {{ request('order_status') ? 'Status Pesanan: '.request('order_status') : 'Semua Status Pesanan' }} |
                {{ request('payment_status') ? 'Status Pembayaran: '.request('payment_status') : 'Semua Status Pembayaran' }}
            </div>
        </div>
        <div class="header-right">
            <div style="font-size:12px; font-weight:700; color:#64748b;">Dokumen Resmi Laporan Penjualan</div>
            <div style="font-size:12px; color:#94a3b8; margin-top:4px;">Dicetak: {{ now()->translatedFormat('d F Y H:i:s') }}</div>
        </div>
    </div>

    {{-- Executive Summary --}}
    <div class="section-title">Ringkasan Eksekutif</div>
    <div class="stats-grid">
        <div class="stat-box">
            <div class="stat-label">Total Transaksi</div>
            <div class="stat-value">{{ number_format($totalTransactions) }} TRX</div>
        </div>
        <div class="stat-box">
            <div class="stat-label">Total Pendapatan (Lunas)</div>
            <div class="stat-value" style="color:#4f46e5;">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</div>
        </div>
        <div class="stat-box">
            <div class="stat-label">Total Produk Terjual</div>
            <div class="stat-value">{{ number_format($totalProductsSold) }} Unit</div>
        </div>
        <div class="stat-box">
            <div class="stat-label">Rata-rata Nilai Pesanan</div>
            <div class="stat-value">Rp {{ number_format($totalTransactions > 0 ? $totalRevenue / $totalTransactions : 0, 0, ',', '.') }}</div>
        </div>
    </div>

    {{-- Store Performance Report --}}
    <div class="section-title">{{ $storeId ? 'Performa Toko Ini' : 'Performa Spesifik Toko' }}</div>
    <table>
        <colgroup>
            <col style="width:22%"><col style="width:13%"><col style="width:13%">
            <col style="width:17%"><col style="width:13%"><col style="width:22%">
        </colgroup>
        <thead>
            <tr>
                <th>Nama Toko</th>
                <th class="text-right">Total Pesanan</th>
                <th class="text-right">Customer Unik</th>
                <th class="text-right">Avg Order Value</th>
                <th class="text-right">Success Rate</th>
                <th class="text-right">Total Pendapatan</th>
            </tr>
        </thead>
        <tbody>
            @foreach($consolidatedReport as $row)
            <tr>
                <td style="font-weight:700;">{{ $row->store_name }}</td>
                <td class="text-right">{{ number_format($row->total_orders) }}</td>
                <td class="text-right">{{ number_format($row->unique_customers) }}</td>
                <td class="text-right">Rp {{ number_format($row->total_orders > 0 ? $row->total_revenue / $row->total_orders : 0, 0, ',', '.') }}</td>
                <td class="text-right" style="color:{{ $row->success_rate >= 80 ? '#16a34a' : ($row->success_rate >= 50 ? '#d97706' : '#dc2626') }};">{{ $row->success_rate }}%</td>
                <td class="text-right" style="font-weight:700; color:#4f46e5;">Rp {{ number_format($row->total_revenue, 0, ',', '.') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    {{-- Consolidated Multi-Store Report --}}
    <div class="section-title">{{ $storeId ? 'Ringkasan Performa Toko' : 'Laporan Konsolidasi & Performa Toko' }}</div>
    <table>
        <colgroup>
            <col style="width:20%"><col style="width:11%"><col style="width:11%"><col style="width:11%">
            <col style="width:11%"><col style="width:16%"><col style="width:20%">
        </colgroup>
        <thead>
            <tr>
                <th>Nama Toko</th>
                <th class="text-right">Total Pesanan</th>
                <th class="text-right">Selesai</th>
                <th class="text-right">Batal/Retur</th>
                <th class="text-right">Produk Terjual</th>
                <th class="text-right">Pendapatan Ongkir</th>
                <th class="text-right">Total Pendapatan (Lunas)</th>
            </tr>
        </thead>
        <tbody>
            @foreach($consolidatedReport as $row)
            <tr>
                <td style="font-weight:700;">{{ $row->store_name }}</td>
                <td class="text-right">{{ number_format($row->total_orders) }}</td>
                <td class="text-right" style="color:#16a34a; font-weight:700;">{{ number_format($row->completed_orders) }}</td>
                <td class="text-right" style="color:#dc2626;">{{ number_format($row->cancelled_orders + $row->refunded_orders) }}</td>
                <td class="text-right">{{ number_format($row->products_sold) }}</td>
                <td class="text-right">Rp {{ number_format($row->shipping_revenue, 0, ',', '.') }}</td>
                <td class="text-right" style="font-weight:800; color:#4f46e5;">Rp {{ number_format($row->total_revenue, 0, ',', '.') }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr class="totals-row">
                <td>TOTAL KESELURUHAN</td>
                <td class="text-right">{{ number_format($consolidatedReport->sum('total_orders')) }}</td>
                <td class="text-right">{{ number_format($consolidatedReport->sum('completed_orders')) }}</td>
                <td class="text-right">{{ number_format($consolidatedReport->sum('cancelled_orders') + $consolidatedReport->sum('refunded_orders')) }}</td>
                <td class="text-right">{{ number_format($consolidatedReport->sum('products_sold')) }}</td>
                <td class="text-right">Rp {{ number_format($consolidatedReport->sum('shipping_revenue'), 0, ',', '.') }}</td>
                <td class="text-right" style="color:#4f46e5;">Rp {{ number_format($consolidatedReport->sum('total_revenue'), 0, ',', '.') }}</td>
            </tr>
        </tfoot>
    </table>

    {{-- Main Sales Transaction Report --}}
    <div class="section-title">Detail Transaksi Penjualan</div>
    <table class="table-compact">
        <colgroup>
            <col style="width:4%"><col style="width:8%"><col style="width:8%"><col style="width:9%">
            <col style="width:8%"><col style="width:12%"><col style="width:4%"><col style="width:7%">
            <col style="width:7%"><col style="width:6%"><col style="width:8%"><col style="width:8%"><col style="width:8%">
        </colgroup>
        <thead>
            <tr>
                <th>No</th>
                <th>No. Invoice</th>
                <th>Tanggal</th>
                <th>Customer</th>
                <th>Toko</th>
                <th>Produk</th>
                <th class="text-right">Qty</th>
                <th class="text-right">Harga</th>
                <th class="text-right">Subtotal</th>
                <th class="text-right">Ongkir</th>
                <th>Pembayaran</th>
                <th>Pesanan</th>
                <th class="text-right">Total</th>
            </tr>
        </thead>
        <tbody>
            @php $exportRowNumber = 0; @endphp
            @forelse($orders as $order)
                @php $exportRowNumber++; @endphp
                @foreach($order->orderItems as $idx => $item)
                <tr>
                    @if($idx === 0)
                        <td rowspan="{{ $order->orderItems->count() }}" style="text-align:right;">{{ $exportRowNumber }}</td>
                    @endif
                    <td style="font-family:monospace; font-weight:700;">{{ $order->invoice->invoice_number ?? $order->invoice->midtrans_order_id ?? '-' }}</td>
                    <td>{{ $order->created_at->format('d/m/y H:i') }}</td>
                    <td style="font-weight:600;">{{ $order->customer_name }}</td>
                    <td>{{ $order->store->name ?? '-' }}</td>
                    <td>{{ $item->product->name ?? 'Produk Dihapus' }}</td>
                    <td class="text-right">{{ $item->quantity }}</td>
                    <td class="text-right">{{ number_format($item->price, 0, ',', '.') }}</td>
                    <td class="text-right">{{ number_format($item->quantity * $item->price, 0, ',', '.') }}</td>
                    @if($idx === 0)
                        <td class="text-right" rowspan="{{ $order->orderItems->count() }}">{{ number_format($order->shipping_cost, 0, ',', '.') }}</td>
                        <td rowspan="{{ $order->orderItems->count() }}">
                            @php
                                $pStatus = $order->payment_status;
                                $pBadge = in_array($pStatus, ['settlement','capture','paid']) ? 'badge-success' : ($pStatus == 'pending' ? 'badge-warning' : 'badge-danger');
                            @endphp
                            <span class="badge {{ $pBadge }}">{{ $pStatus }}</span>
                        </td>
                        <td rowspan="{{ $order->orderItems->count() }}">
                            @php
                                $oStatus = $order->status;
                                $oBadge = $oStatus == 'completed' ? 'badge-success' : (in_array($oStatus, ['cancelled','refunded']) ? 'badge-danger' : 'badge-neutral');
                            @endphp
                            <span class="badge {{ $oBadge }}">{{ $oStatus }}</span>
                        </td>
                        <td class="text-right" rowspan="{{ $order->orderItems->count() }}" style="font-weight:700;">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</td>
                    @endif
                </tr>
                @endforeach
            @empty
            <tr>
                <td colspan="13" style="text-align:center; padding: 30px;">Tidak ada data transaksi.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    {{-- Product Sales Detail Report --}}
    <div class="section-title">Detail Penjualan Produk</div>
    <table>
        <colgroup>
            <col style="width:16%"><col style="width:14%"><col style="width:30%">
            <col style="width:10%"><col style="width:15%"><col style="width:15%">
        </colgroup>
        <thead>
            <tr>
                <th>No. Invoice</th>
                <th>Toko</th>
                <th>Nama Produk</th>
                <th class="text-right">Qty</th>
                <th class="text-right">Harga Satuan</th>
                <th class="text-right">Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @forelse($orderItems as $item)
            <tr>
                <td style="font-family:monospace; font-weight:700;">{{ $item->order->invoice->invoice_number ?? $item->order->invoice->midtrans_order_id ?? '-' }}</td>
                <td>{{ $item->order->store->name ?? '-' }}</td>
                <td style="font-weight:600;">{{ $item->product->name ?? 'Produk Dihapus' }}</td>
                <td class="text-right" style="font-weight:700;">{{ $item->quantity }}</td>
                <td class="text-right">Rp {{ number_format($item->price, 0, ',', '.') }}</td>
                <td class="text-right" style="font-weight:700; color:#4f46e5;">Rp {{ number_format($item->quantity * $item->price, 0, ',', '.') }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="6" style="text-align:center; padding: 30px;">Tidak ada data produk.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    {{-- Footer --}}
    <div class="footer">
        Laporan ini digenerasi secara otomatis oleh sistem Laporan Penjualan Eksekutif.<br>
        &copy; {{ date('Y') }} Sistem Multi-Store Order Tracking
    </div>

</body>
</html>
