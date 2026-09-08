<?php

namespace App\Console\Commands;

use Google\Client as GoogleClient;
use Illuminate\Console\Command;
use Throwable;

/**
 * Autorización OAuth única con Google para poder enviar correos con Gmail API.
 *
 * Flujo seguro para desarrollo local:
 *  1. Registra en Google Cloud Console la URI de redirección EXACTA:
 *     http://127.0.0.1:8765
 *  2. Coloca GOOGLE_CLIENT_ID y GOOGLE_CLIENT_SECRET en el .env.
 *  3. Ejecuta: php artisan gmail:authorize
 *  4. Abre la URL que imprime el comando, autoriza con la cuenta REMITENTE.
 *  5. Google redirige a un servidor local TEMPORAL que este comando levanta
 *     en http://127.0.0.1:8765. El refresh token NUNCA pasa por una página
 *     web, ni se registra en logs, ni se guarda en base de datos ni en
 *     archivos versionados: se muestra UNA SOLA VEZ en esta terminal.
 *
 * Los usuarios finales de la tienda jamás usan este comando.
 */
class GmailAuthorize extends Command
{
    protected $signature = 'gmail:authorize';

    protected $description = 'Autorización OAuth única con Google para Gmail API: obtiene el refresh token para el .env (solo desarrollo local).';

    /** URI de redirección de loopback. Debe coincidir EXACTAMENTE con la registrada en Google Cloud Console. */
    private const REDIRECT_URI = 'http://127.0.0.1:8765';

    /** Scope mínimo: únicamente permiso para enviar correo. */
    private const GMAIL_SEND_SCOPE = 'https://www.googleapis.com/auth/gmail.send';

    /** Puerto local temporal donde este comando espera la respuesta de Google. */
    private const LOCAL_PORT = 8765;

    /** Tiempo máximo (segundos) para completar la autorización en el navegador. */
    private const TIMEOUT_SEGUNDOS = 600;

    public function handle(): int
    {
        $clientId = (string) config('services.gmail.client_id');
        $clientSecret = (string) config('services.gmail.client_secret');

        if ($clientId === '' || $clientSecret === '') {
            $this->error('Faltan GOOGLE_CLIENT_ID y/o GOOGLE_CLIENT_SECRET en el archivo .env.');
            $this->line('');
            $this->line('Cómo conseguirlos:');
            $this->line('  1. Entra en https://console.cloud.google.com');
            $this->line('  2. Habilita la "Gmail API" (APIs y servicios -> Biblioteca).');
            $this->line('  3. APIs y servicios -> Credenciales -> Crear credenciales -> ID de cliente OAuth.');
            $this->line('  4. Tipo de aplicación: "Aplicación de escritorio".');
            $this->line('  5. En el cliente creado, agrega en "URIs de redirección autorizados" EXACTAMENTE:');
            $this->line('     '.self::REDIRECT_URI);
            $this->line('  6. Copia el ID de cliente y el secreto del cliente al .env y ejecuta este comando de nuevo.');

            return self::FAILURE;
        }

        $state = bin2hex(random_bytes(16));

        $client = new GoogleClient();
        $client->setClientId($clientId);
        $client->setClientSecret($clientSecret);
        $client->setRedirectUri(self::REDIRECT_URI);
        $client->addScope(self::GMAIL_SEND_SCOPE);
        $client->setAccessType('offline');
        // Fuerza a Google a entregar un refresh token incluso si ya se había
        // autorizado antes desde esta aplicación.
        $client->setPrompt('consent');
        $client->setState($state);

        $authUrl = $client->createAuthUrl();

        $this->info('Autorización OAuth de Google para Gmail API (El Pirry)');
        $this->line('');
        $this->line('1) Abre esta URL en tu navegador e inicia sesión con la cuenta de');
        $this->line('   Google que será el REMITENTE de los correos (no es la cuenta de los usuarios):');
        $this->line('');
        $this->line($authUrl);
        $this->line('');
        $this->line('2) Autoriza el permiso "Enviar correo" (gmail.send).');
        $this->line('3) Google redirigirá a '.self::REDIRECT_URI.' y este comando');
        $this->line('   terminará solo. Esperando la respuesta de Google (máx. '.(self::TIMEOUT_SEGUNDOS / 60).' minutos)...');
        $this->line('');

        $code = $this->waitForAuthorizationCode($client, $state);

        if ($code === null) {
            return self::FAILURE; // El mensaje de error ya se mostró.
        }

        // Intercambia el código de autorización por los tokens.
        try {
            $tokens = $client->fetchAccessTokenWithAuthCode($code);
        } catch (Throwable $e) {
            $this->error('No se pudo completar el intercambio del código con Google: '.$e->getMessage());

            return self::FAILURE;
        }

        if (! is_array($tokens) || isset($tokens['error'])) {
            $this->error('Google rechazó la autorización (error: '.($tokens['error'] ?? 'desconocido').').');
            $this->line('Revisa que la URI de redirección registrada en Google Cloud sea exactamente '.self::REDIRECT_URI.'.');

            return self::FAILURE;
        }

        $refreshToken = $tokens['refresh_token'] ?? null;

        if (empty($refreshToken)) {
            $this->warn('Google no devolvió un refresh token.');
            $this->line('Eso ocurre si la cuenta ya había autorizado la app antes. Solución:');
            $this->line('  1. Entra en https://myaccount.google.com/permissions');
            $this->line('  2. Revoca el acceso de esta aplicación.');
            $this->line('  3. Vuelve a ejecutar: php artisan gmail:authorize');

            return self::FAILURE;
        }

        // ÚNICA vez que se muestra el refresh token: en esta terminal, para que
        // el usuario lo copie manualmente al .env. No se registra en logs, no
        // se guarda en BD, no se escribe en archivos y no se envía por HTTP.
        $this->info('✅ Autorización completada con éxito.');
        $this->line('');
        $this->comment('Copia esta línea en tu archivo .env (se muestra UNA SOLA VEZ y no se volverá a mostrar):');
        $this->line('');
        $this->line('GOOGLE_REFRESH_TOKEN='.$refreshToken);
        $this->line('');
        $this->line('Completa también en el .env el remitente con el correo de la cuenta');
        $this->line('que acabas de autorizar (debe ser EXACTAMENTE ese correo):');
        $this->line('');
        $this->line('GOOGLE_GMAIL_FROM=<correo-de-la-cuenta-autorizada>');
        $this->line('');
        $this->line('Después ejecuta: php artisan config:clear');
        $this->line('');
        $this->warn('Importante:');
        $this->warn('- Si la pantalla de consentimiento de Google está en estado "Testing", el');
        $this->warn('  refresh token EXPIRA EN 7 DÍAS. Publícala como "In production" o repite');
        $this->warn('  este comando cuando expire.');
        $this->warn('- No compartas este token ni lo subas a Git (el .env ya está en .gitignore).');

        return self::SUCCESS;
    }

    /**
     * Levanta un servidor HTTP local TEMPORAL en 127.0.0.1:8765 y espera la
     * redirección de Google con el código de autorización.
     *
     * Seguridad:
     * - El navegador solo recibe una página de confirmación sin ningún dato.
     * - Se valida el parámetro state (protección CSRF) antes de usar el código.
     * - No guarda nada: devuelve el código en memoria y cierra el servidor.
     *
     * @return string|null Código de autorización o null si falló/timeout.
     */
    private function waitForAuthorizationCode(GoogleClient $client, string $state): ?string
    {
        $server = @stream_socket_server(
            'tcp://127.0.0.1:'.self::LOCAL_PORT,
            $errno,
            $errstr
        );

        if ($server === false) {
            $this->error('No se pudo abrir el puerto local '.self::LOCAL_PORT." ($errstr).");
            $this->line('¿Hay otro proceso usándolo? Ciérralo y vuelve a ejecutar el comando.');

            return null;
        }

        $deadline = time() + self::TIMEOUT_SEGUNDOS;
        $code = null;

        while (time() < $deadline) {
            // Espera una conexión entrante en bloques de 2 s para poder
            // comprobar el tiempo límite entre intentos.
            $conn = @stream_socket_accept($server, 2);

            if ($conn === false) {
                continue;
            }

            stream_set_timeout($conn, 5);

            // Lee la petición HTTP del navegador (la primera línea basta).
            $request = (string) fread($conn, 8192);
            $params = [];

            if (preg_match('/^GET\s+(\S+)/m', $request, $m)) {
                parse_str((string) parse_url($m[1], PHP_URL_QUERY), $params);
            }

            if (isset($params['error'])) {
                $this->respond($conn, 200, 'Autorización cancelada', 'Google reportó un error de autorización. Vuelve a ejecutar el comando e intenta de nuevo.');
                fclose($conn);
                $this->error('La autorización fue cancelada en el navegador ('.$params['error'].').');

                break;
            }

            // Se acepta el código solo si el state coincide (anti-CSRF).
            if (isset($params['code']) && hash_equals($state, (string) ($params['state'] ?? ''))) {
                $this->respond($conn, 200, '✅ Autorización completada', 'Todo listo. Vuelve a la terminal para ver las instrucciones finales. Puedes cerrar esta pestaña.');
                fclose($conn);
                $code = (string) $params['code'];

                break;
            }

            // Petición sin código (por ejemplo favicon.ico del navegador).
            $this->respond($conn, 404, 'No autorizado', 'Esta ventana no contiene nada útil. Vuelve a la pestaña de autorización de Google.');
            fclose($conn);
        }

        fclose($server);

        if ($code === null && time() >= $deadline) {
            $this->error('Tiempo agotado esperando la autorización ('.(self::TIMEOUT_SEGUNDOS / 60).' minutos). Vuelve a ejecutar el comando.');
        }

        return $code;
    }

    /** Responde al navegador con una página mínima que NO contiene secretos. */
    private function respond($conn, int $status, string $titulo, string $mensaje): void
    {
        $body = '<!doctype html><html lang="es"><head><meta charset="utf-8">'
            .'<title>'.htmlspecialchars($titulo, ENT_QUOTES).'</title></head>'
            .'<body style="font-family:sans-serif;text-align:center;padding-top:70px;background:#f4f4f7">'
            .'<h2>'.htmlspecialchars($titulo, ENT_QUOTES).'</h2>'
            .'<p>'.htmlspecialchars($mensaje, ENT_QUOTES).'</p>'
            .'</body></html>';

        @fwrite($conn, "HTTP/1.1 $status OK\r\n");
        @fwrite($conn, "Content-Type: text/html; charset=utf-8\r\n");
        @fwrite($conn, 'Content-Length: '.strlen($body)."\r\n");
        @fwrite($conn, "Cache-Control: no-store\r\n");
        @fwrite($conn, "Connection: close\r\n\r\n");
        @fwrite($conn, $body);
    }
}
