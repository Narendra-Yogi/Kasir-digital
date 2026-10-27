<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title') - Geprek Legend</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,500;9..144,600;9..144,700&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
    <link rel="icon" type="image/png" href="{{ asset('images/icon.png') }}">
</head>
<body class="bg-surface font-sans text-[#1c1410] h-screen overflow-hidden flex">

    {{-- Sidebar Overlay --}}
    <div id="sidebar-overlay" class="fixed inset-0 bg-black/50 backdrop-blur-[2px] z-30 lg:hidden opacity-0 pointer-events-none" onclick="toggleSidebar()"></div>

    {{-- ========== SIDEBAR ========== --}}
    <aside id="sidebar" class="fixed lg:static w-[268px] bg-[#17110c] text-stone-300 flex flex-col h-full z-40 shrink-0 -translate-x-full lg:translate-x-0 transition-transform duration-300">

        {{-- Logo Brand Area --}}
        <div class="px-5 pt-6 pb-5 shrink-0 relative">
            <div class="flex items-center gap-3">
                <div class="w-11 h-11 rounded-xl overflow-hidden shrink-0 ring-1 ring-white/15">
                    <img src="{{ asset('images/logo.jpeg') }}" alt="Geprek Legend" class="w-full h-full object-cover">
                </div>
                <div class="flex flex-col min-w-0">
                    <span class="font-display text-[17px] leading-tight text-[#f7f1e6] font-semibold tracking-tight">Geprek Legend</span>
                    <span class="text-xs text-stone-400 mt-0.5">Kasir harian · dapur & kas</span>
                </div>
            </div>
            <button onclick="toggleSidebar()" class="lg:hidden absolute top-6 right-4 p-1.5 rounded-lg bg-white/10 hover:bg-white/15 text-stone-300 transition-colors z-50">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>

        <div class="mx-5 h-px bg-white/10 shrink-0"></div>

        {{-- Navigasi Menu --}}
        <nav class="py-5 flex-1 flex flex-col gap-0.5 overflow-y-auto px-3">
            <span class="text-xs text-stone-500 px-3 mb-1.5 block">Utama</span>

            <a href="{{ route('dashboard') }}" class="sidebar-link {{ request()->routeIs('dashboard') ? 'active bg-white/[0.07] text-[#f7f1e6]' : 'text-stone-400 hover:text-stone-100 hover:bg-white/[0.04]' }} flex items-center gap-3 px-3 py-2.5 rounded-lg transition-colors text-sm font-medium">
                <span class="nav-indicator"></span>
                <svg class="w-[18px] h-[18px] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path></svg>
                <span>Dashboard</span>
            </a>

            <a href="{{ route('pos.index') }}" class="sidebar-link {{ request()->routeIs('pos.*') ? 'active bg-white/[0.07] text-[#f7f1e6]' : 'text-stone-400 hover:text-stone-100 hover:bg-white/[0.04]' }} flex items-center gap-3 px-3 py-2.5 rounded-lg transition-colors text-sm font-medium">
                <span class="nav-indicator"></span>
                <svg class="w-[18px] h-[18px] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                <span>Kasir (POS)</span>
            </a>

            <a href="{{ route('buku-kas-harian.index') }}" class="sidebar-link {{ request()->routeIs('buku-kas-harian.*') ? 'active bg-white/[0.07] text-[#f7f1e6]' : 'text-stone-400 hover:text-stone-100 hover:bg-white/[0.04]' }} flex items-center gap-3 px-3 py-2.5 rounded-lg transition-colors text-sm font-medium">
                <span class="nav-indicator"></span>
                <svg class="w-[18px] h-[18px] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                <span>Buku Kas Harian</span>
            </a>

            @if(auth()->user()->role === 'admin')
            <span class="text-xs text-stone-500 px-3 mt-5 mb-1.5 block">Kelola</span>

            <a href="{{ route('reports.rekap') }}" class="sidebar-link {{ request()->routeIs('reports.rekap') ? 'active bg-white/[0.07] text-[#f7f1e6]' : 'text-stone-400 hover:text-stone-100 hover:bg-white/[0.04]' }} flex items-center gap-3 px-3 py-2.5 rounded-lg transition-colors text-sm font-medium">
                <span class="nav-indicator"></span>
                <svg class="w-[18px] h-[18px] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                <span>Rekap Hasil</span>
            </a>

            <a href="{{ route('reports.daily') }}" class="sidebar-link {{ request()->routeIs('reports.daily') ? 'active bg-white/[0.07] text-[#f7f1e6]' : 'text-stone-400 hover:text-stone-100 hover:bg-white/[0.04]' }} flex items-center gap-3 px-3 py-2.5 rounded-lg transition-colors text-sm font-medium">
                <span class="nav-indicator"></span>
                <svg class="w-[18px] h-[18px] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                <span>Laporan Harian</span>
            </a>

            <a href="{{ route('items.index') }}" class="sidebar-link {{ request()->routeIs('items.*') ? 'active bg-white/[0.07] text-[#f7f1e6]' : 'text-stone-400 hover:text-stone-100 hover:bg-white/[0.04]' }} flex items-center gap-3 px-3 py-2.5 rounded-lg transition-colors text-sm font-medium">
                <span class="nav-indicator"></span>
                <svg class="w-[18px] h-[18px] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                <span>Daftar Menu</span>
            </a>

            <a href="{{ route('categories.index') }}" class="sidebar-link {{ request()->routeIs('categories.*') ? 'active bg-white/[0.07] text-[#f7f1e6]' : 'text-stone-400 hover:text-stone-100 hover:bg-white/[0.04]' }} flex items-center gap-3 px-3 py-2.5 rounded-lg transition-colors text-sm font-medium">
                <span class="nav-indicator"></span>
                <svg class="w-[18px] h-[18px] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4 6h16M4 10h16M4 14h16M4 18h16"></path></svg>
                <span>Kategori Menu</span>
            </a>

            <span class="text-xs text-stone-500 px-3 mt-5 mb-1.5 block">Laporan & pengaturan</span>

            <a href="{{ route('reports.index') }}" class="sidebar-link {{ request()->routeIs('reports.index') ? 'active bg-white/[0.07] text-[#f7f1e6]' : 'text-stone-400 hover:text-stone-100 hover:bg-white/[0.04]' }} flex items-center gap-3 px-3 py-2.5 rounded-lg transition-colors text-sm font-medium">
                <span class="nav-indicator"></span>
                <svg class="w-[18px] h-[18px] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"></path></svg>
                <span>Rincian Transaksi</span>
            </a>

            <a href="{{ route('pengeluaran.index') }}" class="sidebar-link {{ request()->routeIs('pengeluaran.*') ? 'active bg-white/[0.07] text-[#f7f1e6]' : 'text-stone-400 hover:text-stone-100 hover:bg-white/[0.04]' }} flex items-center gap-3 px-3 py-2.5 rounded-lg transition-colors text-sm font-medium">
                <span class="nav-indicator"></span>
                <svg class="w-[18px] h-[18px] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                <span>Catatan Pengeluaran</span>
            </a>

            <a href="{{ route('users.index') }}" class="sidebar-link {{ request()->routeIs('users.*') ? 'active bg-white/[0.07] text-[#f7f1e6]' : 'text-stone-400 hover:text-stone-100 hover:bg-white/[0.04]' }} flex items-center gap-3 px-3 py-2.5 rounded-lg transition-colors text-sm font-medium">
                <span class="nav-indicator"></span>
                <svg class="w-[18px] h-[18px] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                <span>Kelola Pengguna</span>
            </a>
            @endif
        </nav>

        {{-- Area Keluar (Logout) --}}
        <div class="p-3 border-t border-white/10">
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="w-full flex items-center gap-3 px-3 py-2.5 rounded-lg text-stone-400 hover:bg-white/[0.05] hover:text-stone-100 transition-colors font-medium text-sm cursor-pointer">
                    <svg class="w-[18px] h-[18px] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                    <span>Keluar</span>
                </button>
            </form>
        </div>
    </aside>

    {{-- ========== MAIN CONTENT ========== --}}
    <div class="flex-1 flex flex-col h-screen overflow-hidden w-full bg-surface">
        @if(!request()->routeIs('pos.*'))
        <header class="flex items-center justify-between px-4 lg:px-8 pt-5 lg:pt-7 pb-4 shrink-0 z-20">
            <div class="flex items-center gap-3 min-w-0">
                <button onclick="toggleSidebar()" class="lg:hidden p-2 rounded-lg hover:bg-[#efe7d8] text-[#57504a] transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                </button>
                <div class="min-w-0">
                    <p class="text-xs text-[#8a8179]">{{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }}</p>
                    <h2 class="font-display text-xl lg:text-[28px] font-semibold text-[#1c1410] tracking-tight truncate leading-tight">@yield('page_title')</h2>
                </div>
            </div>
            <div class="flex items-center gap-3">
                <div class="text-right hidden sm:block">
                    <p class="text-sm font-semibold text-[#1c1410] leading-tight">{{ auth()->user()->name }}</p>
                    <p class="text-xs text-[#8a8179] mt-0.5">{{ auth()->user()->role === 'admin' ? 'Pemilik / Admin' : 'Kasir' }} · shift hari ini</p>
                </div>
                <div class="h-10 w-10 rounded-full bg-[#1c1410] text-[#f7f1e6] flex justify-center items-center font-display font-semibold text-sm ring-1 ring-[#e8e0d3]">
                    {{ substr(auth()->user()->name, 0, 1) }}
                </div>
            </div>
        </header>
        @endif

        <main class="flex-1 {{ request()->routeIs('pos.*') ? 'flex flex-col min-h-0 overflow-hidden' : 'overflow-y-auto px-4 lg:px-8 pb-8' }}">
            @yield('content')
        </main>
    </div>

    <script>
        function formatThousandsInput(input) {
            let value = input.value.replace(/\D/g, '');
            input.value = value ? new Intl.NumberFormat('id-ID').format(value) : '';
        }

        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebar-overlay');
            const isOpen = !sidebar.classList.contains('-translate-x-full');
            if (isOpen) {
                sidebar.classList.add('-translate-x-full');
                overlay.classList.add('opacity-0', 'pointer-events-none');
                overlay.classList.remove('opacity-100', 'pointer-events-auto');
                document.body.style.overflow = '';
            } else {
                sidebar.classList.remove('-translate-x-full');
                overlay.classList.remove('opacity-0', 'pointer-events-none');
                overlay.classList.add('opacity-100', 'pointer-events-auto');
                document.body.style.overflow = 'hidden';
            }
        }
        window.addEventListener('resize', function() {
            if (window.innerWidth >= 1024) {
                const sidebar = document.getElementById('sidebar');
                const overlay = document.getElementById('sidebar-overlay');
                sidebar.classList.remove('-translate-x-full');
                overlay.classList.add('opacity-0', 'pointer-events-none');
                overlay.classList.remove('opacity-100', 'pointer-events-auto');
                document.body.style.overflow = '';
            }
        });
    </script>

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true,
            showClass: { popup: 'animate__animated animate__fadeInDown' },
            hideClass: { popup: 'animate__animated animate__fadeOutUp' },
            didOpen: (toast) => {
                toast.addEventListener('mouseenter', Swal.stopTimer)
                toast.addEventListener('mouseleave', Swal.resumeTimer)
            }
        });

        @if(session('success'))
            Toast.fire({ icon: 'success', title: "{!! session('success') !!}" });
        @endif

        @if(session('error'))
            Toast.fire({ icon: 'error', title: "{!! session('error') !!}" });
        @endif

        function confirmDelete(event, form, message) {
            event.preventDefault();
            Swal.fire({
                title: 'Konfirmasi',
                text: message,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#931b13',
                cancelButtonColor: '#57504a',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal',
                showClass: { popup: 'animate__animated animate__fadeInDown' },
                hideClass: { popup: 'animate__animated animate__fadeOutUp' }
            }).then((result) => {
                if (result.isConfirmed) { form.submit(); }
            });
        }
    </script>
</body>
</html>
