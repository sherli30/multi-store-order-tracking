<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resi Pengiriman — {{ $order->order_number }}</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">

    <style>
        /* ─── RESET ─── */
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --bg:        #f1f5f9;
            --topbar:    #ffffff;
            --accent:    #f97316;
            --accent-h:  #ea580c;
            --black:     #000000;
            --muted:     #64748b;
            --border-c:  #000000;
        }

        /* ─── SCREEN CHROME ─── */
        body {
            font-family: 'Inter', sans-serif;
            background: var(--bg);
            color: var(--black);
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            -webkit-font-smoothing: antialiased;
        }

        .topbar {
            background: var(--topbar);
            padding: 14px 28px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 16px rgba(0,0,0,0.06);
            position: sticky;
            top: 0;
            z-index: 100;
            gap: 16px;
        }
        .topbar-left {
            display: flex;
            align-items: center;
            gap: 16px;
            flex-wrap: nowrap;
        }
        .topbar-title {
            font-size: 17px; font-weight: 800; letter-spacing: -0.4px;
            border-left: 2px solid #e2e8f0; padding-left: 16px; color: #0f172a;
        }
        .topbar-meta { font-size: 12px; color: var(--muted); font-weight: 500; }

        .btn-back {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            color: var(--muted);
            text-decoration: none;
            font-weight: 600;
            font-size: 13px;
            padding: 8px 12px;
            border-radius: 8px;
            transition: all 0.15s;
            white-space: nowrap;
            flex-shrink: 0;
            min-width: fit-content;
        }
        .btn-back:hover { background: #f1f5f9; color: #0f172a; }

        .topbar-actions { display: flex; gap: 10px; align-items: center; }

        .btn-print {
            background: var(--accent); color: #fff; border: none;
            padding: 10px 22px; font-family: 'Inter', sans-serif;
            font-size: 13px; font-weight: 700; border-radius: 10px; cursor: pointer;
            box-shadow: 0 6px 18px rgba(249,115,22,0.28);
            display: inline-flex; align-items: center; gap: 8px;
            transition: all 0.2s; white-space: nowrap;
        }
        .btn-print:hover { background: var(--accent-h); transform: translateY(-1px); box-shadow: 0 10px 22px rgba(249,115,22,0.35); }
        .btn-print svg { width: 16px; height: 16px; }

        .workspace {
            flex: 1;
            display: flex;
            align-items: flex-start;
            justify-content: center;
            padding: 36px 20px 60px;
        }

        /* ─── LABEL ─── */
        .label {
            width:  100mm;
            min-height: 150mm;
            height: auto;
            background: #fff;
            border: 2px solid var(--border-c);
            display: flex;
            flex-direction: column;
            box-shadow: 0 20px 50px rgba(0,0,0,0.18);
            font-family: 'Inter', sans-serif;
            color: #000;
            position: relative;
        }

        /* Every direct child row */
        .lrow {
            width: 100%;
            border-bottom: 1.5px solid #000;
            display: flex;
            flex-shrink: 0;
        }
        .lrow:last-child { border-bottom: none; }

        /* ─── Columns ─── */
        .lcol { padding: 7px 9px; }
        .br   { border-right: 1.5px solid #000; }

        /* ─── Typography helpers ─── */
        .t-xs  { font-size: 8.5px; }
        .t-sm  { font-size: 10.5px; }
        .t-md  { font-size: 13px; }
        .t-lg  { font-size: 17px; letter-spacing: -0.4px; }
        .t-xl  { font-size: 26px; letter-spacing: -1px; }
        .fw4   { font-weight: 400; }
        .fw6   { font-weight: 600; }
        .fw7   { font-weight: 700; }
        .fw8   { font-weight: 800; }
        .fw9   { font-weight: 900; }
        .mono  { font-family: 'Inter', monospace; }
        .uc    { text-transform: uppercase; }
        .tc    { text-align: center; }
        .tr    { text-align: right; }
        .lh    { line-height: 1.45; }

        /* ─── Row 1: HEADER — Store | Courier ─── */
        .row-header { min-height: 44px; align-items: stretch; }
        .col-store {
            flex: 1;
            background: #000;
            display: flex; flex-direction: column;
            align-items: center; justify-content: center;
            padding: 8px 10px;
            gap: 2px;
        }
        .col-store .store-name { color: #fff; font-size: 13px; font-weight: 900; text-align: center; text-transform: uppercase; line-height: 1.2; }
        .col-store .store-phone { color: rgba(255,255,255,0.7); font-size: 8px; font-weight: 600; margin-top: 2px; }

        .col-courier {
            flex: 1.3;
            display: flex; flex-direction: column;
            align-items: center; justify-content: center;
            padding: 8px 10px; gap: 3px;
        }
        .courier-name { font-size: 15px; font-weight: 900; text-transform: uppercase; letter-spacing: -0.3px; }
        .service-pill {
            border: 1.5px solid #000; padding: 1.5px 7px;
            border-radius: 4px; font-size: 8px; font-weight: 800;
            text-transform: uppercase; letter-spacing: 0.6px;
        }

        /* ─── Row 2: BARCODE ─── */
        .row-barcode {
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 9px 10px 7px;
        }
        .barcode-img {
            height: 52px; width: 100%; max-width: 240px;
            object-fit: contain; display: block; margin: 0 auto;
        }
        .barcode-num {
            font-size: 14px; font-weight: 800; letter-spacing: 2.5px;
            text-align: center; margin-top: 5px; font-family: 'Inter', monospace;
        }

        /* ─── Row 3: ROUTING CODE ─── */
        .row-routing { padding: 0; }
        .routing-code {
            width: 100%; font-size: 30px; font-weight: 900;
            text-align: center; padding: 5px 0;
            background: #000; color: #fff;
            letter-spacing: 4px;
        }

        /* ─── Row 4: ADDRESSES ─── */
        .row-address { align-items: stretch; }
        .col-addr { display: flex; flex-direction: column; padding: 8px 9px; }
        .addr-label {
            display: inline-block; font-size: 8px; font-weight: 900;
            text-transform: uppercase; letter-spacing: 0.7px;
            padding: 2px 6px; border-radius: 3px; margin-bottom: 5px;
        }
        .addr-label.recv { background: #000; color: #fff; }
        .addr-label.send { background: #e5e7eb; color: #000; }
        .addr-name  { font-size: 12px; font-weight: 900; text-transform: uppercase; margin-bottom: 2px; }
        .addr-phone { font-size: 10px; font-weight: 700; margin-bottom: 4px; }
        .addr-text  { font-size: 9.5px; font-weight: 500; line-height: 1.5; color: #111; }
        .addr-city  { font-size: 9.5px; font-weight: 800; margin-top: 2px; }

        /* ─── Row 5: STATS — weight | shipping cost | payment ─── */
        .row-stats { align-items: stretch; }
        .col-stat { flex: 1; display: flex; flex-direction: column; justify-content: center; padding: 6px 9px; }
        .stat-lbl  { font-size: 7.5px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.5px; color: #555; margin-bottom: 3px; }
        .stat-val  { font-size: 11.5px; font-weight: 900; color: #000; }
        .stat-val.green  { color: #15803d; }
        .stat-val.accent { color: var(--accent); }

        /* Payment status pill */
        .pay-status {
            display: inline-block; padding: 1.5px 6px; border-radius: 3px;
            font-size: 8px; font-weight: 900; text-transform: uppercase; letter-spacing: 0.5px;
        }
        .pay-status.paid     { background: #dcfce7; color: #15803d; border: 1px solid #bbf7d0; }
        .pay-status.pending  { background: #fef9c3; color: #a16207; border: 1px solid #fde68a; }
        .pay-status.cod      { background: #fee2e2; color: #b91c1c; border: 1px solid #fca5a5; }

        /* ─── Row 6: COD amount (conditional) ─── */
        .row-cod { padding: 6px 9px; align-items: center; gap: 8px; background: #fff9f5; }
        .cod-label { font-size: 9px; font-weight: 900; text-transform: uppercase; color: #b45309; letter-spacing: 0.5px; flex-shrink: 0; }
        .cod-amount { font-size: 18px; font-weight: 900; color: #b91c1c; letter-spacing: -0.5px; }
        .cod-note   { font-size: 7.5px; color: #92400e; font-weight: 600; margin-left: auto; text-align: right; }

        /* ─── Row 7: ITEMS ─── */
        .row-items { flex-direction: column; padding: 7px 9px; }
        .items-heading { font-size: 8px; font-weight: 900; text-transform: uppercase; letter-spacing: 0.6px; margin-bottom: 5px; }
        .items-table { width: 100%; border-collapse: collapse; }
        .items-table td { font-size: 10px; padding: 2.5px 0; vertical-align: top; border-bottom: 1px dashed #d1d5db; }
        .items-table tr:last-child td { border-bottom: none; }
        .td-qty  { width: 28px; font-weight: 900; white-space: nowrap; }
        .td-name { font-weight: 700; padding-right: 4px; }
        .td-price{ text-align: right; white-space: nowrap; font-weight: 600; color: #444; width: 1%; }
        .order-total-line { display: flex; justify-content: space-between; align-items: center; border-top: 1.5px solid #000; margin-top: 5px; padding-top: 5px; }
        .total-label { font-size: 8.5px; font-weight: 800; text-transform: uppercase; }
        .total-value { font-size: 13px; font-weight: 900; }

        /* ─── Notes ─── */
        .notes-block { margin-top: 6px; border: 1px solid #d1d5db; border-radius: 3px; padding: 5px 7px; font-size: 9px; line-height: 1.4; color: #333; }
        .notes-block strong { font-weight: 800; }

        /* ─── Row 8: FOOTER ─── */
        .row-footer {
            padding: 5px 9px;
            display: flex; justify-content: space-between; align-items: center;
            border-top: 1.5px solid #000;
            margin-top: auto;
        }
        .footer-item { font-size: 8px; font-weight: 700; color: #333; }
        .footer-item span { color: #000; font-weight: 900; }

        /* ─── PRINT MEDIA QUERY ─── */
        @page {
            size: 4in 6in;
            margin: 0;
        }

        @media print {

            html,
            body {
                width: 100mm;
                margin: 0 !important;
                padding: 0 !important;
                background: #fff !important;
            }

            .topbar {
                display: none !important;
            }

            .workspace {
                width: 100mm;
                padding: 0 !important;
                margin: 0 auto !important;
                display: flex !important;
                justify-content: center !important;
                align-items: flex-start !important;
            }

            .label {
                width: 100mm !important;
                min-height: 150mm !important;
                height: auto !important;
                border: 2px solid #000 !important;
                border-radius: 0 !important;
                box-shadow: none !important;
                overflow: visible !important;
                page-break-inside: avoid;
                page-break-after: avoid;
            }

            .label::after {
                display: none !important;
            }

            .lrow,
            .br,
            .row-footer {
                border-color: #000 !important;
            }

            .col-store {
                background: #000 !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            .routing-code {
                background: #000 !important;
                color: #fff !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            .addr-label.recv {
                background: #000 !important;
                color: #fff !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            .pay-status {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            .row-cod {
                background: #fff9f5 !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
        }
    </style>
</head>
<body>

<!-- ═══ TOPBAR (screen only) ═══ -->
<div class="topbar">
    <div class="topbar-left">
        <a href="{{ route('orders.show', $order) }}" class="btn-back">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
            Kembali ke Detail
        </a>
        <div class="topbar-title">Preview Resi Pengiriman</div>
        <div class="topbar-meta">{{ $order->order_number }} &bull; {{ $order->created_at->format('d M Y') }}</div>
    </div>
    <div class="topbar-actions">
        <button class="btn-print" onclick="window.print()">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                <polyline points="6 9 6 2 18 2 18 9"/><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/><rect x="6" y="14" width="12" height="8"/>
            </svg>
            Cetak Label
        </button>
    </div>
</div>

<!-- ═══ WORKSPACE ═══ -->
<div class="workspace">
    @php
        /* ── Computed values ── */
        $barcodeText  = $order->tracking_number ?? $order->order_number;
        $storeName    = $order->store->name ?? config('app.name', 'TOKO PUSAT');
        $storePhone   = $order->store->phone ?? null;
        $storeAddress = $order->store->address ?? '-';
        $storeCity    = $order->store->city->name ?? ($order->store->city ?? null);

        $courierName  = $order->shipping_courier ?? 'KURIR';
        $serviceType  = $order->shipping_type ?? 'REGULER';

        $totalWeight  = $order->orderItems->sum(fn($i) => ($i->product->weight ?? 0) * $i->quantity);
        $totalWeight  = $order->total_weight ?? $totalWeight;

        $isCod        = strtolower($order->payment_type ?? '') === 'cod';
        $payStatus    = $order->payment_status ?? 'pending';
        $isLunas      = in_array($payStatus, ['settlement', 'capture', 'paid']);

        $city     = $order->city     ?? null;
        $province = $order->province ?? null;
        $postal   = $order->postal_code ?? null;

        /* Deterministic routing abbreviation — no rand() */
        $routing  = strtoupper(substr(preg_replace('/[^A-Za-z]/', '', $city ?? 'XXX'), 0, 3));
        $routeSfx = str_pad(($order->id % 100), 2, '0', STR_PAD_LEFT);

        $subtotal     = $order->orderItems->sum(fn($i) => $i->price * $i->quantity);
        $shippingCost = $order->shipping_cost ?? 0;
        $totalAmount  = $order->total_amount ?? ($subtotal + $shippingCost);
    @endphp

    <!-- ══════════════════════════════════════════════
         LABEL THERMAL  100mm × 150mm
    ══════════════════════════════════════════════ -->
    <div class="label">

        <!-- ── Row 1: Store / Courier ── -->
        <div class="lrow row-header">
            <div class="col-store br">
                <div class="store-name">{{ $storeName }}</div>
                @if($storePhone)
                    <div class="store-phone">{{ $storePhone }}</div>
                @endif
            </div>
            <div class="col-courier lcol">
                <div class="courier-name">{{ strtoupper($courierName) }}</div>
                <div class="service-pill">{{ strtoupper($serviceType) }}</div>
            </div>
        </div>

        <!-- ── Row 2: Barcode ── -->
        <div class="lrow row-barcode">
            <img
                class="barcode-img"
                src="https://bwipjs-api.metafloor.com/?bcid=code128&text={{ urlencode($barcodeText) }}&scale=3&height=10&includetext=false"
                alt="Barcode {{ $barcodeText }}"
            >
            <div class="barcode-num">{{ $barcodeText }}</div>
        </div>

        <!-- ── Row 3: Routing Code ── -->
        <div class="lrow row-routing">
            <div class="routing-code">{{ $routing }}-{{ $routeSfx }}</div>
        </div>

        <!-- ── Row 4: Addresses ── -->
        <div class="lrow row-address">
            <!-- Recipient -->
            <div class="col-addr br" style="flex: 1.45;">
                <span class="addr-label recv">PENERIMA</span>
                <div class="addr-name">{{ $order->customer_name }}</div>
                <div class="addr-phone">{{ $order->customer_phone }}</div>
                <div class="addr-text lh">
                    {{ $order->shipping_address }}
                </div>
                @if($city || $province)
                    <div class="addr-city">{{ implode(', ', array_filter([$city, $province])) }}{{ $postal ? ' ' . $postal : '' }}</div>
                @endif
            </div>
            <!-- Sender -->
            <div class="col-addr" style="flex: 1;">
                <span class="addr-label send">PENGIRIM</span>
                <div class="addr-name" style="font-size:10.5px;">{{ $storeName }}</div>
                @if($storePhone)
                    <div class="addr-phone">{{ $storePhone }}</div>
                @endif
                <div class="addr-text lh">
                    {{ $storeAddress }}
                    @if($storeCity)
                        <div class="addr-city">{{ $storeCity }}</div>
                    @endif
                </div>
            </div>
        </div>

        <!-- ── Row 5: Stats (weight | shipping cost | payment) ── -->
        <div class="lrow row-stats">
            <div class="col-stat br">
                <div class="stat-lbl">Berat</div>
                <div class="stat-val">
                    @if($totalWeight >= 1000)
                        {{ number_format($totalWeight / 1000, 2, ',', '.') }} kg
                    @else
                        {{ number_format($totalWeight, 0, ',', '.') }} g
                    @endif
                </div>
            </div>
            <div class="col-stat br">
                <div class="stat-lbl">Ongkir</div>
                <div class="stat-val">
                    @if($shippingCost > 0)
                        Rp {{ number_format($shippingCost, 0, ',', '.') }}
                    @else
                        —
                    @endif
                </div>
            </div>
            <div class="col-stat">
                <div class="stat-lbl">Pembayaran</div>
                @if($isCod)
                    <div class="stat-val"><span class="pay-status cod">COD</span></div>
                @elseif($isLunas)
                    <div class="stat-val"><span class="pay-status paid">LUNAS</span></div>
                @else
                    <div class="stat-val"><span class="pay-status pending">MENUNGGU</span></div>
                @endif
            </div>
        </div>

        @if($isCod)
        <!-- ── Row 6: COD Amount (only for COD orders) ── -->
        <div class="lrow row-cod">
            <div class="cod-label">⚠ Tagih COD</div>
            <div class="cod-amount">Rp {{ number_format($totalAmount, 0, ',', '.') }}</div>
            <div class="cod-note">Kurir wajib<br>menagih pelanggan</div>
        </div>
        @endif

        <!-- ── Row 7: Order Items ── -->
        <div class="lrow row-items">
            <div class="items-heading">Isi Paket</div>
            <table class="items-table">
                @foreach($order->orderItems as $item)
                    <tr>
                        <td class="td-qty">{{ $item->quantity }}×</td>
                        <td class="td-name">{{ $item->product->name ?? 'Produk (Dihapus)' }}</td>
                        <td class="td-price">Rp {{ number_format($item->price * $item->quantity, 0, ',', '.') }}</td>
                    </tr>
                @endforeach
            </table>

            <div class="order-total-line">
                <span class="total-label">Total Pesanan</span>
                <span class="total-value">Rp {{ number_format($totalAmount, 0, ',', '.') }}</span>
            </div>

            @if($order->notes)
                <div class="notes-block">
                    <strong>Catatan:</strong> {{ $order->notes }}
                </div>
            @endif
        </div>

        <!-- ── Row 8: Footer ── -->
        <div class="row-footer">
            <div class="footer-item">ORDER: <span>{{ $order->order_number }}</span></div>
            @if($order->midtrans_order_id && $order->midtrans_order_id !== $order->order_number)
                <div class="footer-item">TRX: <span style="font-size:7.5px;">{{ $order->midtrans_order_id }}</span></div>
            @endif
            <div class="footer-item">CETAK: <span>{{ now()->format('d/m/Y H:i') }}</span></div>
        </div>

    </div>
    <!-- ══ END LABEL ══ -->

</div>

</body>
</html>
