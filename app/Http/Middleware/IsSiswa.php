<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class IsSiswa
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Cek apakah user sudah login DAN memiliki peran 'siswa'
        if (Auth::check() && Auth::user()->role == 'siswa') {
            // Jika ya, lanjutkan ke halaman yang dituju
            return $next($request);
        }

        // Jika tidak, tolak akses dan tampilkan halaman error 403 (Forbidden)
        abort(403, 'AKSES DITOLAK: Halaman ini hanya untuk Siswa.');
    }
}
