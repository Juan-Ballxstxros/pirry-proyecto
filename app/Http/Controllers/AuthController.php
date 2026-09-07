<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\Usuario;
use App\Services\ResendMailer;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;

class AuthController extends Controller
{
    public function showLogin()
    {
        if (Auth::check()) {
            return redirect()->route('home');
        }

        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'correo' => 'required|email',
            'contrasena' => 'required',
        ]);

        $usuario = Usuario::where('correo', $request->correo)->first();

        if (! $usuario) {
            return back()->withErrors(['correo' => 'Usuario no encontrado.'])->withInput();
        }

        if (! $usuario->isActive()) {
            return back()->withErrors(['correo' => 'Tu cuenta está inactiva.'])->withInput();
        }

        $validPassword = $request->contrasena === $usuario->contrasena
            || Hash::check($request->contrasena, $usuario->contrasena);

        if (! $validPassword) {
            return back()->withErrors(['contrasena' => 'Contraseña incorrecta.'])->withInput();
        }

        Auth::login($usuario);

        if ($usuario->isAdmin()) {
            return redirect()->route('admin.dashboard');
        }

        return redirect()->route('home')->with('success', '¡Bienvenido, '.$usuario->nombre.'!');
    }

    public function showRegister()
    {
        if (Auth::check()) {
            return redirect()->route('home');
        }

        return view('auth.register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:100',
            'documento' => 'required|string|max:50',
            'correo' => 'required|email|unique:usuarios,correo',
            'usuario' => 'required|string|max:50|unique:usuarios,usuario',
            'contrasena' => ['required', 'confirmed', Password::min(6)],
        ], [
            'correo.unique' => 'Este correo ya está registrado.',
            'usuario.unique' => 'Este nombre de usuario ya existe.',
            'contrasena.confirmed' => 'Las contraseñas no coinciden.',
        ]);

        try {
            $rolId = $this->resolverRolCliente();

            $usuario = Usuario::create([
                'nombre' => $request->nombre,
                'documento' => $request->documento,
                'correo' => $request->correo,
                'usuario' => $request->usuario,
                'contrasena' => Hash::make($request->contrasena),
                'id_rol' => $rolId,
                'estado' => 'activo',
            ]);
        } catch (\Throwable $e) {
            report($e);

            return back()
                ->withInput()
                ->withErrors(['correo' => 'No pudimos crear tu cuenta en este momento. Por favor intenta de nuevo.']);
        }

        Auth::login($usuario);

        return redirect()->route('home')
            ->with('success', '¡Tu cuenta fue creada con éxito! Bienvenido a El Pirry 🎉');
    }

    /**
     * Devuelve el id del rol "Cliente" para los nuevos registros públicos.
     *
     * Reglas de seguridad:
     * - El rol se busca sin distinguir mayúsculas/minúsculas.
     * - Si no existe, se crea el rol "Cliente" para que el registro
     *   nunca falle por la llave foránea id_rol.
     * - NUNCA se reutiliza otro rol existente (Administrador, empleado,
     *   etc.): antes, el fallback al primer rol de la tabla asignaba
     *   accidentalmente el rol Administrador (id 1) a cada registro nuevo.
     * - El rol NO se toma del request: el formulario público ni siquiera
     *   lo envía, y aunque alguien lo manipule manualmente, aquí siempre
     *   se asigna el rol Cliente.
     */
    private function resolverRolCliente(): int
    {
        $rol = Role::whereRaw('LOWER(nombre) = ?', ['cliente'])->first();

        if ($rol) {
            return (int) $rol->id_roles;
        }

        return (int) Role::create([
            'nombre' => 'Cliente',
            'descripcion' => 'cliente de la tienda',
            'estado' => 'Activo',
        ])->id_roles;
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')
            ->with('success', 'Sesión cerrada correctamente. ¡Vuelve pronto! 👋');
    }

    /* ---------------------------------------------------------------------
     |  Recuperación de contraseña (correo enviado con Resend)
     | ---------------------------------------------------------------------
     |  Flujo: el usuario pide un enlace -> se genera un token aleatorio que
     |  se guarda HASHEADO (sha256) en la columna reset_token de la tabla
     |  usuarios (columnas reset_token / token_expira ya existentes), se
     |  envía por correo y, al usarse, se valida su expiración y se
     |  invalida. La nueva contraseña se guarda con Hash::make, el mismo
     |  sistema de hash del registro.
     */

    /** Duración de validez del enlace de recuperación (en minutos). */
    private const RESET_TOKEN_MINUTOS = 60;

    /**
     * Formulario "¿Olvidaste tu contraseña?".
     */
    public function showForgotPassword()
    {
        return view('auth.forgot');
    }

    /**
     * Procesa la solicitud de recuperación: genera el token temporal y envía
     * el enlace por correo usando la API de Resend.
     *
     * Seguridad: responde SIEMPRE con el mismo mensaje de éxito, exista o no
     * el correo, para que no se pueda averiguar qué correos están
     * registrados (enumeración de usuarios).
     */
    public function sendResetLink(Request $request)
    {
        $request->validate([
            'correo' => 'required|email',
        ]);

        // Throttle ligero por sesión (el proyecto no tiene tabla de cache,
        // por eso no se usa el middleware throttle): como máximo una
        // solicitud cada 60 segundos. La respuesta sigue siendo idéntica
        // siempre, para no revelar información.
        $ultima = $request->session()->get('password_reset_requested_at');
        if ($ultima && Carbon::parse($ultima)->gt(now()->subSeconds(60))) {
            return back()->with(
                'success',
                'Si tu correo está registrado, te enviamos un enlace para restablecer tu contraseña. Revisa tu bandeja de entrada (y la carpeta de spam).'
            );
        }

        $usuario = Usuario::where('correo', $request->correo)->first();

        // Solo los usuarios activos reciben el enlace. Para correos
        // inexistentes o inactivos la respuesta es idéntica.
        if ($usuario && $usuario->isActive()) {
            $request->session()->put('password_reset_requested_at', now());

            $token = Str::random(64);

            // Se guarda el hash del token, nunca el token en texto plano.
            $usuario->reset_token = hash('sha256', $token);
            $usuario->token_expira = now()->addMinutes(self::RESET_TOKEN_MINUTOS);
            $usuario->save();

            $url = route('password.reset', ['token' => $token]);

            try {
                app(ResendMailer::class)->sendPasswordReset($usuario, $url);
            } catch (\Throwable $e) {
                report($e);

                // Sin API key configurada se registra el enlace para poder
                // probar el flujo en local (solo en entorno local).
                if (app()->environment('local')) {
                    logger()->warning('[Resend] Correo no enviado. Enlace de recuperación generado para pruebas: '.$url);
                }
            }
        }

        return back()->with(
            'success',
            'Si tu correo está registrado, te enviamos un enlace para restablecer tu contraseña. Revisa tu bandeja de entrada (y la carpeta de spam).'
        );
    }

    /**
     * Formulario para establecer la nueva contraseña. Valida el token
     * recibido en el enlace antes de mostrar el formulario.
     */
    public function showResetForm(string $token)
    {
        $usuario = $this->buscarUsuarioPorTokenValido($token);

        if (! $usuario) {
            return redirect()->route('login')
                ->with('error', 'El enlace de recuperación no es válido o ya expiró. Solicita uno nuevo.');
        }

        return view('auth.reset', [
            'token' => $token,
            'correo' => $usuario->correo,
        ]);
    }

    /**
     * Guarda la nueva contraseña si el token es válido y no ha expirado.
     * El token se invalida al usarse (no se puede reutilizar).
     */
    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => 'required|string',
            'correo' => 'required|email',
            // Mismas reglas que el registro de este proyecto.
            'contrasena' => ['required', 'confirmed', Password::min(6)],
        ], [
            'correo.required' => 'El correo es obligatorio.',
            'correo.email' => 'Ingresa un correo válido.',
            'contrasena.required' => 'La nueva contraseña es obligatoria.',
            'contrasena.confirmed' => 'Las contraseñas no coinciden.',
            'contrasena.min' => 'La contraseña debe tener al menos 6 caracteres.',
        ]);

        $usuario = $this->buscarUsuarioPorTokenValido($request->token);

        if (! $usuario || ! $usuario->correo || strtolower($usuario->correo) !== strtolower($request->correo)) {
            return redirect()->route('password.request')
                ->with('error', 'El enlace de recuperación no es válido o ya expiró. Solicita uno nuevo.');
        }

        // Mismo sistema de hash que usa el registro del proyecto.
        $usuario->contrasena = Hash::make($request->contrasena);
        // Invalidar el token: no puede volver a usarse.
        $usuario->reset_token = null;
        $usuario->token_expira = null;
        $usuario->save();

        return redirect()->route('login')
            ->with('success', '¡Contraseña actualizada con éxito! Ya puedes iniciar sesión con tu nueva contraseña.');
    }

    /**
     * Busca el usuario cuyo reset_token coincide con el hash del token del
     * enlace y cuya expiración sigue vigente.
     */
    private function buscarUsuarioPorTokenValido(string $token): ?Usuario
    {
        if ($token === '') {
            return null;
        }

        $usuario = Usuario::where('reset_token', hash('sha256', $token))->first();

        if (! $usuario || empty($usuario->token_expira)) {
            return null;
        }

        if (Carbon::parse($usuario->token_expira)->isPast()) {
            return null;
        }

        return $usuario;
    }
}
