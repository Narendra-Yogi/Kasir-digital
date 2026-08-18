@extends('layouts.app')

@section('title', 'Edit Bahan Baku')
@section('page_title', 'Edit Bahan Baku')

@section('content')
<div class="max-w-2xl">
    <div class="mb-8">
        <a href="{{ route('ingredients.index') }}" class="text-brand-700 font-semibold flex items-center gap-2 hover:underline">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            Kembali ke Daftar Bahan
        </a>
    </div>

    <div class="bg-white rounded-3xl p-8 border border-gray-100 shadow-sm animate-fade-in-up">
        <form action="{{ route('ingredients.update', $ingredient->id) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="space-y-6">
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Nama Bahan</label>
                    <input type="text" name="name" value="{{ $ingredient->name }}" class="w-full px-4 py-3 rounded-xl border border-gray-200 bg-gray-50 focus:bg-white focus:outline-none focus:border-brand-500 transition-colors" required>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Harga Beli (Rp)</label>
                        <input type="text" name="purchase_price" value="{{ number_format($ingredient->purchase_price, 0, ',', '.') }}" class="w-full px-4 py-3 rounded-xl border border-gray-200 bg-gray-50 focus:bg-white focus:outline-none focus:border-brand-500 transition-colors" required oninput="formatThousandsInput(this); hitungCostPerUnit()">
                        <p class="text-xs text-gray-400 mt-1.5">Harga total per pembelian</p>
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Jumlah per Beli</label>
                        <input type="number" step="0.01" min="0.01" name="purchase_quantity" value="{{ $ingredient->purchase_quantity }}" class="w-full px-4 py-3 rounded-xl border border-gray-200 bg-gray-50 focus:bg-white focus:outline-none focus:border-brand-500 transition-colors" required oninput="hitungCostPerUnit()">
                        <p class="text-xs text-gray-400 mt-1.5">Jumlah isi dalam satuan terkecil</p>
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Satuan</label>
                        <select name="unit" class="w-full px-4 py-3 rounded-xl border border-gray-200 bg-gray-50 focus:bg-white focus:outline-none focus:border-brand-500 transition-colors cursor-pointer" required>
                            <option value="gram" {{ $ingredient->unit == 'gram' ? 'selected' : '' }}>gram (g)</option>
                            <option value="kg" {{ $ingredient->unit == 'kg' ? 'selected' : '' }}>kilogram (kg)</option>
                            <option value="ml" {{ $ingredient->unit == 'ml' ? 'selected' : '' }}>mililiter (ml)</option>
                            <option value="liter" {{ $ingredient->unit == 'liter' ? 'selected' : '' }}>liter (L)</option>
                            <option value="pcs" {{ $ingredient->unit == 'pcs' ? 'selected' : '' }}>pcs / buah</option>
                            <option value="lembar" {{ $ingredient->unit == 'lembar' ? 'selected' : '' }}>lembar</option>
                        </select>
                    </div>
                </div>

                {{-- Preview Harga per Unit --}}
                <div id="costPreview" class="p-4 rounded-xl border bg-gradient-to-r from-brand-50 to-amber-50 border-brand-200">
                    <p class="text-xs font-bold text-brand-700 uppercase mb-2">Hasil Kalkulasi</p>
                    <div class="flex items-center gap-2">
                        <div class="w-10 h-10 rounded-xl bg-white border border-brand-200 flex items-center justify-center">
                            <svg class="w-5 h-5 text-brand-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                        </div>
                        <div>
                            <span class="text-xs text-gray-500">Harga per satuan</span>
                            <p class="font-bold text-brand-700 text-lg" id="previewCost">Rp {{ number_format($ingredient->cost_per_unit, 0, ',', '.') }} / {{ $ingredient->unit }}</p>
                        </div>
                    </div>
                </div>

                {{-- Info: Produk yang menggunakan bahan ini --}}
                @php $usedItems = $ingredient->items; @endphp
                @if($usedItems->count() > 0)
                <div class="p-4 rounded-xl bg-blue-50 border border-blue-200">
                    <p class="text-xs font-bold text-blue-700 uppercase mb-2">⚡ Digunakan oleh {{ $usedItems->count() }} produk</p>
                    <p class="text-xs text-blue-600 mb-2">Mengubah harga bahan ini akan otomatis menghitung ulang HPP produk berikut:</p>
                    <div class="flex flex-wrap gap-2">
                        @foreach($usedItems as $usedItem)
                        <span class="px-2.5 py-1 bg-white rounded-full text-xs font-semibold text-blue-700 border border-blue-200">{{ $usedItem->name }}</span>
                        @endforeach
                    </div>
                </div>
                @endif

                <div class="pt-4">
                    <button type="submit" class="w-full bg-brand-700 hover:bg-brand-800 text-white font-bold py-4 rounded-xl shadow-lg shadow-brand-700/20 transition-all cursor-pointer">
                        Simpan Perubahan
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

    document.querySelector('select[name="unit"]').addEventListener('change', hitungCostPerUnit);
</script>
@endsection
