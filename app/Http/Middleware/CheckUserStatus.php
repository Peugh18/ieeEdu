<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckUserStatus
{
    /**
     * Handle an incoming request.
     *
     * Uses the session-cached user model (Auth::user()) — ZERO extra DB queries.
     * The user model is already loaded by the authentication guard from the session.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        // Solo actúa si hay un usuario autenticado
        if ($user && $user->status !== 'activo') {
            Auth::logout();

            $request->session()->invalidate();
            $request->session()->regenerateToken();

            // Para peticiones Inertia/AJAX, Inertia maneja la redirección automáticamente
            return redirect()->route('login')
                ->with('error', 'Tu cuenta ha sido desactivada. Contacta al administrador.');
        }

        return $next($request);
    }
}
