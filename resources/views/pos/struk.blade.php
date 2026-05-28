<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Struk - {{ $order->invoice_number }}</title>
    <style>
        /* Ukuran standar printer kasir thermal 58mm */
        @page { margin: 0; }
        body { font-family: monospace; width: 50mm; margin: 0 auto; padding: 10px; font-size: 12px; color: #000; }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .flex-between { display: flex; justify-content: space-between; }
        hr { border-top: 1px dashed #000; border-bottom: none; margin: 10px 0; }
        .fw-bold { font-weight: bold; }
        @media print { .no-print { display: none; } }
    </style>
</head>
<body onload="window.print()">
    <div class="text-center">
        <h3 style="margin-bottom: 2px;">GEPREK LEGEND</h3>
        <p style="margin-top: 0; font-size: 10px;">Jl. Raya Kuliner No. 1</p>
    </div>
    <hr>
    <div>
        <p style="margin: 2px 0;">INV  : {{ $order->invoice_number }}</p>
        <p style="margin: 2px 0;">TGL  : {{ $order->created_at->format('d/m/Y H:i') }}</p>
        <p style="margin: 2px 0;">KASIR: {{ $order->user->name }}</p>
    </div>
    <hr>
    
    <table style="width: 100%; font-size: 12px; border-collapse: collapse;">
        @foreach($order->orderDetails as $detail)
        <tr>
            <td colspan="3" class="fw-bold">{{ $detail->item->name }}</td>
        </tr>
        <tr>
            <td>{{ $detail->quantity }}x</td>
            <td>{{ number_format($detail->price, 0, ',', '.') }}</td>
            <td class="text-right">{{ number_format($detail->price * $detail->quantity, 0, ',', '.') }}</td>
        </tr>
        @endforeach
    </table>
    
    <hr>
    <div class="flex-between fw-bold">
        <span>TOTAL</span>
        <span>Rp {{ number_format($order->total_amount, 0, ',', '.') }}</span>
    </div>
    <div class="flex-between">
        <span>BAYAR ({{ strtoupper($order->payment_method) }})</span>
        <span>Rp {{ number_format($order->cash_received ?? $order->total_amount, 0, ',', '.') }}</span>
    </div>
    <div class="flex-between">
        <span>KEMBALI</span>
        <span>Rp {{ number_format(($order->cash_received ?? $order->total_amount) - $order->total_amount, 0, ',', '.') }}</span>
    </div>
    <hr>
    <div class="text-center" style="font-size: 10px;">
        <p>Terima Kasih Atas Kunjungan Anda!</p>
    </div>
</body>
</html>