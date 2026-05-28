<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Rekap Laba Rugi - Geprek Legend</title>
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
        .summary-card { display: table-cell; width: 33.33%; padding: 0 5px; }
        .summary-card:first-child { padding-left: 0; }
        .summary-card:last-child { padding-right: 0; }
        .summary-box { border: 1px solid #e2e8f0; border-radius: 8px; padding: 12px 14px; text-align: center; }
        .summary-box.green { background: #f0fdf4; border-color: #bbf7d0; }
        .summary-box.red { background: #fef2f2; border-color: #fecaca; }
        .summary-box.orange { background: #fff7ed; border-color: #fed7aa; }
        .summary-label { font-size: 8px; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 0.5px; }
        .summary-value { font-size: 16px; font-weight: 800; margin-top: 4px; }
        .summary-value.green { color: #16a34a; }
        .summary-value.red { color: #ef4444; }
        .summary-value.orange { color: #ea580c; }

        table { width: 100%; border-collapse: collapse; }
        thead th { background: #f1f5f9; border: 1px solid #e2e8f0; padding: 8px 12px; font-size: 9px; font-weight: 700; text-transform: uppercase; color: #64748b; }
        tbody td { border: 1px solid #e2e8f0; padding: 8px 12px; font-size: 10px; }
        tbody tr:nth-child(even) { background: #fafbfc; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .text-left { text-align: left; }
        .font-bold { font-weight: 700; }
        .text-red { color: #ef4444; }
        .text-green { color: #16a34a; }
        .text-orange { color: #ea580c; }
        .text-muted { color: #94a3b8; font-style: italic; }
        .total-row { background: #f1f5f9 !important; border-top: 2px solid #cbd5e1; }
        .loss-row { background: #fef2f2 !important; }

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
                    <div class="report-title">REKAPITULASI LABA / RUGI</div>
                    <div class="report-period">Periode: {{ $tanggalMulai->format('d M Y') }} — {{ $tanggalSelesai->format('d M Y') }}</div>
                    <div class="report-date">Dicetak: {{ now()->format('d M Y, H:i') }} WIB</div>
                </div>
            </div>
        </div>

        <!-- Summary -->
        <div class="summary-row">
            <div class="summary-card">
                <div class="summary-box green">
                    <div class="summary-label">Total Pemasukan</div>
                    <div class="summary-value green">Rp {{ number_format($totalPemasukan, 0, ',', '.') }}</div>
                </div>
            </div>
            <div class="summary-card">
                <div class="summary-box red">
                    <div class="summary-label">Total Pengeluaran</div>
                    <div class="summary-value red">Rp {{ number_format($totalPengeluaran, 0, ',', '.') }}</div>
                </div>
            </div>
            <div class="summary-card">
                <div class="summary-box orange">
                    <div class="summary-label">Laba Bersih</div>
                    <div class="summary-value {{ $labaBersih >= 0 ? 'green' : 'red' }}">Rp {{ number_format($labaBersih, 0, ',', '.') }}</div>
                </div>
            </div>
        </div>

        <!-- Table -->
        <table>
            <thead>
                <tr>
                    <th class="text-left" style="width: 30%;">Hari / Tanggal</th>
                    <th class="text-right">Pemasukan</th>
                    <th class="text-right">Pengeluaran</th>
                    <th class="text-right">Laba / Rugi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($rekapData as $data)
                <tr class="{{ $data['total'] < 0 ? 'loss-row' : '' }}">
                    <td class="font-bold">{{ $data['tanggal'] }}</td>
                    <td class="text-right text-green font-bold">Rp {{ number_format($data['pemasukan'], 0, ',', '.') }}</td>
                    <td class="text-right text-red font-bold">Rp {{ number_format($data['pengeluaran'], 0, ',', '.') }}</td>
                    <td class="text-right font-bold {{ $data['total'] >= 0 ? 'text-green' : 'text-red' }}">
                        {{ $data['total'] < 0 ? '- ' : '' }}Rp {{ number_format(abs($data['total']), 0, ',', '.') }}
                        @if($data['total'] < 0) ⚠ @endif
                    </td>
                </tr>
                @empty
                <tr><td colspan="4" class="text-center text-muted" style="padding: 20px;">Tidak ada data pada periode ini.</td></tr>
                @endforelse

                @if($rekapData->count() > 0)
                <tr class="total-row">
                    <td class="font-bold" style="font-size: 10px;">TOTAL KESELURUHAN</td>
                    <td class="text-right text-green font-bold" style="font-size: 10px;">Rp {{ number_format($totalPemasukan, 0, ',', '.') }}</td>
                    <td class="text-right text-red font-bold" style="font-size: 10px;">Rp {{ number_format($totalPengeluaran, 0, ',', '.') }}</td>
                    <td class="text-right font-bold {{ $labaBersih >= 0 ? 'text-green' : 'text-red' }}" style="font-size: 11px;">
                        Rp {{ number_format($labaBersih, 0, ',', '.') }}
                    </td>
                </tr>
                @endif
            </tbody>
        </table>

        <div class="footer">
            <div class="footer-left">Geprek Legend — Rekap Laba/Rugi</div>
            <div class="footer-right">Dicetak oleh: {{ auth()->user()->name }} | {{ now()->format('d/m/Y H:i') }}</div>
        </div>
    </div>
</body>
</html>
