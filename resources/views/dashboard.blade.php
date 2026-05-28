@extends('layouts.app')

@section('title', 'Dashboard')
@section('page_title', 'Dashboard Overview')

@section('content')
<div class="grid grid-cols-2 lg:grid-cols-4 gap-3 lg:gap-6 mb-6 lg:mb-8">
    {{-- Omzet Hari Ini --}}
    <div class="bg-white p-4 lg:p-6 rounded-2xl lg:rounded-3xl border border-gray-100 shadow-sm animate-fade-in-up">
        <p class="text-[10px] lg:text-xs font-bold text-gray-400 uppercase tracking-wider mb-1 lg:mb-2">Omzet Hari Ini</p>
        <h3 class="text-lg lg:text-2xl font-bold text-gray-900">Rp {{ number_format($totalPendapatanHariIni, 0, ',', '.') }}</h3>
        <div class="mt-2 lg:mt-4 flex items-center gap-1.5">
            @if($pertumbuhanPendapatan != 0)
                <span class="flex items-center text-xs font-bold px-1.5 py-0.5 rounded {{ $pertumbuhanPendapatan > 0 ? 'bg-green-50 text-green-600' : 'bg-red-50 text-red-500' }}">
                    @if($pertumbuhanPendapatan > 0)
                    <svg class="w-3 h-3 mr-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M5 10l7-7m0 0l7 7m-7-7v18" stroke-width="3"></path></svg>
                    @else
                    <svg class="w-3 h-3 mr-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 14l-7 7m0 0l-7-7m7 7V3" stroke-width="3"></path></svg>
                    @endif
                    {{ abs($pertumbuhanPendapatan) }}%
                </span>
                <span class="text-[10px] text-gray-400">dari kemarin</span>
            @else
                <span class="text-[10px] text-gray-400 italic">Sama dengan kemarin</span>
            @endif
        </div>
    </div>

    {{-- Pesanan Sukses --}}
    <div class="bg-white p-4 lg:p-6 rounded-2xl lg:rounded-3xl border border-gray-100 shadow-sm animate-fade-in-up stagger-1">
        <p class="text-[10px] lg:text-xs font-bold text-gray-400 uppercase tracking-wider mb-1 lg:mb-2">Pesanan Sukses</p>
        <h3 class="text-2xl lg:text-4xl font-bold text-gray-900">{{ $totalPesananHariIni }}</h3>
        <p class="text-[10px] lg:text-xs text-gray-400 mt-2 lg:mt-4 italic">Transaksi diproses hari ini</p>
    </div>

    {{-- Pengeluaran --}}
    <div class="bg-white p-4 lg:p-6 rounded-2xl lg:rounded-3xl border border-gray-100 shadow-sm animate-fade-in-up stagger-2">
        <p class="text-[10px] lg:text-xs font-bold text-red-400 uppercase tracking-wider mb-1 lg:mb-2">Pengeluaran Hari Ini</p>
        <h3 class="text-lg lg:text-2xl font-bold text-red-600">Rp {{ number_format($totalPengeluaranHariIni, 0, ',', '.') }}</h3>
        <p class="text-[10px] lg:text-xs text-gray-400 mt-2 lg:mt-4 italic">
            Laba: <span class="font-bold {{ ($totalPendapatanHariIni - $totalPengeluaranHariIni) >= 0 ? 'text-green-600' : 'text-red-600' }}">
                Rp {{ number_format($totalPendapatanHariIni - $totalPengeluaranHariIni, 0, ',', '.') }}
            </span>
        </p>
    </div>

    {{-- Status --}}
    <div class="bg-white p-4 lg:p-6 rounded-2xl lg:rounded-3xl border border-gray-100 shadow-sm animate-fade-in-up stagger-3">
        <p class="text-[10px] lg:text-xs font-bold text-gray-400 uppercase tracking-wider mb-1 lg:mb-2">Status Kasir</p>
        <div class="flex items-center gap-2 mt-1 lg:mt-2">
            <span class="w-2.5 h-2.5 lg:w-3 lg:h-3 bg-green-500 rounded-full animate-pulse"></span>
            <h3 class="text-lg lg:text-xl font-bold text-gray-900">Online</h3>
        </div>
        <p class="text-[10px] lg:text-xs text-gray-400 mt-3 lg:mt-5">Sistem siap melayani</p>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-4 lg:gap-8 mb-6 lg:mb-8">
    {{-- Weekly Sales Chart --}}
    <div class="lg:col-span-2 bg-white p-4 lg:p-8 rounded-2xl lg:rounded-3xl border border-gray-100 shadow-sm h-[300px] lg:h-[450px]">
        <div class="flex justify-between items-center mb-4 lg:mb-8">
            <h3 class="text-sm lg:text-lg font-bold text-gray-900">Analitik Penjualan Mingguan</h3>
            <span class="text-[10px] lg:text-xs font-bold text-brand-700 bg-brand-50 px-2 lg:px-3 py-1 rounded-lg">7 Hari Terakhir</span>
        </div>
        <div class="relative h-48 lg:h-64">
            <canvas id="salesChart"></canvas>
        </div>
    </div>

    {{-- Recent Transactions --}}
    <div class="bg-white p-4 lg:p-8 rounded-2xl lg:rounded-3xl border border-gray-100 shadow-sm h-[350px] lg:h-[450px] flex flex-col">
        <h3 class="text-sm lg:text-lg font-bold text-gray-900 mb-4 lg:mb-6">Transaksi Terbaru</h3>
        <div class="space-y-4 lg:space-y-6 overflow-y-auto flex-1 pr-2">
            @foreach($transaksiTerbaru as $order)
            <div class="flex items-center gap-3 lg:gap-4 {{ $order->status === 'cancelled' ? 'opacity-50' : '' }}">
                <div class="w-8 h-8 lg:w-10 lg:h-10 rounded-xl {{ $order->status === 'cancelled' ? 'bg-red-50 text-red-500 border-red-100' : 'bg-gray-50 text-brand-700 border-gray-100' }} flex items-center justify-center font-bold border shrink-0 text-xs lg:text-sm">
                    {{ substr($order->customer_name ?? 'P', 0, 1) }}
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-xs lg:text-sm font-bold text-gray-900 truncate">{{ $order->customer_name ?? 'Pelanggan Umum' }}</p>
                    <p class="text-[10px] lg:text-xs text-gray-400">{{ $order->created_at->diffForHumans() }}</p>
                </div>
                <div class="text-right">
                    <p class="text-xs lg:text-sm font-bold {{ $order->status === 'cancelled' ? 'text-red-400 line-through' : 'text-brand-700' }}">Rp{{ number_format($order->total_amount, 0, ',', '.') }}</p>
                    @if($order->status === 'cancelled')
                        <p class="text-[10px] font-bold text-red-400 uppercase">Dibatalkan</p>
                    @else
                        <p class="text-[10px] font-bold text-gray-300 uppercase">{{ $order->payment_method }}</p>
                    @endif
                </div>
            </div>
            @endforeach
        </div>
        @if(auth()->user()->role === 'admin')
        <a href="{{ route('reports.index') }}" class="mt-4 lg:mt-6 text-center text-xs lg:text-sm font-bold text-brand-700 hover:underline">Lihat Semua Laporan</a>
        @endif
    </div>
</div>

{{-- Top Items Today --}}
@if($produkTerlarisHariIni->count() > 0)
<div class="bg-white p-4 lg:p-8 rounded-2xl lg:rounded-3xl border border-gray-100 shadow-sm">
    <div class="flex justify-between items-center mb-4 lg:mb-6">
        <h3 class="text-sm lg:text-lg font-bold text-gray-900">🏆 Produk Terlaris Hari Ini</h3>
        <span class="text-[10px] lg:text-xs font-bold text-brand-700 bg-brand-50 px-2 lg:px-3 py-1 rounded-lg">{{ $produkTerlarisHariIni->sum('total_qty') }} item terjual</span>
    </div>
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3 lg:gap-4">
        @foreach($produkTerlarisHariIni as $i => $topItem)
        <div class="flex items-center gap-3 p-3 lg:p-4 rounded-xl {{ $i === 0 ? 'bg-amber-50 border border-amber-100' : 'bg-gray-50' }}">
            <span class="w-8 h-8 rounded-lg flex items-center justify-center text-sm font-black shrink-0 {{ $i === 0 ? 'bg-amber-200 text-amber-800' : ($i === 1 ? 'bg-gray-200 text-gray-700' : 'bg-orange-100 text-orange-500') }}">
                {{ $i + 1 }}
            </span>
            <div class="flex-1 min-w-0">
                <p class="text-xs font-bold text-gray-900 truncate">{{ $topItem->item->name ?? 'Item Terhapus' }}</p>
                <p class="text-[10px] text-gray-400">{{ $topItem->total_qty }} terjual</p>
                <p class="text-xs font-bold text-brand-700 mt-0.5">Rp {{ number_format($topItem->total_revenue, 0, ',', '.') }}</p>
            </div>
        </div>
        @endforeach
    </div>
</div>
@endif

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const ctx = document.getElementById('salesChart').getContext('2d');
        const isMobile = window.innerWidth < 768;
        
        // Gradient fill
        const gradient = ctx.createLinearGradient(0, 0, 0, isMobile ? 200 : 280);
        gradient.addColorStop(0, 'rgba(9, 121, 70, 0.25)');
        gradient.addColorStop(1, 'rgba(9, 121, 70, 0.02)');

        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: {!! json_encode($hari) !!},
                datasets: [{
                    label: 'Pendapatan (Rp)',
                    data: {!! json_encode($penjualanMingguan) !!},
                    backgroundColor: function(context) {
                        const chart = context.chart;
                        const {ctx, chartArea} = chart;
                        if (!chartArea) return '#097946';
                        const grad = ctx.createLinearGradient(0, chartArea.bottom, 0, chartArea.top);
                        grad.addColorStop(0, '#097946');
                        grad.addColorStop(1, '#0d9e5d');
                        return grad;
                    },
                    borderRadius: isMobile ? 6 : 12,
                    barThickness: isMobile ? 16 : 30,
                    hoverBackgroundColor: '#065f3a',
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                animation: {
                    duration: 1000,
                    easing: 'easeOutQuart'
                },
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: '#1a1a2e',
                        titleFont: { size: 11, weight: 'bold' },
                        bodyFont: { size: 13 },
                        padding: 12,
                        cornerRadius: 10,
                        callbacks: {
                            label: function(ctx) {
                                return 'Rp ' + ctx.parsed.y.toLocaleString('id-ID');
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: { color: '#f1f5f9', drawBorder: false },
                        ticks: {
                            font: { size: isMobile ? 9 : 12 },
                            callback: function(value) {
                                if (value >= 1000000) return 'Rp ' + (value / 1000000).toFixed(1) + 'jt';
                                if (value >= 1000) return 'Rp ' + (value / 1000).toFixed(0) + 'rb';
                                return 'Rp ' + value;
                            }
                        }
                    },
                    x: {
                        grid: { display: false, drawBorder: false },
                        ticks: { font: { size: isMobile ? 9 : 12 } }
                    }
                }
            }
        });
    });
</script>
@endsection