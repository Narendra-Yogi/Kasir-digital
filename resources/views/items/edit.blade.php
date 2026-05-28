@extends('layouts.app')

@section('title', 'Edit Menu')
@section('page_title', 'Edit Menu')

@section('content')
<div class="max-w-2xl">
    <div class="mb-8">
        <a href="{{ route('items.index') }}" class="text-brand-700 font-semibold flex items-center gap-2 hover:underline">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            Kembali ke Daftar Menu
        </a>
    </div>

    <div class="bg-white rounded-3xl p-8 border border-gray-100 shadow-sm animate-fade-in-up">
        <form action="{{ route('items.update', $item->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <div class="space-y-6">
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Nama Menu</label>
                    <input type="text" name="name" value="{{ $item->name }}" class="w-full px-4 py-3 rounded-xl border border-gray-200 bg-gray-50 focus:bg-white focus:outline-none focus:border-brand-500 transition-colors" required>
                </div>

                <div class="grid grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Kategori</label>
                        <select name="category_id" class="w-full px-4 py-3 rounded-xl border border-gray-200 bg-gray-50 focus:bg-white focus:outline-none focus:border-brand-500 transition-colors cursor-pointer" required>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ $item->category_id == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Harga (Rp)</label>
                        <input type="text" name="price" value="{{ number_format($item->price, 0, ',', '.') }}" class="w-full px-4 py-3 rounded-xl border border-gray-200 bg-gray-50 focus:bg-white focus:outline-none focus:border-brand-500 transition-colors" required oninput="formatThousandsInput(this)">
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Stok (Opsional)</label>
                        <input type="number" name="stock" value="{{ $item->stock }}" class="w-full px-4 py-3 rounded-xl border border-gray-200 bg-gray-50 focus:bg-white focus:outline-none focus:border-brand-500 transition-colors" placeholder="0">
                    </div>
                </div>

                {{-- Field Upload Foto --}}
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Ubah Foto Produk / Menu</label>
                    <div class="flex items-center gap-4">
                        <div class="flex-1">
                            <input type="file" name="image" id="imageInput" class="w-full px-4 py-3 rounded-xl border border-gray-200 bg-gray-50 focus:bg-white focus:outline-none focus:border-brand-500 transition-colors text-sm" accept="image/*" onchange="previewImage(this)">
                            <p class="text-xs text-gray-400 mt-1.5">Format: JPG, JPEG, PNG, WEBP. Maksimal ukuran file: 2MB. Kosongkan jika tidak ingin mengubah foto.</p>
                        </div>
                        <div class="w-20 h-20 rounded-2xl bg-gray-50 border border-gray-100 flex items-center justify-center text-gray-300 shrink-0 overflow-hidden" id="previewContainer">
                            @if($item->image)
                                <img src="{{ asset('storage/' . $item->image) }}" class="w-full h-full object-cover">
                            @else
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                            @endif
                        </div>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Status Stok</label>
                    <div class="flex gap-4">
                        <label class="flex-1">
                            <input type="radio" name="is_available" value="1" class="hidden peer" {{ $item->is_available ? 'checked' : '' }}>
                            <div class="p-3 text-center rounded-xl border border-gray-200 bg-gray-50 peer-checked:bg-brand-50 peer-checked:border-brand-500 peer-checked:text-brand-700 cursor-pointer font-medium transition-all">
                                Tersedia
                            </div>
                        </label>
                        <label class="flex-1">
                            <input type="radio" name="is_available" value="0" class="hidden peer" {{ !$item->is_available ? 'checked' : '' }}>
                            <div class="p-3 text-center rounded-xl border border-gray-200 bg-gray-50 peer-checked:bg-red-50 peer-checked:border-red-500 peer-checked:text-red-700 cursor-pointer font-medium transition-all">
                                Habis
                            </div>
                        </label>
                    </div>
                </div>

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
    function previewImage(input) {
        const previewContainer = document.getElementById('previewContainer');
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                previewContainer.innerHTML = `<img src="${e.target.result}" class="w-full h-full object-cover">`;
            }
            reader.readAsDataURL(input.files[0]);
        } else {
            @if($item->image)
                previewContainer.innerHTML = `<img src="{{ asset('storage/' . $item->image) }}" class="w-full h-full object-cover">`;
            @else
                previewContainer.innerHTML = `<svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>`;
            @endif
        }
    }
</script>
@endsection