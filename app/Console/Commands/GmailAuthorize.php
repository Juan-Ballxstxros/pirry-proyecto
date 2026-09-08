<?php

namespace App\Console\Commands;

use Google\Client as GoogleClient;
use Illuminate\Console\Command;
use Throwable;

/**
 * Autorizaci├│n OAuth ├║nica con Google para poder enviar correos con Gmail API.
 *
 * Flujo seguro para desarrollo local:
 *  1. Registra en Google Cloud Console la URI de redirecci├│n EXACTA:
 *     http://127.0.0.1:8765
 *  2. Coloca GOOGLE_CLIENT_ID y GOOGLE_CLIENT_SECRET en el .env.
 *  3. Ejecuta: php artisan gmail:authorize
 *  4. Abre la URL que imprime el comando, autoriza con la cuenta REMITENTE.
 *  5. Google redirige a un servidor local TEMPORAL que este comando levanta
 *     en http://127.0.0.1:8765. El refresh token NUNCA pasa por una p├ígina
 *     web, ni se registra en logs, ni se guarda en base de datos ni en
 *     archivos versionados: se muestra UNA SOLA VEZ en esta terminal.
 *
 * Los usuarios finales de la tienda jam├ís usan este comando.
 */
class GmailAuthorize extends Command
{
    protected $signature = 'gmail:authorize';

    protected $description = 'Autorizaci├│n OAuth ├║nica con Google para Gmail API: obtiene el refresh token para el .env (solo desarrollo local).';

    /** URI de redirecci├│n de loopback. Debe coincidir EXACTAMENTE con la registrada en Google Cloud Console. */
    private const REDIRECT_URI = 'http://127.0.0.1:8765';

    /** Scope m├¡nimo: ├║nicamente permiso para enviar correo. */
    private const GMAIL_SEND_SCOPE = 'https://www.googleapis.com/auth/gmail.send';

    /** Puerto local temporal donde este comando espera la respuesta de Google. */
    private const LOCAL_PORT = 8765;

    /** Tiempo m├íximo (segundos) para completar la autorizaci├│n en el navegador. */
    private const TIMEOUT_SEGUNDOS = 600;

    public function handle(): int
    {
        $clientId = (string) config('services.gmail.client_id');
        $clientSecret = (string) config('services.gmail.client_secret');

        if ($clientId === '' || $clientSecret === '') {
            $this->error('Faltan GOOGLE_CLIENT_ID y/o GOOGLE_CLIENT_SECRET en el archivo .env.');
            $this->line('');
            $this->line('C├│mo conseguirlos:');
            $this->line('  1. Entra en https://console.cloud.google.com');
            $this->line('  2. Habilita la "Gmail API" (APIs y servicios -> Biblioteca).');
            $this->line('  3. APIs y servicios -> Credenciales -> Crear credenciales -> ID de cliente OAuth.');
            $this->line('  4. Tipo de aplicaci├│n: "Aplicaci├│n de escritorio".');
            $this->line('  5. En el cliente creado, agrega en "URIs de redirecci├│n autorizados" EXACTAMENTE:');
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
        // Fuerza a Google a entregar un refresh token incluso si ya se hab├¡a
        // autorizado antes desde esta aplicaci├│n.
        $client->setPrompt('consent');
        $client->setState($state);

        $authUrl = $client->createAuthUrl();

        $this->info('Autorizaci├│n OAuth de Google para Gmail API (El Pirry)');
        $this->line('');
        $this->line('1) Abre esta URL en tu navegador e inicia sesi├│n con la cuenta de');
        $this->line('   Google que ser├í el REMITENTE de los correos (no es la cuenta de los usuarios):');
        $this->line('');
        $this->line($authUrl);
        $this->line('');
        $this->line('2) Autoriza el permiso "Enviar correo" (gmail.send).');
        $this->line('3) Google redirigir├í a '.self::REDIRECT_URI.' y este comando');
        $this->line('   terminar├í solo. Esperando la respuesta de Google (m├íx. '.(self::TIMEOUT_SEGUNDOS / 60).' minutos)...');
        $this->line('');

        $code = $this->waitForAuthorizationCode($client, $state);

        if ($code === null) {
            return self::FAILURE; // El mensaje de error ya se mostr├│.
        }

        // Intercambia el c├│digo de autorizaci├│n por los tokens.
        try {
            $tokens = $client->fetchAccessTokenWithAuthCode($code);
        } catch (Throwable $e) {
            $this->error('No se pudo completar el intercambio del c├│digo con Google: '.$e->getMessage());

            return self::FAILURE;
        }

        if (! is_array($tokens) || isset($tokens['error'])) {
            $this->error('Google rechaz├│ la autorizaci├│n (error: '.($tokens['error'] ?? 'desconocido').').');
            $this->line('Revisa que la URI de redirecci├│n registrada en Google Cloud sea exactamente '.self::REDIRECT_URI.'.');

            return self::FAILURE;
        }

        $refreshToken = $tokens['refresh_token'] ?? null;

        if (empty($refreshToken)) {
            $this->warn('Google no devolvi├│ un refresh token.');
            $this->line('Eso ocurre si la cuenta ya hab├¡a autorizado la app antes. Soluci├│n:');
            $this->line('  1. Entra en https://myaccount.google.com/permissions');
            $this->line('  2. Revoca el acceso de esta aplicaci├│n.');
            $this->line('  3. Vuelve a ejecutar: php artisan gmail:authorize');

            return self::FAILURE;
        }

        // ├ÜNICA vez que se muestra el refresh token: en esta terminal, para que
        // el usuario lo copie manualmente al .env. No se registra en logs, no
        // se guarda en BD, no se escribe en archivos y no se env├¡a por HTTP.
        $this->info('Ô£à Autorizaci├│n completada con ├®xito.');
        $this->line('');
        $this->comment('Copia esta l├¡nea en tu archivo .env (se muestra UNA SOLA VEZ y no se volver├í a mostrar):');
        $this->line('');
        $this->line('GOOGLE_REFRESH_TOKEN='.$refreshToken);
        $this->line('');
        $this->line('Completa tambi├®n en el .env el remitente con el correo de la cuenta');
        $this->line('que acabas de autorizar (debe ser EXACTAMENTE ese correo):');
        $this->line('');
        $this->line('GOOGLE_GMAIL_FROM=<correo-de-la-cuenta-autorizada>');
        $this->line('');
        $this->line('Despu├®s ejecuta: php artisan config:clear');
        $this->line('');
        $this->warn('Importante:');
        $this->warn('- Si la pantalla de consentimiento de Google est├í en estado "Testing", el');
        $this->warn('  refresh token EXPIRA EN 7 D├ìAS. Publ├¡cala como "In production" o repite');
        $this->warn('  este comando cuando expire.');
        $this->warn('- No compartas este token ni lo subas a Git (el .env ya est├í en .gitignore).');

        return self::SUCCESS;
    }

    /**
     * Levanta un servidor HTTP local TEMPORAL en 127.0.0.1:8765 y espera la
     * redirecci├│n de Google con el c├│digo de autorizaci├│n.
     *
     * Seguridad:
     * - El navegador solo recibe una p├ígina de confirmaci├│n sin ning├║n dato.
     * - Se valida el par├ímetro state (protecci├│n CSRF) antes de usar el c├│digo.
     * - No guarda nada: devuelve el c├│digo en memoria y cierra el servidor.
     *
     * @return string|null C├│digo de autorizaci├│n o null si fall├│/timeout.
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
            $this->line('┬┐Hay otro proceso us├índolo? Ci├®rralo y vuelve a ejecutar el comando.');

            return null;
        }

        $deadline = time() + self::TIMEOUT_SEGUNDOS;
        $code = null;

        while (time() < $deadline) {
            // Espera una conexi├│n entrante en bloques de 2 s para poder
            // comprobar el tiempo l├¡mite entre intentos.
            $conn = @stream_socket_accept($server, 2);

            if ($conn === false) {
                continue;
            }

            stream_set_timeout($conn, 5);

            // Lee la petici├│n HTTP del navegador (la primera l├¡nea basta).
            $request = (string) fread($conn, 8192);
            $params = [];

            if (preg_match('/^GET\s+(\S+)/m', $request, $m)) {
                parse_str((string) parse_url($m[1], PHP_URL_QUERY), $params);
            }

            if (isset($params['error'])) {
                $this->respond($conn, 200, 'Autorizaci├│n cancelada', 'Google report├│ un error de autorizaci├│n. Vuelve a ejecutar el comando e intenta de nuevo.');
                fclose($conn);
                $this->error('La autorizaci├│n fue cancelada en el navegador ('.$params['error'].').');

                break;
            }

            // Se acepta el c├│digo solo si el state coincide (anti-CSRF).
            if (isset($params['code']) && hash_equals($state, (string) ($params['state'] ?? ''))) {
                $this->respond($conn, 200, 'Ô£à Autorizaci├│n completada', 'Todo listo. Vuelve a la terminal para ver las instrucciones finales. Puedes cerrar esta pesta├▒a.');
                fclose($conn);
                $code = (string) $params['code'];

                break;
            }

            // Petici├│n sin c├│digo (por ejemplo favicon.ico del navegador).
            $this->respond($conn, 404, 'No autorizado', 'Esta ventana no contiene nada ├║til. Vuelve a la pesta├▒a de autorizaci├│n de Google.');
            fclose($conn);
        }

        fclose($server);

        if ($code === null && time() >= $deadline) {
            $this->error('Tiempo agotado esperando la autorizaci├│n ('.(self::TIMEOUT_SEGUNDOS / 60).' minutos). Vuelve a ejecutar el comando.');
        }

        return $code;
    }

    /** Responde al navegador con una p├ígina m├¡nima que NO contiene secretos. */
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
