<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Harian - Geprek Legend</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 10px; color: #1a1a2e; line-height: 1.5; background: #fff; }
        .page { padding: 30px 40px; }
        .header { border-bottom: 3px solid #ea580c; padding-bottom: 15px; margin-bottom: 20px; }
        .header-top { display: table; width: 100%; }
        .header-left { display: table-cell; vertical-align: top; }
        .header-right { display: table-cell; vertical-align: top; text-align: right; }
        .brand { font-size: 22px; font-weight: 800; color: #ea580c; letter-spacing: -1px; }
        .brand-sub { font-size: 10px; color: #666; margin-top: 2px; }
        .report-title { font-size: 14px; font-weight: 700; color: #1a1a2e; }
        .report-period { font-size: 9px; color: #888; margin-top: 3px; }
        .report-date { font-size: 8px; color: #aaa; margin-top: 2px; }

        .section-title { font-size: 11px; font-weight: 700; color: #1a1a2e; margin-bottom: 8px; padding-bottom: 5px; border-bottom: 2px solid #ea580c; display: inline-block; }
        .section { margin-bottom: 20px; }

        table { width: 100%; border-collapse: collapse; }
        thead th { background: #f1f5f9; border: 1px solid #e2e8f0; padding: 7px 10px; font-size: 8px; font-weight: 700; text-transform: uppercase; color: #64748b; }
        tbody td { border: 1px solid #e2e8f0; padding: 6px 10px; font-size: 9px; }
        tbody tr:nth-child(even) { background: #fafbfc; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .text-left { text-align: left; }
        .font-bold { font-weight: 700; }
        .text-red { color: #ef4444; }
        .text-green { color: #16a34a; }
        .text-muted { color: #94a3b8; font-style: italic; }
        .text-orange { color: #ea580c; }

        .two-col { display: table; width: 100%; }
        .col-half { display: table-cell; width: 49%; vertical-align: top; }
        .col-gap { display: table-cell; width: 2%; }

        .profit-box { background: #f0fdf4; border: 2px solid #16a34a; border-radius: 8px; padding: 15px 20px; margin-top: 20px; display: table; width: 100%; }
        .profit-box.loss { background: #fef2f2; border-color: #ef4444; }
        .profit-left { display: table-cell; vertical-align: middle; }
        .profit-right { display: table-cell; vertical-align: middle; text-align: right; }
        .profit-label { font-size: 9px; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 0.5px; }
        .profit-value { font-size: 22px; font-weight: 800; color: #16a34a; margin-top: 3px; }
        .profit-value.loss { color: #ef4444; }
        .profit-detail { font-size: 8px; color: #94a3b8; }

        .total-row { background: #f1f5f9 !important; font-weight: 700; }
        .footer { border-top: 1px solid #e2e8f0; padding-top: 10px; margin-top: 15px; font-size: 8px; color: #94a3b8; display: table; width: 100%; }
        .footer-left { display: table-cell; }
        .footer-right { display: table-cell; text-align: right; }
    </style>
</head>
<body>
    <div class="page">
        <div class="header">
            <div class="header-top">
                <div class="header-left">
                    <div class="brand">GEPREK LEGEND</div>
                    <div class="brand-sub">Sistem Kasir Digital</div>
                </div>
                <div class="header-right">
                    <div class="report-title">LAPORAN HARIAN</div>
                    <div class="report-period">{{ $tanggal->translatedFormat('l, d F Y') }}</div>
                    <div class="report-date">Dicetak: {{ now()->format('d M Y, H:i') }} WIB</div>
                </div>
            </div>
        </div>

        <!-- I. Ringkasan Pemasukan -->
        <div class="section">
            <div class="section-title">I. RINGKASAN PEMASUKAN</div>
            <table>
                <thead>
                    <tr>
                        <th class="text-center" style="width: 40px;">No</th>
                        <th class="text-left">Uraian</th>
                        <th class="text-center" style="width: 120px;">Hitungan</th>
                        <th class="text-right" style="width: 150px;">Jumlah (Rp)</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="text-center">1</td>
                        <td class="font-bold">Total Penjualan Kasir</td>
                        <td class="text-center">{{ $pesanan->count() }} Transaksi</td>
                        <td class="text-right font-bold">Rp {{ number_format($totalPenjualan, 0, ',', '.') }}</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- II. Stok Bahan Baku -->
        <div class="section">
            <div class="section-title">II. LAPORAN STOK BAHAN BAKU</div>
            <table>
                <thead>
                    <tr>
                        <th class="text-left">Item Bahan</th>
                        <th class="text-center">Stok Masuk</th>
                        <th class="text-center">Stok Awal</th>
                        <th class="text-center">Terjual</th>
                        <th class="text-center" style="background: #fff7ed;">Sisa Stok</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($stokBarang as $inv)
                    <tr>
                        <td class="font-bold" style="text-transform: uppercase; font-size: 8px;">{{ $inv->item_name }}</td>
                        <td class="text-center">{{ $inv->new_stock }}</td>
                        <td class="text-center">{{ $inv->old_stock }}</td>
                        <td class="text-center text-red">-{{ $inv->sold }}</td>
                        <td class="text-center font-bold {{ $inv->remaining_stock < 5 ? 'text-red' : '' }}" style="background: #fffbf5;">
                            {{ $inv->remaining_stock }}
                            @if($inv->remaining_stock < 5) ⚠ @endif
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="5" class="text-center text-muted" style="padding: 12px;">Data stok belum diinput.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- III & IV. Pengeluaran -->
        @php
            $totalBahan = 0;
            $totalLogistik = 0;
        @endphp
        <div class="two-col">
            <div class="col-half">
                <div class="section-title">III. PENGELUARAN BAHAN</div>
                <table>
                    <thead><tr><th class="text-left">Item</th><th class="text-right" style="width: 100px;">Harga</th></tr></thead>
                    <tbody>
                        @foreach($pengeluaran->where('category', 'bahan') as $exp)
                        <tr>
                            <td style="font-size: 8px;">{{ $exp->item_name }}</td>
                            <td class="text-right" style="font-size: 8px;">Rp {{ number_format($exp->amount, 0, ',', '.') }}</td>
                        </tr>
                        @php $totalBahan += $exp->amount; @endphp
                        @endforeach
                        <tr class="total-row">
                            <td class="text-right">TOTAL</td>
                            <td class="text-right text-red" style="font-size: 9px;">Rp {{ number_format($totalBahan, 0, ',', '.') }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="col-gap"></div>
            <div class="col-half">
                <div class="section-title">IV. PENGELUARAN LOGISTIK</div>
                <table>
                    <thead><tr><th class="text-left">Item</th><th class="text-right" style="width: 100px;">Harga</th></tr></thead>
                    <tbody>
                        @foreach($pengeluaran->where('category', 'logistik') as $exp)
                        <tr>
                            <td style="font-size: 8px;">{{ $exp->item_name }}</td>
                            <td class="text-right" style="font-size: 8px;">Rp {{ number_format($exp->amount, 0, ',', '.') }}</td>
                        </tr>
                        @php $totalLogistik += $exp->amount; @endphp
                        @endforeach
                        <tr class="total-row">
                            <td class="text-right">TOTAL</td>
                            <td class="text-right text-red" style="font-size: 9px;">Rp {{ number_format($totalLogistik, 0, ',', '.') }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Profit Box -->
        <div class="profit-box {{ $labaBersih < 0 ? 'loss' : '' }}">
            <div class="profit-left">
                <div class="profit-label">Total Laba Bersih Hari Ini</div>
                <div class="profit-value {{ $labaBersih < 0 ? 'loss' : '' }}">Rp {{ number_format($labaBersih, 0, ',', '.') }}</div>
            </div>
            <div class="profit-right">
                <div class="profit-detail">Pemasukan: Rp {{ number_format($totalPenjualan, 0, ',', '.') }}</div>
                <div class="profit-detail">Pengeluaran: Rp {{ number_format($totalPengeluaran, 0, ',', '.') }}</div>
                <div class="profit-detail" style="margin-top: 5px;">Dicetak oleh: {{ auth()->user()->name }}</div>
            </div>
        </div>

        <div class="footer">
            <div class="footer-left">Geprek Legend — Laporan Harian</div>
            <div class="footer-right">{{ now()->format('d/m/Y H:i') }}</div>
        </div>
    </div>
</body>
</html>
