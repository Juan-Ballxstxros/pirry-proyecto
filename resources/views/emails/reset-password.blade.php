<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recupera tu contraseña - El Pirry</title>
</head>
<body style="margin:0; padding:0; background-color:#f4f4f7; font-family:'Segoe UI', Arial, sans-serif;">
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background-color:#f4f4f7; padding:32px 16px;">
        <tr>
            <td align="center">
                <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="max-width:480px; background-color:#ffffff; border-radius:16px; overflow:hidden; box-shadow:0 6px 18px rgba(0,0,0,0.08);">

                    <!-- Encabezado con el nombre del proyecto -->
                    <tr>
                        <td style="background:linear-gradient(135deg,#ff8c42,#f5a370); padding:28px 32px; text-align:center;">
                            <h1 style="margin:0; color:#ffffff; font-size:24px;">🍔 El Pirry</h1>
                            <p style="margin:6px 0 0; color:rgba(255,255,255,0.9); font-size:13px;">Comidas Rápidas</p>
                        </td>
                    </tr>

                    <!-- Contenido -->
                    <tr>
                        <td style="padding:32px;">
                            <h2 style="margin:0 0 16px; color:#333333; font-size:18px;">Recuperación de contraseña</h2>

                            <p style="margin:0 0 14px; color:#555555; font-size:14px; line-height:1.6;">
                                Hola <strong>{{ $nombre }}</strong>,
                            </p>
                            <p style="margin:0 0 22px; color:#555555; font-size:14px; line-height:1.6;">
                                Recibimos una solicitud para restablecer la contraseña de tu cuenta en
                                <strong>El Pirry</strong>. Haz clic en el siguiente botón para crear una nueva:
                            </p>

                            <!-- Botón de acción -->
                            <p style="margin:0 0 24px; text-align:center;">
                                <a href="{{ $url }}"
                                   style="display:inline-block; background-color:#ff8c42; color:#ffffff; text-decoration:none; font-size:15px; font-weight:600; padding:12px 32px; border-radius:8px;">
                                    Restablecer contraseña
                                </a>
                            </p>

                            <!-- Enlace alternativo -->
                            <p style="margin:0 0 22px; color:#777777; font-size:12px; line-height:1.6;">
                                Si el botón no funciona, copia y pega este enlace en tu navegador:<br>
                                <a href="{{ $url }}" style="color:#ff8c42; word-break:break-all;">{{ $url }}</a>
                            </p>

                            <!-- Aviso de temporalidad -->
                            <p style="margin:0 0 14px; color:#856404; background-color:#fff8e1; border-radius:6px; padding:10px 12px; font-size:13px; line-height:1.5;">
                                ⏳ Este enlace es <strong>temporal</strong>: expira en {{ $minutos }} minutos y solo puede usarse una vez.
                            </p>

                            <!-- Aviso de seguridad -->
                            <p style="margin:0; color:#777777; font-size:13px; line-height:1.6;">
                                Si <strong>no solicitaste</strong> la recuperación de tu contraseña, puedes ignorar este
                                correo de forma segura: tu contraseña actual no cambiará.
                            </p>
                        </td>
                    </tr>

                    <!-- Pie -->
                    <tr>
                        <td style="background-color:#fafafa; border-top:1px solid #eeeeee; padding:16px 32px; text-align:center;">
                            <p style="margin:0; color:#999999; font-size:11px; line-height:1.6;">
                                Este es un mensaje automático de El Pirry. Por favor no respondas a este correo.
                            </p>
                        </td>
                    </tr>

                </table>
            </td>
        </tr>
    </table>
</body>
</html>
