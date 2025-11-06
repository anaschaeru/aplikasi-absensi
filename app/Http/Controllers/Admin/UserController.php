<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * Menampilkan halaman daftar pengguna.
     */
    public function index()
    {
        // Ambil semua pengguna kecuali admin yang sedang login
        $users = User::where('id', '!=', Auth::id())->get();
        return view('admin.users.index', compact('users'));
    }

    /**
     * Mereset password pengguna ke password default.
     */
    public function resetPassword(User $user)
    {
        // Password default yang baru
        $defaultPassword = 'password';

        $user->update([
            'password' => Hash::make($defaultPassword)
        ]);

        return redirect()->route('admin.users.index')
            ->with('success', 'Password untuk pengguna ' . $user->name . ' berhasil direset menjadi "' . $defaultPassword . '".');
    }

    public function updateRole(Request $request, User $user)
    {
        // Validasi input untuk memastikan role yang dikirim adalah salah satu dari daftar yang diizinkan
        $validated = $request->validate([
            'role' => ['required', Rule::in(['admin', 'guru', 'guru_piket', 'siswa'])],
        ]);

        $user->update([
            'role' => $validated['role']
        ]);

        return redirect()->route('admin.users.index')
            ->with('success', 'Peran untuk pengguna ' . $user->name . ' berhasil diubah.');
    }
}
