@extends('layouts.app')

@section('title', 'Tambah Akun')
@section('page_title', 'Tambah Pengguna Baru')

@section('content')
<div class="max-w-2xl">
    <div class="bg-white rounded-3xl p-8 border border-gray-100 shadow-sm">
        <form action="{{ route('users.store') }}" method="POST" class="space-y-6">
            @csrf
            
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-2">Nama Lengkap</label>
                <input type="text" name="name" value="{{ old('name') }}" class="w-full px-4 py-3 rounded-xl border border-gray-200 bg-gray-50 focus:bg-white focus:border-brand-500 outline-none transition-all" required autofocus>
                @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="grid grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Username Login</label>
                    <input type="text" name="username" value="{{ old('username') }}" class="w-full px-4 py-3 rounded-xl border border-gray-200 bg-gray-50 focus:bg-white focus:border-brand-500 outline-none transition-all" required>
                    @error('username') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Hak Akses</label>
                    <select name="role" class="w-full px-4 py-3 rounded-xl border border-gray-200 bg-gray-50 focus:bg-white focus:border-brand-500 outline-none cursor-pointer" required>
                        <option value="kasir" {{ old('role') == 'kasir' ? 'selected' : '' }}>Kasir</option>
                        <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>Admin / Owner</option>
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-sm font-bold text-gray-700 mb-2">Password</label>
                <input type="password" name="password" class="w-full px-4 py-3 rounded-xl border border-gray-200 bg-gray-50 focus:bg-white focus:border-brand-500 outline-none transition-all" placeholder="Minimal 6 karakter" required>
                @error('password') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="pt-4 border-t border-gray-100 space-y-4">
                <p class="text-xs font-bold text-gray-400 uppercase tracking-wider flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                    Pertanyaan Keamanan (untuk Reset Password Mandiri)
                </p>
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Pilih Pertanyaan Keamanan</label>
                    <select name="security_question" class="w-full px-4 py-3 rounded-xl border border-gray-200 bg-gray-50 focus:bg-white focus:border-brand-500 outline-none cursor-pointer" required>
                        <option value="">— Pilih pertanyaan —</option>
                        <option value="Siapa nama hewan peliharaan pertama Anda?" {{ old('security_question') == 'Siapa nama hewan peliharaan pertama Anda?' ? 'selected' : '' }}>Siapa nama hewan peliharaan pertama Anda?</option>
                        <option value="Di kota mana Anda dilahirkan?" {{ old('security_question') == 'Di kota mana Anda dilahirkan?' ? 'selected' : '' }}>Di kota mana Anda dilahirkan?</option>
                        <option value="Siapa nama ibu kandung Anda?" {{ old('security_question') == 'Siapa nama ibu kandung Anda?' ? 'selected' : '' }}>Siapa nama ibu kandung Anda?</option>
                        <option value="Apa nama sekolah dasar Anda?" {{ old('security_question') == 'Apa nama sekolah dasar Anda?' ? 'selected' : '' }}>Apa nama sekolah dasar Anda?</option>
                        <option value="Apa makanan favorit Anda?" {{ old('security_question') == 'Apa makanan favorit Anda?' ? 'selected' : '' }}>Apa makanan favorit Anda?</option>
                        <option value="Siapa nama sahabat terbaik Anda?" {{ old('security_question') == 'Siapa nama sahabat terbaik Anda?' ? 'selected' : '' }}>Siapa nama sahabat terbaik Anda?</option>
                    </select>
                    @error('security_question') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Jawaban Keamanan</label>
                    <input type="text" name="security_answer" value="{{ old('security_answer') }}" class="w-full px-4 py-3 rounded-xl border border-gray-200 bg-gray-50 focus:bg-white focus:border-brand-500 outline-none transition-all" placeholder="Jawaban hanya diketahui pemilik akun" required>
                    <p class="text-[11px] text-gray-400 mt-1">Jawaban tidak bersifat case-sensitive (huruf besar/kecil sama saja).</p>
                    @error('security_answer') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="flex gap-3 pt-4 border-t border-gray-100">
                <a href="{{ route('users.index') }}" class="flex-1 text-center py-3.5 rounded-xl font-bold text-gray-400 hover:bg-gray-50 transition-all">Batal</a>
                <button type="submit" class="flex-1 bg-brand-700 hover:bg-brand-800 text-white font-bold py-3.5 rounded-xl shadow-lg shadow-brand-700/20 transition-all">Simpan Akun</button>
            </div>
        </form>
    </div>
</div>
@endsection