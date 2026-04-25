<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Resi: {{ $order->order_number }}</title>
    <!-- JsBarcode CDN for direct frontend processing without PHP dependencies -->
    <script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.5/dist/JsBarcode.all.min.js"></script>

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap');
        @import url('https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@700&display=swap');

        body {
            font-family: 'Inter', sans-serif;
            background: #f1f5f9;
            margin: 0;
            padding: 20px;
            display: flex;
            justify-content: center;
        }

        .label-container {
            background: #ffffff;
            width: 100mm;      /* Thermal printer standard width */
            min-height: 150mm; /* Thermal printer standard height */
            padding: 8mm;
            box-sizing: border-box;
            border: 1px solid #e2e8f0;
            position: relative;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 2px dashed #0f172a;
            padding-bottom: 6px;
            margin-bottom: 12px;
        }

        .store-info {
            font-size: 14px;
            font-weight: 800;
            letter-spacing: -0.02em;
            color: #0f172a;
        }

        .shipping-type {
            font-size: 10px;
            font-weight: 800;
            background: #0f172a;
            color: #fff;
            padding: 3px 6px;
            border-radius: 4px;
            text-transform: uppercase;
        }

        .barcode-section {
            text-align: center;
            margin-bottom: 12px;
        }

        .barcode-section img {
            max-width: 100%;
            height: auto;
        }

        /* If there's a specific tracking number we render it, otherwise standard order ID */
        .barcode-text {
            font-family: 'JetBrains Mono', monospace;
            font-size: 14px;
            font-weight: 700;
            letter-spacing: 0.1em;
            color: #0f172a;
            margin-top: -5px;
        }

        .party-info {
            margin-bottom: 12px;
            border: 1px solid #0f172a;
            border-radius: 6px;
            padding: 8px;
        }

        .party-title {
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            color: #64748b;
            margin-bottom: 4px;
        }

        .party-name { font-size: 12px; font-weight: 800; color: #0f172a; }
        .party-phone { font-size: 11px; font-weight: 600; color: #0f172a; margin-top: 2px; }
        .party-address { font-size: 11px; font-weight: 500; color: #334155; margin-top: 4px; line-height: 1.4; }

        .item-list {
            margin-top: 15px;
            border-top: 1px dotted #0f172a;
            padding-top: 12px;
        }
        .item-list-title {
            font-size: 11px;
            font-weight: 800;
            margin-bottom: 8px;
            color: #0f172a;
        }
        
        .item-row {
            display: flex;
            justify-content: space-between;
            font-size: 10px;
            font-weight: 600;
            color: #1e293b;
            margin-bottom: 4px;
        }

        .footer {
            margin-top: 15px;
            font-size: 9px;
            font-weight: 600;
            color: #94a3b8;
            text-align: center;
            padding-top: 8px;
            border-top: 1px solid #e2e8f0;
        }

        @media print {
            body { padding: 0; background: #fff; display: block; }
            .label-container { border: none; width: 100mm; height:100%; margin: 0; padding: 5mm; }
            /* Hide print options */
            .no-print { display: none !important; }
        }

        .btn-print {
            position: fixed;
            bottom: 20px;
            right: 20px;
            background: #2563eb;
            color: #fff;
            border: none;
            padding: 12px 24px;
            border-radius: 8px;
            font-family: 'Inter', sans-serif;
            font-weight: 700;
            cursor: pointer;
            box-shadow: 0 4px 12px rgba(37, 99, 235, 0.3);
            font-size: 14px;
        }
        .btn-print:hover { background: #1d4ed8; }
    </style>
</head>
<body>

    <button onclick="window.print()" class="btn-print no-print">🖨️ Cetak Resi Thermal</button>

    <div class="label-container">
        <!-- HEADER -->
        <div class="header">
            <div class="store-info">{{ $order->store->name }}</div>
            <div class="shipping-type">{{ $order->shipping_type }} KARGO</div>
        </div>

        <!-- BARCODE SYSTEM -->
        <div class="barcode-section">
            <!-- If we have tracking_number, we barcode it. Otherwise fallback to order_number -->
            @php
                $barcodeId = $order->tracking_number ?: $order->order_number;
            @endphp
            <svg id="barcode"></svg>
            <div class="barcode-text">{{ $barcodeId }}</div>
        </div>

        <!-- RECIPIENT -->
        <div class="party-info">
            <div class="party-title">Penerima (To):</div>
            <div class="party-name">{{ $order->customer_name }}</div>
            <div class="party-phone">{{ $order->customer_phone }}</div>
            <div class="party-address">{{ $order->shipping_address }}</div>
        </div>

        <!-- SENDER -->
        <div class="party-info">
            <div class="party-title">Pengirim (From):</div>
            <div class="party-name">{{ $order->store->name }}</div>
            <div class="party-phone">Toko Resmi Sipesan</div>
        </div>

        <!-- ITEM LISTING -->
        <div class="item-list">
            <div class="item-list-title">Deskripsi Paket:</div>
            
            @php $totalWeight = 0; @endphp
            @foreach($order->orderItems as $idx => $item)
                @php $totalWeight += ($item->product->weight * $item->quantity); @endphp
                <div class="item-row">
                    <span style="flex:1; padding-right:10px;">{{ $idx + 1 }}. {{ $item->product_name }}</span>
                    <span>{{ $item->quantity }}x</span>
                </div>
            @endforeach
            
            <div class="item-row" style="margin-top:10px; font-weight:800; font-size:11px;">
                <span>Total Berat (Estimasi):</span>
                <span>{{ number_format($totalWeight, 0, ',', '.') }} Gram</span>
            </div>
            <div class="item-row" style="font-weight:800; font-size:11px;">
                <span>Total Biaya (Invoice):</span>
                <span>Rp {{ number_format($order->total_amount, 0, ',', '.') }}</span>
            </div>
        </div>

        <div class="footer">
            Label Dicetak: {{ now()->translatedFormat('d F Y H:i') }} (WIB)
        </div>
    </div>

    <script>
        // Init JsBarcode Library for generating SVG offline flawlessly
        document.addEventListener("DOMContentLoaded", function() {
            var barcodeValue = "{{ $barcodeId }}";
            
            JsBarcode("#barcode", barcodeValue, {
                format: "CODE128",     // CODE128 supports numbers and letters uniformly
                lineColor: "#0f172a",
                width: 2.2,
                height: 60,
                displayValue: false,   // Text rendered manually via CSS below it
                margin: 0
            });
        });
    </script>
</body>
</html>
