<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index()
    {
        $users = User::latest()->get();
        return view('users.index', compact('users'));
    }

    public function create()
    {
        return view('users.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users',
            'password' => 'required|string|min:6',
            'role' => 'required|in:admin,kasir',
            'security_question' => 'required|string|max:255',
            'security_answer' => 'required|string|max:255',
        ]);

        User::create([
            'name' => $request->name,
            'username' => $request->username,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'security_question' => $request->security_question,
            'security_answer' => Hash::make(strtolower(trim($request->security_answer))),
        ]);

        return redirect()->route('users.index')->with('success', 'Akun pengguna berhasil ditambahkan.');
    }

    public function edit(User $user)
    {
        return view('users.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'username' => ['required', 'string', 'max:255', Rule::unique('users')->ignore($user->id)],
            'password' => 'nullable|string|min:6', // Password opsional saat diedit
            'role' => 'required|in:admin,kasir',
            'security_question' => 'required|string|max:255',
            'security_answer' => 'nullable|string|max:255',
        ]);

        // Siapkan data yang akan diupdate
        $data = [
            'name' => $request->name,
            'username' => $request->username,
            'role' => $request->role,
            'security_question' => $request->security_question,
        ];

        // Jika form password diisi, berarti dia ingin ganti password
        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        // Jika jawaban keamanan diisi, hash dan simpan (case-insensitive)
        if ($request->filled('security_answer')) {
            $data['security_answer'] = Hash::make(strtolower(trim($request->security_answer)));
        }

        $user->update($data);

        return redirect()->route('users.index')->with('success', 'Akun pengguna berhasil diperbarui.');
    }

    public function destroy(User $user)
    {
        // Cegah user menghapus akunnya sendiri yang sedang dipakai login
        if ($user->id === auth()->id) {
            return back()->with('error', 'Anda tidak bisa menghapus akun yang sedang Anda gunakan!');
        }

        $user->delete();
        return redirect()->route('users.index')->with('success', 'Akun pengguna berhasil dihapus.');
    }

    /**
     * Reset password user ke password baru yang digenerate otomatis.
     * Hanya bisa diakses admin dari halaman Kelola Pengguna.
     */
    public function resetPassword(User $user)
    {
        // Generate password baru 6 karakter acak (huruf + angka)
        $newPassword = substr(str_shuffle('abcdefghjkmnpqrstuvwxyz23456789'), 0, 6);

        $user->update([
            'password' => Hash::make($newPassword),
        ]);

        return response()->json([
            'success' => true,
            'new_password' => $newPassword,
            'user_name' => $user->name,
        ]);
    }
}