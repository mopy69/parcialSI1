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

        $forwarded = $request->header('X-Forwarded-For');
        $ips = $forwarded ? array_map('trim', explode(',', $forwarded)) : [];
        $publicIps = array_filter($ips, function ($ip) {
                return filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE);
            });
        $ipv4 = array_filter($publicIps, fn($ip) => filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4));

        Log::create([
            'ip_address' => $ipv4 ? reset($ipv4) : ($publicIps ? end($publicIps) : $request->ip()),
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

            Log::create([
                'ip_address' => $request->ip(),
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
