<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Servicios de terceros
    |--------------------------------------------------------------------------
    |
    | Este archivo almacena las credenciales de servicios externos como
    | Mailgun, Postmark, AWS y otros. Las claves NUNCA se escriben
    | aquí: se leen desde las variables de entorno definidas en el .env.
    |
    */

    'gmail' => [
        // Credenciales OAuth 2.0 de Google para enviar el correo con la API
        // de Gmail. Consíguelas en https://console.cloud.google.com
        // (Credenciales -> ID de cliente OAuth, tipo "Aplicación de escritorio")
        // y registra la URI de redirección http://127.0.0.1:8765.
        // El refresh token se obtiene con: php artisan gmail:authorize
        'client_id' => env('GOOGLE_CLIENT_ID'),
        'client_secret' => env('GOOGLE_CLIENT_SECRET'),
        'refresh_token' => env('GOOGLE_REFRESH_TOKEN'),

        // Remitente: DEBE ser exactamente el correo de la cuenta de Google
        // autorizada en el paso de autorización (cuenta emisora del sistema).
        'from_email' => env('GOOGLE_GMAIL_FROM'),
    ],

];
