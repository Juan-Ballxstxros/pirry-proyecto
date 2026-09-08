<?php

namespace App\Services;

use App\Models\Usuario;
use Google\Client as GoogleClient;
use Google\Service\Gmail as GmailService;
use Google\Service\Gmail\Message as GmailMessage;
use RuntimeException;
use Symfony\Component\Mime\Email as MimeEmail;
use Throwable;

/**
 * Servicio de envío de correos mediante la API de Gmail (Google).
 *
 * - Usa el SDK oficial de Google (google/apiclient) instalado con Composer.
 * - Autenticación OAuth 2.0 con refresh token: las credenciales SIEMPRE se
 *   leen de config('services.gmail.*'), que a su vez las toma de las
 *   variables GOOGLE_CLIENT_ID / GOOGLE_CLIENT_SECRET / GOOGLE_REFRESH_TOKEN
 *   / GOOGLE_GMAIL_FROM del .env. Nunca se escriben credenciales en código.
 * - Solo se solicita el scope mínimo: https://www.googleapis.com/auth/gmail.send
 * - El remitente es únicamente la cuenta de Google autorizada; los usuarios
 *   finales NO inician sesión con Google ni autorizan nada.
 */
class GmailMailer
{
    /** Scope mínimo: únicamente permiso para ENVIAR correo con Gmail. */
    public const GMAIL_SEND_SCOPE = 'https://www.googleapis.com/auth/gmail.send';

    /**
     * Envía el correo de recuperación de contraseña de un usuario.
     *
     * Conserva el mismo contenido y diseño actual del proyecto: renderiza la
     * vista emails.reset-password, igual que lo hacía el servicio anterior.
     *
     * @param  Usuario  $usuario  Usuario que solicitó la recuperación
     * @param  string   $url      Enlace completo para restablecer la contraseña
     *
     * @throws Throwable Cuando Gmail API no está configurado o la API falla.
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
     * Envía un correo HTML con Gmail API.
     *
     * @throws RuntimeException Si las credenciales no están configuradas o la
     *                          API falla (mensaje siempre saneado, sin secretos).
     * @throws Throwable        Si falla la comunicación con Google.
     */
    public function send(string $to, string $subject, string $html): void
    {
        $clientId = (string) config('services.gmail.client_id');
        $clientSecret = (string) config('services.gmail.client_secret');
        $refreshToken = (string) config('services.gmail.refresh_token');
        $fromEmail = (string) config('services.gmail.from_email');
        $fromName = (string) config('app.name', 'El Pirry');

        if ($clientId === '' || $clientSecret === '' || $refreshToken === '') {
            throw new RuntimeException(
                'Las credenciales de Gmail API no están configuradas en el archivo .env '
                .'(GOOGLE_CLIENT_ID, GOOGLE_CLIENT_SECRET y GOOGLE_REFRESH_TOKEN). '
                .'Ejecuta "php artisan gmail:authorize" para obtener el refresh token.'
            );
        }

        if ($fromEmail === '') {
            throw new RuntimeException(
                'GOOGLE_GMAIL_FROM no está configurada en el archivo .env. '
                .'Debe ser el correo de la cuenta de Google autorizada como remitente.'
            );
        }

        // Mensaje MIME RFC 2822 completo (cabeceras, HTML, codificación),
        // codificado en base64url como exige users.messages.send.
        $message = new GmailMessage();
        $message->setRaw($this->base64Url(
            $this->buildMime($fromEmail, $fromName, $to, $subject, $html)
        ));

        try {
            $gmail = new GmailService(
                $this->client($clientId, $clientSecret, $refreshToken)
            );

            // "me" = la cuenta autenticada (la cuenta remitente autorizada).
            $gmail->users_messages->send('me', $message);
        } catch (Throwable $e) {
            // Se relanza con un mensaje SANEADO: sin Client Secret, sin refresh
            // token y sin access token. Los logs y el usuario solo verán esto.
            throw new RuntimeException(
                'No se pudo enviar el correo con Gmail API: '.$this->sanitizedError($e),
                0,
                $e
            );
        }
    }

    /**
     * Cliente de Google autenticado por OAuth 2.0 (refresh token -> access
     * token temporal). No guarda nada en disco ni en base de datos.
     */
    private function client(string $clientId, string $clientSecret, string $refreshToken): GoogleClient
    {
        $client = new GoogleClient();
        $client->setClientId($clientId);
        $client->setClientSecret($clientSecret);
        $client->addScope(self::GMAIL_SEND_SCOPE);

        // Intercambia el refresh token por un access token temporal.
        $client->fetchAccessTokenWithRefreshToken($refreshToken);

        return $client;
    }

    /**
     * Construye el mensaje MIME completo con Symfony MIME (ya incluido en
     * Laravel): cabeceras Date/Message-ID/MIME-Version, asunto codificado,
     * parte HTML y transferencia adecuada para Gmail API.
     */
    private function buildMime(
        string $fromEmail,
        string $fromName,
        string $to,
        string $subject,
        string $html
    ): string {
        $email = (new MimeEmail())
            ->from($fromName !== '' ? $fromName.' <'.$fromEmail.'>' : $fromEmail)
            ->to($to)
            ->subject($subject)
            ->html($html);

        return $email->toString();
    }

    /** Codifica en base64url (formato requerido por users.messages.send). */
    private function base64Url(string $raw): string
    {
        return rtrim(strtr(base64_encode($raw), '+/', '-_'), '=');
    }

    /**
     * Devuelve el mensaje de error de una excepción recortado y con cualquier
     * credencial redactada, para que jamás llegue a los logs ni al usuario.
     */
    private function sanitizedError(Throwable $e): string
    {
        $message = mb_substr(trim($e->getMessage()), 0, 300);

        // Redacción defensiva: si el mensaje de Google contuviera por alguna
        // razón una credencial, se elimina antes de registrarla o mostrarla.
        foreach ([
            (string) config('services.gmail.client_secret'),
            (string) config('services.gmail.refresh_token'),
        ] as $secret) {
            if ($secret !== '') {
                $message = str_replace($secret, '[redactado]', $message);
            }
        }

        return $message !== '' ? $message : 'error desconocido';
    }
}
