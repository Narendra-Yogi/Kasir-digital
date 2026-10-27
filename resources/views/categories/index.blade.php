@extends('layouts.app')

@section('title', 'Kategori Menu')
@section('page_title', 'Kategori menu')

@section('content')
<div class="flex justify-between items-center mb-5 rise">
    <p class="text-[13px] text-[#8a8179]">{{ $categories->count() }} kategori</p>
    <a href="{{ route('categories.create') }}" class="btn-ink text-[13px] px-4 py-2.5 inline-flex items-center gap-2">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
        Tambah kategori
    </a>
</div>

<div class="table-shell bg-white rise">
    <table class="w-full text-left">
        <thead>
            <tr>
                <th class="px-6 py-3.5 text-[11px] font-semibold text-[#7a7268]">Nama kategori</th>
                <th class="px-6 py-3.5 text-[11px] font-semibold text-[#7a7268] text-center">Jumlah menu</th>
                <th class="px-6 py-3.5 text-[11px] font-semibold text-[#7a7268] text-right">Aksi</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-[#f1ebe0]">
            @foreach($categories as $category)
            <tr class="table-row-hover">
                <td class="px-6 py-3.5 font-medium text-[14px]">{{ $category->name }}</td>
                <td class="px-6 py-3.5 text-center">
                    <span class="pill pill-muted">{{ $category->items_count }} menu</span>
                </td>
                <td class="px-6 py-3.5">
                    <div class="flex items-center justify-end gap-2">
                        <a href="{{ route('categories.edit', $category->id) }}" class="icon-btn" title="Edit kategori" aria-label="Edit {{ $category->name }}">
                            <svg class="w-[18px] h-[18px]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M11 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-5M16.5 3.5a2.121 2.121 0 113 3L7 19l-4 1 1-4L16.5 3.5z"></path></svg>
                        </a>
                        <form action="{{ route('categories.destroy', $category->id) }}" method="POST" class="block" onsubmit="confirmDelete(event, this, 'Hapus kategori ini?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="icon-btn danger" title="Hapus kategori" aria-label="Hapus {{ $category->name }}">
                                <svg class="w-[18px] h-[18px]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                            </button>
                        </form>
                    </div>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
