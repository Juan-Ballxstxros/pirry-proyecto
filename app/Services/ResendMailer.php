<?php

namespace App\Services;

use App\Models\Usuario;
use Resend;
use RuntimeException;
use Throwable;

/**
 * Servicio de envío de correos mediante la API externa de Resend.
 *
 * - Usa el SDK oficial (resend/resend-php) instalado con Composer.
 * - La API Key SIEMPRE se toma de config('services.resend.key'),
 *   que a su vez la lee de la variable RESEND_API_KEY del .env.
 * - Nunca se escriben credenciales en el código.
 */
class ResendMailer
{
    /**
     * Envía el correo de recuperación de contraseña de un usuario.
     *
     * @param  Usuario  $usuario  Usuario que solicitó la recuperación
     * @param  string   $url      Enlace completo para restablecer la contraseña
     *
     * @throws Throwable Cuando Resend no está configurado o la API falla.
     */
    public function sendPasswordReset(Usuario $usuario, string $url): void
    {
        $html = view('emails.reset-password', [
            'nombre' => $usuario->nombre,
            'url' => $url,
            'minutos' => 60,
        ])->render();

        $this->send(
            $usuario->correo,
            'Recupera tu contraseña - El Pirry',
            $html
        );
    }

    /**
     * Envía un correo HTML con Resend.
     *
     * @throws RuntimeException Si RESEND_API_KEY no está configurada.
     * @throws Throwable        Si la API de Resend responde con error.
     */
    public function send(string $to, string $subject, string $html): void
    {
        $apiKey = (string) config('services.resend.key');

        if ($apiKey === '') {
            throw new RuntimeException(
                'RESEND_API_KEY no está configurada en el archivo .env. '
                .'No se puede enviar el correo con Resend.'
            );
        }

        $fromName = (string) config('services.resend.from_name');
        $fromEmail = (string) config('services.resend.from_email');

        Resend::client($apiKey)->emails->send([
            'from' => $fromName.' <'.$fromEmail.'>',
            'to' => [$to],
            'subject' => $subject,
            'html' => $html,
        ]);
    }
}
