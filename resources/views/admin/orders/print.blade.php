<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Struk #{{ $order->id }} - NJAJAN.CO</title>
    <style>
        /* --- CSS UNTUK TAMPILAN DI LAYAR BROWSER --- */
        body {
            background-color: #f3f4f6; /* Abu-abu muda biar struknya kelihatan menonjol */
            margin: 0;
            padding: 20px;
            display: flex;
            justify-content: center;
            font-family: 'Courier New', Courier, monospace; /* Font Kasir WAJIB */
        }

        .struk-container {
            background-color: white;
            width: 80mm; /* Kita set default 80mm biar lega di layar, nanti di print disesuaikan */
            padding: 6mm;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
            position: relative;
        }

        /* --- CSS UNTUK ELEMEN STRUK (Layar & Print) --- */
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .bold { font-weight: bold; }
        
        .header h2 { margin: 0 0 5px 0; font-size: 18px; }
        .header p { margin: 0; font-size: 12px; }
        
        .info-section {
            font-size: 12px;
            margin: 10px 0;
            border-top: 1px dashed #000;
            border-bottom: 1px dashed #000;
            padding: 5px 0;
        }
        
        .flex-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 2px;
        }

        .items-section {
            font-size: 12px;
            margin-bottom: 10px;
        }
        
        .item-row {
            display: flex;
            margin-bottom: 3px;
        }
        
        .item-name { width: 65%; }
        .item-qty { width: 10%; text-align: center; }
        .item-total { width: 25%; text-align: right; }

        .total-section {
            font-size: 14px;
            border-top: 1px dashed #000;
            padding-top: 5px;
        }

        .footer {
            margin-top: 15px;
            font-size: 10px;
            font-style: italic;
            color: #555;
        }

        /* Tombol Tutup (Hanya Layar) */
        .no-print-zone {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 100;
        }
        .btn-close {
            background-color: #ef4444; color: white; border: none;
            padding: 10px 20px; rounded: 5px; cursor: pointer;
            font-family: sans-serif; font-weight: bold;
        }

        /* --- CSS KHUSUS SAAT DICETAK (PROSES PRINT) --- */
        @media print {
            /* 1. JANGAN TAMPILKAN BACKROUND & TOMBOL */
            body {
                background-color: white;
                padding: 0;
                margin: 0;
                display: block; /* Kembalikan ke blok biasa */
            }

            .struk-container {
                width: 100%; /* Gunakan lebar penuh kertas yang diset di dialog print */
                box-shadow: none;
                padding: 3mm; /* Kurangi padding saat print */
            }

            .no-print {
                display: none !important;
            }

            /* 2. MAINKAN UKURAN KERTAS DI BROWSER */
            /* Kita set kertasnya 'auto' agar mengikuti lebar container */
            @page {
                size: auto;   /* Browser akan mencoba menyesuaikan */
                margin: 0mm;  /* Hilangkan margin kertas bawaan browser (header/footer url) */
            }
        }
    </style>
</head>
<body>

    <div class="no-print-zone no-print">
        <button class="btn-close" onclick="window.close()">TUTUP HALAMAN</button>
        <div style="margin-top:10px; font-size:12px; font-family:sans-serif; text-align:center;">
            Tekan <kbd>Ctrl+P</kbd> untuk Cetak
        </div>
    </div>

    <div class="struk-container">
        <div class="header text-center">
            <h2>NJAJAN.CO</h2>
            <p>Jl. Gatot Subroto, Debong Kulon, Kec. Tegal Sel., Kota Tegal, Jawa Tengah 52133</p>
        </div>

        <div class="info-section">
            <div class="flex-row">
                <span>Date</span>
                <span>: {{ $order->created_at->format('d/m/Y H:i') }}</span>
            </div>
            <div class="flex-row">
                <span>Order</span>
                <span>: #{{ $order->id }}</span>
            </div>
            <div class="flex-row">
                <span>Table</span>
                <span class="bold">: {{ $order->table->number ?? '??' }}</span>
            </div>
            <div class="flex-row">
                <span>Cashier</span>
                <span>: {{ Auth::user()->name }}</span>
            </div>
        </div>

        <div class="items-section">
            <div class="flex-row bold" style="border-bottom: 1px solid #eee; padding-bottom:3px; margin-bottom:5px;">
                <span>Menu</span>
                <div style="display:flex; width:35%; justify-content:space-between;">
                    <span>Qty</span>
                    <span>Total</span>
                </div>
            </div>
            
            @foreach($order->details as $item)
            <div class="item-row">
                <span class="item-name">{{ substr($item->menu->name, 0, 20) }}</span>
                <span class="item-qty">{{ $item->qty }}</span>
                <span class="item-total">{{ number_format($item->price * $item->qty, 0, ',', '.') }}</span>
            </div>
            @endforeach
        </div>

        <div class="total-section">
            <div class="flex-row bold">
                <span>GRAND TOTAL</span>
                <span style="font-size: 16px;">Rp {{ number_format($order->total_price, 0, ',', '.') }}</span>
            </div>
        </div>

        <div class="footer text-center">
            <div class="dashed-line" style="border-top: 1px dashed #ccc; margin: 10px 0;"></div>
            <p>Lunas / Paid</p>
            <p>Terima Kasih Atas Kunjungannya!</p>
            <p>#NJAJANKANTINSEDUNIA</p>
        </div>
    </div>

</body>
</html>