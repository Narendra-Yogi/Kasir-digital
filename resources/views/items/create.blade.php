@extends('layouts.app')

@section('title', 'Tambah Menu Baru')
@section('page_title', 'Tambah Menu Baru')

@section('content')
<div class="max-w-2xl">
    <div class="mb-8">
        <a href="{{ route('items.index') }}" class="text-brand-700 font-semibold flex items-center gap-2 hover:underline">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            Kembali ke Daftar Menu
        </a>
    </div>

    <div class="bg-white rounded-3xl p-8 border border-gray-100 shadow-sm animate-fade-in-up">
        <form action="{{ route('items.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="space-y-6">
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Nama Menu</label>
                    <input type="text" name="name" class="w-full px-4 py-3 rounded-xl border border-gray-200 bg-gray-50 focus:bg-white focus:outline-none focus:border-brand-500 transition-colors" placeholder="Contoh: Ayam Geprek Sambal Ijo" required>
                </div>

                <div class="grid grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Kategori</label>
                        <select name="category_id" class="w-full px-4 py-3 rounded-xl border border-gray-200 bg-gray-50 focus:bg-white focus:outline-none focus:border-brand-500 transition-colors cursor-pointer" required>
                            <option value="" disabled selected>Pilih Kategori</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Harga Jual (Rp)</label>
                        <input type="text" name="price" class="w-full px-4 py-3 rounded-xl border border-gray-200 bg-gray-50 focus:bg-white focus:outline-none focus:border-brand-500 transition-colors" placeholder="Contoh: 20.000" required oninput="formatThousandsInput(this); hitungHppOtomatis()">
                    </div>

                    <div class="col-span-2 md:col-span-1">
                        <label class="block text-sm font-bold text-gray-700 mb-2">Stok Awal (Opsional)</label>
                        <input type="number" name="stock" class="w-full px-4 py-3 rounded-xl border border-gray-200 bg-gray-50 focus:bg-white focus:outline-none focus:border-brand-500 transition-colors" placeholder="Contoh: 10 (Kosongkan jika stok 0)">
                    </div>
                </div>

                {{-- ==================== RESEP BAHAN (HPP OTOMATIS) ==================== --}}
                <div class="border-t border-gray-100 pt-6">
                    <div class="flex items-center justify-between mb-4">
                        <div>
                            <h3 class="text-sm font-bold text-gray-700 flex items-center gap-2">
                                <svg class="w-5 h-5 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"></path></svg>
                                Resep Bahan (HPP Otomatis)
                            </h3>
                            <p class="text-xs text-gray-400 mt-1">Pilih bahan-bahan yang dibutuhkan per 1 porsi. HPP akan dihitung otomatis.</p>
                        </div>
                        <button type="button" onclick="tambahBahan()" class="bg-amber-50 hover:bg-amber-100 text-amber-700 px-4 py-2 rounded-xl font-bold text-xs transition-colors flex items-center gap-1.5 cursor-pointer border border-amber-200">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                            Tambah Bahan
                        </button>
                    </div>

                    {{-- Container bahan-bahan --}}
                    <div id="ingredientContainer" class="space-y-3">
                        {{-- Bahan ditambahkan via JavaScript --}}
                    </div>

                    @if($ingredients->isEmpty())
                    <div class="p-4 rounded-xl bg-amber-50 border border-amber-200 mt-3">
                        <p class="text-xs text-amber-700">⚠️ Belum ada data bahan baku. <a href="{{ route('ingredients.create') }}" class="underline font-bold">Tambah bahan baku dulu</a> sebelum membuat resep.</p>
                    </div>
                    @endif
                </div>

                {{-- Preview HPP Otomatis --}}
                <div id="hppPreview" class="hidden p-5 rounded-xl border bg-gradient-to-r from-brand-50 to-green-50 border-brand-200">
                    <p class="text-xs font-bold text-brand-700 uppercase mb-3">📊 Kalkulasi HPP Otomatis</p>
                    <div id="hppBreakdown" class="space-y-1.5 mb-3">
                        {{-- Breakdown bahan ditampilkan via JavaScript --}}
                    </div>
                    <div class="border-t border-brand-200 pt-3 flex items-center justify-between">
                        <div>
                            <span class="text-xs text-gray-500">Total HPP per Porsi</span>
                            <p class="font-black text-brand-700 text-xl" id="previewHpp">Rp 0</p>
                        </div>
                        <div id="marginInfo" class="text-right hidden">
                            <span class="text-xs text-gray-500">Laba & Margin</span>
                            <p class="font-bold text-sm">
                                <span id="previewLaba" class="text-gray-900">-</span>
                                <span id="previewMargin" class="ml-2 px-2 py-0.5 rounded-full text-xs font-bold">-</span>
                            </p>
                        </div>
                    </div>
                </div>

                {{-- Field Upload Foto --}}
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Foto Produk / Menu</label>
                    <div class="flex items-center gap-4">
                        <div class="flex-1">
                            <input type="file" name="image" id="imageInput" class="w-full px-4 py-3 rounded-xl border border-gray-200 bg-gray-50 focus:bg-white focus:outline-none focus:border-brand-500 transition-colors text-sm" accept="image/*" onchange="previewImage(this)">
                            <p class="text-xs text-gray-400 mt-1.5">Format: JPG, JPEG, PNG, WEBP. Maksimal ukuran file: 2MB.</p>
                        </div>
                        <div class="w-20 h-20 rounded-2xl bg-gray-50 border border-gray-100 flex items-center justify-center text-gray-300 shrink-0 overflow-hidden" id="previewContainer">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                        </div>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Status Stok</label>
                    <div class="flex gap-4">
                        <label class="flex-1">
                            <input type="radio" name="is_available" value="1" class="hidden peer" checked>
                            <div class="p-3 text-center rounded-xl border border-gray-200 bg-gray-50 peer-checked:bg-brand-50 peer-checked:border-brand-500 peer-checked:text-brand-700 cursor-pointer font-medium transition-all">
                                Tersedia
                            </div>
                        </label>
                        <label class="flex-1">
                            <input type="radio" name="is_available" value="0" class="hidden peer">
                            <div class="p-3 text-center rounded-xl border border-gray-200 bg-gray-50 peer-checked:bg-red-50 peer-checked:border-red-500 peer-checked:text-red-700 cursor-pointer font-medium transition-all">
                                Habis
                            </div>
                        </label>
                    </div>
                </div>

                <div class="pt-4">
                    <button type="submit" class="w-full bg-brand-700 hover:bg-brand-800 text-white font-bold py-4 rounded-xl shadow-lg shadow-brand-700/20 transition-all cursor-pointer">
                        Simpan Menu Baru
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
    // Data bahan baku dari database (termasuk compatible_units)
    const ingredientsData = @json($ingredients);

    // Tabel konversi satuan (sinkron dengan Ingredient::UNIT_CONVERSIONS di PHP)
    const unitConversions = {
        'gram':  { 'kg': 0.001 },
        'kg':    { 'gram': 1000 },
        'ml':    { 'liter': 0.001 },
        'liter': { 'ml': 1000 },
    };

    // Label satuan yang lebih readable
    const unitLabels = {
        'gram': 'gram (g)',
        'kg': 'kilogram (kg)',
        'ml': 'mililiter (ml)',
        'liter': 'liter (L)',
        'pcs': 'pcs / buah',
        'lembar': 'lembar',
        'butir': 'butir',
    };

    function getConversionFactor(fromUnit, toUnit) {
        if (fromUnit === toUnit) return 1;
        return (unitConversions[fromUnit] && unitConversions[fromUnit][toUnit]) || 1;
    }

    let ingredientIndex = 0;

    function tambahBahan(selectedId = '', selectedQty = '', selectedUnit = '') {
        const container = document.getElementById('ingredientContainer');
        const idx = ingredientIndex++;

        const row = document.createElement('div');
        row.className = 'flex items-center gap-3 p-3 rounded-xl bg-gray-50 border border-gray-100 animate-fade-in-up';
        row.id = 'ingredient-row-' + idx;

        let optionsHtml = '<option value="" disabled ' + (!selectedId ? 'selected' : '') + '>Pilih Bahan</option>';
        ingredientsData.forEach(function(ing) {
            const sel = (ing.id == selectedId) ? 'selected' : '';
            optionsHtml += '<option value="' + ing.id + '" data-cost="' + ing.cost_per_unit + '" data-unit="' + ing.unit + '" data-compatible=\'' + JSON.stringify(ing.compatible_units) + '\' ' + sel + '>' + ing.name + ' (Rp ' + Math.round(ing.cost_per_unit).toLocaleString('id-ID') + '/' + ing.unit + ')</option>';
        });

        // Build unit dropdown jika ada selectedId
        let unitOptionsHtml = '<option value="">satuan</option>';
        if (selectedId) {
            const ing = ingredientsData.find(i => i.id == selectedId);
            if (ing && ing.compatible_units) {
                unitOptionsHtml = '';
                ing.compatible_units.forEach(function(u) {
                    const sel = (selectedUnit && selectedUnit === u) ? 'selected' : (!selectedUnit && u === ing.unit ? 'selected' : '');
                    unitOptionsHtml += '<option value="' + u + '" ' + sel + '>' + (unitLabels[u] || u) + '</option>';
                });
            }
        }

        row.innerHTML = `
            <div class="flex-1 min-w-0">
                <select name="ingredients[${idx}][id]" class="ingredient-select w-full px-3 py-2.5 rounded-lg border border-gray-200 bg-white text-sm focus:outline-none focus:border-brand-500 cursor-pointer" onchange="onIngredientChange(this, ${idx})" required>
                    ${optionsHtml}
                </select>
            </div>
            <div class="w-24 shrink-0">
                <input type="number" step="0.01" min="0.01" name="ingredients[${idx}][quantity]" value="${selectedQty}" class="w-full px-3 py-2.5 rounded-lg border border-gray-200 bg-white text-sm focus:outline-none focus:border-brand-500 text-center" placeholder="Jumlah" oninput="hitungHppOtomatis()" required>
            </div>
            <div class="w-28 shrink-0">
                <select name="ingredients[${idx}][unit]" class="unit-select w-full px-2.5 py-2.5 rounded-lg border border-gray-200 bg-white text-xs focus:outline-none focus:border-brand-500 cursor-pointer" onchange="hitungHppOtomatis()">
                    ${unitOptionsHtml}
                </select>
            </div>
            <div class="w-24 text-right shrink-0">
                <span class="text-xs font-bold text-brand-600 ingredient-cost">Rp 0</span>
            </div>
            <button type="button" onclick="hapusBahan(${idx})" class="w-8 h-8 rounded-lg bg-red-50 text-red-500 hover:bg-red-100 flex items-center justify-center shrink-0 transition-colors cursor-pointer">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        `;

        container.appendChild(row);
        hitungHppOtomatis();
    }

    /**
     * Dipanggil saat dropdown bahan berubah.
     * Update dropdown satuan sesuai bahan yang dipilih.
     */
    function onIngredientChange(selectEl, idx) {
        const option = selectEl.selectedOptions[0];
        const row = document.getElementById('ingredient-row-' + idx);
        const unitSelect = row.querySelector('.unit-select');

        if (option.value) {
            const compatibleUnits = JSON.parse(option.dataset.compatible || '[]');
            const ingredientUnit = option.dataset.unit;

            let unitHtml = '';
            compatibleUnits.forEach(function(u) {
                const sel = (u === ingredientUnit) ? 'selected' : '';
                unitHtml += '<option value="' + u + '" ' + sel + '>' + (unitLabels[u] || u) + '</option>';
            });
            unitSelect.innerHTML = unitHtml;
        } else {
            unitSelect.innerHTML = '<option value="">satuan</option>';
        }

        hitungHppOtomatis();
    }

    function getUnitById(id) {
        const ing = ingredientsData.find(i => i.id == id);
        return ing ? ing.unit : 'satuan';
    }

    function hapusBahan(idx) {
        const row = document.getElementById('ingredient-row-' + idx);
        if (row) {
            row.remove();
            hitungHppOtomatis();
        }
    }

    function hitungHppOtomatis() {
        const container = document.getElementById('ingredientContainer');
        const rows = container.querySelectorAll('[id^="ingredient-row-"]');
        const hppPreview = document.getElementById('hppPreview');
        const hppBreakdown = document.getElementById('hppBreakdown');
        const previewHpp = document.getElementById('previewHpp');
        const marginInfo = document.getElementById('marginInfo');
        const previewLaba = document.getElementById('previewLaba');
        const previewMargin = document.getElementById('previewMargin');

        let totalHpp = 0;
        let breakdownHtml = '';
        let hasIngredient = false;

        rows.forEach(function(row) {
            const select = row.querySelector('.ingredient-select');
            const qtyInput = row.querySelector('input[type="number"]');
            const unitSelect = row.querySelector('.unit-select');
            const costLabel = row.querySelector('.ingredient-cost');

            if (select.value && qtyInput.value) {
                const option = select.selectedOptions[0];
                const costPerUnit = parseFloat(option.dataset.cost) || 0; // cost per unit asli bahan
                const ingredientUnit = option.dataset.unit; // satuan asli bahan
                const qty = parseFloat(qtyInput.value) || 0;
                const usedUnit = unitSelect.value || ingredientUnit;

                // Konversi quantity ke satuan asli bahan, lalu hitung biaya
                const conversionFactor = getConversionFactor(usedUnit, ingredientUnit);
                const convertedQty = qty * conversionFactor;
                const cost = costPerUnit * convertedQty;

                costLabel.textContent = 'Rp ' + Math.round(cost).toLocaleString('id-ID');
                totalHpp += cost;
                hasIngredient = true;

                breakdownHtml += `<div class="flex justify-between text-xs">
                    <span class="text-gray-600">${option.text.split(' (')[0]} × ${qty} ${usedUnit}</span>
                    <span class="font-medium text-gray-700">Rp ${Math.round(cost).toLocaleString('id-ID')}</span>
                </div>`;
            } else {
                costLabel.textContent = 'Rp 0';
            }
        });

        if (hasIngredient) {
            hppBreakdown.innerHTML = breakdownHtml;
            previewHpp.textContent = 'Rp ' + Math.ceil(totalHpp).toLocaleString('id-ID');
            hppPreview.classList.remove('hidden');

            // Hitung margin jika harga jual sudah diisi
            const hargaInput = document.querySelector('input[name="price"]');
            const harga = parseInt(hargaInput.value.replace(/\./g, '')) || 0;

            if (harga > 0) {
                const laba = harga - Math.ceil(totalHpp);
                const margin = ((laba / harga) * 100).toFixed(1);

                previewLaba.textContent = 'Rp ' + laba.toLocaleString('id-ID');
                
                let marginClass = 'bg-green-100 text-green-700';
                if (margin < 20) marginClass = 'bg-red-100 text-red-700';
                else if (margin < 40) marginClass = 'bg-amber-100 text-amber-700';

                previewMargin.textContent = margin + '%';
                previewMargin.className = 'ml-2 px-2 py-0.5 rounded-full text-xs font-bold ' + marginClass;
                marginInfo.classList.remove('hidden');
            } else {
                marginInfo.classList.add('hidden');
            }
        } else {
            hppPreview.classList.add('hidden');
        }
    }

    function previewImage(input) {
        const previewContainer = document.getElementById('previewContainer');
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                previewContainer.innerHTML = `<img src="${e.target.result}" class="w-full h-full object-cover">`;
            }
            reader.readAsDataURL(input.files[0]);
        } else {
            previewContainer.innerHTML = `<svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>`;
        }
    }
</script>
@endsection