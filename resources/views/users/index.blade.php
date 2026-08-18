@extends('layouts.app')

@section('title', 'Manajemen Akun')
@section('page_title', 'Daftar Pengguna')

@section('content')
<div class="flex justify-between items-center mb-8">
    <h3 class="text-gray-500 text-sm font-medium">Total Akun: {{ $users->count() }}</h3>
    <a href="{{ route('users.create') }}" class="bg-brand-700 hover:bg-brand-800 text-white px-6 py-2.5 rounded-xl font-bold transition-all shadow-lg shadow-brand-700/20 flex items-center gap-2">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
        Tambah Kasir / Admin
    </a>
</div>



<div class="bg-white rounded-3xl border border-gray-100 overflow-hidden shadow-sm">
    <table class="w-full text-left">
        <thead class="bg-gray-50 border-b border-gray-100">
            <tr>
                <th class="px-6 py-4 text-xs font-bold text-gray-400 uppercase">Nama Lengkap</th>
                <th class="px-6 py-4 text-xs font-bold text-gray-400 uppercase">Username</th>
                <th class="px-6 py-4 text-xs font-bold text-gray-400 uppercase">Role / Akses</th>
                <th class="px-6 py-4 text-xs font-bold text-gray-400 uppercase text-right">Aksi</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @foreach($users as $user)
            <tr class="hover:bg-gray-50 transition-colors">
                <td class="px-6 py-4 font-bold text-gray-900 flex items-center gap-3">
                    <div class="w-8 h-8 rounded-full bg-brand-50 text-brand-700 flex items-center justify-center font-bold text-xs border border-brand-100">
                        {{ substr($user->name, 0, 1) }}
                    </div>
                    {{ $user->name }}
                    @if($user->id === auth()->id())
                        <span class="text-[10px] bg-blue-100 text-blue-700 px-2 py-0.5 rounded-full ml-2">Anda</span>
                    @endif
                </td>
                <td class="px-6 py-4 text-gray-500">{{ $user->username }}</td>
                <td class="px-6 py-4">
                    @if($user->role === 'admin')
                        <span class="px-3 py-1 rounded-full bg-purple-100 text-purple-700 text-xs font-bold uppercase">Admin</span>
                    @else
                        <span class="px-3 py-1 rounded-full bg-gray-100 text-gray-700 text-xs font-bold uppercase">Kasir</span>
                    @endif
                </td>
                <td class="px-6 py-4">
                    <div class="flex items-center justify-end gap-2">
                        <button type="button" onclick="resetPassword({{ $user->id }}, '{{ addslashes($user->name) }}')" class="flex items-center justify-center w-9 h-9 rounded-xl bg-amber-50 text-amber-600 hover:bg-amber-100 active:scale-95 transition-all duration-200 shadow-sm cursor-pointer" title="Reset Password">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"></path></svg>
                        </button>
                        <a href="{{ route('users.edit', $user->id) }}" class="flex items-center justify-center w-9 h-9 rounded-xl bg-blue-50 text-blue-600 hover:bg-blue-100 active:scale-95 transition-all duration-200 shadow-sm" title="Edit Pengguna">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-5M16.5 3.5a2.121 2.121 0 113 3L7 19l-4 1 1-4L16.5 3.5z"></path></svg>
                        </a>
                        <form action="{{ route('users.destroy', $user->id) }}" method="POST" class="block" onsubmit="confirmDelete(event, this, 'Hapus akun ini?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="flex items-center justify-center w-9 h-9 rounded-xl bg-red-50 text-red-600 hover:bg-red-100 active:scale-95 transition-all duration-200 shadow-sm disabled:opacity-40 disabled:pointer-events-none cursor-pointer" title="Hapus Pengguna" {{ $user->id === auth()->id() ? 'disabled' : '' }}>
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                            </button>
                        </form>
                    </div>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

<script>
function resetPassword(userId, userName) {
    Swal.fire({
        title: 'Reset Password?',
        html: `Password akun <b>${userName}</b> akan direset ke password baru yang digenerate otomatis.`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ea580c',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Ya, Reset!',
        cancelButtonText: 'Batal',
    }).then((result) => {
        if (result.isConfirmed) {
            fetch(`/users/${userId}/reset-password`, {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json',
                },
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Password Berhasil Direset!',
                        html: `
                            <div style="text-align:left; background:#f9fafb; border:1px solid #e5e7eb; border-radius:12px; padding:16px; margin-top:8px;">
                                <p style="font-size:13px; color:#6b7280; margin-bottom:4px;">Akun: <b style="color:#111827;">${data.user_name}</b></p>
                                <p style="font-size:13px; color:#6b7280; margin-bottom:0;">Password Baru:</p>
                                <p style="font-size:24px; font-weight:900; color:#ea580c; letter-spacing:3px; margin:8px 0 0 0; font-family:monospace;">${data.new_password}</p>
                            </div>
                            <p style="font-size:11px; color:#9ca3af; margin-top:12px;">⚠️ Catat password ini dan beritahukan ke pengguna. Password tidak bisa dilihat lagi setelah dialog ini ditutup.</p>
                        `,
                        confirmButtonColor: '#ea580c',
                        confirmButtonText: 'Sudah Dicatat',
                    });
                }
            })
            .catch(() => {
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal!',
                    text: 'Terjadi kesalahan saat mereset password.',
                    confirmButtonColor: '#d33',
                });
            });
        }
    });
}
</script>
@endsection