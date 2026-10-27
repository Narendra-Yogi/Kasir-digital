<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk - Geprek Legend</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,500;9..144,600;9..144,700&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
</head>

<body class="bg-surface font-sans text-[#1c1410] min-h-screen flex items-center justify-center p-4">

    <div class="w-full max-w-4xl grid md:grid-cols-[1.05fr_1fr] overflow-hidden rounded-[20px] border border-[#e8e0d3] bg-[#fffdf9] rise">
        {{-- Panel merek --}}
        <div class="hidden md:flex flex-col justify-between bg-[#17110c] text-stone-300 p-10">
            <div class="flex items-center gap-3">
                <div class="w-11 h-11 rounded-xl overflow-hidden ring-1 ring-white/15">
                    <img src="{{ asset('images/logo.jpeg') }}" alt="Geprek Legend" class="w-full h-full object-cover">
                </div>
                <div>
                    <p class="font-display text-lg text-[#f7f1e6] font-semibold leading-tight">Geprek Legend</p>
                    <p class="text-xs text-stone-400">Kasir harian · dapur & kas</p>
                </div>
            </div>
            <div>
                <p class="font-display text-[32px] leading-[1.15] text-[#f7f1e6] font-semibold tracking-tight">Dapur yang rapi,<br>kasir yang tenang.</p>
                <p class="text-sm text-stone-400 mt-4 leading-relaxed">Satu tempat untuk pesanan, pengeluaran, dan rekap hasil harian. Dibuat untuk ritme warung yang cepat.</p>
                <div class="mt-8 pt-6 border-t border-white/10 grid grid-cols-3 gap-4">
                    <div>
                        <p class="font-display text-xl text-[#f7f1e6] tnum">POS</p>
                        <p class="text-xs text-stone-500 mt-1">Checkout cepat</p>
                    </div>
                    <div>
                        <p class="font-display text-xl text-[#f7f1e6] tnum">Hasil</p>
                        <p class="text-xs text-stone-500 mt-1">Rekap bersih</p>
                    </div>
                    <div>
                        <p class="font-display text-xl text-[#f7f1e6] tnum">Kas</p>
                        <p class="text-xs text-stone-500 mt-1">Selisih terpantau</p>
                    </div>
                </div>
            </div>
            <p class="text-xs text-stone-500">Shift hari ini · {{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }}</p>
        </div>

        {{-- Formulir --}}
        <div class="p-8 sm:p-10">
            <div class="md:hidden flex items-center gap-3 mb-8">
                <div class="w-10 h-10 rounded-xl overflow-hidden ring-1 ring-[#e8e0d3]">
                    <img src="{{ asset('images/logo.jpeg') }}" alt="Geprek Legend" class="w-full h-full object-cover">
                </div>
                <div>
                    <p class="font-display text-base font-semibold leading-tight">Geprek Legend</p>
                    <p class="text-xs text-[#8a8179]">Kasir harian</p>
                </div>
            </div>

            <h1 class="font-display text-[26px] font-semibold tracking-tight">Selamat datang kembali</h1>
            <p class="text-sm text-[#8a8179] mt-1.5">Masuk untuk membuka kasir dan laporan hari ini.</p>

            @if ($errors->any())
                @php
                    $pesan = $errors->first();
                    $angka = null;
                    if (preg_match('/dalam (\d+) detik/', $pesan, $m)) {
                        $angka = (int) $m[1];
                    }
                @endphp
                <div class="bg-[#fdf0ee] text-[#931b13] px-4 py-3 rounded-[10px] text-sm mt-6 border border-[#f2b3a6] font-medium">
                    @if ($angka !== null)
                        Terlalu banyak percobaan login. Coba lagi dalam <span data-countdown="{{ $angka }}">{{ $angka }}</span> detik.
                    @else
                        {{ $pesan }}
                    @endif
                </div>
            @endif

            <form action="{{ route('login') }}" method="POST" class="space-y-4 mt-7">
                @csrf

                <div>
                    <label for="username" class="block text-sm font-medium text-[#1c1410] mb-1.5">Username</label>
                    <input type="text" name="username" id="username" value="{{ old('username') }}"
                        class="field" placeholder="Nama pengguna kasir" required autofocus>
                </div>

                <div>
                    <div class="flex justify-between items-center mb-1.5">
                        <label for="password" class="block text-sm font-medium text-[#1c1410]">Password</label>
                        <a href="{{ route('password.forgot') }}" class="text-sm font-medium text-[#931b13] hover:text-[#751510] transition-colors">Lupa password?</a>
                    </div>
                    <div class="relative">
                        <input type="password" name="password" id="password"
                            class="field pr-11" placeholder="Kata sandi" required>
                        <button type="button" onclick="togglePassword()" class="absolute right-3 top-1/2 -translate-y-1/2 p-1 text-[#8a8179] hover:text-[#1c1410] focus:outline-none transition-colors" aria-label="Tampilkan sandi">
                            <svg id="eye-icon" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"></path>
                            </svg>
                        </button>
                    </div>
                </div>

                <button type="submit" class="btn-ink w-full py-3 text-sm">
                    Masuk ke sistem
                </button>
                <p class="text-xs text-[#8a8179] text-center pt-1">Gunakan akun kasir untuk jualan, akun admin untuk kelola menu & laporan.</p>
            </form>
        </div>
    </div>

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        (function () {
            const el = document.querySelector('[data-countdown]');
            if (! el) return;

            let detik = parseInt(el.textContent, 10) || 0;
            const tombol = document.querySelector('form button[type="submit"]');
            if (tombol) tombol.disabled = true;

            const timer = setInterval(() => {
                detik = Math.max(0, detik - 1);
                el.textContent = detik;

                if (detik === 0) {
                    clearInterval(timer);
                    if (tombol) tombol.disabled = false;
                }
            }, 1000);
        })();

        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const eyeIcon = document.getElementById('eye-icon');

            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                eyeIcon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>';
            } else {
                passwordInput.type = 'password';
                eyeIcon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"></path>';
            }
        }

        @if(session('success'))
        document.addEventListener('DOMContentLoaded', function() {
            Swal.fire({
                icon: 'success',
                title: 'Berhasil',
                text: '{{ session("success") }}',
                confirmButtonColor: '#931b13',
            });
        });
        @endif
    </script>

</body>

</html>
