<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/**
 * Correo de recuperación de contraseña (RF-017/018).
 * El envío real lo realiza el driver de correo configurado en .env
 * (MAIL_MAILER=resend|mailgun|postmark|ses|smtp), que es la "API externa"
 * a la que Laravel delega el envío. Ver config/mail.php y .env.example.
 */
class ResetPasswordMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $nombre,
        public string $resetUrl,
        public int $minutosExpiracion = 15,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Recupera tu contraseña - El Pirry',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.reset-password',
        );
    }
}
