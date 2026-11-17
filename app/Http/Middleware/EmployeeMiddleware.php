<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EmployeeMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        if (! $user || ! $user->isEmpleado()) {
            if ($request->expectsJson()) {
                return response()->json(['error' => 'No autorizado'], 403);
            }

            return redirect()->route('home')->with('error', 'Esta sección es exclusiva para empleados registrados en el sistema.');
        }

        return $next($request);
    }
}