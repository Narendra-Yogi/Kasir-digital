<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Stok Logistik - Geprek Legend</title>
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
        .summary-box.primary { background: #fff7ed; border-color: #fed7aa; }
        .summary-box.danger { background: #fef2f2; border-color: #fecaca; }
        .summary-box.info { background: #f8fafc; border-color: #e2e8f0; }
        .summary-label { font-size: 8px; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 0.5px; }
        .summary-value { font-size: 20px; font-weight: 800; margin-top: 4px; }
        .text-orange { color: #ea580c; }
        .text-red { color: #ef4444; }
        .text-gray { color: #64748b; }
        .text-muted { color: #94a3b8; font-style: italic; }

        table { width: 100%; border-collapse: collapse; }
        thead th { background: #f1f5f9; border: 1px solid #e2e8f0; padding: 8px 12px; font-size: 8px; font-weight: 700; text-transform: uppercase; color: #64748b; }
        tbody td { border: 1px solid #e2e8f0; padding: 7px 12px; font-size: 10px; }
        tbody tr:nth-child(even) { background: #fafbfc; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .text-left { text-align: left; }
        .font-bold { font-weight: 700; }
        .critical-row { background: #fef2f2 !important; }
        .stock-cell { background: #fffbf5; }
        .alert-icon { color: #ef4444; font-weight: 800; }

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
                    <div class="report-title">LAPORAN STOK LOGISTIK</div>
                    <div class="report-period">Tanggal: {{ $date->translatedFormat('l, d F Y') }}</div>
                    <div class="report-date">Dicetak: {{ now()->format('d M Y, H:i') }} WIB</div>
                </div>
            </div>
        </div>

        <!-- Summary -->
        <div class="summary-row">
            <div class="summary-card">
                <div class="summary-box primary">
                    <div class="summary-label">Total Item Dipantau</div>
                    <div class="summary-value text-orange">{{ $totalItems }}</div>
                </div>
            </div>
            <div class="summary-card">
                <div class="summary-box danger">
                    <div class="summary-label">Stok Kritis (&lt; 5)</div>
                    <div class="summary-value text-red">{{ $criticalStock }}</div>
                </div>
            </div>
            <div class="summary-card">
                <div class="summary-box info">
                    <div class="summary-label">Habis (0)</div>
                    <div class="summary-value text-gray">{{ $outOfStock }}</div>
                </div>
            </div>
        </div>

        <!-- Table -->
        <table>
            <thead>
                <tr>
                    <th class="text-center" style="width: 30px;">No</th>
                    <th class="text-left">Item Bahan</th>
                    <th class="text-center">Stok Masuk</th>
                    <th class="text-center">Stok Awal</th>
                    <th class="text-center">Terjual</th>
                    <th class="text-center" style="background: #fff7ed;">Sisa Stok</th>
                    <th class="text-center">Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($inventories as $index => $inv)
                <tr class="{{ $inv->remaining_stock < 5 ? 'critical-row' : '' }}">
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td class="font-bold" style="text-transform: uppercase; font-size: 9px;">{{ $inv->item_name }}</td>
                    <td class="text-center" style="color: #2563eb;">+{{ $inv->new_stock }}</td>
                    <td class="text-center">{{ $inv->old_stock }}</td>
                    <td class="text-center text-red">-{{ $inv->sold }}</td>
                    <td class="text-center font-bold stock-cell" style="font-size: 12px; {{ $inv->remaining_stock < 5 ? 'color: #ef4444;' : 'color: #ea580c;' }}">
                        {{ $inv->remaining_stock }}
                    </td>
                    <td class="text-center" style="font-size: 8px;">
                        @if($inv->remaining_stock <= 0)
                            <span class="alert-icon">⚠ HABIS</span>
                        @elseif($inv->remaining_stock < 5)
                            <span class="alert-icon">⚠ KRITIS</span>
                        @else
                            <span style="color: #16a34a; font-weight: 700;">✓ AMAN</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" class="text-center text-muted" style="padding: 20px;">Belum ada data stok untuk tanggal ini.</td></tr>
                @endforelse
            </tbody>
        </table>

        <div class="footer">
            <div class="footer-left">Geprek Legend — Laporan Stok Logistik</div>
            <div class="footer-right">Dicetak oleh: {{ auth()->user()->name }} | {{ now()->format('d/m/Y H:i') }}</div>
        </div>
    </div>
</body>
</html>
