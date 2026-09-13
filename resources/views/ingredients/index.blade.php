@extends('layouts.app')

@section('title', 'Bahan Baku')
@section('page_title', 'Master Bahan Baku')

@section('content')
<div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4 animate-fade-in-up">
    <div>
        <h3 class="text-gray-500 text-sm font-medium">Total Bahan: {{ $totalBahan }}</h3>
    </div>
    <a href="{{ route('ingredients.create') }}" class="bg-brand-700 hover:bg-brand-800 text-white px-6 py-2.5 rounded-xl font-bold transition-all shadow-lg shadow-brand-700/20 flex items-center gap-2 cursor-pointer text-sm">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
        Tambah Bahan
    </a>
</div>

{{-- Summary Cards: Stok Habis & Menipis --}}
<div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6 animate-fade-in-up stagger-1">
    <div class="bg-white rounded-2xl border border-gray-100 p-4 flex items-center gap-4">
        <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-emerald-100 to-green-50 flex items-center justify-center shrink-0">
            <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
        </div>
        <div>
            <p class="text-2xl font-extrabold text-gray-900">{{ $totalBahan }}</p>
            <p class="text-xs text-gray-400 font-semibold">Total Bahan</p>
        </div>
    </div>

    <div class="bg-white rounded-2xl border {{ $bahanMenipis > 0 ? 'border-amber-200 bg-amber-50/30' : 'border-gray-100' }} p-4 flex items-center gap-4">
        <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-amber-100 to-yellow-50 flex items-center justify-center shrink-0">
            <svg class="w-6 h-6 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"></path></svg>
        </div>
        <div>
            <p class="text-2xl font-extrabold {{ $bahanMenipis > 0 ? 'text-amber-600' : 'text-gray-900' }}">{{ $bahanMenipis }}</p>
            <p class="text-xs text-gray-400 font-semibold">Stok Menipis</p>
        </div>
    </div>

    <div class="bg-white rounded-2xl border {{ $bahanHabis > 0 ? 'border-red-200 bg-red-50/30' : 'border-gray-100' }} p-4 flex items-center gap-4">
        <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-red-100 to-rose-50 flex items-center justify-center shrink-0">
            <svg class="w-6 h-6 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"></path></svg>
        </div>
        <div>
            <p class="text-2xl font-extrabold {{ $bahanHabis > 0 ? 'text-red-500' : 'text-gray-900' }}">{{ $bahanHabis }}</p>
            <p class="text-xs text-gray-400 font-semibold">Stok Habis</p>
        </div>
    </div>
</div>

{{-- Info Card --}}
<div class="bg-gradient-to-r from-amber-50 to-orange-50 border border-amber-200 rounded-2xl p-4 mb-6 animate-fade-in-up stagger-2">
    <div class="flex items-start gap-3">
        <div class="w-10 h-10 bg-amber-100 text-amber-600 rounded-xl flex items-center justify-center shrink-0 mt-0.5">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
        </div>
        <div>
            <p class="text-sm font-bold text-amber-800">Tracking Stok Bahan Baku</p>
            <p class="text-xs text-amber-700 mt-1 leading-relaxed">Stok bahan <strong>otomatis berkurang</strong> setiap kali ada penjualan lewat kasir, berdasarkan resep yang sudah diatur. Gunakan tombol <strong>"+ Restok"</strong> untuk menambah stok saat beli bahan baru. Status warna: <span class="inline-flex items-center gap-1"><span class="w-2 h-2 rounded-full bg-emerald-500 inline-block"></span>Aman</span> · <span class="inline-flex items-center gap-1"><span class="w-2 h-2 rounded-full bg-amber-500 inline-block"></span>Menipis</span> · <span class="inline-flex items-center gap-1"><span class="w-2 h-2 rounded-full bg-red-500 inline-block"></span>Habis</span></p>
        </div>
    </div>
</div>

<div class="bg-white rounded-3xl border border-gray-100 overflow-hidden shadow-sm animate-fade-in-up stagger-3">
    <div class="overflow-x-auto">
        <table class="w-full text-left min-w-[850px]">
            <thead class="bg-gray-50 border-b border-gray-100">
                <tr>
                    <th class="px-6 py-4 text-xs font-bold text-gray-400 uppercase tracking-wider">Nama Bahan</th>
                    <th class="px-6 py-4 text-xs font-bold text-gray-400 uppercase tracking-wider">Harga Beli</th>
                    <th class="px-6 py-4 text-xs font-bold text-gray-400 uppercase tracking-wider">Jumlah Beli</th>
                    <th class="px-6 py-4 text-xs font-bold text-brand-600 uppercase tracking-wider bg-brand-50/50">Harga per Satuan</th>
                    <th class="px-6 py-4 text-xs font-bold text-emerald-600 uppercase tracking-wider bg-emerald-50/50">Sisa Stok</th>
                    <th class="px-6 py-4 text-xs font-bold text-gray-400 uppercase tracking-wider">Dipakai di</th>
                    <th class="px-6 py-4 text-xs font-bold text-gray-400 uppercase tracking-wider text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($ingredients as $ingredient)
                @php
                    $stockStatus = $ingredient->stock_status;
                    $stockPct = $ingredient->stock_percentage;
                @endphp
                <tr class="hover:bg-gray-50 transition-colors {{ $stockStatus === 'habis' ? 'bg-red-50/30' : ($stockStatus === 'menipis' ? 'bg-amber-50/20' : '') }}">
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-orange-100 to-amber-50 flex items-center justify-center text-orange-500 shrink-0">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                            </div>
                            <div>
                                <span class="font-bold text-gray-900">{{ $ingredient->name }}</span>
                                <span class="block text-xs text-gray-400">{{ $ingredient->unit }}</span>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 font-semibold text-gray-900">Rp {{ number_format($ingredient->purchase_price, 0, ',', '.') }}</td>
                    <td class="px-6 py-4 text-gray-600 font-medium">{{ rtrim(rtrim(number_format($ingredient->purchase_quantity, 2, ',', '.'), '0'), ',') }} {{ $ingredient->unit }}</td>
                    <td class="px-6 py-4 bg-brand-50/30">
                        <span class="font-bold text-brand-700">Rp {{ number_format($ingredient->cost_per_unit, 0, ',', '.') }}</span>
                        <span class="text-xs text-gray-400">/{{ $ingredient->unit }}</span>
                    </td>
                    <td class="px-6 py-4 bg-emerald-50/30">
                        <div class="flex flex-col gap-1.5">
                            <div class="flex items-center gap-2">
                                @if($stockStatus === 'habis')
                                    <span class="w-2.5 h-2.5 rounded-full bg-red-500 shrink-0 animate-pulse"></span>
                                    <span class="font-bold text-red-600">{{ rtrim(rtrim(number_format($ingredient->current_stock, 2, ',', '.'), '0'), ',') }}</span>
                                @elseif($stockStatus === 'menipis')
                                    <span class="w-2.5 h-2.5 rounded-full bg-amber-500 shrink-0 animate-pulse"></span>
                                    <span class="font-bold text-amber-600">{{ rtrim(rtrim(number_format($ingredient->current_stock, 2, ',', '.'), '0'), ',') }}</span>
                                @else
                                    <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 shrink-0"></span>
                                    <span class="font-bold text-emerald-700">{{ rtrim(rtrim(number_format($ingredient->current_stock, 2, ',', '.'), '0'), ',') }}</span>
                                @endif
                                <span class="text-xs text-gray-400">{{ $ingredient->unit }}</span>
                            </div>
                            {{-- Progress bar --}}
                            <div class="w-full bg-gray-200 rounded-full h-1.5">
                                <div class="h-1.5 rounded-full transition-all duration-500 {{ $stockStatus === 'habis' ? 'bg-red-500' : ($stockStatus === 'menipis' ? 'bg-amber-500' : 'bg-emerald-500') }}" style="width: {{ max(0, min(100, $stockPct)) }}%"></div>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        @php $usedCount = $ingredient->itemIngredients()->count(); @endphp
                        @if($usedCount > 0)
                            <span class="px-2.5 py-1 rounded-full bg-green-100 text-green-700 text-xs font-bold">{{ $usedCount }} produk</span>
                        @else
                            <span class="text-gray-300 italic text-xs">Belum dipakai</span>
                        @endif
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex items-center justify-end gap-2">
                            {{-- Tombol Restok --}}
                            <button onclick="openRestockModal({{ $ingredient->id }}, '{{ $ingredient->name }}', '{{ $ingredient->unit }}', {{ $ingredient->purchase_price }}, {{ $ingredient->purchase_quantity }})" class="flex items-center justify-center gap-1 px-3 h-9 rounded-xl bg-emerald-50 text-emerald-600 hover:bg-emerald-100 active:scale-95 transition-all duration-200 shadow-sm text-xs font-bold cursor-pointer" title="Restok Bahan">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                                Restok
                            </button>
                            <a href="{{ route('ingredients.edit', $ingredient->id) }}" class="flex items-center justify-center w-9 h-9 rounded-xl bg-blue-50 text-blue-600 hover:bg-blue-100 active:scale-95 transition-all duration-200 shadow-sm" title="Edit Bahan">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-5M16.5 3.5a2.121 2.121 0 113 3L7 19l-4 1 1-4L16.5 3.5z"></path></svg>
                            </a>
                            <form action="{{ route('ingredients.destroy', $ingredient->id) }}" method="POST" class="block" onsubmit="confirmDelete(event, this, 'Hapus bahan {{ $ingredient->name }}?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="flex items-center justify-center w-9 h-9 rounded-xl bg-red-50 text-red-600 hover:bg-red-100 active:scale-95 transition-all duration-200 shadow-sm cursor-pointer" title="Hapus Bahan">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-6 py-12 text-center text-gray-400 italic">Belum ada bahan baku yang ditambahkan.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- ========== MODAL RESTOK ========== --}}
<div id="restockModal" class="fixed inset-0 z-50 hidden">
    {{-- Overlay --}}
    <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" onclick="closeRestockModal()"></div>

    {{-- Modal Content --}}
    <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-full max-w-md">
        <div class="bg-white rounded-3xl p-8 shadow-2xl border border-gray-100 mx-4">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h3 class="text-lg font-extrabold text-gray-900">Restok Bahan</h3>
                    <p class="text-sm text-gray-500 mt-0.5" id="restockSubtitle">-</p>
                </div>
                <button onclick="closeRestockModal()" class="w-9 h-9 rounded-xl bg-gray-100 hover:bg-gray-200 flex items-center justify-center text-gray-400 transition-colors cursor-pointer">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>

            <form id="restockForm" method="POST">
                @csrf
                <div class="space-y-5">
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Jumlah Beli Tambahan</label>
                        <div class="flex items-center gap-2">
                            <input type="number" step="0.01" min="0.01" name="restock_quantity" id="restockQuantity" class="flex-1 px-4 py-3 rounded-xl border border-gray-200 bg-gray-50 focus:bg-white focus:outline-none focus:border-brand-500 transition-colors" placeholder="Contoh: 1000" required oninput="hitungRestockTotal()">
                            <span class="text-sm font-bold text-gray-500 bg-gray-100 px-4 py-3 rounded-xl" id="restockUnit">-</span>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Harga Beli (Rp)</label>
                        <input type="text" name="restock_price" id="restockPrice" class="w-full px-4 py-3 rounded-xl border border-gray-200 bg-gray-50 focus:bg-white focus:outline-none focus:border-brand-500 transition-colors" placeholder="Contoh: 40.000" required oninput="formatThousandsInput(this); hitungRestockTotal()">
                        <p class="text-xs text-gray-400 mt-1.5">Harga total pembelian kali ini</p>
                    </div>

                    {{-- Preview --}}
                    <div id="restockPreview" class="hidden p-4 rounded-xl bg-gradient-to-r from-emerald-50 to-green-50 border border-emerald-200">
                        <p class="text-xs font-bold text-emerald-700 uppercase mb-1">Harga per Satuan Baru</p>
                        <p class="font-bold text-emerald-800 text-lg" id="restockCostPreview">-</p>
                    </div>

                    <div class="p-3 rounded-xl bg-blue-50 border border-blue-100">
                        <p class="text-xs text-blue-600 leading-relaxed">
                            <strong>Info:</strong> Stok akan bertambah sesuai jumlah beli. Harga per satuan & HPP produk terkait akan otomatis dihitung ulang. Pengeluaran juga otomatis tercatat.
                        </p>
                    </div>

                    <button type="submit" class="w-full bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-3.5 rounded-xl shadow-lg shadow-emerald-600/20 transition-all cursor-pointer flex items-center justify-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                        Tambah Stok & Catat Pengeluaran
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function openRestockModal(id, name, unit, price, quantity) {
        const modal = document.getElementById('restockModal');
        const form = document.getElementById('restockForm');
        const subtitle = document.getElementById('restockSubtitle');
        const unitLabel = document.getElementById('restockUnit');
        const priceInput = document.getElementById('restockPrice');
        const qtyInput = document.getElementById('restockQuantity');

        form.action = `/ingredients/${id}/restock`;
        subtitle.textContent = name;
        unitLabel.textContent = unit;
        priceInput.value = price.toLocaleString('id-ID');
        qtyInput.value = quantity;

        modal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';

        hitungRestockTotal();
        qtyInput.focus();
        qtyInput.select();
    }

    function closeRestockModal() {
        const modal = document.getElementById('restockModal');
        modal.classList.add('hidden');
        document.body.style.overflow = '';
    }

    function hitungRestockTotal() {
        const qty = parseFloat(document.getElementById('restockQuantity').value) || 0;
        const price = parseInt(document.getElementById('restockPrice').value.replace(/\./g, '')) || 0;
        const unit = document.getElementById('restockUnit').textContent;
        const preview = document.getElementById('restockPreview');
        const previewText = document.getElementById('restockCostPreview');

        if (qty > 0 && price > 0) {
            const costPerUnit = price / qty;
            previewText.textContent = 'Rp ' + Math.round(costPerUnit).toLocaleString('id-ID') + ' / ' + unit;
            preview.classList.remove('hidden');
        } else {
            preview.classList.add('hidden');
        }
    }

    // Tutup modal dengan Escape
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') closeRestockModal();
    });
</script>
@endsection
