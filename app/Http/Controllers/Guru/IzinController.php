<?php

namespace App\Http\Controllers\Guru; // Namespace beda dengan Siswa

use App\Http\Controllers\Controller;
use App\Models\Izin;
use Illuminate\Http\Request;

class IzinController extends Controller
{
    // Menampilkan semua daftar izin
    public function index()
    {
        // Ambil semua data izin, urutkan dari yang terbaru
        // Sertakan data siswa dan kelasnya agar bisa ditampilkan
        $izins = Izin::with(['siswa.kelas'])
            ->orderBy('tanggal_izin', 'desc')
            ->orderBy('status', 'asc') // Tampilkan yang 'pending' duluan (opsional)
            ->paginate(10); // Gunakan paginate agar halaman tidak terlalu panjang

        // Arahkan ke view khusus Guru Piket
        return view('guru-piket.izin.index', compact('izins'));
    }

    // Menyetujui atau Menolak Izin
    public function update(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:approved,rejected',
        ]);

        $izin = Izin::findOrFail($id);
        $izin->update([
            'status' => $request->status
        ]);

        return redirect()->back()->with('success', 'Status izin berhasil diperbarui.');
    }
}
