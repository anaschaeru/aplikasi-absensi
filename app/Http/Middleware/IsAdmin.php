<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class IsAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check() && Auth::user()->role == 'admin') {
            return $next($request); // Lanjutkan permintaan
        }

        // Jika tidak, tolak akses
        abort(403, 'AKSES DITOLAK. ANDA BUKAN ADMIN.');
    }
}
