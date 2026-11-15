
<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {

        // Reconocer las conexiones HTTPS que llegan desde Railway
        $middleware->trustProxies(
            at: '*',
            headers: Request::HEADER_X_FORWARDED_FOR |
                     Request::HEADER_X_FORWARDED_HOST |
                     Request::HEADER_X_FORWARDED_PORT |
                     Request::HEADER_X_FORWARDED_PROTO
        );

        // Roles y autenticación
        $middleware->alias([
            'admin' => \App\Http\Middleware\AdminMiddleware::class,
            'empleado' => \App\Http\Middleware\EmployeeMiddleware::class,
            'auth.session' => \App\Http\Middleware\Authenticate::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {

        // Manejo de errores 419 por sesión caducada
        $exceptions->render(function (
            \Symfony\Component\HttpKernel\Exception\HttpException $e,
            \Illuminate\Http\Request $request
        ) {
            if ($e->getStatusCode() !== 419) {
                return null;
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

        $exceptions->render(function (
            \Illuminate\Session\TokenMismatchException $e,
            \Illuminate\Http\Request $request
        ) {
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
