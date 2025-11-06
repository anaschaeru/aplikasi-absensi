<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use App\Models\Izin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class IzinController extends Controller
{
    public function index()
    {
        $siswa = Auth::user()->siswa;
        $izins = Izin::where('siswa_id', $siswa->siswa_id)
            ->orderBy('tanggal_izin', 'desc')
            ->get();
        return view('siswa.izin.index', compact('izins'));
    }

    public function create()
    {
        return view('siswa.izin.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'tanggal_izin' => 'required|date|after_or_equal:today',
            'alasan' => 'required|string|min:10',
        ]);

        $siswa = Auth::user()->siswa;

        // Cek apakah sudah ada pengajuan untuk tanggal yang sama
        $exists = Izin::where('siswa_id', $siswa->siswa_id)
            ->where('tanggal_izin', $request->tanggal_izin)
            ->exists();

        if ($exists) {
            return redirect()->route('siswa.izin.index')->with('error', 'Anda sudah mengajukan izin untuk tanggal tersebut.');
        }

        Izin::create([
            'siswa_id' => $siswa->siswa_id,
            'tanggal_izin' => $request->tanggal_izin,
            'alasan' => $request->alasan,
            'status' => 'pending',
        ]);

        return redirect()->route('siswa.izin.index')->with('success', 'Pengajuan izin berhasil dikirim.');
    }
}
