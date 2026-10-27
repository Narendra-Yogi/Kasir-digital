@extends('layouts.app')

@section('title', 'Edit Kategori')
@section('page_title', 'Edit Kategori')

@section('content')
<div class="max-w-xl">
    <div class="card card-pad rise">
        <form action="{{ route('categories.update', $category->id) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="mb-6">
                <label class="block text-sm font-bold text-gray-700 mb-2">Nama Kategori</label>
                <input type="text" name="name" value="{{ $category->name }}" class="w-full px-4 py-3 rounded-xl border border-gray-200 bg-gray-50 focus:bg-white focus:border-brand-500 outline-none transition-all" required autofocus>
                @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
            <div class="flex gap-3">
                <a href="{{ route('categories.index') }}" class="flex-1 text-center py-3.5 rounded-xl font-bold text-gray-400 hover:bg-gray-50 transition-all">Batal</a>
                <button type="submit" class="flex-1 btn-ink py-3 text-sm">Perbarui</button>
            </div>
        </form>
    </div>
</div>
@endsection