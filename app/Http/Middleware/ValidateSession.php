<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class ValidateSession
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Si el usuario está autenticado pero la sesión está corrupta o expirada
        if (Auth::check()) {
            // Verificar si la sesión tiene los datos básicos necesarios
            if (!$request->session()->has('_token')) {
                // Regenerar token de sesión
                $request->session()->regenerate();
            }
            
            // Actualizar la última actividad
            $request->session()->put('last_activity', time());
        }
        
        return $next($request);
    }
}
