@extends('layouts.app')

@section('title', 'Catat Pengeluaran')
@section('page_title', 'Catat Pengeluaran Baru')

@section('content')
    <div class="max-w-2xl animate-fade-in-up">
        <div class="mb-8">
            <a href="{{ route('pengeluaran.index') }}"
                class="text-brand-700 font-semibold flex items-center gap-2 hover:underline">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18">
                    </path>
                </svg>
                Kembali
            </a>
        </div>

        <div class="card card-pad rise">
            <form action="{{ route('pengeluaran.store') }}" method="POST" class="space-y-6">
                @csrf

                <div class="grid grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Tanggal</label>
                        <input type="date" name="date" value="{{ date('Y-m-d') }}"
                            class="w-full px-4 py-3 rounded-xl border border-gray-200 bg-gray-50 focus:bg-white focus:border-brand-500 outline-none transition-all"
                            required>
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Kategori</label>
                        <select name="category"
                            class="w-full px-4 py-3 rounded-xl border border-gray-200 bg-gray-50 focus:bg-white focus:border-brand-500 outline-none cursor-pointer"
                            required>
                            <option value="belanja_harian">Belanja Harian (bahan, bumbu, kebutuhan dapur)</option>
                            <option value="operasional">Operasional (gas, listrik, plastik, bensin)</option>
                            <option value="gaji">Gaji (karyawan tetap)</option>
                            <option value="lainnya">Lainnya</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Nama Barang / Keperluan</label>
                    <input type="text" name="item_name"
                        class="w-full px-4 py-3 rounded-xl border border-gray-200 bg-gray-50 focus:bg-white focus:border-brand-500 outline-none transition-all"
                        placeholder="Contoh: bayar uang listrik" required autofocus>
                </div>

                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Total Harga (Rp)</label>
                    <input type="text" name="amount"
                        class="w-full px-4 py-3 rounded-xl border border-gray-200 bg-gray-50 focus:bg-white focus:border-brand-500 outline-none transition-all"
                        placeholder="Contoh: 150.000" required oninput="formatThousandsInput(this)">
                </div>

                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Catatan (Opsional)</label>
                    <textarea name="notes" rows="2"
                        class="w-full px-4 py-3 rounded-xl border border-gray-200 bg-gray-50 focus:bg-white focus:border-brand-500 outline-none transition-all"
                        placeholder="Tambahkan keterangan jika perlu..."></textarea>
                </div>

                <div class="pt-4 border-t border-gray-100">
                    <button type="submit"
                        class="w-full btn-ink py-3.5 text-sm">Simpan
                        Pengeluaran</button>
                </div>
            </form>
        </div>
    </div>
@endsection
