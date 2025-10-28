<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\Log;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        $forwarded = $request->header('X-Forwarded-For');
        $ips = $forwarded ? array_map('trim', explode(',', $forwarded)) : [];

        // 2. IP por defecto (si no encontramos una válida)
        $realIp = $request->ip();

        // 3. Busca la primera IP que sea IPv4 válida y no privada/reservada
        foreach ($ips as $ip) {
            if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4 | FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
                $realIp = $ip; // ¡La encontramos!
                break;        // Deja de buscar
            }
        }

        Log::create([
            'ip_address' => $request->$realIp(),
            'action'     => 'Inicio de Sesión',
            'state'      => 'Exitoso',
            'details'    => 'El usuario ' . Auth::user()->email . ' ha iniciado sesión.',
            'user_id'    => Auth::id(),
        ]);

        return redirect()->intended(route('dashboard', absolute: false));
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {

        if (Auth::check()) { // Comprueba si hay un usuario

            $forwarded = $request->header('X-Forwarded-For');
            $ips = $forwarded ? array_map('trim', explode(',', $forwarded)) : [];

            // 2. IP por defecto (si no encontramos una válida)
            $realIp = $request->ip();

            // 3. Busca la primera IP que sea IPv4 válida y no privada/reservada
            foreach ($ips as $ip) {
                if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4 | FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
                    $realIp = $ip; // ¡La encontramos!
                    break;        // Deja de buscar
                }
            }

            Log::create([
                'ip_address' => $realIp,
                'action'     => 'Cierre de Sesión',
                'state'      => 'Exitoso',
                'details'    => 'El usuario ' . Auth::user()->email . ' ha cerrado sesión.',
                'user_id'    => Auth::id(),
            ]);
        }

        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
