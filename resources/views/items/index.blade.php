@extends('layouts.app')

@section('title', 'Kelola Menu')
@section('page_title', 'Daftar menu')

@section('content')
<div class="flex justify-between items-center mb-5 rise">
    <p class="text-[13px] text-[#8a8179]">{{ $items->count() }} menu terdaftar</p>
    <a href="{{ route('items.create') }}" class="btn-ink text-[13px] px-4 py-2.5 inline-flex items-center gap-2">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
        Tambah menu
    </a>
</div>

<div class="table-shell bg-white rise">
    <div class="overflow-x-auto">
        <table class="w-full text-left min-w-[620px]">
            <thead>
                <tr>
                    <th class="px-6 py-3.5 text-[11px] font-semibold text-[#7a7268]">Nama menu</th>
                    <th class="px-6 py-3.5 text-[11px] font-semibold text-[#7a7268]">Kategori</th>
                    <th class="px-6 py-3.5 text-[11px] font-semibold text-[#7a7268]">Harga jual</th>
                    <th class="px-6 py-3.5 text-[11px] font-semibold text-[#7a7268]">Stok</th>
                    <th class="px-6 py-3.5 text-[11px] font-semibold text-[#7a7268]">Status</th>
                    <th class="px-6 py-3.5 text-[11px] font-semibold text-[#7a7268] text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-[#f1ebe0]">
                @forelse($items as $item)
                <tr class="table-row-hover">
                    <td class="px-6 py-3.5">
                        <div class="flex items-center gap-3">
                            <div class="w-11 h-11 rounded-[10px] bg-[#f4eee2] border border-[#e8e0d3] flex items-center justify-center text-[#b3a897] shrink-0 overflow-hidden">
                                @if($item->image)
                                    <img src="{{ asset('storage/' . $item->image) }}" class="w-full h-full object-cover" alt="{{ $item->name }}">
                                @else
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                @endif
                            </div>
                            <span class="font-medium text-[14px]">{{ $item->name }}</span>
                        </div>
                    </td>
                    <td class="px-6 py-3.5 text-[#57504a] text-[13px]">{{ $item->category->name }}</td>
                    <td class="px-6 py-3.5 font-semibold text-[13px] tnum">Rp {{ number_format($item->price, 0, ',', '.') }}</td>
                    <td class="px-6 py-3.5 font-medium text-[13px] tnum">{{ $item->stock }}</td>
                    <td class="px-6 py-3.5">
                        @if(!$item->is_available || $item->stock === 0)
                            <span class="pill pill-danger">Habis</span>
                        @elseif($item->stock > 0 && $item->stock <= 5)
                            <span class="pill pill-warn">Sisa {{ $item->stock }}</span>
                        @else
                            <span class="pill pill-success">Tersedia</span>
                        @endif
                    </td>
                    <td class="px-6 py-3.5">
                        <div class="flex items-center justify-end gap-2">
                            <a href="{{ route('items.edit', $item->id) }}" class="icon-btn" title="Edit menu" aria-label="Edit {{ $item->name }}">
                                <svg class="w-[18px] h-[18px]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M11 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-5M16.5 3.5a2.121 2.121 0 113 3L7 19l-4 1 1-4L16.5 3.5z"></path></svg>
                            </a>
                            <form action="{{ route('items.destroy', $item->id) }}" method="POST" class="block" onsubmit="confirmDelete(event, this, 'Hapus menu ini?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="icon-btn danger" title="Hapus menu" aria-label="Hapus {{ $item->name }}">
                                    <svg class="w-[18px] h-[18px]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-12 text-center text-[#8a8179] text-sm">Belum ada menu yang ditambahkan.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
