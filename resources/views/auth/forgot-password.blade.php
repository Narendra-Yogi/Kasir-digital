<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Reset Password - Geprek Legend</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,500;9..144,600;9..144,700&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
</head>

<body class="bg-surface font-sans text-[#1c1410] min-h-screen flex items-center justify-center p-4">

    <div class="w-full max-w-md rounded-[20px] border border-[#e8e0d3] bg-[#fffdf9] p-8 sm:p-10 rise">

        <div class="flex items-center gap-3 mb-7">
            <div class="w-10 h-10 rounded-xl overflow-hidden ring-1 ring-[#e8e0d3]">
                <img src="{{ asset('images/logo.jpeg') }}" alt="Geprek Legend" class="w-full h-full object-cover">
            </div>
            <div>
                <p class="font-display text-base font-semibold leading-tight">Geprek Legend</p>
                <p class="text-xs text-[#8a8179]">Pemulihan akun</p>
            </div>
        </div>

        <h1 class="font-display text-[24px] font-semibold tracking-tight">Reset password</h1>
        <p class="text-sm text-[#8a8179] mt-1.5">Verifikasi identitas Anda untuk membuat password baru.</p>

        {{-- Step Indicator --}}
        <div class="flex items-center gap-2 my-7">
            <div id="step-dot-1" class="w-8 h-8 rounded-full bg-[#1c1410] text-[#f7f1e6] flex items-center justify-center text-xs font-semibold transition-all">1</div>
            <div id="step-line-1" class="flex-1 h-px bg-[#e8e0d3] transition-all"></div>
            <div id="step-dot-2" class="w-8 h-8 rounded-full bg-[#f1ebe0] text-[#8a8179] flex items-center justify-center text-xs font-semibold transition-all">2</div>
            <div id="step-line-2" class="flex-1 h-px bg-[#e8e0d3] transition-all"></div>
            <div id="step-dot-3" class="w-8 h-8 rounded-full bg-[#f1ebe0] text-[#8a8179] flex items-center justify-center text-xs font-semibold transition-all">3</div>
        </div>

        {{-- Error Messages --}}
        <div id="error-container" class="hidden bg-[#fdf0ee] text-[#931b13] px-4 py-3 rounded-[10px] text-sm mb-5 border border-[#f2b3a6] text-center font-medium"></div>

        @if ($errors->any())
            <div class="bg-[#fdf0ee] text-[#931b13] px-4 py-3 rounded-[10px] text-sm mb-5 border border-[#f2b3a6] text-center font-medium">
                {{ $errors->first() }}
            </div>
        @endif

        @if(session('success'))
            <div class="bg-[#eef4ec] text-[#2c5b34] px-4 py-3 rounded-[10px] text-sm mb-5 border border-[#d9e5d6] text-center font-medium">
                {{ session('success') }}
            </div>
        @endif

        {{-- STEP 1: Input Username --}}
        <div id="step-1" class="space-y-4">
            <div>
                <label for="username" class="block text-sm font-medium mb-1.5">Username akun Anda</label>
                <input type="text" id="username" class="field" placeholder="Masukkan username Anda" required autofocus>
            </div>

            <button type="button" onclick="verifyUsername()" id="btn-step1" class="btn-ink w-full py-3 text-sm flex items-center justify-center gap-2">
                Lanjutkan
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
            </button>
        </div>

        {{-- STEP 2: Jawab Pertanyaan --}}
        <div id="step-2" class="space-y-4 hidden">
            <div class="bg-[#f7f2e9] border border-[#e8e0d3] px-4 py-3.5 rounded-[10px]">
                <p class="text-xs text-[#8a8179] mb-1">Pertanyaan keamanan</p>
                <p class="text-sm font-semibold" id="security-question-text"></p>
                <p class="text-xs text-[#8a8179] mt-1.5">Akun: <span class="font-medium text-[#1c1410]" id="user-name-display"></span></p>
            </div>

            <div>
                <label for="security_answer" class="block text-sm font-medium mb-1.5">Jawaban Anda</label>
                <input type="text" id="security_answer" class="field" placeholder="Ketik jawaban keamanan Anda" required>
            </div>

            <div class="flex gap-2.5">
                <button type="button" onclick="goToStep(1)" class="btn-outline flex-1 justify-center py-3">
                    Kembali
                </button>
                <button type="button" onclick="verifyAnswer()" id="btn-step2" class="btn-ink flex-1 py-3 text-sm flex items-center justify-center gap-2">
                    Verifikasi
                </button>
            </div>
        </div>

        {{-- STEP 3: Password Baru --}}
        <div id="step-3" class="hidden">
            <form action="{{ route('password.reset') }}" method="POST" class="space-y-4">
                @csrf
                <input type="hidden" name="reset_token" id="reset_token">

                <div class="bg-[#eef4ec] border border-[#d9e5d6] px-4 py-3.5 rounded-[10px]">
                    <p class="text-sm font-semibold text-[#2c5b34]">Identitas terverifikasi</p>
                    <p class="text-xs text-[#2c5b34]/80 mt-0.5">Silakan buat password baru di bawah ini.</p>
                </div>

                <div>
                    <label for="password" class="block text-sm font-medium mb-1.5">Password baru</label>
                    <div class="relative">
                        <input type="password" name="password" id="new_password" class="field pr-11" placeholder="Minimal 6 karakter" required>
                        <button type="button" onclick="toggleNewPassword()" class="absolute right-3 top-1/2 -translate-y-1/2 p-1 text-[#8a8179] hover:text-[#1c1410] transition-colors" aria-label="Tampilkan sandi">
                            <svg id="eye-icon-new" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"></path></svg>
                        </button>
                    </div>
                </div>

                <div>
                    <label for="password_confirmation" class="block text-sm font-medium mb-1.5">Konfirmasi password baru</label>
                    <input type="password" name="password_confirmation" id="password_confirmation" class="field" placeholder="Ketik ulang password baru" required>
                    <p id="password-match-msg" class="text-xs mt-1.5 hidden"></p>
                </div>

                <button type="submit" id="btn-step3" disabled class="btn-ink w-full py-3 text-sm flex items-center justify-center gap-2">
                    Simpan password baru
                </button>
            </form>
        </div>

        <div class="mt-6 text-center">
            <a href="{{ route('login') }}" class="text-sm text-[#931b13] hover:text-[#751510] font-medium transition-colors inline-flex items-center gap-1.5">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                Kembali ke login
            </a>
        </div>

    </div>

    <script>
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        function goToStep(step) {
            document.getElementById('step-1').classList.add('hidden');
            document.getElementById('step-2').classList.add('hidden');
            document.getElementById('step-3').classList.add('hidden');
            document.getElementById('step-' + step).classList.remove('hidden');
            hideError();

            for (let i = 1; i <= 3; i++) {
                const dot = document.getElementById('step-dot-' + i);
                if (i < step) {
                    dot.className = 'w-8 h-8 rounded-full bg-[#2c5b34] text-white flex items-center justify-center text-xs font-semibold transition-all';
                    dot.innerHTML = '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>';
                } else if (i === step) {
                    dot.className = 'w-8 h-8 rounded-full bg-[#1c1410] text-[#f7f1e6] flex items-center justify-center text-xs font-semibold transition-all';
                    dot.textContent = i;
                } else {
                    dot.className = 'w-8 h-8 rounded-full bg-[#f1ebe0] text-[#8a8179] flex items-center justify-center text-xs font-semibold transition-all';
                    dot.textContent = i;
                }
            }

            for (let i = 1; i <= 2; i++) {
                const line = document.getElementById('step-line-' + i);
                if (i < step) {
                    line.className = 'flex-1 h-px bg-[#2c5b34] transition-all';
                } else {
                    line.className = 'flex-1 h-px bg-[#e8e0d3] transition-all';
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
                btn.innerHTML = '<svg class="w-5 h-5 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg> Memproses…';
            } else {
                btn.disabled = false;
                btn.innerHTML = btn.dataset.originalHtml;
            }
        }

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
                matchMsg.className = 'text-xs mt-1.5 text-[#7a5c14] font-medium';
                btnStep3.disabled = true;
            } else if (pw !== cpw) {
                matchMsg.textContent = 'Password tidak cocok.';
                matchMsg.className = 'text-xs mt-1.5 text-[#931b13] font-medium';
                btnStep3.disabled = true;
            } else {
                matchMsg.textContent = 'Password cocok.';
                matchMsg.className = 'text-xs mt-1.5 text-[#2c5b34] font-medium';
                btnStep3.disabled = false;
            }
        }

        newPw.addEventListener('input', checkPasswordMatch);
        confirmPw.addEventListener('input', checkPasswordMatch);

        function toggleNewPassword() {
            const input = document.getElementById('new_password');
            const icon = document.getElementById('eye-icon-new');
            if (input.type === 'password') {
                input.type = 'text';
                icon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>';
            } else {
                input.type = 'password';
                icon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"></path>';
            }
        }

        document.getElementById('username').addEventListener('keypress', function(e) {
            if (e.key === 'Enter') { e.preventDefault(); verifyUsername(); }
        });
        document.getElementById('security_answer').addEventListener('keypress', function(e) {
            if (e.key === 'Enter') { e.preventDefault(); verifyAnswer(); }
        });
    </script>

</body>
</html>
