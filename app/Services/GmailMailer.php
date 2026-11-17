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
 * Servicio de env├¡o de correos mediante la API de Gmail (Google).
 *
 * - Usa el SDK oficial de Google (google/apiclient) instalado con Composer.
 * - Autenticaci├│n OAuth 2.0 con refresh token: las credenciales SIEMPRE se
 *   leen de config('services.gmail.*'), que a su vez las toma de las
 *   variables GOOGLE_CLIENT_ID / GOOGLE_CLIENT_SECRET / GOOGLE_REFRESH_TOKEN
 *   / GOOGLE_GMAIL_FROM del .env. Nunca se escriben credenciales en c├│digo.
 * - Solo se solicita el scope m├¡nimo: https://www.googleapis.com/auth/gmail.send
 * - El remitente es ├║nicamente la cuenta de Google autorizada; los usuarios
 *   finales NO inician sesi├│n con Google ni autorizan nada.
 */
class GmailMailer
{
    /** Scope m├¡nimo: ├║nicamente permiso para ENVIAR correo con Gmail. */
    public const GMAIL_SEND_SCOPE = 'https://www.googleapis.com/auth/gmail.send';

    /**
     * Env├¡a el correo de recuperaci├│n de contrase├▒a de un usuario.
     *
     * Conserva el mismo contenido y dise├▒o actual del proyecto: renderiza la
     * vista emails.reset-password, igual que lo hac├¡a el servicio anterior.
     *
     * @param  Usuario  $usuario  Usuario que solicit├│ la recuperaci├│n
     * @param  string   $url      Enlace completo para restablecer la contrase├▒a
     *
     * @throws Throwable Cuando Gmail API no est├í configurado o la API falla.
     */
    public function sendPasswordReset(Usuario $usuario, string $url): void
    {
        $html = view('emails.reset-password', [
            'nombre' => $usuario->nombre,
            'resetUrl' => $url,
            'minutosExpiracion' => 15,
        ])->render();

        $this->send(
            $usuario->correo,
            'Recupera tu contrase├▒a - El Pirry',
            $html
        );
    }

    /**
     * Env├¡a un correo HTML con Gmail API.
     *
     * @throws RuntimeException Si las credenciales no est├ín configuradas o la
     *                          API falla (mensaje siempre saneado, sin secretos).
     * @throws Throwable        Si falla la comunicaci├│n con Google.
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
                'Las credenciales de Gmail API no est├ín configuradas en el archivo .env '
                .'(GOOGLE_CLIENT_ID, GOOGLE_CLIENT_SECRET y GOOGLE_REFRESH_TOKEN). '
                .'Ejecuta "php artisan gmail:authorize" para obtener el refresh token.'
            );
        }

        if ($fromEmail === '') {
            throw new RuntimeException(
                'GOOGLE_GMAIL_FROM no est├í configurada en el archivo .env. '
                .'Debe ser el correo de la cuenta de Google autorizada como remitente.'
            );
        }

        // Mensaje MIME RFC 2822 completo (cabeceras, HTML, codificaci├│n),
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
            // token y sin access token. Los logs y el usuario solo ver├ín esto.
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

        $token = $client->fetchAccessTokenWithRefreshToken($refreshToken);

        if (isset($token['error'])) {
            throw new RuntimeException(
                'Google could not renew OAuth access. Code: '.(string) $token['error']
            );
        }

        $accessToken = $client->getAccessToken();

        if (!is_array($accessToken) || empty($accessToken['access_token'])) {
            throw new RuntimeException(
                'Google did not return a valid access token. Check OAuth credentials.'
            );
        }

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
     * Devuelve el mensaje de error de una excepci├│n recortado y con cualquier
     * credencial redactada, para que jam├ís llegue a los logs ni al usuario.
     */
    private function sanitizedError(Throwable $e): string
    {
        $message = mb_substr(trim($e->getMessage()), 0, 300);

        // Redacci├│n defensiva: si el mensaje de Google contuviera por alguna
        // raz├│n una credencial, se elimina antes de registrarla o mostrarla.
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
