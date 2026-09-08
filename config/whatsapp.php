<?php

/*
|--------------------------------------------------------------------------
| WhatsApp del negocio
|--------------------------------------------------------------------------
| Número de WhatsApp del negocio en formato internacional de Colombia,
| SIN espacios, guiones ni símbolos. Formato de ejemplo (NO es un número
| real): 57XXXXXXXXXX
|
| Se configura desde el archivo .env con la variable:
|     WHATSAPP_NUMERO_NEGOCIO=573001234567   (ejemplo de formato)
|
| Mientras esté vacío, el botón "Enviar pedido por WhatsApp" solo muestra
| un aviso de que aún no está configurado (no produce errores).
*/

return [
    'numero_negocio' => env('WHATSAPP_NUMERO_NEGOCIO', ''),
];
