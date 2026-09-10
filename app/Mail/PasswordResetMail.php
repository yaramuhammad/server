<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PasswordResetMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    /**
     * @param  string  $resetUrl  Fully-qualified frontend URL with email + token query params.
     * @param  int  $expiresInMinutes  Token lifetime, for display in the email.
     */
    public function __construct(
        public string $recipientName,
        public string $resetUrl,
        public string $token,
        public int $expiresInMinutes = 60,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Reset your Edrak password',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.password-reset',
            with: [
                'recipientName' => $this->recipientName,
                'resetUrl' => $this->resetUrl,
                'token' => $this->token,
                'expiresInMinutes' => $this->expiresInMinutes,
            ],
        );
    }
}
