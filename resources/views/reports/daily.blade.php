@extends('layouts.app')

@section('title', 'Laporan Harian')
@section('page_title', 'Laporan Harian')

@section('content')
<div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 lg:mb-8 gap-4 no-print">
    <div class="flex items-center gap-2">
        {{-- Navigasi Hari Sebelumnya --}}
        <a href="{{ route('reports.daily', ['date' => $tanggal->copy()->subDay()->format('Y-m-d')]) }}" 
           class="p-2.5 rounded-xl bg-gray-100 hover:bg-gray-200 text-gray-600 transition-colors" title="Hari Sebelumnya">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"></path></svg>
        </a>
        <form action="{{ route('reports.daily') }}" method="GET" class="flex items-end gap-3">
            <div>
                <label class="block text-xs font-bold text-gray-400 uppercase mb-1">Pilih Tanggal</label>
                <input type="date" name="date" value="{{ $tanggal->format('Y-m-d') }}" class="px-4 py-2 border border-gray-200 rounded-xl bg-white outline-none focus:border-brand-500 text-sm">
            </div>
            <button type="submit" class="bg-gray-100 text-gray-700 px-4 py-2 rounded-xl font-bold hover:bg-gray-200 transition-colors text-sm">Tampilkan</button>
        </form>
        {{-- Navigasi Hari Berikutnya --}}
        @if(!$tanggal->isToday())
        <a href="{{ route('reports.daily', ['date' => $tanggal->copy()->addDay()->format('Y-m-d')]) }}" 
           class="p-2.5 rounded-xl bg-gray-100 hover:bg-gray-200 text-gray-600 transition-colors" title="Hari Berikutnya">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"></path></svg>
        </a>
        @else
        <span class="p-2.5 rounded-xl bg-gray-50 text-gray-300 cursor-not-allowed">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"></path></svg>
        </span>
        @endif
        {{-- Link Cepat --}}
        <div class="hidden sm:flex items-center gap-1 ml-2">
            @if(!$tanggal->isToday())
            <a href="{{ route('reports.daily', ['date' => now()->format('Y-m-d')]) }}" class="px-3 py-1.5 rounded-lg text-xs font-bold bg-brand-50 text-brand-600 hover:bg-brand-100 transition-all">Hari Ini</a>
            @endif
            <a href="{{ route('reports.daily', ['date' => now()->subDay()->format('Y-m-d')]) }}" 
               class="px-3 py-1.5 rounded-lg text-xs font-bold {{ $tanggal->isYesterday() ? 'bg-brand-600 text-white' : 'bg-gray-100 text-gray-600 hover:bg-brand-50 hover:text-brand-600' }} transition-all">Kemarin</a>
        </div>
    </div>
    
    <a href="{{ route('reports.exportDailyPdf', ['date' => $tanggal->format('Y-m-d')]) }}" class="flex items-center gap-2 bg-gray-900 hover:bg-black text-white font-bold text-sm px-6 py-2.5 rounded-xl transition-colors shadow-lg">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
        Export PDF
    </a>
</div>

{{-- Summary Cards dengan Indikator Pertumbuhan --}}
<div class="grid grid-cols-2 md:grid-cols-5 gap-3 lg:gap-4 mb-6 lg:mb-8 no-print">
    <div class="bg-white p-4 lg:p-5 rounded-2xl border border-gray-100 shadow-sm animate-fade-in-up">
        <p class="text-[10px] lg:text-xs font-bold text-gray-400 uppercase mb-1">Pemasukan</p>
        <h3 class="text-xl lg:text-2xl font-bold text-green-600">Rp {{ number_format($totalPenjualan, 0, ',', '.') }}</h3>
        <div class="flex items-center gap-1.5 mt-2">
            <p class="text-xs text-gray-400">{{ $pesanan->count() }} transaksi</p>
            @if($pertumbuhanPenjualan != 0)
            <span class="text-[10px] font-bold px-1.5 py-0.5 rounded {{ $pertumbuhanPenjualan > 0 ? 'bg-green-50 text-green-600' : 'bg-red-50 text-red-500' }}">
                {{ $pertumbuhanPenjualan > 0 ? '↑' : '↓' }} {{ abs($pertumbuhanPenjualan) }}%
            </span>
            @endif
        </div>
    </div>
    <div class="bg-white p-4 lg:p-5 rounded-2xl border border-gray-100 shadow-sm animate-fade-in-up stagger-1">
        <p class="text-[10px] lg:text-xs font-bold text-amber-600 uppercase mb-1">HPP</p>
        <h3 class="text-xl lg:text-2xl font-bold text-amber-600">Rp {{ number_format($totalHpp ?? 0, 0, ',', '.') }}</h3>
        <p class="text-xs text-gray-400 mt-2">Total modal</p>
    </div>
    <div class="bg-white p-4 lg:p-5 rounded-2xl border border-gray-100 shadow-sm animate-fade-in-up stagger-1">
        <p class="text-[10px] lg:text-xs font-bold text-blue-600 uppercase mb-1">Laba Kotor</p>
        <h3 class="text-xl lg:text-2xl font-bold text-blue-600">Rp {{ number_format($labaKotor ?? 0, 0, ',', '.') }}</h3>
        <p class="text-xs text-gray-400 mt-2">Sebelum opex</p>
    </div>
    <div class="bg-white p-4 lg:p-5 rounded-2xl border border-gray-100 shadow-sm animate-fade-in-up stagger-1">
        <p class="text-[10px] lg:text-xs font-bold text-gray-400 uppercase mb-1">Pengeluaran</p>
        <h3 class="text-xl lg:text-2xl font-bold text-red-600">Rp {{ number_format($totalPengeluaran, 0, ',', '.') }}</h3>
        <p class="text-xs text-gray-400 mt-2">{{ $pengeluaran->count() }} catatan</p>
    </div>
    <div class="p-4 lg:p-5 rounded-2xl shadow-lg {{ $labaBersih >= 0 ? 'bg-brand-700 shadow-brand-700/20' : 'bg-red-600 shadow-red-600/20' }} text-white animate-fade-in-up stagger-2">
        <p class="text-[10px] lg:text-xs font-bold opacity-70 uppercase mb-1">Laba Bersih</p>
        <h3 class="text-xl lg:text-2xl font-bold">Rp {{ number_format($labaBersih, 0, ',', '.') }}</h3>
        <p class="text-xs opacity-60 mt-2">{{ $labaBersih >= 0 ? 'Profit hari ini' : '⚠ Rugi hari ini' }}</p>
    </div>
</div>

{{-- Item Breakdown & Performa Kasir --}}
@if($rincianBarang->count() > 0 || $rincianKasir->count() > 0)
<div class="grid grid-cols-1 lg:grid-cols-3 gap-4 lg:gap-6 mb-6 lg:mb-8 no-print">
    {{-- Rincian penjualan per item dengan Doughnut Chart --}}
    <div class="lg:col-span-2 bg-white p-4 lg:p-6 rounded-2xl lg:rounded-3xl border border-gray-100 shadow-sm">
        <h3 class="text-sm font-bold text-gray-900 mb-4">📦 Breakdown Penjualan per Item</h3>
        @if($rincianBarang->count() > 0)
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div class="relative h-48 sm:h-56">
                <canvas id="itemPieChart"></canvas>
            </div>
            <div class="space-y-2 overflow-y-auto max-h-56">
                @foreach($rincianBarang as $i => $item)
                @php
                    $itemHpp = $item->total_hpp ?? 0;
                    $itemLaba = $item->total_amount - $itemHpp;
                    $itemMargin = $item->total_amount > 0 && $itemHpp > 0 ? round(($itemLaba / $item->total_amount) * 100, 1) : null;
                @endphp
                <div class="py-1.5 border-b border-gray-50 last:border-0">
                    <div class="flex items-center gap-2">
                        <span class="w-2.5 h-2.5 rounded-full shrink-0" style="background-color: {{ ['#c2410c','#ea580c','#f97316','#fb923c','#fdba74','#fed7aa','#16a34a','#2563eb','#7c3aed','#db2777'][$i % 10] }}"></span>
                        <span class="text-xs font-medium text-gray-700 truncate flex-1">{{ $item->item->name ?? 'Item Terhapus' }}</span>
                        <span class="text-[10px] font-bold text-gray-400">x{{ $item->total_qty }}</span>
                        <span class="text-xs font-bold text-gray-900">Rp {{ number_format($item->total_amount, 0, ',', '.') }}</span>
                    </div>
                    @if($itemHpp > 0)
                    <div class="flex items-center gap-3 mt-1 ml-4">
                        <span class="text-[10px] text-amber-600">HPP: Rp {{ number_format($itemHpp, 0, ',', '.') }}</span>
                        <span class="text-[10px] text-blue-600 font-semibold">Laba: Rp {{ number_format($itemLaba, 0, ',', '.') }}</span>
                        @if($itemMargin !== null)
                            <span class="text-[10px] font-bold px-1.5 py-0.5 rounded-md {{ $itemMargin >= 40 ? 'bg-green-100 text-green-700' : ($itemMargin >= 20 ? 'bg-amber-100 text-amber-700' : 'bg-red-100 text-red-700') }}">{{ $itemMargin }}%</span>
                        @endif
                    </div>
                    @endif
                </div>
                @endforeach
            </div>
        </div>
        @else
        <p class="text-xs text-gray-400 italic text-center py-6">Belum ada penjualan.</p>
        @endif
    </div>

    {{-- Performa Kasir --}}
    <div class="bg-white p-4 lg:p-6 rounded-2xl lg:rounded-3xl border border-gray-100 shadow-sm">
        <h3 class="text-sm font-bold text-gray-900 mb-4">👤 Performa Kasir</h3>
        @if($rincianKasir->count() > 0)
        <div class="space-y-3">
            @foreach($rincianKasir as $cashier)
            <div class="p-3 bg-gray-50 rounded-xl">
                <div class="flex items-center gap-3 mb-2">
                    <div class="w-8 h-8 rounded-lg bg-brand-100 text-brand-700 flex items-center justify-center font-bold text-sm shrink-0">
                        {{ substr($cashier->user->name ?? 'X', 0, 1) }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-xs font-bold text-gray-900 truncate">{{ $cashier->user->name ?? 'Unknown' }}</p>
                        <p class="text-[10px] text-gray-400">{{ $cashier->total_transactions }} transaksi</p>
                    </div>
                </div>
                <div class="flex justify-between items-center">
                    <div class="flex-1 bg-gray-200 rounded-full h-1.5 mr-3">
                        @php $pct = $totalPenjualan > 0 ? ($cashier->total_amount / $totalPenjualan) * 100 : 0; @endphp
                        <div class="bg-brand-600 h-1.5 rounded-full transition-all" style="width: {{ $pct }}%"></div>
                    </div>
                    <span class="text-xs font-bold text-brand-700 shrink-0">Rp {{ number_format($cashier->total_amount, 0, ',', '.') }}</span>
                </div>
            </div>
            @endforeach
        </div>
        @else
        <p class="text-xs text-gray-400 italic text-center py-6">Belum ada data kasir.</p>
        @endif
    </div>
</div>
@endif

{{-- Format Cetak Laporan Formal (Print-Ready) --}}
<div class="bg-white p-4 lg:p-10 rounded-2xl lg:rounded-3xl border border-gray-100 shadow-sm">
    <div class="text-center mb-8">
        <h1 class="text-2xl font-black text-gray-900 uppercase tracking-tighter">Geprek Legend</h1>
        <p class="text-sm font-bold border-y-2 border-black py-1 inline-block px-8 mt-2 uppercase">Laporan Pemasukan & Logistik Harian</p>
        <div class="flex justify-between mt-6 text-sm font-bold">
            <span>HARI: {{ $tanggal->translatedFormat('l') }}</span>
            <span>TANGGAL: {{ $tanggal->format('d F Y') }}</span>
        </div>
    </div>

    {{-- I. Ringkasan Pemasukan --}}
    <div class="mb-8">
        <h3 class="text-xs font-bold mb-2 uppercase text-gray-500">I. Ringkasan Pemasukan</h3>
        <table class="w-full report-table">
            <thead>
                <tr>
                    <th class="w-16 px-3 py-2 bg-gray-50 border border-gray-200 text-[10px] font-bold text-gray-400 uppercase text-center">NO</th>
                    <th class="px-3 py-2 bg-gray-50 border border-gray-200 text-[10px] font-bold text-gray-400 uppercase text-left">URAIAN TRANSAKSI</th>
                    <th class="w-40 px-3 py-2 bg-gray-50 border border-gray-200 text-[10px] font-bold text-gray-400 uppercase text-center">HITUNGAN</th>
                    <th class="w-48 px-3 py-2 bg-gray-50 border border-gray-200 text-[10px] font-bold text-gray-400 uppercase text-right">JUMLAH (Rp)</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="text-center px-3 py-2 border border-gray-200">1</td>
                    <td class="font-bold px-3 py-2 border border-gray-200">Total Penjualan Kasir</td>
                    <td class="text-center px-3 py-2 border border-gray-200">{{ $pesanan->count() }} Transaksi</td>
                    <td class="text-right font-bold px-3 py-2 border border-gray-200">Rp {{ number_format($totalPenjualan, 0, ',', '.') }}</td>
                </tr>
            </tbody>
        </table>
    </div>

    {{-- II. Laporan Stok Bahan Baku --}}
    <div class="mb-8">
        <h3 class="text-xs font-bold mb-2 uppercase text-gray-500">II. Laporan Stok Bahan Baku</h3>
        <table class="w-full report-table">
            <thead>
                <tr>
                    <th class="px-3 py-2 bg-gray-50 border border-gray-200 text-[10px] font-bold text-gray-400 uppercase text-left">ITEM BAHAN</th>
                    <th class="px-3 py-2 bg-gray-50 border border-gray-200 text-[10px] font-bold text-gray-400 uppercase text-center">STOK MASUK</th>
                    <th class="px-3 py-2 bg-gray-50 border border-gray-200 text-[10px] font-bold text-gray-400 uppercase text-center">STOK AWAL</th>
                    <th class="px-3 py-2 bg-gray-50 border border-gray-200 text-[10px] font-bold text-gray-400 uppercase text-center">TERJUAL</th>
                    <th class="px-3 py-2 bg-brand-50 border border-gray-200 text-[10px] font-bold text-brand-700 uppercase text-center">SISA STOK</th>
                </tr>
            </thead>
            <tbody>
                @forelse($stokBarang as $inv)
                <tr class="{{ $inv->remaining_stock < 5 ? 'bg-red-50/50' : '' }}">
                    <td class="font-bold uppercase text-xs px-3 py-2 border border-gray-200">
                        {{ $inv->item_name }}
                        @if($inv->remaining_stock < 5)
                            <span class="ml-1 text-[9px] text-red-500 font-bold">⚠ KRITIS</span>
                        @endif
                    </td>
                    <td class="text-center px-3 py-2 border border-gray-200 text-blue-600 font-bold">+{{ $inv->new_stock }}</td>
                    <td class="text-center px-3 py-2 border border-gray-200">{{ $inv->old_stock }}</td>
                    <td class="text-center px-3 py-2 border border-gray-200 text-red-500">-{{ $inv->sold }}</td>
                    <td class="text-center font-bold px-3 py-2 border border-gray-200 {{ $inv->remaining_stock < 5 ? 'text-red-600 bg-red-50' : 'bg-brand-50/50 text-brand-700' }} text-lg">{{ $inv->remaining_stock }}</td>
                </tr>
                @empty
                <tr><td colspan="5" class="text-center italic text-gray-400 px-3 py-6 border border-gray-200">Data stok belum diinput untuk tanggal ini.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- III & IV. Pengeluaran --}}
    @php $totalBahan = 0; $totalLogistik = 0; @endphp
    <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-8">
        <div>
            <h3 class="text-xs font-bold mb-2 uppercase text-gray-500 text-center">III. Pengeluaran Bahan</h3>
            <table class="w-full report-table">
                <thead>
                    <tr>
                        <th class="px-3 py-2 bg-gray-50 border border-gray-200 text-[10px] font-bold text-gray-400 uppercase text-left">ITEM</th>
                        <th class="w-32 px-3 py-2 bg-gray-50 border border-gray-200 text-[10px] font-bold text-gray-400 uppercase text-right">HARGA</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($pengeluaran->where('category', 'bahan') as $exp)
                    <tr>
                        <td class="text-xs px-3 py-2 border border-gray-200">{{ $exp->item_name }}</td>
                        <td class="text-right text-xs px-3 py-2 border border-gray-200">Rp {{ number_format($exp->amount, 0, ',', '.') }}</td>
                    </tr>
                    @php $totalBahan += $exp->amount; @endphp
                    @empty
                    <tr><td colspan="2" class="text-center italic text-gray-400 px-3 py-4 border border-gray-200 text-xs">Tidak ada pengeluaran bahan.</td></tr>
                    @endforelse
                    <tr class="bg-gray-50 font-bold">
                        <td class="text-right px-3 py-2 border border-gray-200">TOTAL</td>
                        <td class="text-right text-xs text-red-600 px-3 py-2 border border-gray-200">Rp {{ number_format($totalBahan, 0, ',', '.') }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div>
            <h3 class="text-xs font-bold mb-2 uppercase text-gray-500 text-center">IV. Pengeluaran Logistik</h3>
            <table class="w-full report-table">
                <thead>
                    <tr>
                        <th class="px-3 py-2 bg-gray-50 border border-gray-200 text-[10px] font-bold text-gray-400 uppercase text-left">ITEM</th>
                        <th class="w-32 px-3 py-2 bg-gray-50 border border-gray-200 text-[10px] font-bold text-gray-400 uppercase text-right">HARGA</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($pengeluaran->where('category', 'logistik') as $exp)
                    <tr>
                        <td class="text-xs px-3 py-2 border border-gray-200">{{ $exp->item_name }}</td>
                        <td class="text-right text-xs px-3 py-2 border border-gray-200">Rp {{ number_format($exp->amount, 0, ',', '.') }}</td>
                    </tr>
                    @php $totalLogistik += $exp->amount; @endphp
                    @empty
                    <tr><td colspan="2" class="text-center italic text-gray-400 px-3 py-4 border border-gray-200 text-xs">Tidak ada pengeluaran logistik.</td></tr>
                    @endforelse
                    <tr class="bg-gray-50 font-bold">
                        <td class="text-right px-3 py-2 border border-gray-200">TOTAL</td>
                        <td class="text-right text-xs text-red-600 px-3 py-2 border border-gray-200">Rp {{ number_format($totalLogistik, 0, ',', '.') }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    {{-- Laba Bersih Box --}}
    <div class="border-2 {{ $labaBersih >= 0 ? 'border-green-600 bg-green-50/30' : 'border-red-600 bg-red-50/30' }} p-4 rounded-xl">
        <div class="flex justify-between items-center">
            <div>
                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Total Laba Bersih Hari Ini</p>
                <h2 class="text-3xl font-black {{ $labaBersih >= 0 ? 'text-green-700' : 'text-red-600' }}">
                    Rp {{ number_format($labaBersih, 0, ',', '.') }}
                </h2>
                @if($labaBersih < 0)
                    <p class="text-xs text-red-500 font-bold mt-1">⚠ Hari ini mengalami kerugian</p>
                @endif
            </div>
            <div class="text-right text-xs italic text-gray-400">
                Dicetak oleh: {{ auth()->user()->name }}<br>
                Waktu: {{ now()->format('d/m/Y H:i') }}
            </div>
        </div>
    </div>
</div>

{{-- Pie Chart Script --}}
@if($rincianBarang->count() > 0)
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const pieCtx = document.getElementById('itemPieChart').getContext('2d');
        const colors = ['#c2410c','#ea580c','#f97316','#fb923c','#fdba74','#fed7aa','#16a34a','#2563eb','#7c3aed','#db2777'];
        
        new Chart(pieCtx, {
            type: 'doughnut',
            data: {
                labels: {!! json_encode($rincianBarang->map(fn($i) => $i->item->name ?? 'Terhapus')->values()) !!},
                datasets: [{
                    data: {!! json_encode($rincianBarang->pluck('total_amount')->values()) !!},
                    backgroundColor: colors.slice(0, {{ $rincianBarang->count() }}),
                    borderWidth: 2,
                    borderColor: '#fff',
                    hoverOffset: 8,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '55%',
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: '#1a1a2e',
                        titleFont: { size: 11, weight: 'bold' },
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