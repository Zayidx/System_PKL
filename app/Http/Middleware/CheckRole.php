<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  $role  Nama role yang diizinkan
     */
    public function handle(Request $request, Closure $next, string $role): Response
    {
        // Periksa apakah pengguna sudah login DAN memiliki role yang sesuai.
        // Kita menggunakan relasi 'role' yang sudah ada di model User.
        if (!Auth::check() || Auth::user()->role->name !== $role) {
            // Jika tidak sesuai, kembalikan halaman "Forbidden".
            abort(403, 'ANDA TIDAK MEMILIKI AKSES.');
        }

        // Jika sesuai, lanjutkan ke halaman berikutnya.
        return $next($request);
    }
}
