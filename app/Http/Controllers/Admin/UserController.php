<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Yajra\DataTables\Facades\DataTables; // WAJIB TAMBAHKAN INI

class UserController extends Controller
{
    /**
     * Menampilkan halaman daftar pengguna (Menggunakan Server-Side DataTables).
     */
    public function index(Request $request)
    {
        // Jika request datang dari AJAX (DataTables)
        if ($request->ajax()) {
            // Ambil data user, kecualikan admin yang sedang login
            $users = User::where('id', '!=', Auth::id())
                ->select(['id', 'name', 'email', 'role']);

            return DataTables::of($users)
                ->addIndexColumn() // Untuk nomor urut otomatis (DT_RowIndex)
                ->addColumn('role_form', function ($user) {
                    // Render form ubah role
                    $roles = ['admin', 'guru', 'guru_piket', 'siswa', 'walikelas'];
                    $options = '';
                    foreach ($roles as $r) {
                        $selected = $user->role == $r ? 'selected' : '';
                        $label = ucfirst(str_replace('_', ' ', $r));
                        $options .= "<option value='{$r}' {$selected}>{$label}</option>";
                    }

                    $route = route('admin.users.updateRole', $user->id);
                    $csrf = csrf_field();
                    $method = method_field('PATCH');

                    return "
                    <form action='{$route}' method='POST' class='flex items-center gap-2'>
                        {$csrf}
                        {$method}
                        <select name='role' class='block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm text-sm'>
                            {$options}
                        </select>
                        <button type='submit' class='p-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 transition' title='Simpan Perubahan Role'>
                            <svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 20 20' fill='currentColor' class='w-4 h-4'>
                                <path d='M9.25 13.5a.75.75 0 001.5 0V4.636l2.955 3.129a.75.75 0 001.09-1.03l-4.25-4.5a.75.75 0 00-1.09 0l-4.25 4.5a.75.75 0 101.09 1.03L9.25 4.636v8.864z' />
                                <path d='M3.5 12.75a.75.75 0 00-1.5 0v2.5A2.75 2.75 0 004.75 18h10.5A2.75 2.75 0 0018 15.25v-2.5a.75.75 0 00-1.5 0v2.5c0 .69-.56 1.25-1.25 1.25H4.75c-.69 0-1.25-.56-1.25-1.25v-2.5z' />
                            </svg>
                        </button>
                    </form>";
                })
                ->addColumn('action', function ($user) {
                    // Render form reset password
                    $route = route('admin.users.resetPassword', $user->id);
                    $csrf = csrf_field();
                    $name = addslashes($user->name);

                    return "
                    <form id='reset-form-{$user->id}' action='{$route}' method='POST'>
                        {$csrf}
                        <button type='button' class='inline-flex items-center px-3 py-2 bg-yellow-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-yellow-600 transition' onclick=\"confirmReset({$user->id}, '{$name}')\">
                            Reset Pass
                        </button>
                    </form>";
                })
                ->rawColumns(['role_form', 'action']) // Wajib agar tag HTML dirender, tidak dibaca sebagai teks biasa
                ->make(true);
        }

        // Jika bukan AJAX, kembalikan tampilan view kosong (data akan ditarik lewat AJAX dari Blade)
        return view('admin.users.index');
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
            'role' => ['required', Rule::in(['admin', 'guru', 'guru_piket', 'siswa', 'walikelas'])],
        ]);

        $user->update([
            'role' => $validated['role']
        ]);

        return redirect()->route('admin.users.index')
            ->with('success', 'Peran untuk pengguna ' . $user->name . ' berhasil diubah.');
    }
}
