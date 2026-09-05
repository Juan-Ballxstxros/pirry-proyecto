<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Servicios de terceros
    |--------------------------------------------------------------------------
    |
    | Este archivo almacena las credenciales de servicios externos como
    | Resend, Mailgun, Postmark, AWS y otros. Las claves NUNCA se escriben
    | aquí: se leen desde las variables de entorno definidas en el .env.
    |
    */

    'resend' => [
        // API Key de Resend (https://resend.com/api-keys) -> variable RESEND_API_KEY en .env
        'key' => env('RESEND_API_KEY'),

        // Remitente del correo. Con una cuenta nueva de Resend sin dominio
        // verificado solo se puede enviar desde onboarding@resend.dev.
        'from_email' => env('RESEND_FROM_EMAIL', 'onboarding@resend.dev'),

        // Nombre visible del remitente.
        'from_name' => env('RESEND_FROM_NAME', 'El Pirry'),
    ],

];
