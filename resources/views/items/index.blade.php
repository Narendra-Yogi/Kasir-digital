@extends('layouts.app')

@section('title', 'Kelola Menu')
@section('page_title', 'Daftar Menu')

@section('content')
<div class="flex justify-between items-center mb-8 animate-fade-in-up">
    <div>
        <h3 class="text-gray-500 text-sm font-medium">Total Menu: {{ $items->count() }}</h3>
    </div>
    <a href="{{ route('items.create') }}" class="bg-brand-700 hover:bg-brand-800 text-white px-6 py-2.5 rounded-xl font-bold transition-all shadow-lg shadow-brand-700/20 flex items-center gap-2 cursor-pointer">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
        Tambah Menu
    </a>
</div>

<div class="bg-white rounded-3xl border border-gray-100 overflow-hidden shadow-sm animate-fade-in-up stagger-1">
    <div class="overflow-x-auto">
        <table class="w-full text-left min-w-[700px]">
            <thead class="bg-gray-50 border-b border-gray-100">
                <tr>
                    <th class="px-6 py-4 text-xs font-bold text-gray-400 uppercase tracking-wider">Nama Menu</th>
                    <th class="px-6 py-4 text-xs font-bold text-gray-400 uppercase tracking-wider">Kategori</th>
                    <th class="px-6 py-4 text-xs font-bold text-gray-400 uppercase tracking-wider">Harga Jual</th>
                    <th class="px-6 py-4 text-xs font-bold text-gray-400 uppercase tracking-wider">HPP</th>
                    <th class="px-6 py-4 text-xs font-bold text-gray-400 uppercase tracking-wider">Margin</th>
                    <th class="px-6 py-4 text-xs font-bold text-gray-400 uppercase tracking-wider">Stok</th>
                    <th class="px-6 py-4 text-xs font-bold text-gray-400 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-4 text-xs font-bold text-gray-400 uppercase tracking-wider text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($items as $item)
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-12 rounded-xl bg-gray-50 border border-gray-100 flex items-center justify-center text-gray-300 shrink-0 overflow-hidden">
                                @if($item->image)
                                    <img src="{{ asset('storage/' . $item->image) }}" class="w-full h-full object-cover">
                                @else
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                @endif
                            </div>
                            <span class="font-bold text-gray-900">{{ $item->name }}</span>
                        </div>
                    </td>
                    <td class="px-6 py-4 text-gray-500 text-sm">{{ $item->category->name }}</td>
                    <td class="px-6 py-4 font-semibold text-gray-900">Rp {{ number_format($item->price, 0, ',', '.') }}</td>
                    <td class="px-6 py-4 text-sm">
                        @if($item->hpp > 0)
                            <div class="group relative">
                                <span class="text-gray-700 font-medium cursor-help border-b border-dashed border-gray-300">Rp {{ number_format($item->hpp, 0, ',', '.') }}</span>
                                @if($item->itemIngredients->count() > 0)
                                {{-- Tooltip detail resep bahan --}}
                                <div class="absolute z-50 bottom-full left-0 mb-2 w-64 p-3 bg-gray-900 text-white text-xs rounded-xl shadow-xl opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 pointer-events-none">
                                    <p class="font-bold text-amber-300 mb-2">📊 Detail Resep Bahan:</p>
                                    @foreach($item->itemIngredients as $ii)
                                    <div class="flex justify-between py-0.5">
                                        <span>{{ $ii->ingredient->name }} × {{ rtrim(rtrim(number_format($ii->quantity_needed, 2, ',', '.'), '0'), ',') }} {{ $ii->unit_used ?? $ii->ingredient->unit }}</span>
                                        <span class="font-medium">Rp {{ number_format($ii->cost, 0, ',', '.') }}</span>
                                    </div>
                                    @endforeach
                                    <div class="border-t border-gray-600 mt-1.5 pt-1.5 flex justify-between font-bold">
                                        <span>Total HPP</span>
                                        <span class="text-amber-300">Rp {{ number_format($item->hpp, 0, ',', '.') }}</span>
                                    </div>
                                    <div class="absolute bottom-0 left-4 translate-y-full w-0 h-0 border-l-4 border-r-4 border-t-4 border-transparent border-t-gray-900"></div>
                                </div>
                                <p class="text-[10px] text-gray-400 mt-0.5">{{ $item->itemIngredients->count() }} bahan</p>
                                @endif
                            </div>
                        @else
                            <span class="text-gray-300 italic text-xs">Belum ada resep</span>
                        @endif
                    </td>
                    <td class="px-6 py-4">
                        @if($item->hpp > 0)
                            @php $margin = $item->margin; @endphp
                            @if($margin >= 40)
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-green-100 text-green-700 text-xs font-bold">
                                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M12 7a1 1 0 110-2h5a1 1 0 011 1v5a1 1 0 11-2 0V8.414l-4.293 4.293a1 1 0 01-1.414 0L8 10.414l-4.293 4.293a1 1 0 01-1.414-1.414l5-5a1 1 0 011.414 0L11 10.586 14.586 7H12z" clip-rule="evenodd"/></svg>
                                    {{ $margin }}%
                                </span>
                            @elseif($margin >= 20)
                                <span class="px-2.5 py-1 rounded-full bg-amber-100 text-amber-700 text-xs font-bold">{{ $margin }}%</span>
                            @else
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-red-100 text-red-700 text-xs font-bold">
                                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M12 13a1 1 0 100 2h5a1 1 0 001-1V9a1 1 0 10-2 0v2.586l-4.293-4.293a1 1 0 00-1.414 0L8 9.586 3.707 5.293a1 1 0 00-1.414 1.414l5 5a1 1 0 001.414 0L11 9.414 14.586 13H12z" clip-rule="evenodd"/></svg>
                                    {{ $margin }}%
                                </span>
                            @endif
                        @else
                            <span class="text-gray-300 text-xs">-</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 font-medium text-gray-700">{{ $item->stock }}</td>
                    <td class="px-6 py-4">
                        @if(!$item->is_available || $item->stock === 0)
                            <span class="px-3 py-1 rounded-full bg-red-100 text-red-700 text-xs font-bold">Habis</span>
                        @elseif($item->stock > 0 && $item->stock <= 5)
                            <span class="px-3 py-1 rounded-full bg-amber-100 text-amber-700 text-xs font-bold">Sedang (Sisa {{ $item->stock }})</span>
                        @else
                            <span class="px-3 py-1 rounded-full bg-green-100 text-green-700 text-xs font-bold">Tersedia</span>
                        @endif
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex items-center justify-end gap-2">
                            <a href="{{ route('items.edit', $item->id) }}" class="flex items-center justify-center w-9 h-9 rounded-xl bg-blue-50 text-blue-600 hover:bg-blue-100 active:scale-95 transition-all duration-200 shadow-sm" title="Edit Menu">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-5M16.5 3.5a2.121 2.121 0 113 3L7 19l-4 1 1-4L16.5 3.5z"></path></svg>
                            </a>
                            <form action="{{ route('items.destroy', $item->id) }}" method="POST" class="block" onsubmit="confirmDelete(event, this, 'Hapus menu ini?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="flex items-center justify-center w-9 h-9 rounded-xl bg-red-50 text-red-600 hover:bg-red-100 active:scale-95 transition-all duration-200 shadow-sm cursor-pointer" title="Hapus Menu">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="px-6 py-12 text-center text-gray-400 italic">Belum ada menu yang ditambahkan.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection