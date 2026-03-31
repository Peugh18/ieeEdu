<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EnsureAdmin
{
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();

        if (!$user) {
            abort(403, 'Access denied. User not authenticated.');
        }

        if ($user->role !== 'admin') {
            abort(403, "Access denied. User role '{$user->role}' is not admin.");
        }

        if ($user->status !== 'activo') {
            abort(403, "Access denied. User status '{$user->status}' is not activo.");
        }

        return $next($request);
    }
}
