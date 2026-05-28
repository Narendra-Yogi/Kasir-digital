@extends('layouts.app')

@section('title', 'Edit Pengeluaran')
@section('page_title', 'Edit Data Pengeluaran')

@section('content')
<div class="max-w-2xl animate-fade-in-up">
    <div class="mb-8">
        <a href="{{ route('pengeluaran.index') }}" class="text-brand-700 font-semibold flex items-center gap-2 hover:underline">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            Kembali
        </a>
    </div>

    <div class="bg-white rounded-3xl p-8 border border-gray-100 shadow-sm">
        <form action="{{ route('pengeluaran.update', $pengeluaran->id) }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')
            
            <div class="grid grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Tanggal</label>
                    <input type="date" name="date" value="{{ $pengeluaran->date }}" class="w-full px-4 py-3 rounded-xl border border-gray-200 bg-gray-50 focus:bg-white focus:border-brand-500 outline-none transition-all" required>
                </div>
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Kategori</label>
                    <select name="category" class="w-full px-4 py-3 rounded-xl border border-gray-200 bg-gray-50 focus:bg-white focus:border-brand-500 outline-none cursor-pointer" required>
                        <option value="bahan" {{ $pengeluaran->category == 'bahan' ? 'selected' : '' }}>Bahan Baku</option>
                        <option value="logistik" {{ $pengeluaran->category == 'logistik' ? 'selected' : '' }}>Logistik</option>
                        <option value="lainnya" {{ $pengeluaran->category == 'lainnya' ? 'selected' : '' }}>Lainnya</option>
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-sm font-bold text-gray-700 mb-2">Nama Barang / Keperluan</label>
                <input type="text" name="item_name" value="{{ $pengeluaran->item_name }}" class="w-full px-4 py-3 rounded-xl border border-gray-200 bg-gray-50 focus:bg-white focus:border-brand-500 outline-none transition-all" required>
            </div>

            <div>
                <label class="block text-sm font-bold text-gray-700 mb-2">Total Harga (Rp)</label>
                <input type="text" name="amount" value="{{ number_format($pengeluaran->amount, 0, ',', '.') }}" class="w-full px-4 py-3 rounded-xl border border-gray-200 bg-gray-50 focus:bg-white focus:border-brand-500 outline-none transition-all" required oninput="formatThousandsInput(this)">
            </div>

            <div>
                <label class="block text-sm font-bold text-gray-700 mb-2">Catatan (Opsional)</label>
                <textarea name="notes" rows="2" class="w-full px-4 py-3 rounded-xl border border-gray-200 bg-gray-50 focus:bg-white focus:outline-none focus:border-brand-500 transition-colors text-sm text-gray-800">{{ $pengeluaran->notes }}</textarea>
            </div>

            <div class="pt-4 border-t border-gray-100 flex gap-3">
                <a href="{{ route('pengeluaran.index') }}" class="flex-1 text-center py-3.5 rounded-xl font-bold text-gray-400 hover:bg-gray-50 transition-all">Batal</a>
                <button type="submit" class="flex-1 bg-brand-700 hover:bg-brand-800 text-white font-bold py-3.5 rounded-xl shadow-lg shadow-brand-700/20 transition-all cursor-pointer">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>
@endsection
