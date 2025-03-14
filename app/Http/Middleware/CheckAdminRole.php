<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckAdminRole
{
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();
        if (!in_array($user->rol, ['admin', 'encargado_turismo'])) {
            return redirect()->route('home')->with('error', 'No tienes permisos para acceder a esta sección.');
        }

        return $next($request);
    }
}