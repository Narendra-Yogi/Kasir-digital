@extends('layouts.app')

@section('title', 'Catatan Pengeluaran')
@section('page_title', 'Catatan Pengeluaran')

@section('content')
{{-- Filter Tanggal --}}
<div class="bg-white p-4 lg:p-6 rounded-2xl lg:rounded-3xl border border-gray-100 shadow-sm mb-6 animate-fade-in-up">
    <form action="{{ route('pengeluaran.index') }}" method="GET" class="flex flex-col sm:flex-row flex-wrap items-end gap-3 lg:gap-4">
        <div class="flex-1 min-w-0 w-full sm:w-auto">
            <label class="block text-xs font-bold text-gray-400 uppercase mb-2">Mulai Dari</label>
            <input type="date" name="start_date" value="{{ $startDate->format('Y-m-d') }}" class="w-full px-4 py-2 rounded-xl border border-gray-100 bg-gray-50 focus:bg-white focus:border-brand-500 outline-none text-sm">
        </div>
        <div class="flex-1 min-w-0 w-full sm:w-auto">
            <label class="block text-xs font-bold text-gray-400 uppercase mb-2">Sampai Dengan</label>
            <input type="date" name="end_date" value="{{ $endDate->format('Y-m-d') }}" class="w-full px-4 py-2 rounded-xl border border-gray-100 bg-gray-50 focus:bg-white focus:border-brand-500 outline-none text-sm">
        </div>
        <button type="submit" class="w-full sm:w-auto bg-brand-700 text-white px-6 py-2.5 rounded-xl font-bold hover:bg-brand-800 transition-colors shadow-lg shadow-brand-700/20 cursor-pointer">
            Filter Data
        </button>
    </form>
</div>

{{-- Summary Cards --}}
<div class="grid grid-cols-2 lg:grid-cols-4 gap-3 lg:gap-4 mb-6">
    <div class="bg-red-500 p-4 lg:p-6 rounded-2xl lg:rounded-3xl shadow-lg shadow-red-500/20 text-white flex flex-col justify-center">
        <p class="text-[10px] lg:text-xs font-bold opacity-70 uppercase tracking-widest mb-1">Total Pengeluaran</p>
        <h3 class="text-lg lg:text-2xl font-bold">Rp {{ number_format($totalExpense, 0, ',', '.') }}</h3>
        <p class="text-[10px] mt-2 opacity-60 italic">Periode terpilih</p>
    </div>
    <div class="bg-white p-4 lg:p-6 rounded-2xl lg:rounded-3xl border border-gray-100 shadow-sm flex flex-col justify-center">
        <div class="flex items-center gap-2 mb-1">
            <span class="w-2 h-2 rounded-full bg-orange-500"></span>
            <p class="text-[10px] lg:text-xs font-bold text-gray-400 uppercase tracking-widest">Bahan Baku</p>
        </div>
        <h3 class="text-lg lg:text-2xl font-bold text-orange-600">Rp {{ number_format($totalBahan, 0, ',', '.') }}</h3>
    </div>
    <div class="bg-white p-4 lg:p-6 rounded-2xl lg:rounded-3xl border border-gray-100 shadow-sm flex flex-col justify-center">
        <div class="flex items-center gap-2 mb-1">
            <span class="w-2 h-2 rounded-full bg-blue-500"></span>
            <p class="text-[10px] lg:text-xs font-bold text-gray-400 uppercase tracking-widest">Logistik</p>
        </div>
        <h3 class="text-lg lg:text-2xl font-bold text-blue-600">Rp {{ number_format($totalLogistik, 0, ',', '.') }}</h3>
    </div>
    <div class="bg-white p-4 lg:p-6 rounded-2xl lg:rounded-3xl border border-gray-100 shadow-sm flex flex-col justify-center">
        <div class="flex items-center gap-2 mb-1">
            <span class="w-2 h-2 rounded-full bg-gray-400"></span>
            <p class="text-[10px] lg:text-xs font-bold text-gray-400 uppercase tracking-widest">Lainnya</p>
        </div>
        <h3 class="text-lg lg:text-2xl font-bold text-gray-600">Rp {{ number_format($totalLainnya, 0, ',', '.') }}</h3>
    </div>
</div>

{{-- Action Bar --}}
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-3">
    <a href="{{ route('pengeluaran.create') }}" class="bg-brand-700 hover:bg-brand-800 text-white px-6 py-3 rounded-xl font-bold transition-all shadow-lg shadow-brand-700/20 flex items-center gap-2">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
        Catat Pengeluaran
    </a>
    <a href="{{ route('pengeluaran.exportPdf', ['start_date' => $startDate->format('Y-m-d'), 'end_date' => $endDate->format('Y-m-d')]) }}" class="flex items-center gap-2 bg-gray-900 hover:bg-black text-white font-bold text-xs lg:text-sm px-5 py-2.5 rounded-xl transition-colors shadow-lg">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
        Export PDF
    </a>
</div>

{{-- Table --}}
<div class="bg-white rounded-3xl border border-gray-100 overflow-hidden shadow-sm">
    <div class="overflow-x-auto">
        <table class="w-full text-left min-w-[650px]">
            <thead class="bg-gray-50 border-b border-gray-100">
                <tr>
                    <th class="px-4 lg:px-6 py-4 text-xs font-bold text-gray-400 uppercase">Tanggal</th>
                    <th class="px-4 lg:px-6 py-4 text-xs font-bold text-gray-400 uppercase">Nama Item / Keterangan</th>
                    <th class="px-4 lg:px-6 py-4 text-xs font-bold text-gray-400 uppercase">Kategori</th>
                    <th class="px-4 lg:px-6 py-4 text-xs font-bold text-gray-400 uppercase text-right">Nominal (Rp)</th>
                    <th class="px-4 lg:px-6 py-4 text-xs font-bold text-gray-400 uppercase text-center">Dicatat Oleh</th>
                    <th class="px-4 lg:px-6 py-4 text-xs font-bold text-gray-400 uppercase text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($expenses as $exp)
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="px-4 lg:px-6 py-4 text-sm font-semibold text-gray-700">{{ \Carbon\Carbon::parse($exp->date)->format('d M Y') }}</td>
                    <td class="px-4 lg:px-6 py-4">
                        <p class="font-bold text-gray-900">
                            {{ $exp->item_name }}
                            @if($exp->isAutoGenerated())
                                <span class="inline-flex items-center gap-1 ml-1.5 px-2 py-0.5 rounded-full bg-brand-50 text-brand-600 text-[10px] font-bold uppercase align-middle" title="Dicatat otomatis dari input bahan baku">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path></svg>
                                    Otomatis
                                </span>
                            @endif
                        </p>
                        @if($exp->notes)<p class="text-xs text-gray-400 mt-1">{{ $exp->notes }}</p>@endif
                    </td>
                    <td class="px-4 lg:px-6 py-4">
                        @if($exp->category == 'bahan')
                            <span class="px-3 py-1 rounded-full bg-orange-50 text-orange-600 text-xs font-bold uppercase">Bahan</span>
                        @elseif($exp->category == 'logistik')
                            <span class="px-3 py-1 rounded-full bg-blue-50 text-blue-600 text-xs font-bold uppercase">Logistik</span>
                        @else
                            <span class="px-3 py-1 rounded-full bg-gray-100 text-gray-600 text-xs font-bold uppercase">Lainnya</span>
                        @endif
                    </td>
                    <td class="px-4 lg:px-6 py-4 text-right font-bold text-red-600">
                        - Rp {{ number_format($exp->amount, 0, ',', '.') }}
                    </td>
                    <td class="px-4 lg:px-6 py-4 text-center text-sm text-gray-500">{{ $exp->user->name ?? '-' }}</td>
                    <td class="px-4 lg:px-6 py-4">
                        @if($exp->isAutoGenerated())
                            {{-- Pengeluaran otomatis: tidak bisa edit/hapus dari sini --}}
                            <div class="flex items-center justify-end">
                                <span class="text-[10px] text-gray-300 italic">Kelola di Bahan Baku</span>
                            </div>
                        @else
                        <div class="flex items-center justify-end gap-2">
                            <a href="{{ route('pengeluaran.edit', $exp->id) }}" class="flex items-center justify-center w-9 h-9 rounded-xl bg-blue-50 text-blue-600 hover:bg-blue-100 active:scale-95 transition-all duration-200 shadow-sm" title="Edit Pengeluaran">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-5M16.5 3.5a2.121 2.121 0 113 3L7 19l-4 1 1-4L16.5 3.5z"></path></svg>
                            </a>
                            <form action="{{ route('pengeluaran.destroy', $exp->id) }}" method="POST" class="block" onsubmit="confirmDelete(event, this, 'Hapus data pengeluaran ini?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="flex items-center justify-center w-9 h-9 rounded-xl bg-red-50 text-red-600 hover:bg-red-100 active:scale-95 transition-all duration-200 shadow-sm cursor-pointer" title="Hapus Pengeluaran">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                </button>
                            </form>
                        </div>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-12 text-center text-gray-400 italic">Belum ada data pengeluaran pada periode ini.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- Pagination --}}
@if($expenses->hasPages())
<div class="mt-4 lg:mt-6">
    {{ $expenses->links() }}
</div>
@endif
@endsection
