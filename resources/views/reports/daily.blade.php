@extends('layouts.app')

@section('title', 'Laporan Harian')
@section('page_title', 'Laporan harian')

@section('content')
<div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-5 gap-3 no-print">
    <div class="flex items-center gap-2">
        <a href="{{ route('reports.daily', ['date' => $tanggal->copy()->subDay()->format('Y-m-d')]) }}"
           class="icon-btn" title="Hari sebelumnya" aria-label="Hari sebelumnya">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
        </a>
        <form action="{{ route('reports.daily') }}" method="GET" class="flex items-end gap-2">
            <div>
                <label class="block text-[13px] text-[#57504a] mb-1.5">Tanggal</label>
                <input type="date" name="date" value="{{ $tanggal->format('Y-m-d') }}" class="field !w-auto">
            </div>
            <button type="submit" class="btn-outline">Tampilkan</button>
        </form>
        @if(!$tanggal->isToday())
        <a href="{{ route('reports.daily', ['date' => $tanggal->copy()->addDay()->format('Y-m-d')]) }}"
           class="icon-btn" title="Hari berikutnya" aria-label="Hari berikutnya">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
        </a>
        @endif
        <div class="hidden sm:flex items-center gap-2 ml-1">
            @if(!$tanggal->isToday())
            <a href="{{ route('reports.daily', ['date' => now()->format('Y-m-d')]) }}" class="preset">Hari ini</a>
            @endif
            <a href="{{ route('reports.daily', ['date' => now()->subDay()->format('Y-m-d')]) }}"
               class="preset {{ $tanggal->isYesterday() ? 'is-active' : '' }}">Kemarin</a>
        </div>
    </div>

    <a href="{{ route('reports.exportDailyPdf', ['date' => $tanggal->format('Y-m-d')]) }}" class="btn-ink text-[13px] px-4 py-2.5 inline-flex items-center gap-2">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
        Export PDF
    </a>
</div>

{{-- Ringkasan --}}
<div class="card overflow-hidden mb-5 no-print rise">
    <div class="grid grid-cols-2 md:grid-cols-4 divide-x divide-y md:divide-y-0 divide-[#f1ebe0]">
        <div class="p-5">
            <p class="metric-label">Pemasukan</p>
            <p class="metric-value text-lg lg:text-xl mt-1.5">Rp {{ number_format($totalPenjualan, 0, ',', '.') }}</p>
            <p class="text-xs text-[#8a8179] mt-1.5">{{ $pesanan->count() }} transaksi @if($pertumbuhanPenjualan != 0)· {{ $pertumbuhanPenjualan > 0 ? '+' : '' }}{{ $pertumbuhanPenjualan }}% vs kemarin @endif</p>
        </div>
        <div class="p-5">
            <p class="metric-label">Pengeluaran</p>
            <p class="metric-value text-lg lg:text-xl mt-1.5">Rp {{ number_format($totalPengeluaran, 0, ',', '.') }}</p>
            <p class="text-xs text-[#8a8179] mt-1.5">{{ $pengeluaran->count() }} catatan</p>
        </div>
        <div class="p-5">
            <p class="metric-label">Rata-rata transaksi</p>
            <p class="metric-value text-lg lg:text-xl mt-1.5">Rp {{ number_format($pesanan->count() > 0 ? $totalPenjualan / $pesanan->count() : 0, 0, ',', '.') }}</p>
            <p class="text-xs text-[#8a8179] mt-1.5">Per transaksi</p>
        </div>
        <div class="p-5 bg-[#17110c]">
            <p class="text-[13px] text-stone-400">Hasil bersih</p>
            <p class="metric-value text-lg lg:text-xl mt-1.5 text-[#f7f1e6]">Rp {{ number_format(abs($hasilBersih ?? $labaBersih), 0, ',', '.') }}</p>
            <p class="text-xs mt-1.5 {{ ($hasilBersih ?? $labaBersih) >= 0 ? 'text-[#e8c96a]' : 'text-[#e68373]' }}">{{ ($hasilBersih ?? $labaBersih) >= 0 ? 'Untung hari ini' : 'Rugi hari ini' }}</p>
        </div>
    </div>
</div>

{{-- Rincian item & kasir --}}
@if($rincianBarang->count() > 0 || $rincianKasir->count() > 0)
<div class="grid grid-cols-1 lg:grid-cols-3 gap-4 mb-5 no-print">
    <div class="lg:col-span-2 card card-pad">
        <h3 class="font-display text-[17px] font-semibold tracking-tight">Penjualan per item</h3>
        <p class="text-[13px] text-[#8a8179] mt-0.5 mb-3">{{ $tanggal->translatedFormat('l, d F Y') }}</p>
        @if($rincianBarang->count() > 0)
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div class="relative h-48 sm:h-56">
                <canvas id="itemPieChart"></canvas>
            </div>
            <div class="divide-y divide-[#f1ebe0] overflow-y-auto max-h-56">
                @foreach($rincianBarang as $i => $item)
                <div class="py-2.5">
                    <div class="flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full shrink-0" style="background-color: {{ ['#1c1410','#931b13','#c9a227','#7a7268','#b3a897','#3d4c5c','#2c5b34','#8a8179'][$i % 8] }}"></span>
                        <span class="text-[13px] font-medium truncate flex-1">{{ $item->item->name ?? 'Item terhapus' }}</span>
                        <span class="text-xs text-[#8a8179] tnum">×{{ $item->total_qty }}</span>
                        <span class="text-[13px] font-semibold tnum">Rp {{ number_format($item->total_amount, 0, ',', '.') }}</span>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @else
        <p class="text-[13px] text-[#8a8179] text-center py-6">Belum ada penjualan.</p>
        @endif
    </div>

    <div class="card card-pad">
        <h3 class="font-display text-[17px] font-semibold tracking-tight">Performa kasir</h3>
        <p class="text-[13px] text-[#8a8179] mt-0.5 mb-3">Berdasarkan nominal</p>
        @if($rincianKasir->count() > 0)
        <div class="divide-y divide-[#f1ebe0]">
            @foreach($rincianKasir as $cashier)
            <div class="py-3">
                <div class="flex items-center gap-3 mb-2">
                    <div class="w-8 h-8 rounded-full bg-[#f4eee2] text-[#57504a] flex items-center justify-center font-display font-semibold text-sm shrink-0">
                        {{ substr($cashier->user->name ?? 'X', 0, 1) }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-[13px] font-medium truncate">{{ $cashier->user->name ?? 'Unknown' }}</p>
                        <p class="text-xs text-[#8a8179]">{{ $cashier->total_transactions }} transaksi</p>
                    </div>
                </div>
                <div class="flex justify-between items-center gap-3">
                    <div class="flex-1 bg-[#f1ebe0] rounded-full h-1.5">
                        @php $pct = $totalPenjualan > 0 ? ($cashier->total_amount / $totalPenjualan) * 100 : 0; @endphp
                        <div class="bg-[#1c1410] h-1.5 rounded-full" style="width: {{ $pct }}%"></div>
                    </div>
                    <span class="text-[13px] font-semibold tnum shrink-0">Rp {{ number_format($cashier->total_amount, 0, ',', '.') }}</span>
                </div>
            </div>
            @endforeach
        </div>
        @else
        <p class="text-[13px] text-[#8a8179] text-center py-6">Belum ada data kasir.</p>
        @endif
    </div>
</div>
@endif

{{-- Dokumen harian (print-ready) --}}
<div class="card card-pad">
    <div class="text-center mb-7">
        <p class="font-display text-2xl font-semibold tracking-tight">Geprek Legend</p>
        <p class="text-[13px] text-[#57504a] mt-1.5 inline-block border-y border-[#1c1410] py-1 px-8">Laporan pemasukan & logistik harian</p>
        <div class="flex justify-between mt-5 text-[13px] font-medium">
            <span>{{ $tanggal->translatedFormat('l') }}</span>
            <span class="tnum">{{ $tanggal->format('d F Y') }}</span>
        </div>
    </div>

    <div class="mb-7">
        <p class="text-[13px] text-[#8a8179] mb-2">I · Ringkasan pemasukan</p>
        <table class="w-full">
            <thead>
                <tr class="border-y border-[#e8e0d3]">
                    <th class="w-14 py-2.5 text-[11px] font-semibold text-[#7a7268] text-center">No</th>
                    <th class="py-2.5 text-[11px] font-semibold text-[#7a7268] text-left">Uraian transaksi</th>
                    <th class="w-40 py-2.5 text-[11px] font-semibold text-[#7a7268] text-center">Hitungan</th>
                    <th class="w-48 py-2.5 text-[11px] font-semibold text-[#7a7268] text-right">Jumlah (Rp)</th>
                </tr>
            </thead>
            <tbody>
                <tr class="border-b border-[#f1ebe0]">
                    <td class="text-center py-2.5 text-[13px] tnum">1</td>
                    <td class="font-medium py-2.5 text-[13px]">Total penjualan kasir</td>
                    <td class="text-center py-2.5 text-[13px] tnum">{{ $pesanan->count() }} transaksi</td>
                    <td class="text-right font-semibold py-2.5 text-[13px] tnum">Rp {{ number_format($totalPenjualan, 0, ',', '.') }}</td>
                </tr>
            </tbody>
        </table>
    </div>

    <div class="mb-7">
        <p class="text-[13px] text-[#8a8179] mb-2">II · Stok menu menipis (kurang dari 5)</p>
        <table class="w-full">
            <thead>
                <tr class="border-y border-[#e8e0d3]">
                    <th class="py-2.5 text-[11px] font-semibold text-[#7a7268] text-left">Menu</th>
                    <th class="py-2.5 text-[11px] font-semibold text-[#7a7268] text-center">Sisa</th>
                    <th class="py-2.5 text-[11px] font-semibold text-[#7a7268] text-center">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-[#f1ebe0]">
                @forelse($stokMenipis as $item)
                <tr>
                    <td class="font-medium text-[13px] py-2.5">{{ $item->name }}</td>
                    <td class="text-center font-semibold py-2.5 text-[15px] tnum {{ $item->stock <= 0 ? 'text-[#931b13]' : '' }}">{{ $item->stock }}</td>
                    <td class="text-center py-2.5">
                        @if($item->stock <= 0)
                            <span class="pill pill-danger">Habis</span>
                        @else
                            <span class="pill pill-warn">Menipis</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr><td colspan="3" class="text-center text-[13px] text-[#8a8179] py-6">Semua stok menu aman.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @php
        $pengeluaranHarian = $pengeluaranPerKategori ?? $pengeluaran->groupBy('category')->map(fn($rows) => $rows->sum('amount'));
        $labelKat = ['belanja_harian' => 'Belanja Harian', 'operasional' => 'Operasional', 'gaji' => 'Gaji', 'lainnya' => 'Lainnya', 'bahan' => 'Belanja Harian', 'logistik' => 'Operasional'];
    @endphp
    <div class="mb-7">
        <p class="text-[13px] text-[#8a8179] mb-2">III · Pengeluaran hari ini per kategori</p>
        <table class="w-full">
            <thead>
                <tr class="border-y border-[#e8e0d3]">
                    <th class="py-2.5 text-[11px] font-semibold text-[#7a7268] text-left">Kategori</th>
                    <th class="w-48 py-2.5 text-[11px] font-semibold text-[#7a7268] text-right">Jumlah (Rp)</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-[#f1ebe0]">
                @forelse($pengeluaranHarian as $kat => $nominal)
                <tr>
                    <td class="text-[13px] py-2">{{ $labelKat[$kat] ?? ucfirst($kat) }}</td>
                    <td class="text-right text-[13px] tnum py-2">Rp {{ number_format($nominal, 0, ',', '.') }}</td>
                </tr>
                @empty
                <tr><td colspan="2" class="text-center text-[13px] text-[#8a8179] py-4">Tidak ada pengeluaran hari ini.</td></tr>
                @endforelse
                <tr class="border-t border-[#e8e0d3]">
                    <td class="text-right text-[13px] font-semibold py-2.5">Total</td>
                    <td class="text-right text-[13px] font-semibold tnum py-2.5">Rp {{ number_format($totalPengeluaran, 0, ',', '.') }}</td>
                </tr>
            </tbody>
        </table>
    </div>

    <div class="mb-7">
        <p class="text-[13px] text-[#8a8179] mb-2">IV · Rincian pengeluaran</p>
        <table class="w-full">
            <thead>
                <tr class="border-y border-[#e8e0d3]">
                    <th class="py-2.5 text-[11px] font-semibold text-[#7a7268] text-left">Item / keperluan</th>
                    <th class="w-32 py-2.5 text-[11px] font-semibold text-[#7a7268] text-right">Harga</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-[#f1ebe0]">
                @forelse($pengeluaran as $exp)
                <tr>
                    <td class="text-[13px] py-2">{{ $exp->item_name }} <span class="text-xs text-[#8a8179]">· {{ $labelKat[$exp->category] ?? $exp->category }}</span></td>
                    <td class="text-right text-[13px] tnum py-2">Rp {{ number_format($exp->amount, 0, ',', '.') }}</td>
                </tr>
                @empty
                <tr><td colspan="2" class="text-center text-[13px] text-[#8a8179] py-4">Tidak ada pengeluaran.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @php $hasilHarian = $hasilBersih ?? $labaBersih ?? 0; @endphp
    <div class="rounded-[12px] border border-[#e8e0d3] {{ $hasilHarian >= 0 ? 'bg-[#f7f2e9]' : 'bg-[#fdf0ee]' }} px-5 py-4">
        <div class="flex justify-between items-center gap-4">
            <div>
                <p class="text-[13px] text-[#8a8179]">Hasil bersih hari ini</p>
                <p class="font-display text-[26px] font-semibold tnum tracking-tight">Rp {{ number_format(abs($hasilHarian), 0, ',', '.') }}</p>
                @if($hasilHarian < 0)
                    <p class="text-[13px] text-[#931b13] font-medium mt-1">Hari ini rugi</p>
                @else
                    <p class="text-[13px] text-[#57504a] mt-1">Untung hari ini</p>
                @endif
            </div>
            <div class="text-right text-xs text-[#8a8179]">
                Dicetak oleh {{ auth()->user()->name }}<br>
                <span class="tnum">{{ now()->format('d/m/Y H:i') }}</span>
            </div>
        </div>
    </div>
</div>

@if($rincianBarang->count() > 0)
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const pieCtx = document.getElementById('itemPieChart').getContext('2d');
        const colors = ['#1c1410','#931b13','#c9a227','#7a7268','#b3a897','#3d4c5c','#2c5b34','#8a8179'];

        new Chart(pieCtx, {
            type: 'doughnut',
            data: {
                labels: {!! json_encode($rincianBarang->map(fn($i) => $i->item->name ?? 'Terhapus')->values()) !!},
                datasets: [{
                    data: {!! json_encode($rincianBarang->pluck('total_amount')->values()) !!},
                    backgroundColor: colors.slice(0, {{ $rincianBarang->count() }}),
                    borderWidth: 2,
                    borderColor: '#fffdf9',
                    hoverOffset: 6,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '62%',
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: '#17110c',
                        titleFont: { size: 11, weight: '500' },
                        bodyFont: { size: 12 },
                        padding: 12,
                        cornerRadius: 10,
                        callbacks: {
                            label: function(ctx) {
                                const total = ctx.dataset.data.reduce((a, b) => a + b, 0);
                                const pct = ((ctx.parsed / total) * 100).toFixed(1);
                                return 'Rp ' + ctx.parsed.toLocaleString('id-ID') + ' (' + pct + '%)';
                            }
                        }
                    }
                }
            }
        });
    });
</script>
@endif
@endsection
