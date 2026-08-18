@extends('layouts.app')

@section('title', 'Rekap Laba Rugi')
@section('page_title', 'Rekapitulasi Laba / Rugi')

@section('content')
{{-- Filter --}}
<div class="bg-white p-4 lg:p-6 rounded-2xl lg:rounded-3xl border border-gray-100 shadow-sm mb-6">
    <form action="{{ route('reports.rekap') }}" method="GET" class="flex flex-col gap-4">
        {{-- Quick Date Presets --}}
        <div class="flex flex-wrap gap-2">
            <span class="text-[10px] font-bold text-gray-400 uppercase self-center mr-1">Cepat:</span>
            <a href="{{ route('reports.rekap', ['start_date' => now()->subDays(6)->format('Y-m-d'), 'end_date' => now()->format('Y-m-d')]) }}" 
               class="px-3 py-1.5 rounded-lg text-xs font-bold transition-all {{ $tanggalMulai->diffInDays($tanggalSelesai) == 6 && $tanggalSelesai->isToday() ? 'bg-brand-600 text-white shadow-md shadow-brand-600/30' : 'bg-gray-100 text-gray-600 hover:bg-brand-50 hover:text-brand-600' }}">
                7 Hari
            </a>
            <a href="{{ route('reports.rekap', ['start_date' => now()->subDays(13)->format('Y-m-d'), 'end_date' => now()->format('Y-m-d')]) }}" 
               class="px-3 py-1.5 rounded-lg text-xs font-bold transition-all {{ $tanggalMulai->diffInDays($tanggalSelesai) == 13 && $tanggalSelesai->isToday() ? 'bg-brand-600 text-white shadow-md shadow-brand-600/30' : 'bg-gray-100 text-gray-600 hover:bg-brand-50 hover:text-brand-600' }}">
                14 Hari
            </a>
            <a href="{{ route('reports.rekap', ['start_date' => now()->startOfMonth()->format('Y-m-d'), 'end_date' => now()->format('Y-m-d')]) }}" 
               class="px-3 py-1.5 rounded-lg text-xs font-bold transition-all {{ $tanggalMulai->isSameDay(now()->startOfMonth()) && $tanggalSelesai->isToday() ? 'bg-brand-600 text-white shadow-md shadow-brand-600/30' : 'bg-gray-100 text-gray-600 hover:bg-brand-50 hover:text-brand-600' }}">
                Bulan Ini
            </a>
            <a href="{{ route('reports.rekap', ['start_date' => now()->subMonth()->startOfMonth()->format('Y-m-d'), 'end_date' => now()->subMonth()->endOfMonth()->format('Y-m-d')]) }}" 
               class="px-3 py-1.5 rounded-lg text-xs font-bold transition-all bg-gray-100 text-gray-600 hover:bg-brand-50 hover:text-brand-600">
                Bulan Lalu
            </a>
        </div>
        {{-- Date Inputs --}}
        <div class="flex flex-col sm:flex-row flex-wrap items-end gap-3 lg:gap-4">
            <div class="flex-1 min-w-0 w-full sm:w-auto">
                <label class="block text-xs font-bold text-gray-400 uppercase mb-2">Mulai Tanggal</label>
                <input type="date" name="start_date" value="{{ $tanggalMulai->format('Y-m-d') }}" class="w-full px-4 py-2 rounded-xl border border-gray-100 bg-gray-50 focus:bg-white focus:border-brand-500 outline-none text-sm">
            </div>
            <div class="flex-1 min-w-0 w-full sm:w-auto">
                <label class="block text-xs font-bold text-gray-400 uppercase mb-2">Sampai Tanggal</label>
                <input type="date" name="end_date" value="{{ $tanggalSelesai->format('Y-m-d') }}" class="w-full px-4 py-2 rounded-xl border border-gray-100 bg-gray-50 focus:bg-white focus:border-brand-500 outline-none text-sm">
            </div>
            <button type="submit" class="w-full sm:w-auto bg-brand-700 text-white px-6 py-2.5 rounded-xl font-bold hover:bg-brand-800 transition-colors shadow-lg shadow-brand-700/20">
                Tampilkan Data
            </button>
        </div>
    </form>
</div>

{{-- Summary Cards --}}
<div class="grid grid-cols-2 lg:grid-cols-6 gap-3 lg:gap-4 mb-6">
    <div class="bg-white p-4 lg:p-6 rounded-2xl lg:rounded-3xl border border-gray-100 shadow-sm text-center animate-fade-in-up">
        <p class="text-[10px] lg:text-xs font-bold text-gray-400 uppercase mb-1">Total Pemasukan</p>
        <h3 class="text-lg lg:text-2xl font-bold text-green-600">Rp {{ number_format($totalPemasukan, 0, ',', '.') }}</h3>
    </div>
    <div class="bg-white p-4 lg:p-6 rounded-2xl lg:rounded-3xl border border-gray-100 shadow-sm text-center animate-fade-in-up stagger-1">
        <p class="text-[10px] lg:text-xs font-bold text-gray-400 uppercase mb-1">Total HPP</p>
        <h3 class="text-lg lg:text-2xl font-bold text-amber-600">Rp {{ number_format($totalHpp ?? 0, 0, ',', '.') }}</h3>
        <p class="text-[10px] text-gray-400 mt-0.5">Modal / HPP</p>
    </div>
    <div class="bg-white p-4 lg:p-6 rounded-2xl lg:rounded-3xl border border-gray-100 shadow-sm text-center animate-fade-in-up stagger-1">
        <p class="text-[10px] lg:text-xs font-bold text-gray-400 uppercase mb-1">Laba Kotor</p>
        <h3 class="text-lg lg:text-2xl font-bold {{ ($totalLabaKotor ?? 0) >= 0 ? 'text-blue-600' : 'text-red-600' }}">Rp {{ number_format($totalLabaKotor ?? 0, 0, ',', '.') }}</h3>
        <p class="text-[10px] text-gray-400 mt-0.5">{{ $marginKotor ?? 0 }}% margin kotor</p>
    </div>
    <div class="bg-white p-4 lg:p-6 rounded-2xl lg:rounded-3xl border border-gray-100 shadow-sm text-center animate-fade-in-up stagger-1">
        <p class="text-[10px] lg:text-xs font-bold text-gray-400 uppercase mb-1">Total Pengeluaran</p>
        <h3 class="text-lg lg:text-2xl font-bold text-red-600">Rp {{ number_format($totalPengeluaran, 0, ',', '.') }}</h3>
        <p class="text-[10px] text-gray-400 mt-0.5">Operasional</p>
    </div>
    <div class="{{ $labaBersih >= 0 ? 'bg-brand-700 shadow-brand-700/20' : 'bg-red-600 shadow-red-600/20' }} p-4 lg:p-6 rounded-2xl lg:rounded-3xl border border-transparent shadow-lg text-center text-white animate-fade-in-up stagger-2">
        <p class="text-[10px] lg:text-xs font-bold opacity-80 uppercase mb-1">{{ $labaBersih >= 0 ? 'Laba Bersih' : '⚠ Kerugian' }}</p>
        <h3 class="text-lg lg:text-2xl font-bold">Rp {{ number_format($labaBersih, 0, ',', '.') }}</h3>
        <p class="text-[10px] opacity-70 mt-0.5">Setelah pengeluaran</p>
    </div>
    <div class="bg-white p-4 lg:p-6 rounded-2xl lg:rounded-3xl border border-gray-100 shadow-sm text-center animate-fade-in-up stagger-3">
        <p class="text-[10px] lg:text-xs font-bold text-gray-400 uppercase mb-1">Profit Margin</p>
        <h3 class="text-lg lg:text-2xl font-bold {{ $profitMargin >= 0 ? 'text-green-600' : 'text-red-600' }}">{{ $profitMargin }}%</h3>
        <p class="text-[10px] text-gray-400 mt-1">{{ $rekapData->count() }} hari aktif</p>
    </div>
</div>

{{-- Best & Worst Day Highlights --}}
@if($bestDay && $worstDay && $rekapData->count() > 1)
<div class="grid grid-cols-1 sm:grid-cols-2 gap-3 lg:gap-4 mb-6">
    <div class="bg-green-50/50 border border-green-100 p-4 rounded-2xl flex items-center gap-3">
        <span class="text-2xl">🏆</span>
        <div>
            <p class="text-[10px] font-bold text-green-600 uppercase">Hari Terbaik</p>
            <p class="text-sm font-bold text-gray-900">{{ $bestDay['tanggal'] }}</p>
            <p class="text-xs text-green-600 font-bold">Laba Rp {{ number_format($bestDay['total'], 0, ',', '.') }}</p>
        </div>
    </div>
    <div class="bg-red-50/50 border border-red-100 p-4 rounded-2xl flex items-center gap-3">
        <span class="text-2xl">📉</span>
        <div>
            <p class="text-[10px] font-bold text-red-500 uppercase">Hari Terendah</p>
            <p class="text-sm font-bold text-gray-900">{{ $worstDay['tanggal'] }}</p>
            <p class="text-xs {{ $worstDay['total'] >= 0 ? 'text-gray-600' : 'text-red-600' }} font-bold">Laba Rp {{ number_format($worstDay['total'], 0, ',', '.') }}</p>
        </div>
    </div>
</div>
@endif

{{-- Chart --}}
@if($rekapData->count() > 0)
<div class="bg-white p-4 lg:p-8 rounded-2xl lg:rounded-3xl border border-gray-100 shadow-sm mb-6">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-2">
        <h3 class="text-sm lg:text-lg font-bold text-gray-900">Grafik Pemasukan vs Pengeluaran</h3>
        <span class="text-[10px] lg:text-xs font-bold text-brand-700 bg-brand-50 px-3 py-1 rounded-lg">{{ $tanggalMulai->format('d M') }} - {{ $tanggalSelesai->format('d M Y') }}</span>
    </div>
    <div class="relative h-56 lg:h-80">
        <canvas id="rekapChart"></canvas>
    </div>
</div>
@endif

{{-- Table --}}
<div class="bg-white rounded-2xl lg:rounded-3xl border border-gray-100 overflow-hidden shadow-sm">
    <div class="p-4 lg:p-6 border-b border-gray-100 bg-gray-50 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3">
        <h2 class="text-sm lg:text-lg font-bold text-gray-900">Detail Harian</h2>
        <a href="{{ route('reports.exportRekapPdf', ['start_date' => $tanggalMulai->format('Y-m-d'), 'end_date' => $tanggalSelesai->format('Y-m-d')]) }}" class="flex items-center gap-2 bg-gray-900 hover:bg-black text-white font-bold text-xs px-4 py-2 rounded-xl transition-colors shadow-lg">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
            Export PDF
        </a>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-left min-w-[500px]">
            <thead class="bg-gray-100 border-b border-gray-200">
                <tr>
                    <th class="px-4 lg:px-6 py-3 lg:py-4 text-[10px] lg:text-xs font-bold text-gray-700 uppercase">Hari / Tanggal</th>
                    <th class="px-4 lg:px-6 py-3 lg:py-4 text-[10px] lg:text-xs font-bold text-gray-700 uppercase text-right">Pemasukan</th>
                    <th class="px-4 lg:px-6 py-3 lg:py-4 text-[10px] lg:text-xs font-bold text-amber-600 uppercase text-right">HPP</th>
                    <th class="px-4 lg:px-6 py-3 lg:py-4 text-[10px] lg:text-xs font-bold text-blue-600 uppercase text-right">Laba Kotor</th>
                    <th class="px-4 lg:px-6 py-3 lg:py-4 text-[10px] lg:text-xs font-bold text-gray-700 uppercase text-right">Pengeluaran</th>
                    <th class="px-4 lg:px-6 py-3 lg:py-4 text-[10px] lg:text-xs font-bold text-gray-700 uppercase text-right">Laba Bersih</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($rekapData as $data)
                @php
                    $isBest = $bestDay && $data['tanggal'] === $bestDay['tanggal'] && $rekapData->count() > 1;
                @endphp
                <tr class="hover:bg-gray-50 transition-colors {{ $data['total'] < 0 ? 'bg-red-50/50' : '' }} {{ $isBest ? 'bg-green-50/30' : '' }}">
                    <td class="px-4 lg:px-6 py-3 lg:py-4 text-xs lg:text-sm font-bold text-gray-900">
                        {{ $data['tanggal'] }}
                        @if($isBest) <span class="text-[10px] text-amber-500">🏆</span> @endif
                    </td>
                    <td class="px-4 lg:px-6 py-3 lg:py-4 text-xs lg:text-sm font-semibold text-green-600 text-right">
                        Rp {{ number_format($data['pemasukan'], 0, ',', '.') }}
                    </td>
                    <td class="px-4 lg:px-6 py-3 lg:py-4 text-xs lg:text-sm font-medium text-amber-600 text-right">
                        @if(($data['hpp'] ?? 0) > 0)
                            Rp {{ number_format($data['hpp'], 0, ',', '.') }}
                        @else
                            <span class="text-gray-300">-</span>
                        @endif
                    </td>
                    <td class="px-4 lg:px-6 py-3 lg:py-4 text-xs lg:text-sm font-semibold text-blue-600 text-right">
                        @if(($data['hpp'] ?? 0) > 0)
                            Rp {{ number_format($data['laba_kotor'] ?? 0, 0, ',', '.') }}
                        @else
                            <span class="text-gray-300 text-xs">-</span>
                        @endif
                    </td>
                    <td class="px-4 lg:px-6 py-3 lg:py-4 text-xs lg:text-sm font-semibold text-red-600 text-right">
                        Rp {{ number_format($data['pengeluaran'], 0, ',', '.') }}
                    </td>
                    <td class="px-4 lg:px-6 py-3 lg:py-4 text-xs lg:text-sm font-bold text-right {{ $data['total'] >= 0 ? 'text-gray-900' : 'text-red-600' }}">
                        {{ $data['total'] < 0 ? '- ' : '' }}Rp {{ number_format(abs($data['total']), 0, ',', '.') }}
                        @if($data['total'] < 0) <span class="text-[10px]">⚠</span> @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-12 text-center text-gray-400 italic">Tidak ada data transaksi atau pengeluaran pada rentang waktu ini.</td>
                </tr>
                @endforelse
                
                @if($rekapData->count() > 0)
                <tr class="bg-gray-50 border-t-2 border-gray-200">
                    <td class="px-4 lg:px-6 py-3 lg:py-4 text-xs lg:text-sm font-bold text-gray-900 uppercase">Total Keseluruhan</td>
                    <td class="px-4 lg:px-6 py-3 lg:py-4 text-xs lg:text-sm font-bold text-green-600 text-right">Rp {{ number_format($totalPemasukan, 0, ',', '.') }}</td>
                    <td class="px-4 lg:px-6 py-3 lg:py-4 text-xs lg:text-sm font-bold text-amber-600 text-right">Rp {{ number_format($totalHpp ?? 0, 0, ',', '.') }}</td>
                    <td class="px-4 lg:px-6 py-3 lg:py-4 text-xs lg:text-sm font-bold text-blue-600 text-right">Rp {{ number_format($totalLabaKotor ?? 0, 0, ',', '.') }}</td>
                    <td class="px-4 lg:px-6 py-3 lg:py-4 text-xs lg:text-sm font-bold text-red-600 text-right">Rp {{ number_format($totalPengeluaran, 0, ',', '.') }}</td>
                    <td class="px-4 lg:px-6 py-3 lg:py-4 text-xs lg:text-sm font-bold {{ $labaBersih >= 0 ? 'text-brand-700' : 'text-red-600' }} text-right">Rp {{ number_format($labaBersih, 0, ',', '.') }}</td>
                </tr>
                @endif
            </tbody>
        </table>
    </div>
</div>

@if($rekapData->count() > 0)
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const ctx = document.getElementById('rekapChart').getContext('2d');
        const isMobile = window.innerWidth < 768;

        // Gradient for laba line
        const labaGradient = ctx.createLinearGradient(0, 0, 0, isMobile ? 220 : 320);
        labaGradient.addColorStop(0, 'rgba(194, 65, 12, 0.15)');
        labaGradient.addColorStop(1, 'rgba(194, 65, 12, 0.01)');

        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: {!! json_encode($chartLabels) !!},
                datasets: [
                    {
                        label: 'Pemasukan',
                        data: {!! json_encode($chartPemasukan) !!},
                        backgroundColor: 'rgba(22, 163, 74, 0.8)',
                        borderRadius: isMobile ? 4 : 8,
                        barThickness: isMobile ? 10 : 20,
                        order: 2,
                    },
                    {
                        label: 'Pengeluaran',
                        data: {!! json_encode($chartPengeluaran) !!},
                        backgroundColor: 'rgba(239, 68, 68, 0.8)',
                        borderRadius: isMobile ? 4 : 8,
                        barThickness: isMobile ? 10 : 20,
                        order: 3,
                    },
                    {
                        label: 'Laba Bersih',
                        data: {!! json_encode($chartLaba) !!},
                        type: 'line',
                        borderColor: '#c2410c',
                        backgroundColor: labaGradient,
                        fill: true,
                        tension: 0.4,
                        pointRadius: isMobile ? 3 : 5,
                        pointBackgroundColor: '#c2410c',
                        pointBorderColor: '#fff',
                        pointBorderWidth: 2,
                        pointHoverRadius: 7,
                        borderWidth: 2.5,
                        order: 1,
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { 
                        position: 'top',
                        labels: { 
                            usePointStyle: true, 
                            pointStyle: 'circle',
                            padding: isMobile ? 12 : 20,
                            font: { size: isMobile ? 10 : 12, weight: 'bold' }
                        }
                    },
                    tooltip: {
                        backgroundColor: '#1a1a2e',
                        titleFont: { size: 11, weight: 'bold' },
                        bodyFont: { size: 12 },
                        padding: 12,
                        cornerRadius: 10,
                        callbacks: {
                            label: function(ctx) {
                                const val = ctx.parsed.y;
                                const sign = val < 0 ? '- ' : '';
                                return ctx.dataset.label + ': ' + sign + 'Rp ' + Math.abs(val).toLocaleString('id-ID');
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: { color: '#f1f5f9', drawBorder: false },
                        ticks: {
                            font: { size: isMobile ? 9 : 11 },
                            callback: function(value) {
                                if (Math.abs(value) >= 1000000) return 'Rp ' + (value / 1000000).toFixed(1) + 'jt';
                                if (Math.abs(value) >= 1000) return 'Rp ' + (value / 1000).toFixed(0) + 'rb';
                                return 'Rp ' + value;
                            }
                        }
                    },
                    x: {
                        grid: { display: false },
                        ticks: { font: { size: isMobile ? 8 : 11 }, maxRotation: 45 }
                    }
                },
                interaction: { intersect: false, mode: 'index' }
            }
        });
    });
</script>
@endif
@endsection