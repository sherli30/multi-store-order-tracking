<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Resi: {{ $order->order_number }}</title>
    <script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.5/dist/JsBarcode.all.min.js"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap');
        @import url('https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@600;700&display=swap');

        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Inter', sans-serif;
            background: #f1f5f9;
            padding: 24px;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 16px;
            min-height: 100vh;
        }

        /* ── Preview toolbar ── */
        .toolbar {
            display: flex; gap: 10px; align-items: center;
            background: #1e293b; padding: 10px 18px; border-radius: 10px;
            box-shadow: 0 4px 16px rgba(0,0,0,0.2);
        }
        .toolbar-label { font-size: 12px; font-weight: 700; color: #94a3b8; letter-spacing: 0.04em; }
        .btn-print {
            background: #2563eb; color: #fff; border: none;
            padding: 9px 20px; border-radius: 7px; font-family: 'Inter', sans-serif;
            font-weight: 700; cursor: pointer; font-size: 13px;
            display: flex; align-items: center; gap: 7px;
            box-shadow: 0 2px 8px rgba(37,99,235,0.4);
            transition: background 0.15s;
        }
        .btn-print:hover { background: #1d4ed8; }
        .btn-close {
            background: transparent; color: #64748b; border: 1px solid #334155;
            padding: 9px 16px; border-radius: 7px; font-family: 'Inter', sans-serif;
            font-weight: 700; cursor: pointer; font-size: 13px; text-decoration: none;
            display: flex; align-items: center; gap: 7px; transition: all 0.15s;
        }
        .btn-close:hover { border-color: #64748b; color: #cbd5e1; }

        /* ── Label: 100mm × 150mm (thermal standard) ── */
        .label {
            width: 100mm;
            min-height: 150mm;
            background: #ffffff;
            padding: 7mm 7mm 6mm;
            border: 1px solid #cbd5e1;
            border-radius: 4px;
            box-shadow: 0 4px 24px rgba(0,0,0,0.12);
            position: relative;
        }

        /* ── Header ── */
        .lbl-header {
            display: flex; align-items: center; justify-content: space-between;
            padding-bottom: 5mm; margin-bottom: 5mm;
            border-bottom: 2px dashed #0f172a;
        }
        .lbl-store { font-size: 13pt; font-weight: 900; color: #0f172a; letter-spacing: -0.02em; }
        .lbl-type {
            font-size: 7pt; font-weight: 900; color: #fff;
            background: #0f172a; padding: 3px 7px; border-radius: 4px;
            text-transform: uppercase; letter-spacing: 0.08em;
        }

        /* ── Barcode ── */
        .lbl-barcode {
            text-align: center; margin-bottom: 4mm;
        }
        .lbl-barcode svg { max-width: 100%; height: auto; display: block; margin: 0 auto; }
        .lbl-barcode-text {
            font-family: 'JetBrains Mono', monospace;
            font-size: 8.5pt; font-weight: 700;
            color: #0f172a; letter-spacing: 0.1em;
            margin-top: -2px;
        }

        /* ── Party boxes ── */
        .lbl-party {
            border: 1.5px solid #0f172a; border-radius: 5px;
            padding: 5px 7px; margin-bottom: 3mm;
        }
        .lbl-party-title {
            font-size: 6.5pt; font-weight: 800; text-transform: uppercase;
            color: #64748b; margin-bottom: 3px; letter-spacing: 0.06em;
        }
        .lbl-party-name  { font-size: 10pt; font-weight: 900; color: #0f172a; line-height: 1.3; }
        .lbl-party-phone { font-size: 8.5pt; font-weight: 700; color: #0f172a; margin-top: 2px; }
        .lbl-party-addr  { font-size: 8pt; font-weight: 500; color: #334155; margin-top: 3px; line-height: 1.45; }

        /* ── Divider ── */
        .lbl-divider { border: none; border-top: 1px dotted #94a3b8; margin: 3mm 0; }

        /* ── Items ── */
        .lbl-items-title { font-size: 7pt; font-weight: 800; color: #0f172a; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 3px; }
        .lbl-item-row {
            display: flex; justify-content: space-between;
            font-size: 8pt; font-weight: 600; color: #1e293b; margin-bottom: 2px;
        }
        .lbl-item-name { flex: 1; padding-right: 8px; }
        .lbl-summary-row {
            display: flex; justify-content: space-between;
            font-size: 8.5pt; font-weight: 800; color: #0f172a;
            margin-top: 4px; padding-top: 3px; border-top: 1px solid #e2e8f0;
        }

        /* ── Footer ── */
        .lbl-footer {
            margin-top: 4mm; padding-top: 3mm; border-top: 1px solid #e2e8f0;
            font-size: 6.5pt; font-weight: 600; color: #94a3b8; text-align: center;
        }

        /* ── Print rules ── */
        @media print {
            * { -webkit-print-color-adjust: exact !important; print-color-adjust: exact !important; }
            body {
                background: #fff; padding: 0; margin: 0;
                display: block; min-height: unset;
            }
            .toolbar { display: none !important; }
            .label {
                width: 100mm; min-height: 150mm;
                border: none; box-shadow: none; border-radius: 0;
                padding: 5mm 6mm 5mm;
                page-break-after: avoid;
            }
            @page {
                size: 100mm 150mm;
                margin: 0;
            }
        }
    </style>
</head>
<body>

    {{-- Toolbar (hidden on print) --}}
    <div class="toolbar no-print">
        <span class="toolbar-label">PRATINJAU LABEL — 100 × 150mm</span>
        <button onclick="window.print()" class="btn-print">
            <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="6 9 6 2 18 2 18 9"/><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/><rect x="6" y="14" width="12" height="8"/></svg>
            Cetak Resi
        </button>
        <a href="javascript:window.close()" class="btn-close">Tutup</a>
    </div>

    @php
        $barcodeId   = $order->tracking_number ?: $order->order_number;
        $totalWeight = $order->orderItems->sum(fn($i) => ($i->product->weight ?? 0) * $i->quantity);
    @endphp

    <div class="label">

        {{-- Header --}}
        <div class="lbl-header">
            <div class="lbl-store">{{ $order->store->name }}</div>
            <div class="lbl-type">{{ $order->shipping_type }}</div>
        </div>

        {{-- Barcode --}}
        <div class="lbl-barcode">
            <svg id="barcode"></svg>
            <div class="lbl-barcode-text">{{ $barcodeId }}</div>
        </div>

        {{-- Recipient --}}
        <div class="lbl-party">
            <div class="lbl-party-title">Penerima (To)</div>
            <div class="lbl-party-name">{{ $order->customer_name }}</div>
            <div class="lbl-party-phone">{{ $order->customer_phone }}</div>
            <div class="lbl-party-addr">
                {{ $order->shipping_address }}<br>
                {{ implode(', ', array_filter([$order->city, $order->province])) }} {{ $order->postal_code }}
            </div>
        </div>

        {{-- Sender --}}
        <div class="lbl-party">
            <div class="lbl-party-title">Pengirim (From)</div>
            <div class="lbl-party-name">{{ $order->store->name }}</div>
            <div class="lbl-party-phone">{{ $order->store->phone ?? 'Toko Resmi Sipesan' }}</div>
            <div class="lbl-party-addr">
                {{ $order->store->address ?? '-' }}<br>
                {{ $order->store->city->name ?? ($order->store->city ?? '') }}
            </div>
        </div>

        <hr class="lbl-divider">

        {{-- Item list --}}
        <div class="lbl-items-title">Deskripsi Paket</div>
        @foreach($order->orderItems as $idx => $item)
            <div class="lbl-item-row">
                <span class="lbl-item-name">{{ $idx + 1 }}. {{ $item->product->name ?? 'Produk (Dihapus)' }}</span>
                <span>{{ $item->quantity }}×</span>
            </div>
        @endforeach

        <div class="lbl-summary-row">
            <span>Total Berat (Est.)</span>
            <span>{{ number_format($totalWeight, 0, ',', '.') }} g</span>
        </div>
        <div class="lbl-summary-row">
            <span>Total Invoice</span>
            <span>Rp {{ number_format($order->total_amount, 0, ',', '.') }}</span>
        </div>

        {{-- Footer --}}
        <div class="lbl-footer">
            Dicetak {{ now()->translatedFormat('d F Y H:i') }} WIB
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            JsBarcode('#barcode', '{{ $barcodeId }}', {
                format:       'CODE128',
                lineColor:    '#0f172a',
                width:        2,
                height:       52,
                displayValue: false,
                margin:       0,
            });
        });
    </script>
</body>
</html>
