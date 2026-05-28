@extends('layouts.app')
@section('title', 'Edit Stok')
@section('page_title', 'Perbarui Data Stok')

@section('content')
<div class="max-w-2xl bg-white rounded-3xl p-8 border border-gray-100 shadow-sm">
    <form action="{{ route('inventories.update', $inventory->id) }}" method="POST" class="space-y-6">
        @csrf @method('PUT')
        <div class="grid grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-2">Tanggal</label>
                <input type="date" name="date" value="{{ $inventory->date }}" class="w-full px-4 py-3 rounded-xl border border-gray-200 bg-gray-50 focus:bg-white focus:border-brand-500 outline-none" required>
            </div>
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-2">Nama Item / Bahan</label>
                <input type="text" name="item_name" value="{{ $inventory->item_name }}" class="w-full px-4 py-3 rounded-xl border border-gray-200 bg-gray-50 focus:bg-white focus:border-brand-500 outline-none" required>
            </div>
        </div>

        <div class="grid grid-cols-3 gap-6 bg-gray-50 p-6 rounded-2xl border border-gray-100">
            <div>
                <label class="block text-sm font-bold text-blue-600 mb-2">Stok Baru</label>
                <input type="number" name="new_stock" value="{{ $inventory->new_stock }}" class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-blue-500 outline-none" required>
            </div>
            <div>
                <label class="block text-sm font-bold text-gray-600 mb-2">Stok Lama</label>
                <input type="number" name="old_stock" value="{{ $inventory->old_stock }}" class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-gray-500 outline-none" required>
            </div>
            <div>
                <label class="block text-sm font-bold text-red-600 mb-2">Terjual (Laku)</label>
                <input type="number" name="sold" value="{{ $inventory->sold }}" class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-red-500 outline-none" required>
            </div>
        </div>

        <div class="flex gap-3 pt-4">
            <a href="{{ route('inventories.index') }}" class="flex-1 text-center py-3.5 rounded-xl font-bold text-gray-400 hover:bg-gray-50">Batal</a>
            <button type="submit" class="flex-1 bg-brand-700 hover:bg-brand-800 text-white font-bold py-3.5 rounded-xl shadow-lg shadow-brand-700/20">Simpan Perubahan</button>
        </div>
    </form>
</div>
@endsection