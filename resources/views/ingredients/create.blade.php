@extends('layouts.app')

@section('title', 'Tambah Bahan Baku')
@section('page_title', 'Tambah Bahan Baku')

@section('content')
<div class="max-w-2xl">
    <div class="mb-8">
        <a href="{{ route('ingredients.index') }}" class="text-brand-700 font-semibold flex items-center gap-2 hover:underline">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            Kembali ke Daftar Bahan
        </a>
    </div>

    <div class="bg-white rounded-3xl p-8 border border-gray-100 shadow-sm animate-fade-in-up">
        <form action="{{ route('ingredients.store') }}" method="POST">
            @csrf
            <div class="space-y-6">
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Nama Bahan</label>
                    <input type="text" name="name" value="{{ old('name') }}" class="w-full px-4 py-3 rounded-xl border border-gray-200 bg-gray-50 focus:bg-white focus:outline-none focus:border-brand-500 transition-colors" placeholder="Contoh: Cabe Rawit, Tepung Terigu, Ayam" required>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Harga Beli (Rp)</label>
                        <input type="text" name="purchase_price" value="{{ old('purchase_price') }}" class="w-full px-4 py-3 rounded-xl border border-gray-200 bg-gray-50 focus:bg-white focus:outline-none focus:border-brand-500 transition-colors" placeholder="Contoh: 40.000" required oninput="formatThousandsInput(this); hitungCostPerUnit()">
                        <p class="text-xs text-gray-400 mt-1.5">Harga total per pembelian</p>
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Jumlah per Beli</label>
                        <input type="number" step="0.01" min="0.01" name="purchase_quantity" value="{{ old('purchase_quantity') }}" class="w-full px-4 py-3 rounded-xl border border-gray-200 bg-gray-50 focus:bg-white focus:outline-none focus:border-brand-500 transition-colors" placeholder="Contoh: 1000" required oninput="hitungCostPerUnit()">
                        <p class="text-xs text-gray-400 mt-1.5">Jumlah isi dalam satuan terkecil</p>
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Satuan</label>
                        <select name="unit" class="w-full px-4 py-3 rounded-xl border border-gray-200 bg-gray-50 focus:bg-white focus:outline-none focus:border-brand-500 transition-colors cursor-pointer" required>
                            <option value="" disabled selected>Pilih Satuan</option>
                            <option value="gram" {{ old('unit') == 'gram' ? 'selected' : '' }}>gram (g)</option>
                            <option value="kg" {{ old('unit') == 'kg' ? 'selected' : '' }}>kilogram (kg)</option>
                            <option value="ml" {{ old('unit') == 'ml' ? 'selected' : '' }}>mililiter (ml)</option>
                            <option value="liter" {{ old('unit') == 'liter' ? 'selected' : '' }}>liter (L)</option>
                            <option value="pcs" {{ old('unit') == 'pcs' ? 'selected' : '' }}>pcs / buah</option>
                            <option value="lembar" {{ old('unit') == 'lembar' ? 'selected' : '' }}>lembar</option>
                        </select>
                    </div>
                </div>

                {{-- Preview Harga per Unit --}}
                <div id="costPreview" class="hidden p-4 rounded-xl border bg-gradient-to-r from-brand-50 to-amber-50 border-brand-200">
                    <p class="text-xs font-bold text-brand-700 uppercase mb-2">Hasil Kalkulasi</p>
                    <div class="flex items-center gap-2">
                        <div class="w-10 h-10 rounded-xl bg-white border border-brand-200 flex items-center justify-center">
                            <svg class="w-5 h-5 text-brand-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                        </div>
                        <div>
                            <span class="text-xs text-gray-500">Harga per satuan</span>
                            <p class="font-bold text-brand-700 text-lg" id="previewCost">-</p>
                        </div>
                    </div>
                </div>

                {{-- Contoh Ilustrasi --}}
                <div class="p-4 rounded-xl bg-gray-50 border border-gray-100">
                    <p class="text-xs font-bold text-gray-500 uppercase mb-2">💡 Contoh Pengisian</p>
                    <div class="space-y-1.5 text-xs text-gray-500">
                        <p>• <strong>Cabe Rawit</strong>: Harga Rp 40.000, Jumlah 1000, Satuan gram → <span class="text-brand-600 font-bold">Rp 40/gram</span></p>
                        <p>• <strong>Ayam Dada</strong>: Harga Rp 39.000, Jumlah 1, Satuan kg → <span class="text-brand-600 font-bold">Rp 39.000/kg</span></p>
                        <p>• <strong>Styrofoam</strong>: Harga Rp 15.000, Jumlah 50, Satuan pcs → <span class="text-brand-600 font-bold">Rp 300/pcs</span></p>
                        <p>• <strong>Minyak Goreng</strong>: Harga Rp 18.000, Jumlah 1000, Satuan ml → <span class="text-brand-600 font-bold">Rp 18/ml</span></p>
                    </div>
                    <div class="mt-3 pt-3 border-t border-gray-200">
                        <p class="text-xs text-brand-600 leading-relaxed">✨ <strong>Fitur Konversi Otomatis:</strong> Anda bisa beli dalam kg tapi saat bikin resep, pilih satuan gram. Sistem otomatis konversi!</p>
                    </div>
                </div>

                {{-- Info: Pengeluaran Otomatis --}}
                <div class="p-4 rounded-xl bg-gradient-to-r from-green-50 to-emerald-50 border border-green-200">
                    <div class="flex items-start gap-3">
                        <div class="w-8 h-8 bg-green-100 text-green-600 rounded-lg flex items-center justify-center shrink-0 mt-0.5">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </div>
                        <p class="text-xs text-green-700 leading-relaxed">Saat bahan disimpan, <strong>pengeluaran akan otomatis tercatat</strong> di halaman Catatan Pengeluaran. Anda tidak perlu input pengeluaran bahan secara manual.</p>
                    </div>
                </div>

                <div class="pt-4">
                    <button type="submit" class="w-full bg-brand-700 hover:bg-brand-800 text-white font-bold py-4 rounded-xl shadow-lg shadow-brand-700/20 transition-all cursor-pointer flex items-center justify-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                        Simpan Bahan & Catat Pengeluaran
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
    function hitungCostPerUnit() {
        const hargaInput = document.querySelector('input[name="purchase_price"]');
        const qtyInput = document.querySelector('input[name="purchase_quantity"]');
        const unitSelect = document.querySelector('select[name="unit"]');
        const costPreview = document.getElementById('costPreview');
        const previewCost = document.getElementById('previewCost');

        const harga = parseInt(hargaInput.value.replace(/\./g, '')) || 0;
        const qty = parseFloat(qtyInput.value) || 0;
        const unit = unitSelect.value || 'satuan';

        if (harga > 0 && qty > 0) {
            const costPerUnit = harga / qty;
            previewCost.textContent = 'Rp ' + Math.round(costPerUnit).toLocaleString('id-ID') + ' / ' + unit;
            costPreview.classList.remove('hidden');
        } else {
            costPreview.classList.add('hidden');
        }
    }

    // Trigger saat satuan berubah juga
    document.querySelector('select[name="unit"]').addEventListener('change', hitungCostPerUnit);
</script>
@endsection
