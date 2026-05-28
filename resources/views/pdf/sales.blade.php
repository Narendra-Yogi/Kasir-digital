<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Penjualan - Geprek Legend</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 10px; color: #1a1a2e; line-height: 1.5; background: #fff; }
        .page { padding: 30px 40px; }

        /* Header */
        .header { border-bottom: 3px solid #ea580c; padding-bottom: 15px; margin-bottom: 20px; }
        .header-top { display: table; width: 100%; }
        .header-left { display: table-cell; vertical-align: top; }
        .header-right { display: table-cell; vertical-align: top; text-align: right; }
        .brand { font-size: 22px; font-weight: 800; color: #ea580c; letter-spacing: -1px; }
        .brand-sub { font-size: 10px; color: #666; margin-top: 2px; }
        .report-title { font-size: 14px; font-weight: 700; color: #1a1a2e; }
        .report-period { font-size: 9px; color: #888; margin-top: 3px; }
        .report-date { font-size: 8px; color: #aaa; margin-top: 2px; }

        /* Summary Cards */
        .summary-row { display: table; width: 100%; margin-bottom: 20px; }
        .summary-card { display: table-cell; width: 25%; padding: 0 5px; }
        .summary-card:first-child { padding-left: 0; }
        .summary-card:last-child { padding-right: 0; }
        .summary-box { background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 12px 14px; }
        .summary-box.highlight { background: #fff7ed; border-color: #fed7aa; }
        .summary-label { font-size: 8px; font-weight: 700; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.5px; }
        .summary-value { font-size: 16px; font-weight: 800; color: #1a1a2e; margin-top: 4px; }
        .summary-value.orange { color: #ea580c; }
        .summary-value.red { color: #ef4444; }
        .summary-value.green { color: #16a34a; }
        .summary-note { font-size: 7px; color: #94a3b8; margin-top: 3px; }

        /* Table */
        table { width: 100%; border-collapse: collapse; margin-bottom: 15px; }
        thead th { background: #f1f5f9; border: 1px solid #e2e8f0; padding: 8px 10px; font-size: 8px; font-weight: 700; text-transform: uppercase; color: #64748b; letter-spacing: 0.3px; }
        tbody td { border: 1px solid #e2e8f0; padding: 7px 10px; font-size: 9px; vertical-align: top; }
        tbody tr:nth-child(even) { background: #fafbfc; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .text-left { text-align: left; }
        .font-bold { font-weight: 700; }
        .text-sm { font-size: 8px; }
        .text-muted { color: #94a3b8; }
        .text-red { color: #ef4444; }
        .text-green { color: #16a34a; }
        .text-orange { color: #ea580c; }
        .line-through { text-decoration: line-through; }
        .opacity-60 { opacity: 0.6; }
        .badge { display: inline-block; padding: 2px 8px; border-radius: 10px; font-size: 7px; font-weight: 700; text-transform: uppercase; }
        .badge-success { background: #dcfce7; color: #16a34a; }
        .badge-danger { background: #fee2e2; color: #ef4444; }
        .badge-blue { background: #dbeafe; color: #2563eb; }
        .badge-orange { background: #fff7ed; color: #ea580c; }

        /* Footer */
        .footer { border-top: 1px solid #e2e8f0; padding-top: 10px; margin-top: 15px; font-size: 8px; color: #94a3b8; display: table; width: 100%; }
        .footer-left { display: table-cell; }
        .footer-right { display: table-cell; text-align: right; }
    </style>
</head>
<body>
    <div class="page">
        <!-- Header -->
        <div class="header">
            <div class="header-top">
                <div class="header-left">
                    <div class="brand">GEPREK LEGEND</div>
                    <div class="brand-sub">Sistem Kasir Digital</div>
                </div>
                <div class="header-right">
                    <div class="report-title">LAPORAN PENJUALAN</div>
                    <div class="report-period">Periode: {{ $tanggalMulai->format('d M Y') }} — {{ $tanggalSelesai->format('d M Y') }}</div>
                    <div class="report-date">Dicetak: {{ now()->format('d M Y, H:i') }} WIB</div>
                </div>
            </div>
        </div>

        <!-- Summary -->
        <div class="summary-row">
            <div class="summary-card">
                <div class="summary-box highlight">
                    <div class="summary-label">Total Pendapatan</div>
                    <div class="summary-value orange">Rp {{ number_format($totalPendapatan, 0, ',', '.') }}</div>
                    <div class="summary-note">{{ $totalTransaksi }} transaksi sukses</div>
                </div>
            </div>
            <div class="summary-card">
                <div class="summary-box">
                    <div class="summary-label">Rata-rata Transaksi</div>
                    <div class="summary-value">Rp {{ number_format($rataRataTransaksi, 0, ',', '.') }}</div>
                    <div class="summary-note">Per transaksi</div>
                </div>
            </div>
            <div class="summary-card">
                <div class="summary-box">
                    <div class="summary-label">Total Transaksi</div>
                    <div class="summary-value green">{{ $totalTransaksi }}</div>
                    <div class="summary-note">Berhasil diproses</div>
                </div>
            </div>
            <div class="summary-card">
                <div class="summary-box">
                    <div class="summary-label">Dibatalkan</div>
                    <div class="summary-value red">{{ $totalDibatalkan }}</div>
                    <div class="summary-note">Transaksi batal</div>
                </div>
            </div>
        </div>

        <!-- Table -->
        <table>
            <thead>
                <tr>
                    <th class="text-center" style="width: 30px;">No</th>
                    <th class="text-left">Invoice</th>
                    <th class="text-left">Waktu</th>
                    <th class="text-left">Pelanggan</th>
                    <th class="text-left">Detail Pesanan</th>
                    <th class="text-center">Metode</th>
                    <th class="text-center">Status</th>
                    <th class="text-right">Total</th>
                </tr>
            </thead>
            <tbody>
                @forelse($pesanan as $index => $order)
                <tr class="{{ $order->status === 'cancelled' ? 'opacity-60' : '' }}">
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td class="font-bold">{{ $order->invoice_number }}</td>
                    <td>{{ $order->created_at->format('d/m/Y H:i') }}</td>
                    <td>{{ $order->customer_name ?? 'Pelanggan Umum' }}</td>
                    <td class="text-sm">
                        @foreach($order->orderDetails as $detail)
                            {{ $detail->item->name }} (x{{ $detail->quantity }}){{ !$loop->last ? ', ' : '' }}
                        @endforeach
                    </td>
                    <td class="text-center">
                        <span class="badge {{ $order->payment_method == 'qris' ? 'badge-blue' : 'badge-orange' }}">{{ strtoupper($order->payment_method) }}</span>
                    </td>
                    <td class="text-center">
                        <span class="badge {{ $order->status === 'success' ? 'badge-success' : 'badge-danger' }}">{{ $order->status === 'success' ? 'Sukses' : 'Batal' }}</span>
                    </td>
                    <td class="text-right font-bold {{ $order->status === 'cancelled' ? 'text-red line-through' : '' }}">
                        Rp {{ number_format($order->total_amount, 0, ',', '.') }}
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="text-center text-muted" style="padding: 20px;">Tidak ada transaksi pada periode ini.</td>
                </tr>
                @endforelse
            </tbody>
        </table>

        <!-- Footer -->
        <div class="footer">
            <div class="footer-left">Geprek Legend — Laporan Penjualan</div>
            <div class="footer-right">Dicetak oleh: {{ auth()->user()->name }} | {{ now()->format('d/m/Y H:i') }}</div>
        </div>
    </div>
</body>
</html>
