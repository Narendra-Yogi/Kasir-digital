@extends('layouts.app')

@section('title', 'Laporan Penjualan')
@section('page_title', 'Laporan Penjualan')

@section('content')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

@if(session('success'))
<script>
    document.addEventListener("DOMContentLoaded", function() {
        Swal.fire({
            icon: 'success',
            title: 'Berhasil!',
            text: '{!! session("success") !!}',
            confirmButtonColor: '#ea580c',
        });
    });
</script>
@endif

@if(session('error'))
<script>
    document.addEventListener("DOMContentLoaded", function() {
        Swal.fire({
            icon: 'error',
            title: 'Gagal!',
            text: '{!! session("error") !!}',
            confirmButtonColor: '#d33',
        });
    });
</script>
@endif

{{-- Filter --}}
<div class="no-print flex flex-col gap-4 lg:gap-6 mb-6 lg:mb-8">
    <div class="bg-white p-4 lg:p-6 rounded-2xl lg:rounded-3xl border border-gray-100 shadow-sm">
        <form action="{{ route('reports.index') }}" method="GET" class="flex flex-col gap-4">
            {{-- Quick Date Presets --}}
            @php
                $isBulanLalu = $tanggalMulai->isSameDay(now()->subMonth()->startOfMonth()) && $tanggalSelesai->isSameDay(now()->subMonth()->endOfMonth());
            @endphp
            <div class="flex flex-wrap gap-2">
                <span class="text-[10px] font-bold text-gray-400 uppercase self-center mr-1">Cepat:</span>

                {{-- Hari Ini --}}
                <a href="{{ route('reports.index', ['start_date' => now()->format('Y-m-d'), 'end_date' => now()->format('Y-m-d')]) }}"
                   @if($tanggalMulai->format('Y-m-d') === now()->format('Y-m-d') && $tanggalSelesai->format('Y-m-d') === now()->format('Y-m-d'))
                       class="px-3 py-1.5 rounded-lg text-xs font-bold transition-all bg-brand-600 text-white shadow-md"
                   @else
                       class="px-3 py-1.5 rounded-lg text-xs font-bold transition-all bg-gray-100 text-gray-600 hover:bg-brand-50 hover:text-brand-600"
                   @endif>
                    Hari Ini
                </a>

                {{-- 7 Hari --}}
                <a href="{{ route('reports.index', ['start_date' => now()->subDays(6)->format('Y-m-d'), 'end_date' => now()->format('Y-m-d')]) }}"
                   @if($tanggalMulai->format('Y-m-d') === now()->subDays(6)->format('Y-m-d') && $tanggalSelesai->format('Y-m-d') === now()->format('Y-m-d'))
                       class="px-3 py-1.5 rounded-lg text-xs font-bold transition-all bg-brand-600 text-white shadow-md"
                   @else
                       class="px-3 py-1.5 rounded-lg text-xs font-bold transition-all bg-gray-100 text-gray-600 hover:bg-brand-50 hover:text-brand-600"
                   @endif>
                    7 Hari
                </a>

                {{-- Bulan Ini --}}
                <a href="{{ route('reports.index', ['start_date' => now()->startOfMonth()->format('Y-m-d'), 'end_date' => now()->format('Y-m-d')]) }}"
                   @if($tanggalMulai->format('Y-m-d') === now()->startOfMonth()->format('Y-m-d') && $tanggalSelesai->format('Y-m-d') === now()->format('Y-m-d'))
                       class="px-3 py-1.5 rounded-lg text-xs font-bold transition-all bg-brand-600 text-white shadow-md"
                   @else
                       class="px-3 py-1.5 rounded-lg text-xs font-bold transition-all bg-gray-100 text-gray-600 hover:bg-brand-50 hover:text-brand-600"
                   @endif>
                    Bulan Ini
                </a>

                {{-- Bulan Lalu --}}
                <a href="{{ route('reports.index', ['start_date' => now()->subMonth()->startOfMonth()->format('Y-m-d'), 'end_date' => now()->subMonth()->endOfMonth()->format('Y-m-d')]) }}"
                   @if($tanggalMulai->format('Y-m-d') === now()->subMonth()->startOfMonth()->format('Y-m-d') && $tanggalSelesai->format('Y-m-d') === now()->subMonth()->endOfMonth()->format('Y-m-d'))
                       class="px-3 py-1.5 rounded-lg text-xs font-bold transition-all bg-brand-600 text-white shadow-md"
                   @else
                       class="px-3 py-1.5 rounded-lg text-xs font-bold transition-all bg-gray-100 text-gray-600 hover:bg-brand-50 hover:text-brand-600"
                   @endif>
                    Bulan Lalu
                </a>
            </div>
            {{-- Date Inputs --}}
            <div class="flex flex-col sm:flex-row flex-wrap items-end gap-3 lg:gap-4">
                <div class="flex-1 min-w-0 w-full sm:w-auto">
                    <label class="block text-xs font-bold text-gray-400 uppercase mb-2">Mulai Dari</label>
                    <input type="date" name="start_date" value="{{ $tanggalMulai->format('Y-m-d') }}" class="w-full px-4 py-2 rounded-xl border border-gray-100 bg-gray-50 focus:bg-white focus:border-brand-500 outline-none text-sm">
                </div>
                <div class="flex-1 min-w-0 w-full sm:w-auto">
                    <label class="block text-xs font-bold text-gray-400 uppercase mb-2">Sampai Dengan</label>
                    <input type="date" name="end_date" value="{{ $tanggalSelesai->format('Y-m-d') }}" class="w-full px-4 py-2 rounded-xl border border-gray-100 bg-gray-50 focus:bg-white focus:border-brand-500 outline-none text-sm">
                </div>
                <button type="submit" class="w-full sm:w-auto bg-brand-700 text-white px-6 py-2.5 rounded-xl font-bold hover:bg-brand-800 transition-colors shadow-lg shadow-brand-700/20">
                    Filter Data
                </button>
            </div>
        </form>
    </div>

    {{-- Summary Cards --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 lg:gap-4">
        <div class="bg-brand-700 p-4 lg:p-6 rounded-2xl lg:rounded-3xl shadow-lg shadow-brand-700/20 text-white flex flex-col justify-center animate-fade-in-up">
            <p class="text-[10px] lg:text-xs font-bold opacity-70 uppercase tracking-widest mb-1">Total Pendapatan</p>
            <h3 class="text-lg lg:text-2xl font-bold" data-counter="{{ $totalPendapatan }}">Rp {{ number_format($totalPendapatan, 0, ',', '.') }}</h3>
            <p class="text-[10px] mt-2 opacity-60 italic">{{ $totalTransaksi }} Transaksi Berhasil</p>
        </div>
        <div class="bg-white p-4 lg:p-6 rounded-2xl lg:rounded-3xl border border-gray-100 shadow-sm flex flex-col justify-center animate-fade-in-up stagger-1">
            <p class="text-[10px] lg:text-xs font-bold text-gray-400 uppercase tracking-widest mb-1">Rata-rata Transaksi</p>
            <h3 class="text-lg lg:text-2xl font-bold text-gray-900" data-counter="{{ $rataRataTransaksi }}">Rp {{ number_format($rataRataTransaksi, 0, ',', '.') }}</h3>
            <p class="text-[10px] mt-2 text-gray-400 italic">Per transaksi</p>
        </div>
        <div class="bg-white p-4 lg:p-6 rounded-2xl lg:rounded-3xl border border-gray-100 shadow-sm flex flex-col justify-center animate-fade-in-up stagger-2">
            <p class="text-[10px] lg:text-xs font-bold text-gray-400 uppercase tracking-widest mb-1">Metode Bayar</p>
            <div class="flex items-center gap-2 mt-1">
                <span class="px-2 py-0.5 rounded bg-orange-50 text-orange-600 text-[10px] font-bold">TUNAI {{ $jumlahTunai }}</span>
                <span class="px-2 py-0.5 rounded bg-blue-50 text-blue-600 text-[10px] font-bold">QRIS {{ $jumlahQris }}</span>
            </div>
            <p class="text-[10px] mt-2 text-gray-400 italic">Breakdown metode</p>
        </div>
        <div class="bg-red-500 p-4 lg:p-6 rounded-2xl lg:rounded-3xl shadow-lg shadow-red-500/20 text-white flex flex-col justify-center animate-fade-in-up stagger-3">
            <p class="text-[10px] lg:text-xs font-bold opacity-70 uppercase tracking-widest mb-1">Dibatalkan</p>
            <h3 class="text-lg lg:text-2xl font-bold">{{ $totalDibatalkan }}</h3>
            <p class="text-[10px] mt-2 opacity-60 italic">Transaksi dibatalkan</p>
        </div>
    </div>

    {{-- Mini Revenue Chart + Top Items --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 lg:gap-6">
        {{-- Mini Revenue Chart --}}
        <div class="lg:col-span-2 bg-white p-4 lg:p-6 rounded-2xl lg:rounded-3xl border border-gray-100 shadow-sm">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-sm font-bold text-gray-900">Tren Pendapatan Harian</h3>
                <span class="text-[10px] font-bold text-brand-700 bg-brand-50 px-2 py-1 rounded-lg">{{ $tanggalMulai->format('d M') }} - {{ $tanggalSelesai->format('d M Y') }}</span>
            </div>
            <div class="relative h-48 lg:h-56">
                <canvas id="revenueChart"></canvas>
            </div>
        </div>
        {{-- Top Items --}}
        <div class="bg-white p-4 lg:p-6 rounded-2xl lg:rounded-3xl border border-gray-100 shadow-sm">
            <h3 class="text-sm font-bold text-gray-900 mb-4">🏆 Laporan Produk Terlaris</h3>
            @if($produkTerlaris->count() > 0)
            <div class="space-y-3">
                @foreach($produkTerlaris as $i => $topItem)
                <div class="flex items-center gap-3">
                    <span class="w-7 h-7 rounded-lg flex items-center justify-center text-xs font-black shrink-0 {{ $i === 0 ? 'bg-amber-100 text-amber-700' : ($i === 1 ? 'bg-gray-100 text-gray-600' : 'bg-orange-50 text-orange-400') }}">
                        {{ $i + 1 }}
                    </span>
                    <div class="flex-1 min-w-0">
                        <p class="text-xs font-bold text-gray-900 truncate">{{ $topItem->item->name ?? 'Item Terhapus' }}</p>
                        <p class="text-[10px] text-gray-400">{{ $topItem->total_qty }} terjual</p>
                    </div>
                    <span class="text-xs font-bold text-brand-700">Rp {{ number_format($topItem->total_revenue, 0, ',', '.') }}</span>
                </div>
                @endforeach
            </div>
            @else
            <p class="text-xs text-gray-400 italic text-center py-6">Belum ada data penjualan.</p>
            @endif
        </div>
    </div>

    {{-- Export & Search Bar --}}
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3">
        <div class="relative w-full sm:w-72">
            <input type="text" id="searchInput" placeholder="Cari invoice, pelanggan..." class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-gray-100 bg-white text-sm outline-none focus:border-brand-500 focus:ring-2 focus:ring-brand-100 transition-all">
            <svg class="w-4 h-4 text-gray-400 absolute left-3.5 top-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
        </div>
        <div class="flex items-center gap-2">
            {{-- Status filter pills --}}
            <button type="button" data-filter="all" class="filter-pill active px-3 py-1.5 rounded-lg text-xs font-bold bg-gray-900 text-white transition-all">Semua</button>
            <button type="button" data-filter="success" class="filter-pill px-3 py-1.5 rounded-lg text-xs font-bold bg-gray-100 text-gray-600 hover:bg-green-50 hover:text-green-600 transition-all">Sukses</button>
            <button type="button" data-filter="cancelled" class="filter-pill px-3 py-1.5 rounded-lg text-xs font-bold bg-gray-100 text-gray-600 hover:bg-red-50 hover:text-red-600 transition-all">Batal</button>
            <a href="{{ route('reports.exportSalesPdf', ['start_date' => $tanggalMulai->format('Y-m-d'), 'end_date' => $tanggalSelesai->format('Y-m-d')]) }}" class="flex items-center gap-2 bg-gray-900 hover:bg-black text-white font-bold text-xs lg:text-sm px-5 py-2.5 rounded-xl transition-colors shadow-lg">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                Export PDF
            </a>
        </div>
    </div>
</div>

{{-- Tabel Laporan --}}
<div class="bg-white rounded-2xl lg:rounded-3xl border border-gray-100 shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse min-w-[700px]" id="salesTable">
            <thead class="bg-gray-50 border-b border-gray-100 sticky top-0 z-10">
                <tr>
                    <th class="px-3 lg:px-6 py-3 lg:py-4 text-[10px] lg:text-xs font-bold text-gray-400 uppercase">No</th>
                    <th class="px-3 lg:px-6 py-3 lg:py-4 text-[10px] lg:text-xs font-bold text-gray-400 uppercase">Waktu & Invoice</th>
                    <th class="px-3 lg:px-6 py-3 lg:py-4 text-[10px] lg:text-xs font-bold text-gray-400 uppercase">Pelanggan</th>
                    <th class="px-3 lg:px-6 py-3 lg:py-4 text-[10px] lg:text-xs font-bold text-gray-400 uppercase">Detail Pesanan</th>
                    <th class="px-3 lg:px-6 py-3 lg:py-4 text-[10px] lg:text-xs font-bold text-gray-400 uppercase">Metode</th>
                    <th class="px-3 lg:px-6 py-3 lg:py-4 text-[10px] lg:text-xs font-bold text-gray-400 uppercase">Status</th>
                    <th class="px-3 lg:px-6 py-3 lg:py-4 text-[10px] lg:text-xs font-bold text-gray-400 uppercase text-right">Total</th>
                    <th class="px-3 lg:px-6 py-3 lg:py-4 text-[10px] lg:text-xs font-bold text-gray-400 uppercase text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($pesanan as $index => $order)
                <tr class="hover:bg-gray-50/50 transition-colors {{ $order->status === 'cancelled' ? 'opacity-60' : '' }} table-row" 
                    data-status="{{ $order->status }}"
                    data-search="{{ strtolower($order->invoice_number . ' ' . ($order->customer_name ?? 'Pelanggan Umum') . ' ' . $order->payment_method) }}">
                    <td class="px-3 lg:px-6 py-3 lg:py-4 text-xs lg:text-sm text-gray-500">{{ $pesanan->firstItem() + $index }}</td>
                    <td class="px-3 lg:px-6 py-3 lg:py-4">
                        <p class="text-xs lg:text-sm font-bold text-gray-900">{{ $order->invoice_number }}</p>
                        <p class="text-[10px] text-gray-400 uppercase">{{ $order->created_at->format('d M Y, H:i') }}</p>
                    </td>
                    <td class="px-3 lg:px-6 py-3 lg:py-4">
                        <p class="text-xs lg:text-sm font-medium text-gray-700">{{ $order->customer_name ?? 'Pelanggan Umum' }}</p>
                    </td>
                    <td class="px-3 lg:px-6 py-3 lg:py-4">
                        <div class="max-w-[200px] lg:max-w-[250px]">
                            @foreach($order->orderDetails as $detail)
                                <span class="text-[10px] lg:text-xs text-gray-500">{{ $detail->item->name }} (x{{ $detail->quantity }}){{ !$loop->last ? ',' : '' }} </span>
                            @endforeach
                        </div>
                    </td>
                    <td class="px-3 lg:px-6 py-3 lg:py-4 text-center">
                        <span class="px-2 py-1 rounded text-[10px] font-bold uppercase {{ $order->payment_method == 'qris' ? 'bg-blue-50 text-blue-600' : 'bg-orange-50 text-orange-600' }}">
                            {{ $order->payment_method }}
                        </span>
                    </td>
                    <td class="px-3 lg:px-6 py-3 lg:py-4 text-center">
                        @if($order->status === 'success')
                            <span class="px-2 lg:px-2.5 py-1 rounded-full text-[10px] font-bold uppercase bg-green-50 text-green-600">Sukses</span>
                        @else
                            <span class="px-2 lg:px-2.5 py-1 rounded-full text-[10px] font-bold uppercase bg-red-50 text-red-500">Dibatalkan</span>
                        @endif
                    </td>
                    <td class="px-3 lg:px-6 py-3 lg:py-4 text-right">
                        <p class="text-xs lg:text-sm font-bold {{ $order->status === 'cancelled' ? 'text-gray-400 line-through' : 'text-gray-900' }}">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</p>
                    </td>
                    <td class="px-3 lg:px-6 py-3 lg:py-4 text-center">
                        @if($order->status === 'success')
                            <button type="button" onclick="confirmCancel({{ $order->id }}, '{{ $order->invoice_number }}')" class="px-2 lg:px-3 py-1 lg:py-1.5 rounded-lg bg-red-50 text-red-500 text-[10px] lg:text-xs font-bold hover:bg-red-100 transition-colors">
                                Batalkan
                            </button>
                            <form id="cancel-form-{{ $order->id }}" action="{{ route('orders.cancel', $order->id) }}" method="POST" class="hidden">
                                @csrf
                                @method('PATCH')
                            </form>
                        @else
                            <span class="text-xs text-gray-400 italic">—</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="px-6 py-12 text-center text-gray-400 italic">
                        Tidak ada transaksi ditemukan pada rentang tanggal ini.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- Pagination --}}
@if($pesanan->hasPages())
<div class="mt-4 lg:mt-6">
    {{ $pesanan->links() }}
</div>
@endif

{{-- Revenue Chart --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // --- Mini Revenue Chart ---
        const ctx = document.getElementById('revenueChart').getContext('2d');
        const isMobile = window.innerWidth < 768;
        const gradient = ctx.createLinearGradient(0, 0, 0, isMobile ? 200 : 250);
        gradient.addColorStop(0, 'rgba(194, 65, 12, 0.3)');
        gradient.addColorStop(1, 'rgba(194, 65, 12, 0.02)');

        new Chart(ctx, {
            type: 'line',
            data: {
                labels: {!! json_encode($labelGrafikMini) !!},
                datasets: [{
                    label: 'Pendapatan',
                    data: {!! json_encode($dataGrafikMini) !!},
                    borderColor: '#c2410c',
                    backgroundColor: gradient,
                    fill: true,
                    tension: 0.4,
                    pointRadius: isMobile ? 2 : 4,
                    pointBackgroundColor: '#c2410c',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    pointHoverRadius: 6,
                    borderWidth: 2.5,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
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
                            font: { size: isMobile ? 9 : 11 },
                            callback: function(value) {
                                if (value >= 1000000) return 'Rp ' + (value / 1000000).toFixed(1) + 'jt';
                                if (value >= 1000) return 'Rp ' + (value / 1000).toFixed(0) + 'rb';
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

        // --- Animated Counters ---
        document.querySelectorAll('[data-counter]').forEach(el => {
            const target = parseInt(el.dataset.counter);
            if (target <= 0) return;
            const duration = 1200;
            const start = performance.now();
            const animate = (now) => {
                const elapsed = now - start;
                const progress = Math.min(elapsed / duration, 1);
                const eased = 1 - Math.pow(1 - progress, 3);
                const current = Math.floor(eased * target);
                el.textContent = 'Rp ' + current.toLocaleString('id-ID');
                if (progress < 1) requestAnimationFrame(animate);
            };
            requestAnimationFrame(animate);
        });

        // --- Search ---
        const searchInput = document.getElementById('searchInput');
        searchInput.addEventListener('input', function() {
            const term = this.value.toLowerCase();
            document.querySelectorAll('#salesTable tbody .table-row').forEach(row => {
                const searchData = row.dataset.search;
                row.style.display = searchData.includes(term) ? '' : 'none';
            });
        });

        // --- Filter pills ---
        document.querySelectorAll('.filter-pill').forEach(pill => {
            pill.addEventListener('click', function() {
                const filter = this.dataset.filter;
                document.querySelectorAll('.filter-pill').forEach(p => {
                    p.classList.remove('bg-gray-900', 'text-white', 'active');
                    p.classList.add('bg-gray-100', 'text-gray-600');
                });
                this.classList.remove('bg-gray-100', 'text-gray-600');
                this.classList.add('bg-gray-900', 'text-white', 'active');
                
                document.querySelectorAll('#salesTable tbody .table-row').forEach(row => {
                    if (filter === 'all') {
                        row.style.display = '';
                    } else {
                        row.style.display = row.dataset.status === filter ? '' : 'none';
                    }
                });
            });
        });
    });

    function confirmCancel(orderId, invoiceNumber) {
        Swal.fire({
            title: 'Batalkan Transaksi?',
            html: `Transaksi <strong>${invoiceNumber}</strong> akan dibatalkan.<br><span class="text-sm text-gray-500">Tindakan ini tidak dapat dikembalikan.</span>`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Ya, Batalkan!',
            cancelButtonText: 'Kembali'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('cancel-form-' + orderId).submit();
            }
        });
    }
</script>
@endsection