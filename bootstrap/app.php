<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'admin' => \App\Http\Middleware\AdminMiddleware::class,
            'auth.session' => \App\Http\Middleware\Authenticate::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        // 419 "Página caducada" (token CSRF vencido): en lugar de la página
        // de error en blanco, devuelve al usuario a una página utilizable
        // con un mensaje claro. CSRF permanece ACTIVO: esto solo cambia
        // CÓMO se presenta el error, no la validación ni la seguridad.
        // - Navegación normal (formularios Blade) -> redirect al login.
        // - Peticiones AJAX/JSON (carrito Vue, CRUD admin) -> JSON 419,
        //   que el frontend ya sabe mostrar como aviso.
        //
        // Laravel 11 convierte el fallo CSRF en HttpException(419) antes
        // de renderizar, por eso se intercepta ESA excepción (y también
        // TokenMismatchException por si otra ruta del framework la lanza
        // sin convertir).
        $exceptions->render(function (\Symfony\Component\HttpKernel\Exception\HttpException $e, \Illuminate\Http\Request $request) {
            if ($e->getStatusCode() !== 419) {
                return null; // otros errores HTTP los maneja Laravel como siempre
            }

            if ($request->expectsJson()) {
                return response()->json([
                    'error' => 'Tu sesión había caducado. Recarga la página e intenta de nuevo.',
                ], 419);
            }

            return redirect()
                ->route('login')
                ->with('error', 'Tu sesión había caducado. Por favor inicia sesión de nuevo.');
        });

        $exceptions->render(function (\Illuminate\Session\TokenMismatchException $e, \Illuminate\Http\Request $request) {
            if ($request->expectsJson()) {
                return response()->json([
                    'error' => 'Tu sesión había caducado. Recarga la página e intenta de nuevo.',
                ], 419);
            }

            return redirect()
                ->route('login')
                ->with('error', 'Tu sesión había caducado. Por favor inicia sesión de nuevo.');
        });
    })->create();
