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
        $avatarUrl = null;
        $avatarInitials = null;

        if ($notifiable->avatar) {
            $avatarUrl = url('storage/'.$notifiable->avatar);
        } else {
            // Generate initials for client without avatar
            if ($notifiable->type === 'company') {
                $avatarInitials = strtoupper(substr($notifiable->company_name ?? '', 0, 1)).
                                 strtoupper(substr($notifiable->contact_name ?? '', 0, 1));
            } else {
                $avatarInitials = strtoupper(substr($notifiable->first_name ?? '', 0, 1)).
                                 strtoupper(substr($notifiable->last_name ?? '', 0, 1));
            }
        }

        return (new MailMessage)
            ->subject(__('client.client_auth.activation_email_subject'))
            ->markdown('emails.client-activation', [
                'activationUrl' => route('client.activate', ['token' => $this->rawToken]),
                'name' => $notifiable->name,
                'expiresAt' => now()->addHours(24)->format('d/m/Y H:i'),
                'avatarUrl' => $avatarUrl,
                'avatarInitials' => $avatarInitials,
            ]);
    }
}
