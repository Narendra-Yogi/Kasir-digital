@extends('layouts.app')

@section('title', 'Laporan Penjualan')
@section('page_title', 'Laporan penjualan')

@section('content')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

@if(session('success'))
<script>
    document.addEventListener("DOMContentLoaded", function() {
        Swal.fire({
            icon: 'success',
            title: 'Berhasil',
            text: '{!! session("success") !!}',
            confirmButtonColor: '#931b13',
        });
    });
</script>
@endif

@if(session('error'))
<script>
    document.addEventListener("DOMContentLoaded", function() {
        Swal.fire({
            icon: 'error',
            title: 'Gagal',
            text: '{!! session("error") !!}',
            confirmButtonColor: '#931b13',
        });
    });
</script>
@endif

{{-- Filter --}}
<div class="no-print flex flex-col gap-4 mb-5">
    <div class="card card-pad">
        <form action="{{ route('reports.index') }}" method="GET" class="flex flex-col gap-4">
            @php
                $isBulanLalu = $tanggalMulai->isSameDay(now()->subMonth()->startOfMonth()) && $tanggalSelesai->isSameDay(now()->subMonth()->endOfMonth());
            @endphp
            <div class="flex flex-wrap items-center gap-2">
                <span class="text-[13px] text-[#8a8179] mr-1">Rentang cepat:</span>
                <a href="{{ route('reports.index', ['start_date' => now()->format('Y-m-d'), 'end_date' => now()->format('Y-m-d')]) }}"
                   class="preset {{ $tanggalMulai->format('Y-m-d') === now()->format('Y-m-d') && $tanggalSelesai->format('Y-m-d') === now()->format('Y-m-d') ? 'is-active' : '' }}">
                    Hari ini
                </a>
                <a href="{{ route('reports.index', ['start_date' => now()->subDays(6)->format('Y-m-d'), 'end_date' => now()->format('Y-m-d')]) }}"
                   class="preset {{ $tanggalMulai->format('Y-m-d') === now()->subDays(6)->format('Y-m-d') && $tanggalSelesai->format('Y-m-d') === now()->format('Y-m-d') ? 'is-active' : '' }}">
                    7 hari
                </a>
                <a href="{{ route('reports.index', ['start_date' => now()->startOfMonth()->format('Y-m-d'), 'end_date' => now()->format('Y-m-d')]) }}"
                   class="preset {{ $tanggalMulai->format('Y-m-d') === now()->startOfMonth()->format('Y-m-d') && $tanggalSelesai->format('Y-m-d') === now()->format('Y-m-d') ? 'is-active' : '' }}">
                    Bulan ini
                </a>
                <a href="{{ route('reports.index', ['start_date' => now()->subMonth()->startOfMonth()->format('Y-m-d'), 'end_date' => now()->subMonth()->endOfMonth()->format('Y-m-d')]) }}"
                   class="preset {{ $isBulanLalu ? 'is-active' : '' }}">
                    Bulan lalu
                </a>
            </div>
            <div class="flex flex-col sm:flex-row flex-wrap items-end gap-3">
                <div class="flex-1 min-w-0 w-full sm:w-auto">
                    <label class="block text-[13px] text-[#57504a] mb-1.5">Mulai dari</label>
                    <input type="date" name="start_date" value="{{ $tanggalMulai->format('Y-m-d') }}" class="field">
                </div>
                <div class="flex-1 min-w-0 w-full sm:w-auto">
                    <label class="block text-[13px] text-[#57504a] mb-1.5">Sampai dengan</label>
                    <input type="date" name="end_date" value="{{ $tanggalSelesai->format('Y-m-d') }}" class="field">
                </div>
                <button type="submit" class="btn-ink w-full sm:w-auto px-6 py-2.5 text-sm">
                    Terapkan filter
                </button>
            </div>
        </form>
    </div>

    {{-- Ringkasan --}}
    <div class="card overflow-hidden rise">
        <div class="grid grid-cols-2 lg:grid-cols-4 divide-x divide-y lg:divide-y-0 divide-[#f1ebe0]">
            <div class="p-5">
                <p class="metric-label">Total pendapatan</p>
                <p class="metric-value text-lg lg:text-xl mt-1.5" data-counter="{{ $totalPendapatan }}">Rp {{ number_format($totalPendapatan, 0, ',', '.') }}</p>
                <p class="text-xs text-[#8a8179] mt-1.5">{{ $totalTransaksi }} transaksi berhasil</p>
            </div>
            <div class="p-5">
                <p class="metric-label">Rata-rata transaksi</p>
                <p class="metric-value text-lg lg:text-xl mt-1.5" data-counter="{{ $rataRataTransaksi }}">Rp {{ number_format($rataRataTransaksi, 0, ',', '.') }}</p>
                <p class="text-xs text-[#8a8179] mt-1.5">Per transaksi</p>
            </div>
            <div class="p-5">
                <p class="metric-label">Metode bayar</p>
                <p class="metric-value text-lg lg:text-xl mt-1.5 tnum">{{ $jumlahTunai + $jumlahQris }} <span class="text-sm font-sans font-normal text-[#8a8179]">transaksi</span></p>
                <p class="text-xs text-[#8a8179] mt-1.5">Tunai {{ $jumlahTunai }} · QRIS {{ $jumlahQris }}</p>
            </div>
            <div class="p-5 bg-[#17110c]">
                <p class="text-[13px] text-stone-400">Dibatalkan</p>
                <p class="metric-value text-lg lg:text-xl mt-1.5 text-[#f7f1e6] tnum">{{ $totalDibatalkan }}</p>
                <p class="text-xs text-stone-500 mt-1.5">Transaksi dibatalkan</p>
            </div>
        </div>
    </div>

    {{-- Grafik + terlaris --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
        <div class="lg:col-span-2 card card-pad">
            <div class="flex justify-between items-baseline mb-4">
                <div>
                    <h3 class="font-display text-[17px] font-semibold tracking-tight">Tren pendapatan harian</h3>
                    <p class="text-[13px] text-[#8a8179] mt-0.5">{{ $tanggalMulai->format('d M') }} – {{ $tanggalSelesai->format('d M Y') }}</p>
                </div>
            </div>
            <div class="relative h-48 lg:h-56">
                <canvas id="revenueChart"></canvas>
            </div>
        </div>
        <div class="card card-pad">
            <h3 class="font-display text-[17px] font-semibold tracking-tight">Produk terlaris</h3>
            <p class="text-[13px] text-[#8a8179] mt-0.5 mb-3">Periode terpilih</p>
            @if($produkTerlaris->count() > 0)
            <div class="divide-y divide-[#f1ebe0]">
                @foreach($produkTerlaris as $i => $topItem)
                <div class="flex items-center gap-3 py-2.5">
                    <span class="font-display text-lg font-semibold tnum w-6 shrink-0 {{ $i === 0 ? 'text-[#c9a227]' : 'text-[#b3a897]' }}">{{ $i + 1 }}</span>
                    <div class="flex-1 min-w-0">
                        <p class="text-[13px] font-medium truncate">{{ $topItem->item->name ?? 'Item terhapus' }}</p>
                        <p class="text-xs text-[#8a8179]">{{ $topItem->total_qty }} terjual</p>
                    </div>
                    <span class="text-[13px] font-semibold tnum">Rp {{ number_format($topItem->total_revenue, 0, ',', '.') }}</span>
                </div>
                @endforeach
            </div>
            @else
            <p class="text-[13px] text-[#8a8179] text-center py-6">Belum ada data penjualan.</p>
            @endif
        </div>
    </div>

    {{-- Cari + filter status + export --}}
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3">
        <div class="relative w-full sm:w-72">
            <input type="text" id="searchInput" placeholder="Cari invoice, pelanggan…" class="field pl-10">
            <svg class="w-4 h-4 text-[#b3a897] absolute left-3.5 top-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
        </div>
        <div class="flex flex-wrap items-center gap-2">
            <button type="button" data-filter="all" class="filter-pill active">Semua</button>
            <button type="button" data-filter="success" class="filter-pill">Sukses</button>
            <button type="button" data-filter="cancelled" class="filter-pill">Batal</button>
            <a href="{{ route('reports.exportSalesPdf', ['start_date' => $tanggalMulai->format('Y-m-d'), 'end_date' => $tanggalSelesai->format('Y-m-d')]) }}" class="btn-ink text-[13px] px-4 py-2.5 inline-flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                Export PDF
            </a>
        </div>
    </div>
</div>

{{-- Tabel laporan --}}
<div class="table-shell bg-white">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse min-w-[700px]" id="salesTable">
            <thead>
                <tr>
                    <th class="px-4 lg:px-6 py-3.5 text-[11px] font-semibold text-[#7a7268]">No</th>
                    <th class="px-4 lg:px-6 py-3.5 text-[11px] font-semibold text-[#7a7268]">Waktu & invoice</th>
                    <th class="px-4 lg:px-6 py-3.5 text-[11px] font-semibold text-[#7a7268]">Pelanggan</th>
                    <th class="px-4 lg:px-6 py-3.5 text-[11px] font-semibold text-[#7a7268]">Detail pesanan</th>
                    <th class="px-4 lg:px-6 py-3.5 text-[11px] font-semibold text-[#7a7268]">Metode</th>
                    <th class="px-4 lg:px-6 py-3.5 text-[11px] font-semibold text-[#7a7268]">Status</th>
                    <th class="px-4 lg:px-6 py-3.5 text-[11px] font-semibold text-[#7a7268] text-right">Total</th>
                    <th class="px-4 lg:px-6 py-3.5 text-[11px] font-semibold text-[#7a7268] text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-[#f1ebe0]">
                @forelse($pesanan as $index => $order)
                <tr class="table-row-hover {{ $order->status === 'cancelled' ? 'opacity-60' : '' }} table-row"
                    data-status="{{ $order->status }}"
                    data-search="{{ strtolower($order->invoice_number . ' ' . ($order->customer_name ?? 'Pelanggan Umum') . ' ' . $order->payment_method) }}">
                    <td class="px-4 lg:px-6 py-3.5 text-[13px] text-[#8a8179] tnum">{{ $pesanan->firstItem() + $index }}</td>
                    <td class="px-4 lg:px-6 py-3.5">
                        <p class="text-[13px] font-semibold">{{ $order->invoice_number }}</p>
                        <p class="text-xs text-[#8a8179]">{{ $order->created_at->format('d M Y, H:i') }}</p>
                    </td>
                    <td class="px-4 lg:px-6 py-3.5 text-[13px]">{{ $order->customer_name ?? 'Pelanggan umum' }}</td>
                    <td class="px-4 lg:px-6 py-3.5">
                        <div class="max-w-[200px] lg:max-w-[250px] text-xs text-[#57504a]">
                            @foreach($order->orderDetails as $detail)
                                <span>{{ $detail->item->name }} (×{{ $detail->quantity }}){{ !$loop->last ? ',' : '' }} </span>
                            @endforeach
                        </div>
                    </td>
                    <td class="px-4 lg:px-6 py-3.5">
                        <span class="pill {{ $order->payment_method == 'qris' ? 'pill-info' : 'pill-muted' }}">{{ $order->payment_method == 'qris' ? 'QRIS' : 'Tunai' }}</span>
                    </td>
                    <td class="px-4 lg:px-6 py-3.5">
                        @if($order->status === 'success')
                            <span class="pill pill-success">Sukses</span>
                        @else
                            <span class="pill pill-danger">Dibatalkan</span>
                        @endif
                    </td>
                    <td class="px-4 lg:px-6 py-3.5 text-right">
                        <p class="text-[13px] font-semibold tnum {{ $order->status === 'cancelled' ? 'text-[#b3a897] line-through' : '' }}">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</p>
                    </td>
                    <td class="px-4 lg:px-6 py-3.5 text-center">
                        @if($order->status === 'success')
                            <button type="button" onclick="confirmCancel({{ $order->id }}, '{{ $order->invoice_number }}')" class="text-[13px] font-medium text-[#931b13] hover:underline">
                                Batalkan
                            </button>
                            <form id="cancel-form-{{ $order->id }}" action="{{ route('orders.cancel', $order->id) }}" method="POST" class="hidden">
                                @csrf
                                @method('PATCH')
                            </form>
                        @else
                            <span class="text-[13px] text-[#b3a897]">—</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="px-6 py-12 text-center text-[#8a8179] text-sm">
                        Tidak ada transaksi pada rentang tanggal ini.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- Pagination --}}
@if($pesanan->hasPages())
<div class="mt-4">
    {{ $pesanan->links() }}
</div>
@endif

{{-- Revenue Chart --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const ctx = document.getElementById('revenueChart').getContext('2d');

        new Chart(ctx, {
            type: 'line',
            data: {
                labels: {!! json_encode($labelGrafikMini) !!},
                datasets: [{
                    label: 'Pendapatan',
                    data: {!! json_encode($dataGrafikMini) !!},
                    borderColor: '#1c1410',
                    backgroundColor: 'rgba(28,20,16,0.06)',
                    fill: true,
                    tension: 0.4,
                    pointRadius: 3,
                    pointBackgroundColor: '#1c1410',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    pointHoverRadius: 5,
                    borderWidth: 2,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
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
                            font: { size: 11 },
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
                        ticks: { font: { size: 11 }, color: '#57504a', maxRotation: 45 }
                    }
                },
                interaction: { intersect: false, mode: 'index' }
            }
        });

        // --- Animated Counters ---
        document.querySelectorAll('[data-counter]').forEach(el => {
            const target = parseInt(el.dataset.counter);
            if (target <= 0) return;
            const duration = 900;
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
                document.querySelectorAll('.filter-pill').forEach(p => p.classList.remove('active'));
                this.classList.add('active');

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
            title: 'Batalkan transaksi?',
            html: `Transaksi <strong>${invoiceNumber}</strong> akan dibatalkan.<br><span class="text-sm text-gray-500">Tindakan ini tidak dapat dikembalikan.</span>`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#931b13',
            cancelButtonColor: '#57504a',
            confirmButtonText: 'Ya, batalkan',
            cancelButtonText: 'Kembali'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('cancel-form-' + orderId).submit();
            }
        });
    }
</script>
@endsection
