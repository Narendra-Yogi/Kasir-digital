<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    // Menampilkan halaman form login
    public function showLogin()
    {
        return view('auth.login');
    }

    // Memproses data login
    // Memproses data login
    public function authenticate(Request $request)
    {
        $credentials = $request->validate([
            'username' => ['required', 'string'],
            'password' => ['required'],
        ]);

        // FIX #6: 5 percobaan gagal per username+IP dalam 60 detik
        $throttleKey = strtolower($request->input('username')).'|'.$request->ip();

        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $detik = RateLimiter::availableIn($throttleKey);

            return back()->withErrors([
                'username' => "Terlalu banyak percobaan login. Coba lagi dalam {$detik} detik.",
            ])->onlyInput('username');
        }

        // Cek kecocokan username dan password di database
        if (Auth::attempt($credentials)) {
            RateLimiter::clear($throttleKey);
            $request->session()->regenerate();

            // Jika berhasil, arahkan ke halaman kasir
            return redirect()->intended('kasir');
        }

        // Jika gagal, kembalikan ke halaman login dengan pesan error
        RateLimiter::hit($throttleKey, 60);

        return back()->withErrors([
            'username' => 'Username atau password salah.',
        ])->onlyInput('username');
    }

    // Memproses logout
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }

    // ========================================================================
    // RESET PASSWORD MANDIRI (SELF-SERVICE)
    // ========================================================================

    /**
     * Menampilkan halaman lupa password (multi-step form).
     */
    public function showForgotPassword()
    {
        return view('auth.forgot-password');
    }

    /**
     * Step 1 & 2: Verifikasi username dan jawaban keamanan via AJAX.
     * Jika step=1, validasi username dan kembalikan pertanyaan keamanan.
     * Jika step=2, validasi jawaban keamanan dan generate reset token.
     */
    public function verifySecurityAnswer(Request $request)
    {
        $step = $request->input('step', 1);

        if ($step == 1) {
            // Step 1: Validasi username, kembalikan pertanyaan keamanan
            $request->validate(['username' => 'required|string']);

            $throttleKey = 'password-reset:'.strtolower($request->input('username')).'|'.$request->ip();

            if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
                $detik = RateLimiter::availableIn($throttleKey);

                return response()->json([
                    'success' => false,
                    'message' => "Terlalu banyak percobaan. Coba lagi dalam {$detik} detik.",
                ], 429);
            }
            RateLimiter::hit($throttleKey, 300); // jeda 5 menit per percobaan

            $user = User::where('username', $request->username)->first();

            if (! $user || ! $user->security_question || ! $user->security_answer) {
                return response()->json([
                    'success' => false,
                    'message' => 'Reset password mandiri tidak tersedia untuk akun ini. Hubungi Administrator.',
                ], 422);
            }

            // Simpan username di session untuk verifikasi step berikutnya
            $request->session()->put('reset_username', $user->username);

            return response()->json([
                'success' => true,
                'security_question' => $user->security_question,
                'user_name' => $user->name,
            ]);
        }

        if ($step == 2) {
            // Step 2: Validasi jawaban keamanan
            $request->validate(['security_answer' => 'required|string']);

            $username = $request->session()->get('reset_username');
            if (! $username) {
                return response()->json([
                    'success' => false,
                    'message' => 'Sesi telah kedaluwarsa. Silakan ulangi dari awal.',
                ], 422);
            }

            $throttleKey = 'password-answer:'.strtolower($username).'|'.$request->ip();

            if (RateLimiter::tooManyAttempts($throttleKey, 3)) {
                $detik = RateLimiter::availableIn($throttleKey);

                return response()->json([
                    'success' => false,
                    'message' => "Terlalu banyak percobaan jawaban salah. Coba lagi dalam {$detik} detik.",
                ], 429);
            }

            $user = User::where('username', $username)->first();
            if (! $user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Sesi tidak valid. Silakan ulangi dari awal.',
                ], 422);
            }

            // Cek jawaban (case-insensitive) — jawaban di-lowercase sebelum dibandingkan
            if (! Hash::check(strtolower(trim($request->security_answer)), $user->security_answer)) {
                RateLimiter::hit($throttleKey, 600); // jeda 10 menit setelah jawaban salah

                return response()->json([
                    'success' => false,
                    'message' => 'Jawaban keamanan salah. Silakan coba lagi.',
                ], 422);
            }

            RateLimiter::clear($throttleKey);

            // Generate reset token sementara (berlaku 10 menit)
            $resetToken = Str::random(40);
            $request->session()->put('reset_token', $resetToken);
            $request->session()->put('reset_token_expires', now()->addMinutes(10));

            return response()->json([
                'success' => true,
                'reset_token' => $resetToken,
            ]);
        }

        return response()->json(['success' => false, 'message' => 'Step tidak valid.'], 422);
    }

    /**
     * Step 3: Proses ganti password baru setelah verifikasi berhasil.
     */
    public function resetPassword(Request $request)
    {
        $request->validate([
            'password' => 'required|string|min:6|confirmed',
            'reset_token' => 'required|string',
        ]);

        // Validasi reset token dari session
        $sessionToken = $request->session()->get('reset_token');
        $tokenExpires = $request->session()->get('reset_token_expires');
        $username = $request->session()->get('reset_username');

        if (! $sessionToken || ! $username || $request->reset_token !== $sessionToken) {
            return back()->withErrors(['reset_token' => 'Token reset tidak valid. Silakan ulangi proses dari awal.']);
        }

        if ($tokenExpires && now()->isAfter($tokenExpires)) {
            // Bersihkan session reset
            $request->session()->forget(['reset_token', 'reset_token_expires', 'reset_username']);

            return back()->withErrors(['reset_token' => 'Token reset telah kedaluwarsa. Silakan ulangi proses dari awal.']);
        }

        $user = User::where('username', $username)->first();
        if (! $user) {
            return back()->withErrors(['reset_token' => 'User tidak ditemukan.']);
        }

        // Update password
        $user->update([
            'password' => Hash::make($request->password),
        ]);

        // Bersihkan session reset
        $request->session()->forget(['reset_token', 'reset_token_expires', 'reset_username']);

        return redirect()->route('login')->with('success', 'Password berhasil diubah! Silakan login dengan password baru Anda.');
    }
}
