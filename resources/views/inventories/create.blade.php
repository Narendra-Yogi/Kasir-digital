@extends('layouts.app')
@section('title', 'Input Stok Baru')
@section('page_title', 'Catat Pergerakan Stok')

@section('content')
<div class="max-w-2xl bg-white rounded-3xl p-8 border border-gray-100 shadow-sm">
    <form action="{{ route('inventories.store') }}" method="POST" class="space-y-6">
        @csrf
        <div class="grid grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-2">Tanggal</label>
                <input type="date" name="date" value="{{ date('Y-m-d') }}" class="w-full px-4 py-3 rounded-xl border border-gray-200 bg-gray-50 focus:bg-white focus:border-brand-500 outline-none" required>
            </div>
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-2">Nama Item / Bahan</label>
                <input type="text" name="item_name" class="w-full px-4 py-3 rounded-xl border border-gray-200 bg-gray-50 focus:bg-white focus:border-brand-500 outline-none" placeholder="Contoh: Ayam Paha Atas" required autofocus>
            </div>
        </div>

        <div class="grid grid-cols-3 gap-6 bg-gray-50 p-6 rounded-2xl border border-gray-100">
            <div>
                <label class="block text-sm font-bold text-blue-600 mb-2">Stok Baru</label>
                <input type="number" name="new_stock" value="0" class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-blue-500 outline-none" required>
            </div>
            <div>
                <label class="block text-sm font-bold text-gray-600 mb-2">Stok Lama</label>
                <input type="number" name="old_stock" value="0" class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-gray-500 outline-none" required>
            </div>
            <div>
                <label class="block text-sm font-bold text-red-600 mb-2">Terjual (Laku)</label>
                <input type="number" name="sold" value="0" class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-red-500 outline-none" required>
            </div>
        </div>
        <p class="text-xs text-gray-400 italic text-center">*Sisa stok akan dihitung otomatis oleh sistem.</p>

        <div class="flex gap-3 pt-4">
            <a href="{{ route('inventories.index') }}" class="flex-1 text-center py-3.5 rounded-xl font-bold text-gray-400 hover:bg-gray-50">Batal</a>
            <button type="submit" class="flex-1 bg-brand-700 hover:bg-brand-800 text-white font-bold py-3.5 rounded-xl shadow-lg shadow-brand-700/20">Simpan Data Stok</button>
        </div>
    </form>
</div>
@endsection