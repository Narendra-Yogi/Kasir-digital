@extends('layouts.app')

@section('title', 'Catatan Pengeluaran')
@section('page_title', 'Catatan pengeluaran')

@section('content')
{{-- Filter tanggal --}}
<div class="card card-pad mb-4 rise">
    <form action="{{ route('pengeluaran.index') }}" method="GET" class="flex flex-col sm:flex-row flex-wrap items-end gap-3">
        <div class="flex-1 min-w-0 w-full sm:w-auto">
            <label class="block text-[13px] text-[#57504a] mb-1.5">Mulai dari</label>
            <input type="date" name="start_date" value="{{ $startDate->format('Y-m-d') }}" class="field">
        </div>
        <div class="flex-1 min-w-0 w-full sm:w-auto">
            <label class="block text-[13px] text-[#57504a] mb-1.5">Sampai dengan</label>
            <input type="date" name="end_date" value="{{ $endDate->format('Y-m-d') }}" class="field">
        </div>
        <button type="submit" class="btn-ink w-full sm:w-auto px-6 py-2.5 text-sm">
            Terapkan filter
        </button>
    </form>
</div>

{{-- Ringkasan --}}
<div class="card overflow-hidden mb-4 rise">
    <div class="grid grid-cols-2 lg:grid-cols-5 divide-x divide-y lg:divide-y-0 divide-[#f1ebe0]">
        <div class="p-5 bg-[#17110c]">
            <p class="text-[13px] text-stone-400">Total pengeluaran</p>
            <p class="metric-value text-lg lg:text-xl mt-1.5 text-[#f7f1e6]">Rp {{ number_format($totalExpense, 0, ',', '.') }}</p>
            <p class="text-xs text-stone-500 mt-1.5">Periode terpilih</p>
        </div>
        <div class="p-5">
            <p class="metric-label">Belanja Harian</p>
            <p class="metric-value text-lg lg:text-xl mt-1.5">Rp {{ number_format($totalBelanjaHarian ?? $totalBahan ?? 0, 0, ',', '.') }}</p>
        </div>
        <div class="p-5">
            <p class="metric-label">Operasional</p>
            <p class="metric-value text-lg lg:text-xl mt-1.5">Rp {{ number_format($totalOperasional ?? $totalLogistik ?? 0, 0, ',', '.') }}</p>
        </div>
        <div class="p-5">
            <p class="metric-label">Gaji</p>
            <p class="metric-value text-lg lg:text-xl mt-1.5">Rp {{ number_format($totalGaji ?? 0, 0, ',', '.') }}</p>
        </div>
        <div class="p-5">
            <p class="metric-label">Lainnya</p>
            <p class="metric-value text-lg lg:text-xl mt-1.5">Rp {{ number_format($totalLainnya, 0, ',', '.') }}</p>
        </div>
    </div>
</div>

{{-- Action bar --}}
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-4 gap-3">
    <a href="{{ route('pengeluaran.create') }}" class="btn-ink text-[13px] px-4 py-2.5 inline-flex items-center gap-2">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
        Catat pengeluaran
    </a>
    <a href="{{ route('pengeluaran.exportPdf', ['start_date' => $startDate->format('Y-m-d'), 'end_date' => $endDate->format('Y-m-d')]) }}" class="btn-outline">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
        Export PDF
    </a>
</div>

{{-- Tabel --}}
<div class="table-shell bg-white rise">
    <div class="overflow-x-auto">
        <table class="w-full text-left min-w-[650px]">
            <thead>
                <tr>
                    <th class="px-4 lg:px-6 py-3.5 text-[11px] font-semibold text-[#7a7268]">Tanggal</th>
                    <th class="px-4 lg:px-6 py-3.5 text-[11px] font-semibold text-[#7a7268]">Nama item / keterangan</th>
                    <th class="px-4 lg:px-6 py-3.5 text-[11px] font-semibold text-[#7a7268]">Kategori</th>
                    <th class="px-4 lg:px-6 py-3.5 text-[11px] font-semibold text-[#7a7268] text-right">Nominal (Rp)</th>
                    <th class="px-4 lg:px-6 py-3.5 text-[11px] font-semibold text-[#7a7268] text-center">Dicatat oleh</th>
                    <th class="px-4 lg:px-6 py-3.5 text-[11px] font-semibold text-[#7a7268] text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-[#f1ebe0]">
                @forelse($expenses as $exp)
                <tr class="table-row-hover">
                    <td class="px-4 lg:px-6 py-3.5 text-[13px] font-medium tnum">{{ \Carbon\Carbon::parse($exp->date)->format('d M Y') }}</td>
                    <td class="px-4 lg:px-6 py-3.5">
                        <p class="font-medium text-[14px]">
                            {{ $exp->item_name }}
                        </p>
                        @if($exp->notes)<p class="text-xs text-[#8a8179] mt-0.5">{{ $exp->notes }}</p>@endif
                    </td>
                    <td class="px-4 lg:px-6 py-3.5">
                        @if($exp->category == 'belanja_harian')
                            <span class="pill pill-warn">Belanja Harian</span>
                        @elseif($exp->category == 'operasional')
                            <span class="pill pill-info">Operasional</span>
                        @elseif($exp->category == 'gaji')
                            <span class="pill pill-success">Gaji</span>
                        @else
                            <span class="pill pill-muted">Lainnya</span>
                        @endif
                    </td>
                    <td class="px-4 lg:px-6 py-3.5 text-right font-semibold text-[13px] tnum">
                        − Rp {{ number_format($exp->amount, 0, ',', '.') }}
                    </td>
                    <td class="px-4 lg:px-6 py-3.5 text-center text-[13px] text-[#57504a]">{{ $exp->user->name ?? '-' }}</td>
                    <td class="px-4 lg:px-6 py-3.5">
                        <div class="flex items-center justify-end gap-2">
                            <a href="{{ route('pengeluaran.edit', $exp->id) }}" class="icon-btn" title="Edit pengeluaran" aria-label="Edit {{ $exp->item_name }}">
                                <svg class="w-[18px] h-[18px]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M11 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-5M16.5 3.5a2.121 2.121 0 113 3L7 19l-4 1 1-4L16.5 3.5z"></path></svg>
                            </a>
                            <form action="{{ route('pengeluaran.destroy', $exp->id) }}" method="POST" class="block" onsubmit="confirmDelete(event, this, 'Hapus data pengeluaran ini?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="icon-btn danger" title="Hapus pengeluaran" aria-label="Hapus {{ $exp->item_name }}">
                                    <svg class="w-[18px] h-[18px]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-12 text-center text-[#8a8179] text-sm">Belum ada data pengeluaran pada periode ini.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- Pagination --}}
@if($expenses->hasPages())
<div class="mt-4">
    {{ $expenses->links() }}
</div>
@endif
@endsection
