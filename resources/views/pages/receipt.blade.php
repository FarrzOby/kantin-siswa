<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Struk — {{ $order->order_number }}</title>
    <style>
        /* Thermal receipt — 80mm width */
        @page {
            size: 80mm auto;
            margin: 0;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Courier New', Courier, monospace;
            font-size: 11px;
            color: #000;
            background: #fff;
            width: 80mm;
            max-width: 80mm;
            padding: 6mm 5mm;
        }

        .center { text-align: center; }
        .right   { text-align: right; }
        .bold    { font-weight: bold; }
        .lg      { font-size: 15px; }
        .xl      { font-size: 18px; }
        .sm      { font-size: 10px; }
        .xs      { font-size: 9px; color: #555; }

        hr.dashed { border: none; border-top: 1px dashed #000; margin: 4px 0; }
        hr.solid  { border: none; border-top: 1px solid #000; margin: 4px 0; }

        .row { display: flex; justify-content: space-between; margin: 2px 0; }
        .row .label { flex: 1; }
        .row .val   { text-align: right; white-space: nowrap; margin-left: 4px; }

        .item-row .name  { margin-bottom: 1px; }
        .item-row .detail{ display: flex; justify-content: space-between; color: #333; }

        .total-row { font-size: 13px; font-weight: bold; }

        .logo-text {
            font-size: 24px; font-weight: bold; letter-spacing: 4px;
            border: 2px solid #000; padding: 4px 12px; display: inline-block;
        }

        .status-box {
            border: 2px solid #000; padding: 3px 8px;
            font-size: 13px; font-weight: bold; letter-spacing: 1px;
        }

        .barcode-placeholder {
            width: 100%; height: 40px; border: 1px solid #ccc;
            display: flex; align-items: center; justify-content: center;
            font-size: 8px; color: #999; margin: 4px 0;
            letter-spacing: 3px;
        }

        /* Print button — screen only */
        @media screen {
            .print-btn {
                position: fixed; bottom: 20px; right: 20px;
                background: #000; color: #fff; border: none;
                padding: 12px 24px; font-size: 14px; cursor: pointer;
                border-radius: 6px; font-family: sans-serif;
            }
            .print-btn:hover { background: #333; }
            body { background: #f0f0f0; width: auto; max-width: none; }
            .receipt-wrapper {
                background: #fff; width: 80mm; margin: 20px auto;
                padding: 6mm 5mm;
                box-shadow: 0 2px 12px rgba(0,0,0,0.15);
            }
        }

        @media print {
            .print-btn { display: none; }
            .receipt-wrapper { padding: 0; }
            body { background: white; }
        }
    </style>
</head>
<body>
    <button class="print-btn" onclick="window.print()">🖨️ Print Struk</button>

    <div class="receipt-wrapper">
        {{-- Header --}}
        <div class="center" style="margin-bottom: 6px;">
            <div class="logo-text">KS</div>
            <div class="bold lg" style="margin-top: 4px;">KANTIN SISWA</div>
            <div class="xs">Jl. Sekolah No. 1, Kota Pendidikan</div>
            <div class="xs">Telp: (021) 000-0000</div>
        </div>

        <hr class="dashed">

        {{-- Order info --}}
        <div style="margin: 4px 0;">
            <div class="row">
                <span class="label">No. Pesanan</span>
                <span class="val bold">{{ $order->order_number }}</span>
            </div>
            <div class="row">
                <span class="label">Tanggal</span>
                <span class="val">{{ $order->created_at->format('d/m/Y H:i') }}</span>
            </div>
            <div class="row">
                <span class="label">Kasir</span>
                <span class="val">{{ $order->kasir?->name ?? 'Sistem' }}</span>
            </div>
            <div class="row">
                <span class="label">Pemesan</span>
                <span class="val">{{ $order->user->name }}</span>
            </div>
            @if($order->user->kelas)
            <div class="row">
                <span class="label">Kelas</span>
                <span class="val">{{ $order->user->kelas }}</span>
            </div>
            @endif
            <div class="row">
                <span class="label">Pembayaran</span>
                <span class="val bold">{{ strtoupper($order->payment_method) }}</span>
            </div>
        </div>

        <hr class="solid">

        {{-- Items --}}
        <div style="margin: 4px 0;">
            @foreach($order->items as $item)
            <div class="item-row" style="margin-bottom: 4px;">
                <div class="name bold">{{ $item->item_name }}</div>
                <div class="detail">
                    <span>{{ $item->quantity }}x @Rp{{ number_format($item->item_price, 0, ',', '.') }}</span>
                    <span>Rp{{ number_format($item->subtotal, 0, ',', '.') }}</span>
                </div>
                @if($item->notes)
                <div class="xs" style="padding-left:8px;">*{{ $item->notes }}</div>
                @endif
            </div>
            @endforeach
        </div>

        <hr class="dashed">

        {{-- Totals --}}
        <div style="margin: 4px 0;">
            <div class="row">
                <span class="label">Subtotal</span>
                <span class="val">Rp{{ number_format($order->subtotal, 0, ',', '.') }}</span>
            </div>
            @if($order->tax > 0)
            <div class="row">
                <span class="label">Pajak</span>
                <span class="val">Rp{{ number_format($order->tax, 0, ',', '.') }}</span>
            </div>
            @endif
        </div>

        <hr class="solid">

        <div class="row total-row" style="margin: 4px 0;">
            <span class="label">TOTAL</span>
            <span class="val">Rp{{ number_format($order->total, 0, ',', '.') }}</span>
        </div>

        @if($order->amount_paid)
        <hr class="dashed">
        <div style="margin: 4px 0;">
            <div class="row">
                <span class="label">Bayar</span>
                <span class="val">Rp{{ number_format($order->amount_paid, 0, ',', '.') }}</span>
            </div>
            <div class="row bold">
                <span class="label">Kembalian</span>
                <span class="val">Rp{{ number_format($order->change_amount, 0, ',', '.') }}</span>
            </div>
        </div>
        @endif

        @if($order->qris_ref)
        <hr class="dashed">
        <div style="margin: 4px 0;">
            <div class="row">
                <span class="label">Ref QRIS</span>
                <span class="val xs">{{ substr($order->qris_ref, 0, 20) }}...</span>
            </div>
        </div>
        @endif

        <hr class="solid">

        {{-- Status --}}
        <div class="center" style="margin: 6px 0;">
            <span class="status-box">✓ LUNAS</span>
        </div>

        {{-- Barcode placeholder --}}
        <div class="barcode-placeholder">
            ||| {{ $order->order_number }} |||
        </div>

        {{-- Footer --}}
        <hr class="dashed">
        <div class="center xs" style="margin-top: 4px; line-height: 1.6;">
            <div>Terima kasih telah berbelanja!</div>
            <div>Selamat makan & semangat belajar 😊</div>
            <div style="margin-top: 4px;">{{ now()->format('d/m/Y H:i:s') }}</div>
        </div>
    </div>

    <script>
        // Auto-trigger print if ?autoprint=1
        const params = new URLSearchParams(window.location.search);
        if (params.get('autoprint') === '1') {
            window.onload = () => setTimeout(() => window.print(), 500);
        }
    </script>
</body>
</html>
