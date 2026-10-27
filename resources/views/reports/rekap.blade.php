@extends('layouts.app')

@section('title', 'Rekap Hasil')
@section('page_title', 'Rekap hasil usaha')

@section('content')
@php
    $hasilBersih = $hasilBersih ?? $labaBersih ?? 0;
    $marginBersih = $marginBersih ?? $profitMargin ?? 0;
    $isUntung = $hasilBersih >= 0;
@endphp
{{-- Filter tanggal saja (sesuai keputusan v2) --}}
<div class="card card-pad mb-4">
    <form action="{{ route('reports.rekap') }}" method="GET" class="flex flex-col gap-4">
        <div class="flex flex-wrap items-center gap-2">
            <span class="text-[13px] text-[#8a8179] mr-1">Rentang cepat:</span>
            <a href="{{ route('reports.rekap', ['start_date' => now()->format('Y-m-d'), 'end_date' => now()->format('Y-m-d')]) }}"
               class="preset {{ $tanggalMulai->isToday() && $tanggalSelesai->isToday() ? 'is-active' : '' }}">
                Hari ini
            </a>
            <a href="{{ route('reports.rekap', ['start_date' => now()->subDays(6)->format('Y-m-d'), 'end_date' => now()->format('Y-m-d')]) }}"
               class="preset {{ $tanggalMulai->diffInDays($tanggalSelesai) == 6 && $tanggalSelesai->isToday() ? 'is-active' : '' }}">
                7 hari
            </a>
            <a href="{{ route('reports.rekap', ['start_date' => now()->subDays(13)->format('Y-m-d'), 'end_date' => now()->format('Y-m-d')]) }}"
               class="preset {{ $tanggalMulai->diffInDays($tanggalSelesai) == 13 && $tanggalSelesai->isToday() ? 'is-active' : '' }}">
                14 hari
            </a>
            <a href="{{ route('reports.rekap', ['start_date' => now()->startOfMonth()->format('Y-m-d'), 'end_date' => now()->format('Y-m-d')]) }}"
               class="preset {{ $tanggalMulai->isSameDay(now()->startOfMonth()) && $tanggalSelesai->isToday() ? 'is-active' : '' }}">
                Bulan ini
            </a>
            <a href="{{ route('reports.rekap', ['start_date' => now()->subMonth()->startOfMonth()->format('Y-m-d'), 'end_date' => now()->subMonth()->endOfMonth()->format('Y-m-d')]) }}"
               class="preset">
                Bulan lalu
            </a>
        </div>
        <div class="flex flex-col sm:flex-row flex-wrap items-end gap-3">
            <div class="flex-1 min-w-0 w-full sm:w-auto">
                <label class="block text-[13px] text-[#57504a] mb-1.5">Mulai tanggal</label>
                <input type="date" name="start_date" value="{{ $tanggalMulai->format('Y-m-d') }}" class="field">
            </div>
            <div class="flex-1 min-w-0 w-full sm:w-auto">
                <label class="block text-[13px] text-[#57504a] mb-1.5">Sampai tanggal</label>
                <input type="date" name="end_date" value="{{ $tanggalSelesai->format('Y-m-d') }}" class="field">
            </div>
            <button type="submit" class="btn-ink w-full sm:w-auto px-6 py-2.5 text-sm">
                Tampilkan data
            </button>
        </div>
    </form>
</div>

@if($rekapData->count() === 0)
<div class="card card-pad text-center py-12 mb-4">
    <p class="font-display text-xl font-semibold">Belum ada data pada periode ini</p>
    <p class="text-sm text-[#8a8179] mt-2">Coba pilih rentang lain, atau mulai jualan dari Kasir dan catat pengeluaran.</p>
    <div class="flex flex-col sm:flex-row justify-center gap-2 mt-5">
        <a href="{{ route('pos.index') }}" class="btn-ink text-sm px-5 py-2.5">Buka Kasir</a>
        <a href="{{ route('pengeluaran.create') }}" class="btn-outline text-sm px-5 py-2.5">Catat Pengeluaran</a>
    </div>
</div>
@else
{{-- Ringkasan utama --}}
<div class="card overflow-hidden mb-4 rise">
    <div class="grid grid-cols-2 lg:grid-cols-6 divide-x divide-y lg:divide-y-0 divide-[#f1ebe0]">
        <div class="p-5">
            <p class="metric-label">Total pemasukan</p>
            <p class="metric-value text-lg lg:text-xl mt-1.5">Rp {{ number_format($totalPemasukan, 0, ',', '.') }}</p>
            <p class="text-xs text-[#8a8179] mt-1.5">{{ $totalTransaksi ?? 0 }} transaksi · rata-rata Rp {{ number_format($rataRataTransaksi ?? 0, 0, ',', '.') }}</p>
        </div>
        <div class="p-5">
            <p class="metric-label">Total pengeluaran</p>
            <p class="metric-value text-lg lg:text-xl mt-1.5">Rp {{ number_format($totalPengeluaran, 0, ',', '.') }}</p>
            <p class="text-xs text-[#8a8179] mt-1.5">Belanja {{ number_format($pengeluaranPerKategori['belanja_harian'] ?? 0, 0, ',', '.') != '0' ? '· Rp '.number_format($pengeluaranPerKategori['belanja_harian'] ?? 0, 0, ',', '.') : 'operasional & gaji' }}</p>
        </div>
        <div class="p-5 bg-[#17110c]">
            <p class="text-[13px] text-stone-400">{{ $isUntung ? 'Hasil bersih (Untung)' : 'Hasil bersih (Rugi)' }}</p>
            <p class="metric-value text-lg lg:text-xl mt-1.5 text-[#f7f1e6]">Rp {{ number_format(abs($hasilBersih), 0, ',', '.') }}</p>
            <p class="text-xs text-stone-500 mt-1.5">Pemasukan − Pengeluaran</p>
        </div>
        <div class="p-5">
            <p class="metric-label">Margin bersih</p>
            <p class="metric-value text-lg lg:text-xl mt-1.5">{{ $marginBersih }}%</p>
            <p class="text-xs text-[#8a8179] mt-1.5">{{ $hariUntung ?? 0 }} dari {{ $rekapData->count() }} hari untung</p>
        </div>
        <div class="p-5">
            <p class="metric-label">Tunai / QRIS</p>
            <p class="metric-value text-lg lg:text-xl mt-1.5 tnum">{{ ($jumlahTunai ?? 0) + ($jumlahQris ?? 0) }} <span class="text-sm font-sans font-normal text-[#8a8179]">trx</span></p>
            <p class="text-xs text-[#8a8179] mt-1.5">Tunai {{ $jumlahTunai ?? 0 }} · QRIS {{ $jumlahQris ?? 0 }}</p>
        </div>
        <div class="p-5">
            <p class="metric-label">Vs periode lalu</p>
            @php $selisih = $selisihHasil ?? 0; @endphp
            <p class="metric-value text-lg lg:text-xl mt-1.5 {{ $selisih >= 0 ? 'text-[#1a6b3c]' : 'text-[#931b13]' }}">{{ $selisih >= 0 ? '+' : '−' }}Rp {{ number_format(abs($selisih), 0, ',', '.') }}</p>
            <p class="text-xs text-[#8a8179] mt-1.5">Hasil {{ $selisih >= 0 ? 'naik' : 'turun' }} vs {{ $tanggalMulai->diffInDays($tanggalSelesai)+1 }} hari sebelumnya</p>
        </div>
    </div>
</div>

{{-- Insight bahasa warung --}}
<div class="grid grid-cols-1 lg:grid-cols-3 gap-4 mb-4">
    <div class="card card-pad">
        <p class="metric-label">Ringkasan singkat</p>
        <p class="text-sm mt-2 leading-relaxed">
            Periode <strong>{{ $tanggalMulai->format('d M') }} – {{ $tanggalSelesai->format('d M Y') }}</strong>
            masuk <strong class="tnum">Rp {{ number_format($totalPemasukan, 0, ',', '.') }}</strong>,
            keluar <strong class="tnum">Rp {{ number_format($totalPengeluaran, 0, ',', '.') }}</strong>,
            sisa <strong class="tnum">{{ $isUntung ? 'untung' : 'rugi' }} Rp {{ number_format(abs($hasilBersih), 0, ',', '.') }}</strong>.
            Rata-rata pemasukan harian <strong class="tnum">Rp {{ number_format($rataRataPemasukan ?? 0, 0, ',', '.') }}</strong>.
        </p>
        @if(!empty($stokMenipis) && $stokMenipis->count() > 0)
        <p class="text-[13px] text-[#931b13] mt-2">⚠ Stok menipis: {{ $stokMenipis->take(3)->pluck('name')->join(', ') }}. Segera tambah stok.</p>
        @endif
    </div>
    <div class="card card-pad">
        <p class="metric-label">Pengeluaran per kategori</p>
        <div class="mt-3 space-y-2 text-[13px]">
            @php
                $kat = ['belanja_harian' => 'Belanja Harian', 'operasional' => 'Operasional', 'gaji' => 'Gaji', 'lainnya' => 'Lainnya'];
            @endphp
            @foreach($kat as $key => $label)
            @php $val = $pengeluaranPerKategori[$key] ?? 0; $pct = $totalPengeluaran > 0 ? round($val / $totalPengeluaran * 100, 1) : 0; @endphp
            <div class="flex justify-between items-center gap-3">
                <span class="text-[#57504a]">{{ $label }}</span>
                <span class="tnum font-semibold">Rp {{ number_format($val, 0, ',', '.') }} <span class="text-xs font-normal text-[#8a8179]">{{ $pct }}%</span></span>
            </div>
            @endforeach
        </div>
    </div>
    <div class="card card-pad">
        <p class="metric-label">Menu terlaris periode ini</p>
        @if(!empty($menuTerlaris) && $menuTerlaris->count() > 0)
        <div class="mt-2 divide-y divide-[#f1ebe0]">
            @foreach($menuTerlaris->take(5) as $i => $m)
            <div class="flex items-center gap-2 py-2">
                <span class="font-display font-semibold w-5 {{ $i===0 ? 'text-[#c9a227]' : 'text-[#b3a897]' }}">{{ $i+1 }}</span>
                <span class="flex-1 text-[13px] font-medium truncate">{{ $m->item->name ?? 'Item terhapus' }}</span>
                <span class="text-xs text-[#8a8179] tnum">×{{ $m->total_qty }}</span>
                <span class="text-[13px] font-semibold tnum">Rp {{ number_format($m->total_revenue, 0, ',', '.') }}</span>
            </div>
            @endforeach
        </div>
        @else
        <p class="text-[13px] text-[#8a8179] mt-2">Belum ada penjualan.</p>
        @endif
    </div>
</div>

{{-- Hari terbaik & terendah --}}
@if($bestDay && $worstDay && $rekapData->count() > 1)
<div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
    <div class="card card-pad !py-4 flex items-center gap-4">
        <span class="font-display text-2xl font-semibold tnum w-8 text-[#c9a227]">1</span>
        <div>
            <p class="text-[13px] text-[#8a8179]">Hari terbaik</p>
            <p class="text-sm font-semibold">{{ $bestDay['tanggal'] }}</p>
            <p class="text-[13px] tnum mt-0.5">Hasil Rp {{ number_format($bestDay['total'], 0, ',', '.') }}</p>
        </div>
    </div>
    <div class="card card-pad !py-4 flex items-center gap-4">
        <span class="font-display text-2xl font-semibold tnum w-8 text-[#b3a897]">–</span>
        <div>
            <p class="text-[13px] text-[#8a8179]">Hari terendah</p>
            <p class="text-sm font-semibold">{{ $worstDay['tanggal'] }}</p>
            <p class="text-[13px] tnum mt-0.5">Hasil Rp {{ number_format($worstDay['total'], 0, ',', '.') }}</p>
        </div>
    </div>
</div>
@endif

{{-- Chart --}}
<div class="card card-pad mb-4">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-baseline mb-4 gap-1">
        <div>
            <h3 class="font-display text-[17px] font-semibold tracking-tight">Pemasukan vs pengeluaran</h3>
            <p class="text-[13px] text-[#8a8179] mt-0.5">{{ $tanggalMulai->format('d M') }} – {{ $tanggalSelesai->format('d M Y') }}</p>
        </div>
    </div>
    <div class="relative h-56 lg:h-80">
        <canvas id="rekapChart"></canvas>
    </div>
</div>

{{-- Tabel + aksi share --}}
<div class="table-shell bg-white">
    <div class="px-5 lg:px-6 py-4 border-b border-[#f1ebe0] bg-[#f7f2e9] flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3">
        <h2 class="font-display text-[17px] font-semibold tracking-tight">Detail harian</h2>
        <div class="flex flex-wrap gap-2">
            <button type="button" onclick="salinRingkasanWA()" class="btn-outline text-xs px-4 py-2 inline-flex items-center gap-2 cursor-pointer">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M8 12h8m-4-4v8m9-4a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                Salin untuk WA
            </button>
            <a href="{{ route('reports.exportRekapPdf', ['start_date' => $tanggalMulai->format('Y-m-d'), 'end_date' => $tanggalSelesai->format('Y-m-d')]) }}" class="btn-ink text-xs px-4 py-2 inline-flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                Export PDF
            </a>
        </div>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-left min-w-[560px]">
            <thead>
                <tr>
                    <th class="px-4 lg:px-6 py-3.5 text-[11px] font-semibold text-[#7a7268]">Hari / tanggal</th>
                    <th class="px-4 lg:px-6 py-3.5 text-[11px] font-semibold text-[#7a7268] text-right">Pemasukan</th>
                    <th class="px-4 lg:px-6 py-3.5 text-[11px] font-semibold text-[#7a7268] text-right">Pengeluaran</th>
                    <th class="px-4 lg:px-6 py-3.5 text-[11px] font-semibold text-[#7a7268] text-right">Hasil bersih</th>
                    <th class="px-4 lg:px-6 py-3.5 text-[11px] font-semibold text-[#7a7268] text-right">Transaksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-[#f1ebe0]">
                @foreach($rekapData as $data)
                @php
                    $isBest = $bestDay && $data['tanggal'] === $bestDay['tanggal'] && $rekapData->count() > 1;
                @endphp
                <tr class="table-row-hover {{ $data['total'] < 0 ? 'bg-[#fdf0ee]/60' : '' }}">
                    <td class="px-4 lg:px-6 py-3.5 text-[13px] font-semibold">
                        <a href="{{ route('reports.daily', ['date' => $data['tanggal_iso']]) }}" class="hover:underline">{{ $data['tanggal'] }}</a>
                        @if($isBest) <span class="pill pill-warn ml-1.5">Terbaik</span> @endif
                    </td>
                    <td class="px-4 lg:px-6 py-3.5 text-[13px] tnum text-right">Rp {{ number_format($data['pemasukan'], 0, ',', '.') }}</td>
                    <td class="px-4 lg:px-6 py-3.5 text-[13px] tnum text-right">Rp {{ number_format($data['pengeluaran'], 0, ',', '.') }}</td>
                    <td class="px-4 lg:px-6 py-3.5 text-[13px] font-semibold tnum text-right {{ $data['total'] >= 0 ? '' : 'text-[#931b13]' }}">
                        {{ $data['total'] < 0 ? '− ' : '' }}Rp {{ number_format(abs($data['total']), 0, ',', '.') }}
                    </td>
                    <td class="px-4 lg:px-6 py-3.5 text-[13px] tnum text-right text-[#57504a]">{{ $data['transaksi'] ?? 0 }}</td>
                </tr>
                @endforeach

                <tr class="bg-[#f7f2e9]">
                    <td class="px-4 lg:px-6 py-3.5 text-[13px] font-semibold">Total keseluruhan</td>
                    <td class="px-4 lg:px-6 py-3.5 text-[13px] font-semibold tnum text-right">Rp {{ number_format($totalPemasukan, 0, ',', '.') }}</td>
                    <td class="px-4 lg:px-6 py-3.5 text-[13px] font-semibold tnum text-right">Rp {{ number_format($totalPengeluaran, 0, ',', '.') }}</td>
                    <td class="px-4 lg:px-6 py-3.5 text-[13px] font-semibold tnum text-right">Rp {{ number_format($hasilBersih, 0, ',', '.') }}</td>
                    <td class="px-4 lg:px-6 py-3.5 text-[13px] font-semibold tnum text-right">{{ $totalTransaksi ?? 0 }}</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
@php
    $waPeriode = $tanggalMulai->format('d M').'–'.$tanggalSelesai->format('d M Y');
    $waStatus = $isUntung ? 'Untung' : 'Rugi';
    $waBest = $bestDay ? 'Hari terbaik: '.$bestDay['tanggal'].' (Rp '.number_format($bestDay['total'], 0, ',', '.').')' : '-';
    $waTop = (!empty($menuTerlaris) && $menuTerlaris->count() > 0 && $menuTerlaris->first()->item) ? 'Top: '.$menuTerlaris->first()->item->name.' ('.$menuTerlaris->first()->total_qty.')' : 'Top: -';
    $waDetailUrl = url()->current().'?start_date='.$tanggalMulai->format('Y-m-d').'&end_date='.$tanggalSelesai->format('Y-m-d');
    $fmtMasuk = number_format($totalPemasukan, 0, ',', '.');
    $fmtKeluar = number_format($totalPengeluaran, 0, ',', '.');
    $fmtHasil = number_format(abs($hasilBersih), 0, ',', '.');
    $waText = '*Rekap Hasil '.$waPeriode.'*'."\n".'Masuk Rp '.$fmtMasuk."\n".'Keluar Rp '.$fmtKeluar."\n".$waStatus.' Rp '.$fmtHasil.' ('.$marginBersih.'%)'."\n".($totalTransaksi ?? 0).' transaksi, Tunai '.($jumlahTunai ?? 0).', QRIS '.($jumlahQris ?? 0)."\n".$waBest."\n".$waTop."\nDetail: ".$waDetailUrl;
@endphp
<script>
    const waText = @json($waText);

    function salinRingkasanWA() {
        const done = () => {
            if (window.Swal) { Swal.fire({icon:'success', title:'Tersalin!', text:'Ringkasan siap ditempel ke WhatsApp.', timer:1800, showConfirmButton:false}); }
            else { alert('Ringkasan tersalin, tempel ke WhatsApp.'); }
        };
        if (navigator.clipboard && navigator.clipboard.writeText) {
            navigator.clipboard.writeText(waText).then(done).catch(() => fallbackSalin());
        } else { fallbackSalin(); }
        function fallbackSalin() {
            const ta = document.createElement('textarea');
            ta.value = waText; document.body.appendChild(ta); ta.select();
            try { document.execCommand('copy'); done(); } catch(e) { alert(waText); }
            ta.remove();
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        const ctx = document.getElementById('rekapChart').getContext('2d');

        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: {!! json_encode($chartLabels) !!},
                datasets: [
                    {
                        label: 'Pemasukan',
                        data: {!! json_encode($chartPemasukan) !!},
                        backgroundColor: '#1c1410',
                        borderRadius: 6,
                        barThickness: window.innerWidth < 768 ? 10 : 20,
                        order: 2,
                    },
                    {
                        label: 'Pengeluaran',
                        data: {!! json_encode($chartPengeluaran) !!},
                        backgroundColor: '#d8cfbf',
                        borderRadius: 6,
                        barThickness: window.innerWidth < 768 ? 10 : 20,
                        order: 3,
                    },
                    {
                        label: 'Hasil bersih',
                        data: {!! json_encode($chartHasil ?? $chartLaba ?? []) !!},
                        type: 'line',
                        borderColor: '#931b13',
                        backgroundColor: 'rgba(147,27,19,0.07)',
                        fill: true,
                        tension: 0.4,
                        pointRadius: 4,
                        pointBackgroundColor: '#931b13',
                        pointBorderColor: '#fffdf9',
                        pointBorderWidth: 2,
                        pointHoverRadius: 6,
                        borderWidth: 2,
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
                            padding: 16,
                            font: { size: 12, weight: '500' },
                            color: '#57504a'
                        }
                    },
                    tooltip: {
                        backgroundColor: '#17110c',
                        titleFont: { size: 11, weight: '500' },
                        bodyFont: { size: 12 },
                        padding: 12,
                        cornerRadius: 10,
                        callbacks: {
                            label: function(ctx) {
                                const val = ctx.parsed.y;
                                const sign = val < 0 ? '− ' : '';
                                return ctx.dataset.label + ': ' + sign + 'Rp ' + Math.abs(val).toLocaleString('id-ID');
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        grid: { color: '#f1ebe0' },
                        border: { display: false },
                        ticks: {
                            font: { size: 11 },
                            color: '#8a8179',
                            callback: function(value) {
                                if (Math.abs(value) >= 1000000) return (value / 1000000).toFixed(1) + ' jt';
                                if (Math.abs(value) >= 1000) return (value / 1000).toFixed(0) + ' rb';
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
    });
</script>
@endif
@endsection
