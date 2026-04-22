<?php

namespace App\Notifications;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ClientPasswordReset extends Notification
{
    public function __construct(private string $rawToken) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject(__('client.client_auth.password_reset_email_subject'))
            ->markdown('emails.client-password-reset', [
                'resetUrl' => route('client.password.reset', ['token' => $this->rawToken, 'email' => $notifiable->email]),
                'name' => $notifiable->name,
                'expiresAt' => now()->addHour()->format('d/m/Y H:i'),
            ]);
    }
}
