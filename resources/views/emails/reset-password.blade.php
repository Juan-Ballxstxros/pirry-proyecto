<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Recupera tu contraseña</title>
</head>
<body style="margin:0; padding:0; background:#f3f5fa; font-family: 'Segoe UI', Arial, sans-serif;">
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="padding:32px 0;">
        <tr>
            <td align="center">
                <table role="presentation" width="480" cellpadding="0" cellspacing="0" style="background:#ffffff; border-radius:16px; overflow:hidden;">
                    <tr>
                        <td style="background:#ff8c42; padding:24px 32px;">
                            <span style="color:#fff; font-size:20px; font-weight:700;">El Pirry</span>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:32px;">
                            <h1 style="font-size:20px; color:#1f2430; margin:0 0 16px;">Hola {{ $nombre }},</h1>
                            <p style="font-size:14.5px; color:#4b5563; line-height:1.6; margin:0 0 24px;">
                                Recibimos una solicitud para restablecer tu contraseña. Haz clic en el
                                siguiente botón para crear una nueva. Este enlace es válido por
                                {{ $minutosExpiracion }} minutos y solo se puede usar una vez.
                            </p>
                            <table role="presentation" cellpadding="0" cellspacing="0">
                                <tr>
                                    <td style="border-radius:10px; background:#ff8c42;">
                                        <a href="{{ $resetUrl }}" style="display:inline-block; padding:12px 28px; color:#fff; text-decoration:none; font-weight:600; font-size:14.5px;">
                                            Restablecer contraseña
                                        </a>
                                    </td>
                                </tr>
                            </table>
                            <p style="font-size:12.5px; color:#9ca3af; line-height:1.6; margin:28px 0 0;">
                                Si tú no solicitaste este cambio, puedes ignorar este correo:
                                tu contraseña seguirá siendo la misma.
                            </p>
                            <p style="font-size:12.5px; color:#9ca3af; line-height:1.6; margin:12px 0 0; word-break:break-all;">
                                ¿El botón no funciona? Copia y pega este enlace: {{ $resetUrl }}
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
