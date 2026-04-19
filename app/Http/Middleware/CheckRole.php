<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  ...$roles  <-- Perhatikan titik tiga ini (Splat Operator)
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        // 1. Cek apakah user sudah login
        // 2. Cek apakah role user ada di dalam daftar $roles (misal: ['admin', 'kasir'])
        if (!$request->user() || !in_array($request->user()->role, $roles)) {
            
            // PENTING: Jangan tendang ke /dashboard untuk menghindari loop!
            // Kita tendang ke halaman '/' (Welcome) atau kasih pesan error akses.
            return redirect('/')->with('error', 'Maaf, Anda tidak memiliki izin akses.');
        }

        return $next($request);
    }
}