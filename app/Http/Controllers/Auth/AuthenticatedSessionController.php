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

        Log::create([
            'ip_address' => $request->ip(),
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
