<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Catatan Pengeluaran - Geprek Legend</title>
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

        .summary-row { display: table; width: 100%; margin-bottom: 20px; }
        .summary-card { display: table-cell; width: 25%; padding: 0 5px; }
        .summary-card:first-child { padding-left: 0; }
        .summary-card:last-child { padding-right: 0; }
        .summary-box { border: 1px solid #e2e8f0; border-radius: 8px; padding: 10px 12px; text-align: center; }
        .summary-box.total { background: #fef2f2; border-color: #fecaca; }
        .summary-box.bahan { background: #fff7ed; border-color: #fed7aa; }
        .summary-box.logistik { background: #eff6ff; border-color: #bfdbfe; }
        .summary-box.lainnya { background: #f8fafc; border-color: #e2e8f0; }
        .summary-label { font-size: 7px; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 0.5px; }
        .summary-value { font-size: 14px; font-weight: 800; margin-top: 3px; }
        .text-red { color: #ef4444; }
        .text-orange { color: #ea580c; }
        .text-blue { color: #2563eb; }
        .text-gray { color: #64748b; }
        .text-muted { color: #94a3b8; font-style: italic; }

        table { width: 100%; border-collapse: collapse; }
        thead th { background: #f1f5f9; border: 1px solid #e2e8f0; padding: 7px 10px; font-size: 8px; font-weight: 700; text-transform: uppercase; color: #64748b; }
        tbody td { border: 1px solid #e2e8f0; padding: 6px 10px; font-size: 9px; }
        tbody tr:nth-child(even) { background: #fafbfc; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .text-left { text-align: left; }
        .font-bold { font-weight: 700; }
        .badge { display: inline-block; padding: 2px 8px; border-radius: 10px; font-size: 7px; font-weight: 700; text-transform: uppercase; }
        .badge-orange { background: #fff7ed; color: #ea580c; }
        .badge-blue { background: #eff6ff; color: #2563eb; }
        .badge-gray { background: #f1f5f9; color: #64748b; }

        .total-row { background: #fef2f2 !important; border-top: 2px solid #fecaca; }
        .footer { border-top: 1px solid #e2e8f0; padding-top: 10px; margin-top: 20px; font-size: 8px; color: #94a3b8; display: table; width: 100%; }
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
                    <div class="report-title">CATATAN PENGELUARAN</div>
                    <div class="report-period">Periode: {{ $startDate->format('d M Y') }} — {{ $endDate->format('d M Y') }}</div>
                    <div class="report-date">Dicetak: {{ now()->format('d M Y, H:i') }} WIB</div>
                </div>
            </div>
        </div>

        <!-- Ringkasan Nominal -->
        <div class="summary-row">
            <div class="summary-card">
                <div class="summary-box total">
                    <div class="summary-label">Total Pengeluaran</div>
                    <div class="summary-value text-red">Rp {{ number_format($totalExpense, 0, ',', '.') }}</div>
                </div>
            </div>
            <div class="summary-card">
                <div class="summary-box bahan">
                    <div class="summary-label">Bahan Baku</div>
                    <div class="summary-value text-orange">Rp {{ number_format($totalBahan, 0, ',', '.') }}</div>
                </div>
            </div>
            <div class="summary-card">
                <div class="summary-box logistik">
                    <div class="summary-label">Logistik</div>
                    <div class="summary-value text-blue">Rp {{ number_format($totalLogistik, 0, ',', '.') }}</div>
                </div>
            </div>
            <div class="summary-card">
                <div class="summary-box lainnya">
                    <div class="summary-label">Lainnya</div>
                    <div class="summary-value text-gray">Rp {{ number_format($totalLainnya, 0, ',', '.') }}</div>
                </div>
            </div>
        </div>

        <!-- Tabel Catatan -->
        <table>
            <thead>
                <tr>
                    <th class="text-center" style="width: 30px;">No</th>
                    <th class="text-left">Tanggal</th>
                    <th class="text-left">Item / Keterangan</th>
                    <th class="text-center">Kategori</th>
                    <th class="text-right">Nominal (Rp)</th>
                    <th class="text-center">Dicatat Oleh</th>
                </tr>
            </thead>
            <tbody>
                @forelse($expenses as $index => $exp)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td class="font-bold">{{ \Carbon\Carbon::parse($exp->date)->format('d M Y') }}</td>
                    <td>
                        <span class="font-bold">{{ $exp->item_name }}</span>
                        @if($exp->notes)<br><span class="text-muted" style="font-size: 7px;">{{ $exp->notes }}</span>@endif
                    </td>
                    <td class="text-center">
                        @if($exp->category == 'bahan')
                            <span class="badge badge-orange">Bahan</span>
                        @elseif($exp->category == 'logistik')
                            <span class="badge badge-blue">Logistik</span>
                        @else
                            <span class="badge badge-gray">Lainnya</span>
                        @endif
                    </td>
                    <td class="text-right font-bold text-red">- Rp {{ number_format($exp->amount, 0, ',', '.') }}</td>
                    <td class="text-center" style="font-size: 8px;">{{ $exp->user->name ?? '-' }}</td>
                </tr>
                @empty
                <tr><td colspan="6" class="text-center text-muted" style="padding: 20px;">Tidak ada data pengeluaran pada periode ini.</td></tr>
                @endforelse

                @if($expenses->count() > 0)
                <tr class="total-row">
                    <td colspan="4" class="text-right font-bold" style="font-size: 10px;">TOTAL PENGELUARAN</td>
                    <td class="text-right font-bold text-red" style="font-size: 11px;">Rp {{ number_format($totalExpense, 0, ',', '.') }}</td>
                    <td></td>
                </tr>
                @endif
            </tbody>
        </table>

        <div class="footer">
            <div class="footer-left">Geprek Legend — Catatan Pengeluaran</div>
            <div class="footer-right">Dicetak oleh: {{ auth()->user()->name }} | {{ now()->format('d/m/Y H:i') }}</div>
        </div>
    </div>
</body>
</html>
