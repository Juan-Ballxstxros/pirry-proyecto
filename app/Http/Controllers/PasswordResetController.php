<?php

namespace App\Http\Controllers;

use App\Services\GmailMailer;
use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;

/**
 * RF-017/018 · RI-018 · RN-006: recuperación de contraseña por e-mail.
 *
 * El correo se envía a través del driver configurado en MAIL_MAILER (.env),
 * que delega el envío real a una API externa de correo transaccional
 * (Resend, Mailgun, Postmark, SES...). Ver config/mail.php.
 *
 * El token nunca se guarda en texto plano: se genera aleatorio, se envía
 * al usuario por correo y en la base de datos solo queda su hash. Expira
 * a los 15 minutos y se invalida (se borra) en cuanto se usa con éxito.
 */
class PasswordResetController extends Controller
{
    private const MINUTOS_EXPIRACION = 15;

    public function showForgotForm()
    {
        return view('auth.forgot-password');
    }

    public function sendResetLink(Request $request)
    {
        $request->validate([
            'correo' => 'required|email',
        ]);

        // Por seguridad, la respuesta es la misma exista o no el correo:
        // así no se puede usar este formulario para averiguar qué e-mails
        // están registrados en el sistema.
        $mensajeGenerico = 'Si el correo está registrado, te enviamos un enlace de recuperación.';

        $usuario = Usuario::where('correo', $request->correo)->first();

        if (! $usuario) {
            return back()->with('success', $mensajeGenerico);
        }

        $token = Str::random(64);

        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $usuario->correo],
            ['token' => Hash::make($token), 'created_at' => now()]
        );

        $resetUrl = route('password.reset.form', [
            'token' => $token,
            'email' => $usuario->correo,
        ]);

        try {
            app(GmailMailer::class)->sendPasswordReset($usuario, $resetUrl);
        } catch (\Throwable $e) {
            report($e);

            return back()->withErrors([
                'correo' => 'No pudimos enviar el correo en este momento. Intenta más tarde.',
            ]);
        }

        return back()->with('success', $mensajeGenerico);
    }

    public function showResetForm(Request $request, string $token)
    {
        return view('auth.reset-password', [
            'token' => $token,
            'correo' => $request->query('email', ''),
        ]);
    }

    public function reset(Request $request)
    {
        $request->validate([
            'token' => 'required|string',
            'correo' => 'required|email',
            'contrasena' => ['required', 'confirmed', Password::min(6)],
        ], [
            'contrasena.confirmed' => 'Las contraseñas no coinciden.',
        ]);

        $registro = DB::table('password_reset_tokens')
            ->where('email', $request->correo)
            ->first();

        $tokenValido = $registro
            && Hash::check($request->token, $registro->token)
            && now()->diffInMinutes($registro->created_at) <= self::MINUTOS_EXPIRACION;

        if (! $tokenValido) {
            return back()->withErrors([
                'token' => 'El enlace de recuperación no es válido o ya expiró. Solicita uno nuevo.',
            ]);
        }

        $usuario = Usuario::where('correo', $request->correo)->first();

        if (! $usuario) {
            return back()->withErrors(['correo' => 'No encontramos una cuenta con ese correo.']);
        }

        $usuario->update(['contrasena' => Hash::make($request->contrasena)]);

        // El enlace se invalida en cuanto se usa con éxito (RN-006).
        DB::table('password_reset_tokens')->where('email', $request->correo)->delete();

        return redirect()->route('login')
            ->with('success', 'Tu contraseña fue actualizada. Ya puedes iniciar sesión.');
    }
}
