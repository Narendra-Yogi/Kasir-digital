<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title') - Geprek Legend</title>
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
    <link rel="icon" type="image/png" href="{{ asset('images/icon.png') }}">
</head>
<body class="bg-surface font-sans text-gray-800 h-screen overflow-hidden flex">

    {{-- Sidebar Overlay --}}
    <div id="sidebar-overlay" class="fixed inset-0 bg-black/40 backdrop-blur-sm z-30 lg:hidden opacity-0 pointer-events-none" onclick="toggleSidebar()"></div>

    {{-- ========== SIDEBAR (LEBAR W-64 DENGAN TEKS MENU PREMIUM) ========== --}}
    <aside id="sidebar" class="fixed lg:static w-64 bg-white border-r border-gray-100 flex flex-col h-full z-40 shrink-0 -translate-x-full lg:translate-x-0 transition-transform duration-300">
        
        {{-- Logo Brand Area --}}
        <div class="h-20 flex items-center px-6 gap-3 shrink-0 border-b border-gray-100 relative bg-white">
            <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-brand-600 to-brand-400 text-white flex items-center justify-center shadow-lg shadow-brand-600/30 shrink-0">
                <svg class="w-6 h-6 text-white" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M12 2C11.5 3.5 10.5 4.5 9.5 5.5C7.5 7.5 6.5 9.5 6.5 12C6.5 15 8.5 17.5 11.5 18C12.5 18.2 13.5 18 14.5 17.5C16.5 16.5 17.5 14.5 17.5 12C17.5 9.5 16 7.5 14 5.5C13 4.5 12.5 3.5 12 2Z" fill="currentColor"/>
                    <path d="M12 6.5C11.7 7.5 11 8.2 10.3 8.8C8.8 10.2 8 11.5 8 13.2C8 15.2 9.5 16.8 11.5 17.2C12 17.3 12.5 17.2 13 16.8C14.2 16 15 14.8 15 13.2C15 11.5 14 10.2 12.7 8.8C12.3 8.2 12 7.5 12 6.5Z" fill="#fff7ed"/>
                    <path d="M12 11.5L13.5 12.2L12 13V11.5Z" fill="#ea580c"/>
                </svg>
            </div>
            <div class="flex flex-col">
                <h1 class="text-sm font-extrabold tracking-tight text-gray-900 leading-none">Geprek Legend</h1>
                <span class="text-[9px] text-brand-600 mt-1 font-semibold uppercase tracking-wider">Kasir Digital</span>
            </div>
            <button onclick="toggleSidebar()" class="lg:hidden absolute top-1/2 -translate-y-1/2 right-3 p-1.5 rounded-lg bg-gray-100 hover:bg-gray-200 text-gray-500 transition-colors z-50">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>

        {{-- Navigasi Menu --}}
        <div class="py-6 flex-1 flex flex-col gap-1 overflow-y-auto custom-scrollbar">
            
            <span class="text-[9px] font-black text-gray-400 uppercase tracking-widest px-6 mb-2 block">Menu Utama</span>
            
            <a href="{{ route('dashboard') }}" class="sidebar-link {{ request()->routeIs('dashboard') ? 'active text-brand-750 bg-brand-50/50 font-bold' : 'text-gray-500 hover:text-brand-600 hover:bg-gray-50/50' }} flex items-center gap-3.5 px-6 py-3 mx-3 rounded-xl transition-all duration-200 text-sm font-semibold">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path></svg>
                <span>Dashboard</span>
            </a>
            
            <a href="{{ route('pos.index') }}" class="sidebar-link {{ request()->routeIs('pos.*') ? 'active text-brand-750 bg-brand-50/50 font-bold' : 'text-gray-500 hover:text-brand-600 hover:bg-gray-50/50' }} flex items-center gap-3.5 px-6 py-3 mx-3 rounded-xl transition-all duration-200 text-sm font-semibold">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                <span>Kasir (POS)</span>
            </a>

            <a href="{{ route('buku-kas-harian.index') }}" class="sidebar-link {{ request()->routeIs('buku-kas-harian.*') ? 'active text-brand-750 bg-brand-50/50 font-bold' : 'text-gray-500 hover:text-brand-600 hover:bg-gray-50/50' }} flex items-center gap-3.5 px-6 py-3 mx-3 rounded-xl transition-all duration-200 text-sm font-semibold">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                <span>Buku Kas Harian</span>
            </a>

            @if(auth()->user()->role === 'admin')
            <span class="text-[9px] font-black text-gray-400 uppercase tracking-widest px-6 mt-4 mb-2 block">Administrator</span>

            <a href="{{ route('items.index') }}" class="sidebar-link {{ request()->routeIs('items.*') ? 'active text-brand-750 bg-brand-50/50 font-bold' : 'text-gray-500 hover:text-brand-600 hover:bg-gray-50/50' }} flex items-center gap-3.5 px-6 py-3 mx-3 rounded-xl transition-all duration-200 text-sm font-semibold">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                <span>Daftar Menu</span>
            </a>

            <a href="{{ route('ingredients.index') }}" class="sidebar-link {{ request()->routeIs('ingredients.*') ? 'active text-brand-750 bg-brand-50/50 font-bold' : 'text-gray-500 hover:text-brand-600 hover:bg-gray-50/50' }} flex items-center gap-3.5 px-6 py-3 mx-3 rounded-xl transition-all duration-200 text-sm font-semibold">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"></path></svg>
                <span>Bahan Baku</span>
            </a>

            <a href="{{ route('categories.index') }}" class="sidebar-link {{ request()->routeIs('categories.*') ? 'active text-brand-750 bg-brand-50/50 font-bold' : 'text-gray-500 hover:text-brand-600 hover:bg-gray-50/50' }} flex items-center gap-3.5 px-6 py-3 mx-3 rounded-xl transition-all duration-200 text-sm font-semibold">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"></path></svg>
                <span>Kategori Menu</span>
            </a>

            <a href="{{ route('inventories.index') }}" class="sidebar-link {{ request()->routeIs('inventories.*') ? 'active text-brand-750 bg-brand-50/50 font-bold' : 'text-gray-500 hover:text-brand-600 hover:bg-gray-50/50' }} flex items-center gap-3.5 px-6 py-3 mx-3 rounded-xl transition-all duration-200 text-sm font-semibold">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                <span>Stok Bahan</span>
            </a>

            <span class="text-[9px] font-black text-gray-400 uppercase tracking-widest px-6 mt-4 mb-2 block">Laporan & Pengaturan</span>

            <a href="{{ route('reports.index') }}" class="sidebar-link {{ request()->routeIs('reports.*') ? 'active text-brand-750 bg-brand-50/50 font-bold' : 'text-gray-500 hover:text-brand-600 hover:bg-gray-50/50' }} flex items-center gap-3.5 px-6 py-3 mx-3 rounded-xl transition-all duration-200 text-sm font-semibold">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"></path></svg>
                <span>Laporan Kasir</span>
            </a>

            <a href="{{ route('pengeluaran.index') }}" class="sidebar-link {{ request()->routeIs('pengeluaran.*') ? 'active text-brand-750 bg-brand-50/50 font-bold' : 'text-gray-500 hover:text-brand-600 hover:bg-gray-50/50' }} flex items-center gap-3.5 px-6 py-3 mx-3 rounded-xl transition-all duration-200 text-sm font-semibold">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                <span>Catatan Pengeluaran</span>
            </a>

            <a href="{{ route('users.index') }}" class="sidebar-link {{ request()->routeIs('users.*') ? 'active text-brand-750 bg-brand-50/50 font-bold' : 'text-gray-500 hover:text-brand-600 hover:bg-gray-50/50' }} flex items-center gap-3.5 px-6 py-3 mx-3 rounded-xl transition-all duration-200 text-sm font-semibold">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                <span>Kelola Pengguna</span>
            </a>
            @endif
        </div>

        {{-- Area Keluar (Logout) --}}
        <div class="p-3 border-t border-gray-100 bg-white">
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="w-full flex items-center gap-3.5 px-6 py-3 rounded-xl text-red-500 hover:bg-red-50/60 transition-colors font-semibold text-sm cursor-pointer">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                    <span>Keluar</span>
                </button>
            </form>
        </div>
    </aside>

    {{-- ========== MAIN CONTENT ========== --}}
    <div class="flex-1 flex flex-col h-screen overflow-hidden w-full bg-surface">
        @if(!request()->routeIs('pos.*'))
        <header class="h-16 lg:h-20 flex items-center justify-between px-4 lg:px-8 shrink-0 z-20">
            <div class="flex items-center gap-3">
                <button onclick="toggleSidebar()" class="lg:hidden p-2 rounded-xl hover:bg-gray-100 text-gray-500 transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                </button>
                <h2 class="text-xl lg:text-2xl font-bold text-gray-900 truncate">@yield('page_title')</h2>
            </div>
            <div class="flex items-center gap-3">
                <div class="text-right hidden sm:block">
                    <p class="text-sm font-bold text-gray-900">{{ auth()->user()->name }}</p>
                    <p class="text-[11px] text-brand-600 font-medium uppercase tracking-wide">{{ auth()->user()->role }}</p>
                </div>
                <div class="h-10 w-10 rounded-full bg-gray-200 text-gray-600 flex justify-center items-center font-bold text-sm">
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
                confirmButtonColor: '#ea580c',
                cancelButtonColor: '#6b7280',
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