<?php

namespace App\Notifications;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ClientActivation extends Notification
{
    public function __construct(private string $rawToken) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject(__('client.client_auth.activation_email_subject'))
            ->markdown('emails.client-activation', [
                'activationUrl' => route('client.activate', ['token' => $this->rawToken]),
                'name' => $notifiable->name,
                'expiresAt' => now()->addHours(24)->format('d/m/Y H:i'),
            ]);
    }
}
