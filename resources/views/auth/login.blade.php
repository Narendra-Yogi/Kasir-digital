<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Geprek Legend</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
</head>

<body class="bg-surface font-sans text-gray-800 h-screen flex items-center justify-center">

    <div
        class="bg-white p-8 sm:p-10 rounded-3xl shadow-[0_10px_40px_rgba(0,0,0,0.04)] w-full max-w-md border border-gray-100">

        <div class="flex justify-center mb-8">
            <div class="w-14 h-14 bg-gradient-to-tr from-brand-600 to-brand-400 rounded-2xl flex items-center justify-center text-white shadow-lg shadow-brand-600/30">
                <svg class="w-8 h-8 text-white" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M12 2C11.5 3.5 10.5 4.5 9.5 5.5C7.5 7.5 6.5 9.5 6.5 12C6.5 15 8.5 17.5 11.5 18C12.5 18.2 13.5 18 14.5 17.5C16.5 16.5 17.5 14.5 17.5 12C17.5 9.5 16 7.5 14 5.5C13 4.5 12.5 3.5 12 2Z" fill="currentColor"/>
                    <path d="M12 6.5C11.7 7.5 11 8.2 10.3 8.8C8.8 10.2 8 11.5 8 13.2C8 15.2 9.5 16.8 11.5 17.2C12 17.3 12.5 17.2 13 16.8C14.2 16 15 14.8 15 13.2C15 11.5 14 10.2 12.7 8.8C12.3 8.2 12 7.5 12 6.5Z" fill="#fff7ed"/>
                    <path d="M12 11.5L13.5 12.2L12 13V11.5Z" fill="#ea580c"/>
                </svg>
            </div>
        </div>

        <div class="text-center mb-8">
            <h1 class="text-2xl font-bold text-gray-900">Selamat Datang</h1>
            <p class="text-sm text-gray-500 mt-2">Silakan login untuk mengakses sistem kasir.</p>
        </div>

        @if ($errors->any())
            <div
                class="bg-red-50 text-red-600 p-4 rounded-xl text-sm mb-6 border border-red-100 text-center font-medium">
                {{ $errors->first() }}
            </div>
        @endif

        <form action="{{ route('login') }}" method="POST" class="space-y-5">
            @csrf

            <div>
                <label for="username" class="block text-sm font-semibold text-gray-700 mb-2">Username</label>
                <input type="text" name="username" id="username" value="{{ old('username') }}"
                    class="w-full px-4 py-3 rounded-xl border border-gray-200 bg-gray-50 focus:bg-white focus:outline-none focus:border-brand-500 transition-colors"
                    placeholder="Masukkan username" required autofocus>
            </div>

            <div>
                <div class="flex justify-between items-center mb-2">
                    <label for="password" class="block text-sm font-semibold text-gray-700">Password</label>
                    <a href="#" onclick="showForgotPasswordModal()" class="text-sm font-medium text-brand-700 hover:text-brand-800 transition-colors">Lupa Password?</a>
                </div>
                <div class="relative">
                    <input type="password" name="password" id="password"
                        class="w-full px-4 py-3 pr-12 rounded-xl border border-gray-200 bg-gray-50 focus:bg-white focus:outline-none focus:border-brand-500 transition-colors"
                        placeholder="••••••••" required>
                    <button type="button" onclick="togglePassword()" class="absolute right-3 top-1/2 -translate-y-1/2 p-1 text-gray-400 hover:text-gray-600 focus:outline-none transition-colors">
                        <svg id="eye-icon" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <!-- Eye closed icon (default) -->
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"></path>
                        </svg>
                    </button>
                </div>
            </div>

            <button type="submit"
                class="w-full bg-brand-700 hover:bg-brand-800 text-white font-bold py-3.5 rounded-xl transition-colors shadow-lg shadow-brand-700/30 mt-4">
                Masuk ke Sistem
            </button>
        </form>

    </div>

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const eyeIcon = document.getElementById('eye-icon');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                // Eye open icon
                eyeIcon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>';
            } else {
                passwordInput.type = 'password';
                // Eye closed icon
                eyeIcon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"></path>';
            }
        }

        function showForgotPasswordModal() {
            Swal.fire({
                icon: 'info',
                title: 'Lupa Password?',
                text: 'Silakan hubungi Administrator (Owner) untuk melakukan reset password akun Anda.',
                confirmButtonColor: '#ea580c',
                confirmButtonText: 'Mengerti'
            });
        }
    </script>

</body>

</html>
