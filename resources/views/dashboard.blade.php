@extends('layouts.app')

@section('title', 'Dashboard')
@section('page_title', 'Ringkasan hari ini')

@section('content')
@php $hasilBersihHariIni = $totalPendapatanHariIni - $totalPengeluaranHariIni; $labaBersih = $hasilBersihHariIni; @endphp

<div class="card rise overflow-hidden mb-5">
    <div class="grid grid-cols-2 lg:grid-cols-4 divide-x divide-y lg:divide-y-0 divide-[#f1ebe0]">
        <div class="p-5 lg:p-6">
            <p class="text-[13px] text-[#8a8179]">Omzet hari ini</p>
            <p class="font-display text-xl lg:text-[26px] font-semibold tnum tracking-tight mt-1.5">Rp {{ number_format($totalPendapatanHariIni, 0, ',', '.') }}</p>
            <p class="text-xs mt-2 {{ $pertumbuhanPendapatan > 0 ? 'text-[#1a6b3c]' : ($pertumbuhanPendapatan < 0 ? 'text-[#931b13]' : 'text-[#8a8179]') }}">
                @if($pertumbuhanPendapatan != 0)
                    {{ $pertumbuhanPendapatan > 0 ? '+' : '' }}{{ $pertumbuhanPendapatan }}% dibanding kemarin
                @else
                    Sama seperti kemarin
                @endif
            </p>
        </div>
        <div class="p-5 lg:p-6">
            <p class="text-[13px] text-[#8a8179]">Pesanan sukses</p>
            <p class="font-display text-xl lg:text-[26px] font-semibold tnum tracking-tight mt-1.5">{{ $totalPesananHariIni }} <span class="text-sm font-sans font-normal text-[#8a8179]">transaksi</span></p>
            <p class="text-xs text-[#8a8179] mt-2">Diproses hari ini</p>
        </div>
        <div class="p-5 lg:p-6">
            <p class="text-[13px] text-[#8a8179]">Pengeluaran</p>
            <p class="font-display text-xl lg:text-[26px] font-semibold tnum tracking-tight mt-1.5">Rp {{ number_format($totalPengeluaranHariIni, 0, ',', '.') }}</p>
            <p class="text-xs text-[#8a8179] mt-2">Biaya operasional</p>
        </div>
        <div class="p-5 lg:p-6 bg-[#17110c] text-stone-200">
            <p class="text-[13px] text-stone-400">Hasil bersih</p>
            <p class="font-display text-xl lg:text-[26px] font-semibold tnum tracking-tight mt-1.5 text-[#f7f1e6]">Rp {{ number_format(abs($hasilBersihHariIni), 0, ',', '.') }}</p>
            <p class="text-xs mt-2 {{ $hasilBersihHariIni >= 0 ? 'text-[#e8c96a]' : 'text-[#e68373]' }}">{{ $hasilBersihHariIni >= 0 ? 'Untung hari ini' : 'Rugi hari ini' }}</p>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-5 mb-5">
    {{-- Weekly Sales Chart --}}
    <div class="lg:col-span-2 card card-pad rise">
        <div class="flex justify-between items-baseline mb-5">
            <div>
                <h3 class="font-display text-lg font-semibold tracking-tight">Penjualan 7 hari terakhir</h3>
                <p class="text-[13px] text-[#8a8179] mt-0.5">Pendapatan kotor per hari</p>
            </div>
        </div>
        <div class="relative h-52 lg:h-64">
            <canvas id="salesChart"></canvas>
        </div>
    </div>

    {{-- Recent Transactions --}}
    <div class="card card-pad rise flex flex-col">
        <div class="mb-4">
            <h3 class="font-display text-lg font-semibold tracking-tight">Transaksi terbaru</h3>
            <p class="text-[13px] text-[#8a8179] mt-0.5">Aktivitas kasir terkini</p>
        </div>
        <div class="divide-y divide-[#f1ebe0] overflow-y-auto flex-1 -mx-1 pr-1">
            @foreach($transaksiTerbaru as $order)
            <div class="flex items-center gap-3 py-3 {{ $order->status === 'cancelled' ? 'opacity-55' : '' }}">
                <div class="w-9 h-9 rounded-full {{ $order->status === 'cancelled' ? 'bg-[#fdf0ee] text-[#931b13]' : 'bg-[#f4eee2] text-[#57504a]' }} flex items-center justify-center font-display font-semibold text-sm shrink-0">
                    {{ substr($order->customer_name ?? 'P', 0, 1) }}
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-medium text-[#1c1410] truncate">{{ $order->customer_name ?? 'Pelanggan umum' }}</p>
                    <p class="text-xs text-[#8a8179]">{{ $order->created_at->diffForHumans() }} · {{ $order->payment_method }}</p>
                </div>
                <p class="text-sm font-semibold tnum {{ $order->status === 'cancelled' ? 'text-[#931b13] line-through' : 'text-[#1c1410]' }}">Rp{{ number_format($order->total_amount, 0, ',', '.') }}</p>
            </div>
            @endforeach
        </div>
        @if(auth()->user()->role === 'admin')
        <a href="{{ route('reports.index') }}" class="mt-4 pt-4 border-t border-[#f1ebe0] text-sm font-medium text-[#931b13] hover:text-[#751510]">Buka laporan kasir</a>
        @endif
    </div>
</div>

{{-- Top Items Today --}}
@if($produkTerlarisHariIni->count() > 0)
<div class="card card-pad rise">
    <div class="flex justify-between items-baseline mb-4">
        <div>
            <h3 class="font-display text-lg font-semibold tracking-tight">Terlaris hari ini</h3>
            <p class="text-[13px] text-[#8a8179] mt-0.5">{{ $produkTerlarisHariIni->sum('total_qty') }} porsi terjual</p>
        </div>
    </div>
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">
        @foreach($produkTerlarisHariIni as $i => $topItem)
        <div class="flex items-center gap-3 px-4 py-3.5 rounded-xl border border-[#f1ebe0] bg-white">
            <span class="font-display text-xl font-semibold tnum w-7 shrink-0 {{ $i === 0 ? 'text-[#c9a227]' : 'text-[#b3a897]' }}">{{ $i + 1 }}</span>
            <div class="flex-1 min-w-0">
                <p class="text-[13px] font-medium text-[#1c1410] truncate">{{ $topItem->item->name ?? 'Item terhapus' }}</p>
                <p class="text-xs text-[#8a8179] mt-0.5">{{ $topItem->total_qty }} terjual · <span class="tnum font-medium text-[#1c1410]">Rp {{ number_format($topItem->total_revenue, 0, ',', '.') }}</span></p>
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

        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: {!! json_encode($hari) !!},
                datasets: [{
                    label: 'Pendapatan (Rp)',
                    data: {!! json_encode($penjualanMingguan) !!},
                    backgroundColor: '#1c1410',
                    hoverBackgroundColor: '#931b13',
                    borderRadius: isMobile ? 5 : 7,
                    barThickness: isMobile ? 16 : 30,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                animation: { duration: 700, easing: 'easeOutQuart' },
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: '#17110c',
                        titleFont: { size: 11, weight: '500' },
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
                        grid: { color: '#f1ebe0' },
                        border: { display: false },
                        ticks: {
                            font: { size: isMobile ? 10 : 11 },
                            color: '#8a8179',
                            callback: function(value) {
                                if (value >= 1000000) return (value / 1000000).toFixed(1) + ' jt';
                                if (value >= 1000) return (value / 1000).toFixed(0) + ' rb';
                                return value;
                            }
                        }
                    },
                    x: {
                        grid: { display: false },
                        border: { display: false },
                        ticks: { font: { size: isMobile ? 10 : 11 }, color: '#57504a' }
                    }
                }
            }
        });
    });
</script>
@endsection
