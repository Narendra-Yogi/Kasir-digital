@extends('layouts.app')

@section('title', 'Manajemen Akun')
@section('page_title', 'Daftar pengguna')

@section('content')
<div class="flex justify-between items-center mb-5 rise">
    <p class="text-[13px] text-[#8a8179]">{{ $users->count() }} akun terdaftar</p>
    <a href="{{ route('users.create') }}" class="btn-ink text-[13px] px-4 py-2.5 inline-flex items-center gap-2">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
        Tambah kasir / admin
    </a>
</div>

<div class="table-shell bg-white rise">
    <table class="w-full text-left">
        <thead>
            <tr>
                <th class="px-6 py-3.5 text-[11px] font-semibold text-[#7a7268]">Nama lengkap</th>
                <th class="px-6 py-3.5 text-[11px] font-semibold text-[#7a7268]">Username</th>
                <th class="px-6 py-3.5 text-[11px] font-semibold text-[#7a7268]">Peran</th>
                <th class="px-6 py-3.5 text-[11px] font-semibold text-[#7a7268] text-right">Aksi</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-[#f1ebe0]">
            @foreach($users as $user)
            <tr class="table-row-hover">
                <td class="px-6 py-3.5 font-medium text-[14px]">
                    <span class="inline-flex items-center gap-3">
                        <span class="w-8 h-8 rounded-full bg-[#1c1410] text-[#f7f1e6] inline-flex items-center justify-center font-display font-semibold text-xs shrink-0">
                            {{ substr($user->name, 0, 1) }}
                        </span>
                        {{ $user->name }}
                        @if($user->id === auth()->id())
                            <span class="pill pill-info">Anda</span>
                        @endif
                    </span>
                </td>
                <td class="px-6 py-3.5 text-[#57504a] text-[13px]">{{ $user->username }}</td>
                <td class="px-6 py-3.5">
                    @if($user->role === 'admin')
                        <span class="pill pill-warn">Admin</span>
                    @else
                        <span class="pill pill-muted">Kasir</span>
                    @endif
                </td>
                <td class="px-6 py-3.5">
                    <div class="flex items-center justify-end gap-2">
                        <button type="button" onclick="resetPassword({{ $user->id }}, '{{ addslashes($user->name) }}')" class="icon-btn" title="Reset password" aria-label="Reset password {{ $user->name }}">
                            <svg class="w-[18px] h-[18px]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"></path></svg>
                        </button>
                        <a href="{{ route('users.edit', $user->id) }}" class="icon-btn" title="Edit pengguna" aria-label="Edit {{ $user->name }}">
                            <svg class="w-[18px] h-[18px]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M11 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-5M16.5 3.5a2.121 2.121 0 113 3L7 19l-4 1 1-4L16.5 3.5z"></path></svg>
                        </a>
                        <form action="{{ route('users.destroy', $user->id) }}" method="POST" class="block" onsubmit="confirmDelete(event, this, 'Hapus akun ini?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="icon-btn danger disabled:opacity-40 disabled:pointer-events-none" title="Hapus pengguna" aria-label="Hapus {{ $user->name }}" {{ $user->id === auth()->id() ? 'disabled' : '' }}>
                                <svg class="w-[18px] h-[18px]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
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
        title: 'Reset password?',
        html: `Password akun <b>${userName}</b> akan diganti dengan password baru yang dibuat otomatis.`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#931b13',
        cancelButtonColor: '#57504a',
        confirmButtonText: 'Ya, reset',
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
                        title: 'Password berhasil direset',
                        html: `
                            <div style="text-align:left; background:#f7f2e9; border:1px solid #e8e0d3; border-radius:12px; padding:16px; margin-top:8px;">
                                <p style="font-size:13px; color:#57504a; margin-bottom:4px;">Akun: <b style="color:#1c1410;">${data.user_name}</b></p>
                                <p style="font-size:13px; color:#57504a; margin-bottom:0;">Password baru:</p>
                                <p style="font-size:24px; font-weight:700; color:#1c1410; letter-spacing:3px; margin:8px 0 0 0; font-variant-numeric:tabular-nums;">${data.new_password}</p>
                            </div>
                            <p style="font-size:12px; color:#8a8179; margin-top:12px;">Catat password ini dan beritahukan ke pengguna. Password tidak bisa dilihat lagi setelah dialog ini ditutup.</p>
                        `,
                        confirmButtonColor: '#1c1410',
                        confirmButtonText: 'Sudah dicatat',
                    });
                }
            })
            .catch(() => {
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal',
                    text: 'Terjadi kesalahan saat mereset password.',
                    confirmButtonColor: '#931b13',
                });
            });
        }
    });
}
</script>
@endsection
