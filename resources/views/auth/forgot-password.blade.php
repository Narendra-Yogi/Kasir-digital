<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Reset Password - Geprek Legend</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
</head>

<body class="bg-surface font-sans text-gray-800 min-h-screen flex items-center justify-center p-4">

    <div class="bg-white p-8 sm:p-10 rounded-3xl shadow-[0_10px_40px_rgba(0,0,0,0.04)] w-full max-w-md border border-gray-100">

        {{-- Logo --}}
        <div class="flex justify-center mb-6">
            <div class="w-14 h-14 bg-gradient-to-tr from-brand-600 to-brand-400 rounded-2xl flex items-center justify-center text-white shadow-lg shadow-brand-600/30">
                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"></path></svg>
            </div>
        </div>

        <div class="text-center mb-8">
            <h1 class="text-2xl font-bold text-gray-900">Reset Password</h1>
            <p class="text-sm text-gray-500 mt-2">Verifikasi identitas Anda untuk membuat password baru.</p>
        </div>

        {{-- Step Indicator --}}
        <div class="flex items-center justify-center gap-2 mb-8">
            <div id="step-dot-1" class="w-8 h-8 rounded-full bg-brand-600 text-white flex items-center justify-center text-xs font-bold transition-all shadow-md shadow-brand-600/20">1</div>
            <div id="step-line-1" class="w-8 h-0.5 bg-gray-200 transition-all"></div>
            <div id="step-dot-2" class="w-8 h-8 rounded-full bg-gray-200 text-gray-400 flex items-center justify-center text-xs font-bold transition-all">2</div>
            <div id="step-line-2" class="w-8 h-0.5 bg-gray-200 transition-all"></div>
            <div id="step-dot-3" class="w-8 h-8 rounded-full bg-gray-200 text-gray-400 flex items-center justify-center text-xs font-bold transition-all">3</div>
        </div>

        {{-- Error Messages --}}
        <div id="error-container" class="hidden bg-red-50 text-red-600 p-4 rounded-xl text-sm mb-6 border border-red-100 text-center font-medium"></div>

        @if ($errors->any())
            <div class="bg-red-50 text-red-600 p-4 rounded-xl text-sm mb-6 border border-red-100 text-center font-medium">
                {{ $errors->first() }}
            </div>
        @endif

        @if(session('success'))
            <div class="bg-green-50 text-green-600 p-4 rounded-xl text-sm mb-6 border border-green-100 text-center font-medium">
                {{ session('success') }}
            </div>
        @endif

        {{-- ============================== --}}
        {{-- STEP 1: Input Username         --}}
        {{-- ============================== --}}
        <div id="step-1" class="space-y-5">
            <div>
                <label for="username" class="block text-sm font-semibold text-gray-700 mb-2">Username Akun Anda</label>
                <input type="text" id="username" class="w-full px-4 py-3 rounded-xl border border-gray-200 bg-gray-50 focus:bg-white focus:outline-none focus:border-brand-500 transition-colors" placeholder="Masukkan username Anda" required autofocus>
            </div>

            <button type="button" onclick="verifyUsername()" id="btn-step1" class="w-full bg-brand-700 hover:bg-brand-800 text-white font-bold py-3.5 rounded-xl transition-colors shadow-lg shadow-brand-700/30 flex items-center justify-center gap-2">
                Lanjutkan
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
            </button>
        </div>

        {{-- ============================== --}}
        {{-- STEP 2: Jawab Pertanyaan       --}}
        {{-- ============================== --}}
        <div id="step-2" class="space-y-5 hidden">
            <div class="bg-brand-50 border border-brand-100 p-4 rounded-2xl">
                <p class="text-xs font-bold text-brand-700 uppercase tracking-wider mb-1">Pertanyaan Keamanan</p>
                <p class="text-sm font-semibold text-brand-900" id="security-question-text"></p>
                <p class="text-[11px] text-brand-600 mt-2">Akun: <b id="user-name-display"></b></p>
            </div>

            <div>
                <label for="security_answer" class="block text-sm font-semibold text-gray-700 mb-2">Jawaban Anda</label>
                <input type="text" id="security_answer" class="w-full px-4 py-3 rounded-xl border border-gray-200 bg-gray-50 focus:bg-white focus:outline-none focus:border-brand-500 transition-colors" placeholder="Ketik jawaban keamanan Anda" required>
            </div>

            <div class="flex gap-3">
                <button type="button" onclick="goToStep(1)" class="flex-1 py-3.5 rounded-xl font-bold text-gray-400 hover:bg-gray-50 transition-all text-center">
                    Kembali
                </button>
                <button type="button" onclick="verifyAnswer()" id="btn-step2" class="flex-1 bg-brand-700 hover:bg-brand-800 text-white font-bold py-3.5 rounded-xl transition-colors shadow-lg shadow-brand-700/30 flex items-center justify-center gap-2">
                    Verifikasi
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                </button>
            </div>
        </div>

        {{-- ============================== --}}
        {{-- STEP 3: Password Baru          --}}
        {{-- ============================== --}}
        <div id="step-3" class="hidden">
            <form action="{{ route('password.reset') }}" method="POST" class="space-y-5">
                @csrf
                <input type="hidden" name="reset_token" id="reset_token">

                <div class="bg-emerald-50 border border-emerald-100 p-4 rounded-2xl flex items-start gap-3">
                    <svg class="w-5 h-5 text-emerald-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                    <div>
                        <p class="text-sm font-bold text-emerald-800">Identitas Terverifikasi!</p>
                        <p class="text-xs text-emerald-600 mt-0.5">Silakan buat password baru Anda di bawah ini.</p>
                    </div>
                </div>

                <div>
                    <label for="password" class="block text-sm font-semibold text-gray-700 mb-2">Password Baru</label>
                    <div class="relative">
                        <input type="password" name="password" id="new_password" class="w-full px-4 py-3 pr-12 rounded-xl border border-gray-200 bg-gray-50 focus:bg-white focus:outline-none focus:border-brand-500 transition-colors" placeholder="Minimal 6 karakter" required>
                        <button type="button" onclick="toggleNewPassword()" class="absolute right-3 top-1/2 -translate-y-1/2 p-1 text-gray-400 hover:text-gray-600 transition-colors">
                            <svg id="eye-icon-new" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"></path></svg>
                        </button>
                    </div>
                </div>

                <div>
                    <label for="password_confirmation" class="block text-sm font-semibold text-gray-700 mb-2">Konfirmasi Password Baru</label>
                    <input type="password" name="password_confirmation" id="password_confirmation" class="w-full px-4 py-3 rounded-xl border border-gray-200 bg-gray-50 focus:bg-white focus:outline-none focus:border-brand-500 transition-colors" placeholder="Ketik ulang password baru" required>
                    <p id="password-match-msg" class="text-xs mt-1.5 hidden"></p>
                </div>

                <button type="submit" id="btn-step3" disabled class="w-full bg-brand-700 hover:bg-brand-800 disabled:bg-gray-300 disabled:shadow-none text-white font-bold py-3.5 rounded-xl transition-colors shadow-lg shadow-brand-700/30 flex items-center justify-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"></path></svg>
                    Simpan Password Baru
                </button>
            </form>
        </div>

        {{-- Back to Login Link --}}
        <div class="mt-6 text-center">
            <a href="{{ route('login') }}" class="text-sm text-brand-700 hover:text-brand-800 font-semibold transition-colors flex items-center justify-center gap-1.5">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                Kembali ke Login
            </a>
        </div>

    </div>

    <script>
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        // --- Step Navigation ---
        function goToStep(step) {
            document.getElementById('step-1').classList.add('hidden');
            document.getElementById('step-2').classList.add('hidden');
            document.getElementById('step-3').classList.add('hidden');
            document.getElementById('step-' + step).classList.remove('hidden');
            hideError();

            // Update step dots
            for (let i = 1; i <= 3; i++) {
                const dot = document.getElementById('step-dot-' + i);
                if (i < step) {
                    dot.className = 'w-8 h-8 rounded-full bg-emerald-500 text-white flex items-center justify-center text-xs font-bold transition-all shadow-md shadow-emerald-500/20';
                    dot.innerHTML = '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>';
                } else if (i === step) {
                    dot.className = 'w-8 h-8 rounded-full bg-brand-600 text-white flex items-center justify-center text-xs font-bold transition-all shadow-md shadow-brand-600/20';
                    dot.textContent = i;
                } else {
                    dot.className = 'w-8 h-8 rounded-full bg-gray-200 text-gray-400 flex items-center justify-center text-xs font-bold transition-all';
                    dot.textContent = i;
                }
            }

            // Update step lines
            for (let i = 1; i <= 2; i++) {
                const line = document.getElementById('step-line-' + i);
                if (i < step) {
                    line.className = 'w-8 h-0.5 bg-emerald-400 transition-all';
                } else {
                    line.className = 'w-8 h-0.5 bg-gray-200 transition-all';
                }
            }
        }

        function showError(message) {
            const container = document.getElementById('error-container');
            container.textContent = message;
            container.classList.remove('hidden');
        }

        function hideError() {
            document.getElementById('error-container').classList.add('hidden');
        }

        function setButtonLoading(btnId, loading) {
            const btn = document.getElementById(btnId);
            if (loading) {
                btn.disabled = true;
                btn.dataset.originalHtml = btn.innerHTML;
                btn.innerHTML = '<svg class="w-5 h-5 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg> Memproses...';
            } else {
                btn.disabled = false;
                btn.innerHTML = btn.dataset.originalHtml;
            }
        }

        // --- Step 1: Verify Username ---
        function verifyUsername() {
            const username = document.getElementById('username').value.trim();
            if (!username) {
                showError('Silakan masukkan username Anda.');
                return;
            }

            hideError();
            setButtonLoading('btn-step1', true);

            fetch('{{ route("password.verify") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json',
                },
                body: JSON.stringify({ step: 1, username: username }),
            })
            .then(res => res.json())
            .then(data => {
                setButtonLoading('btn-step1', false);
                if (data.success) {
                    document.getElementById('security-question-text').textContent = data.security_question;
                    document.getElementById('user-name-display').textContent = data.user_name;
                    goToStep(2);
                    document.getElementById('security_answer').focus();
                } else {
                    showError(data.message || 'Terjadi kesalahan.');
                }
            })
            .catch(() => {
                setButtonLoading('btn-step1', false);
                showError('Terjadi kesalahan koneksi. Silakan coba lagi.');
            });
        }

        // --- Step 2: Verify Security Answer ---
        function verifyAnswer() {
            const answer = document.getElementById('security_answer').value.trim();
            if (!answer) {
                showError('Silakan masukkan jawaban keamanan Anda.');
                return;
            }

            hideError();
            setButtonLoading('btn-step2', true);

            fetch('{{ route("password.verify") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json',
                },
                body: JSON.stringify({ step: 2, security_answer: answer }),
            })
            .then(res => res.json())
            .then(data => {
                setButtonLoading('btn-step2', false);
                if (data.success) {
                    document.getElementById('reset_token').value = data.reset_token;
                    goToStep(3);
                    document.getElementById('new_password').focus();
                } else {
                    showError(data.message || 'Jawaban keamanan salah.');
                }
            })
            .catch(() => {
                setButtonLoading('btn-step2', false);
                showError('Terjadi kesalahan koneksi. Silakan coba lagi.');
            });
        }

        // --- Password Match Validation ---
        const newPw = document.getElementById('new_password');
        const confirmPw = document.getElementById('password_confirmation');
        const matchMsg = document.getElementById('password-match-msg');
        const btnStep3 = document.getElementById('btn-step3');

        function checkPasswordMatch() {
            const pw = newPw.value;
            const cpw = confirmPw.value;

            if (!cpw) {
                matchMsg.classList.add('hidden');
                btnStep3.disabled = true;
                return;
            }

            matchMsg.classList.remove('hidden');

            if (pw.length < 6) {
                matchMsg.textContent = 'Password minimal 6 karakter.';
                matchMsg.className = 'text-xs mt-1.5 text-amber-600 font-medium';
                btnStep3.disabled = true;
            } else if (pw !== cpw) {
                matchMsg.textContent = 'Password tidak cocok.';
                matchMsg.className = 'text-xs mt-1.5 text-red-500 font-medium';
                btnStep3.disabled = true;
            } else {
                matchMsg.textContent = 'Password cocok ✓';
                matchMsg.className = 'text-xs mt-1.5 text-emerald-600 font-bold';
                btnStep3.disabled = false;
            }
        }

        newPw.addEventListener('input', checkPasswordMatch);
        confirmPw.addEventListener('input', checkPasswordMatch);

        // --- Toggle Password Visibility ---
        function toggleNewPassword() {
            const input = document.getElementById('new_password');
            const icon = document.getElementById('eye-icon-new');
            if (input.type === 'password') {
                input.type = 'text';
                icon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>';
            } else {
                input.type = 'password';
                icon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"></path>';
            }
        }

        // Enter key support
        document.getElementById('username').addEventListener('keypress', function(e) {
            if (e.key === 'Enter') { e.preventDefault(); verifyUsername(); }
        });
        document.getElementById('security_answer').addEventListener('keypress', function(e) {
            if (e.key === 'Enter') { e.preventDefault(); verifyAnswer(); }
        });
    </script>

</body>
</html>
