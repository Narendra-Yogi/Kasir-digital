@extends('layouts.app')

@section('title', 'Stok Logistik')
@section('page_title', 'Laporan Stok Logistik Harian')

@section('content')
{{-- Filter & Actions --}}
<div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
    <form action="{{ route('inventories.index') }}" method="GET" class="flex items-end gap-3">
        <div>
            <label class="block text-xs font-bold text-gray-400 uppercase mb-1">Pilih Tanggal</label>
            <input type="date" name="date" value="{{ $date->format('Y-m-d') }}" class="px-4 py-2 border border-gray-200 rounded-xl bg-white outline-none focus:border-brand-500 text-sm">
        </div>
        <button type="submit" class="bg-gray-100 text-gray-700 px-4 py-2 rounded-xl font-bold hover:bg-gray-200 transition-colors text-sm">Tampilkan</button>
    </form>
    <div class="flex items-center gap-3">
        <a href="{{ route('inventories.exportPdf', ['date' => $date->format('Y-m-d')]) }}" class="flex items-center gap-2 bg-gray-900 hover:bg-black text-white font-bold text-xs lg:text-sm px-5 py-2.5 rounded-xl transition-colors shadow-lg">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
            Export PDF
        </a>
        <a href="{{ route('inventories.create') }}" class="bg-brand-700 hover:bg-brand-800 text-white px-6 py-2.5 rounded-xl font-bold transition-all shadow-lg shadow-brand-700/20 flex items-center gap-2 text-sm">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
            Input Stok Baru
        </a>
    </div>
</div>

{{-- Summary Cards --}}
<div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
    <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm flex items-center gap-4">
        <div class="w-12 h-12 bg-brand-50 text-brand-600 rounded-xl flex items-center justify-center shrink-0">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
        </div>
        <div>
            <p class="text-xs font-bold text-gray-400 uppercase">Total Item</p>
            <h3 class="text-2xl font-bold text-gray-900">{{ $totalItems }}</h3>
            <p class="text-[10px] text-gray-400 italic">Dipantau hari ini</p>
        </div>
    </div>
    <div class="bg-white p-5 rounded-2xl border {{ $criticalStock > 0 ? 'border-red-200 bg-red-50/30' : 'border-gray-100' }} shadow-sm flex items-center gap-4">
        <div class="w-12 h-12 {{ $criticalStock > 0 ? 'bg-red-100 text-red-600' : 'bg-gray-100 text-gray-400' }} rounded-xl flex items-center justify-center shrink-0">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.999L13.732 4.001c-.77-1.333-2.694-1.333-3.464 0L3.34 16.001c-.77 1.332.192 2.999 1.732 2.999z"></path></svg>
        </div>
        <div>
            <p class="text-xs font-bold {{ $criticalStock > 0 ? 'text-red-400' : 'text-gray-400' }} uppercase">Stok Kritis</p>
            <h3 class="text-2xl font-bold {{ $criticalStock > 0 ? 'text-red-600' : 'text-gray-900' }}">{{ $criticalStock }}</h3>
            <p class="text-[10px] {{ $criticalStock > 0 ? 'text-red-400' : 'text-gray-400' }} italic">Sisa stok &lt; 5</p>
        </div>
    </div>
    <div class="bg-white p-5 rounded-2xl border {{ $outOfStock > 0 ? 'border-red-300 bg-red-50/50' : 'border-gray-100' }} shadow-sm flex items-center gap-4">
        <div class="w-12 h-12 {{ $outOfStock > 0 ? 'bg-red-200 text-red-700' : 'bg-gray-100 text-gray-400' }} rounded-xl flex items-center justify-center shrink-0">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"></path></svg>
        </div>
        <div>
            <p class="text-xs font-bold {{ $outOfStock > 0 ? 'text-red-500' : 'text-gray-400' }} uppercase">Stok Habis</p>
            <h3 class="text-2xl font-bold {{ $outOfStock > 0 ? 'text-red-700' : 'text-gray-900' }}">{{ $outOfStock }}</h3>
            <p class="text-[10px] {{ $outOfStock > 0 ? 'text-red-400' : 'text-gray-400' }} italic">Perlu restock segera</p>
        </div>
    </div>
</div>

{{-- Table --}}
<div class="bg-white rounded-3xl border border-gray-100 overflow-hidden shadow-sm">
    <div class="p-4 lg:p-6 border-b border-gray-100 bg-gray-50">
        <h3 class="text-sm font-bold text-gray-700">
            📅 Data Stok — {{ $date->translatedFormat('l, d F Y') }}
        </h3>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-center min-w-[600px]">
            <thead class="bg-gray-50 border-b border-gray-100">
                <tr>
                    <th class="px-4 lg:px-6 py-4 text-xs font-bold text-gray-400 uppercase text-left">ITEM</th>
                    <th class="px-4 lg:px-6 py-4 text-xs font-bold text-blue-500 uppercase">STOK MASUK</th>
                    <th class="px-4 lg:px-6 py-4 text-xs font-bold text-gray-400 uppercase">STOK AWAL</th>
                    <th class="px-4 lg:px-6 py-4 text-xs font-bold text-red-400 uppercase">TERJUAL</th>
                    <th class="px-4 lg:px-6 py-4 text-xs font-bold text-brand-700 uppercase bg-brand-50">SISA STOK</th>
                    <th class="px-4 lg:px-6 py-4 text-xs font-bold text-gray-400 uppercase">STATUS</th>
                    <th class="px-4 lg:px-6 py-4 text-xs font-bold text-gray-400 uppercase text-right">AKSI</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($inventories as $inv)
                <tr class="hover:bg-gray-50 transition-colors {{ $inv->remaining_stock < 5 ? 'bg-red-50/40' : '' }}">
                    <td class="px-4 lg:px-6 py-4 font-bold text-gray-900 text-left uppercase text-sm">
                        {{ $inv->item_name }}
                    </td>
                    <td class="px-4 lg:px-6 py-4 text-blue-600 font-bold">+{{ $inv->new_stock }}</td>
                    <td class="px-4 lg:px-6 py-4 text-gray-600 font-medium">{{ $inv->old_stock }}</td>
                    <td class="px-4 lg:px-6 py-4 text-red-600 font-bold">-{{ $inv->sold }}</td>
                    <td class="px-4 lg:px-6 py-4 font-black text-lg {{ $inv->remaining_stock < 5 ? 'text-red-600 bg-red-50' : 'text-brand-700 bg-brand-50/50' }}">{{ $inv->remaining_stock }}</td>
                    <td class="px-4 lg:px-6 py-4">
                        @if($inv->remaining_stock <= 0)
                            <span class="px-2.5 py-1 rounded-full bg-red-100 text-red-700 text-[10px] font-bold uppercase">Habis</span>
                        @elseif($inv->remaining_stock < 5)
                            <span class="px-2.5 py-1 rounded-full bg-amber-100 text-amber-700 text-[10px] font-bold uppercase">⚠ Kritis</span>
                        @else
                            <span class="px-2.5 py-1 rounded-full bg-green-100 text-green-700 text-[10px] font-bold uppercase">Aman</span>
                        @endif
                    </td>
                    <td class="px-4 lg:px-6 py-4">
                        <div class="flex items-center justify-end gap-2">
                            <a href="{{ route('inventories.edit', $inv->id) }}" class="flex items-center justify-center w-9 h-9 rounded-xl bg-blue-50 text-blue-600 hover:bg-blue-100 active:scale-95 transition-all duration-200 shadow-sm" title="Edit Data Stok">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-5M16.5 3.5a2.121 2.121 0 113 3L7 19l-4 1 1-4L16.5 3.5z"></path></svg>
                            </a>
                            <form action="{{ route('inventories.destroy', $inv->id) }}" method="POST" class="block" onsubmit="confirmDelete(event, this, 'Hapus data stok ini?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="flex items-center justify-center w-9 h-9 rounded-xl bg-red-50 text-red-600 hover:bg-red-100 active:scale-95 transition-all duration-200 shadow-sm cursor-pointer" title="Hapus Data Stok">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-6 py-12 text-center text-gray-400 italic">Belum ada catatan stok untuk tanggal ini.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection