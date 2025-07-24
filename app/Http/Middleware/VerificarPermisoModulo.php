<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class VerificarPermisoModulo
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle($request, Closure $next, $modulo)
    {
        $usuario = Auth::user();

        // Permitir siempre si es admin
        if ($usuario->rol === 'admin') {
            return $next($request);
        }

        // Si el módulo está bloqueado (existe en la tabla), denegar
        if ($usuario->permisos->pluck('modulo')->contains($modulo)) {
            return redirect()->route('dashboard.index')->with('error', 'No tienes permiso para acceder a este módulo.');
        }

        // Si no está bloqueado, dejar pasar
        return $next($request);
    }
}
