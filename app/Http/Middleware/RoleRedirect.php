<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleRedirect
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->check()) {
            $user = auth()->user();
            // Hanya redirect JIKA Kasir mencoba masuk ke Dashboard
            if ($user->role === 'kasir' && $request->is('dashboard')) {
                return redirect()->route('admin.orders.index');
            }
        }
        return $next($request);
    }
}
