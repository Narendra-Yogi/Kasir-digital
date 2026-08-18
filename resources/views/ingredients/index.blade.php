@extends('layouts.app')

@section('title', 'Bahan Baku')
@section('page_title', 'Master Bahan Baku')

@section('content')
<div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4 animate-fade-in-up">
    <div>
        <h3 class="text-gray-500 text-sm font-medium">Total Bahan: {{ $ingredients->count() }}</h3>
    </div>
    <a href="{{ route('ingredients.create') }}" class="bg-brand-700 hover:bg-brand-800 text-white px-6 py-2.5 rounded-xl font-bold transition-all shadow-lg shadow-brand-700/20 flex items-center gap-2 cursor-pointer text-sm">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
        Tambah Bahan
    </a>
</div>

{{-- Info Card --}}
<div class="bg-gradient-to-r from-amber-50 to-orange-50 border border-amber-200 rounded-2xl p-4 mb-6 animate-fade-in-up stagger-1">
    <div class="flex items-start gap-3">
        <div class="w-10 h-10 bg-amber-100 text-amber-600 rounded-xl flex items-center justify-center shrink-0 mt-0.5">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
        </div>
        <div>
            <p class="text-sm font-bold text-amber-800">Cara Kerja HPP Otomatis</p>
            <p class="text-xs text-amber-700 mt-1 leading-relaxed">Input bahan baku beserta harga beli &amp; jumlahnya. Sistem akan menghitung <strong>harga per satuan</strong> secara otomatis. Saat membuat produk/menu, pilih bahan-bahan yang digunakan beserta jumlah per porsi, maka <strong>HPP akan terhitung otomatis</strong>. Anda juga bisa beli dalam satuan besar (kg/liter) namun input resep dalam satuan kecil (gram/ml) — sistem <strong>otomatis konversi</strong>!</p>
        </div>
    </div>
</div>

<div class="bg-white rounded-3xl border border-gray-100 overflow-hidden shadow-sm animate-fade-in-up stagger-2">
    <div class="overflow-x-auto">
        <table class="w-full text-left min-w-[700px]">
            <thead class="bg-gray-50 border-b border-gray-100">
                <tr>
                    <th class="px-6 py-4 text-xs font-bold text-gray-400 uppercase tracking-wider">Nama Bahan</th>
                    <th class="px-6 py-4 text-xs font-bold text-gray-400 uppercase tracking-wider">Harga Beli</th>
                    <th class="px-6 py-4 text-xs font-bold text-gray-400 uppercase tracking-wider">Jumlah Beli</th>
                    <th class="px-6 py-4 text-xs font-bold text-gray-400 uppercase tracking-wider">Satuan</th>
                    <th class="px-6 py-4 text-xs font-bold text-brand-600 uppercase tracking-wider bg-brand-50/50">Harga per Satuan</th>
                    <th class="px-6 py-4 text-xs font-bold text-gray-400 uppercase tracking-wider">Dipakai di</th>
                    <th class="px-6 py-4 text-xs font-bold text-gray-400 uppercase tracking-wider text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($ingredients as $ingredient)
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-orange-100 to-amber-50 flex items-center justify-center text-orange-500 shrink-0">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                            </div>
                            <span class="font-bold text-gray-900">{{ $ingredient->name }}</span>
                        </div>
                    </td>
                    <td class="px-6 py-4 font-semibold text-gray-900">Rp {{ number_format($ingredient->purchase_price, 0, ',', '.') }}</td>
                    <td class="px-6 py-4 text-gray-600 font-medium">{{ rtrim(rtrim(number_format($ingredient->purchase_quantity, 2, ',', '.'), '0'), ',') }}</td>
                    <td class="px-6 py-4">
                        <span class="px-2.5 py-1 rounded-full bg-gray-100 text-gray-600 text-xs font-bold">{{ $ingredient->unit }}</span>
                    </td>
                    <td class="px-6 py-4 bg-brand-50/30">
                        <span class="font-bold text-brand-700">Rp {{ number_format($ingredient->cost_per_unit, 0, ',', '.') }}</span>
                        <span class="text-xs text-gray-400">/{{ $ingredient->unit }}</span>
                    </td>
                    <td class="px-6 py-4">
                        @php $usedCount = $ingredient->itemIngredients()->count(); @endphp
                        @if($usedCount > 0)
                            <span class="px-2.5 py-1 rounded-full bg-green-100 text-green-700 text-xs font-bold">{{ $usedCount }} produk</span>
                        @else
                            <span class="text-gray-300 italic text-xs">Belum dipakai</span>
                        @endif
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex items-center justify-end gap-2">
                            <a href="{{ route('ingredients.edit', $ingredient->id) }}" class="flex items-center justify-center w-9 h-9 rounded-xl bg-blue-50 text-blue-600 hover:bg-blue-100 active:scale-95 transition-all duration-200 shadow-sm" title="Edit Bahan">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-5M16.5 3.5a2.121 2.121 0 113 3L7 19l-4 1 1-4L16.5 3.5z"></path></svg>
                            </a>
                            <form action="{{ route('ingredients.destroy', $ingredient->id) }}" method="POST" class="block" onsubmit="confirmDelete(event, this, 'Hapus bahan {{ $ingredient->name }}?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="flex items-center justify-center w-9 h-9 rounded-xl bg-red-50 text-red-600 hover:bg-red-100 active:scale-95 transition-all duration-200 shadow-sm cursor-pointer" title="Hapus Bahan">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-6 py-12 text-center text-gray-400 italic">Belum ada bahan baku yang ditambahkan.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
