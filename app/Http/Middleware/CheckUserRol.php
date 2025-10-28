<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckUserRol
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    
    public function handle(Request $request, Closure $next, string $roles): Response
    {
        $user = $request->user();

        if (! $user) {
            return redirect()->route('login');
        }

        //solo para que pueda aceptar varios roles separados por |
        $allowedRoles = array_map('strtolower', explode('|', $roles));

        // Obtener el rol del usuario autenticado      
        $userRoleName = strtolower(trim($user->role->name));

        //verifica si el rol del usuario está en la lista de roles permitidos
        if (! in_array($userRoleName, $allowedRoles)) {
            abort(403, 'Acceso denegado. Rol insuficiente.');
        }


        return $next($request);
    }
}
