<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;
use App\Models\Term;
use Symfony\Component\HttpFoundation\Response;

class SetCurrentTerm
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Solo establecer si el usuario está autenticado y no hay término en sesión
        if (Auth::check() && !Session::has('current_term')) {
            // Buscar el término académico activo actual
            $currentTerm = Term::where('asset', true)
                ->where('start_date', '<=', now())
                ->where('end_date', '>=', now())
                ->first();

            // Si no hay término activo actual, buscar el próximo activo
            if (!$currentTerm) {
                $currentTerm = Term::where('asset', true)
                    ->where('start_date', '>', now())
                    ->orderBy('start_date', 'asc')
                    ->first();
            }

            // Si aún no hay término activo, buscar el más reciente activo
            if (!$currentTerm) {
                $currentTerm = Term::where('asset', true)
                    ->orderBy('end_date', 'desc')
                    ->first();
            }

            if ($currentTerm) {
                Session::put('current_term', $currentTerm);
            }
        }

        return $next($request);
    }
}
