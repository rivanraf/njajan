<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Table;

class RestoreTableSession
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Restore session from cookie if table_id is missing but cookie exists
        if (!session()->has('table_id') && $request->hasCookie('table_hash')) {
            $hash = $request->cookie('table_hash');
            $table = Table::where('hash', $hash)->first();
            
            if ($table && $table->status !== 'nonaktif') {
                session([
                    'table_id' => $table->id,
                    'table_number' => $table->number,
                    'table_hash' => $hash
                ]);
            }
        }

        return $next($request);
    }
}
