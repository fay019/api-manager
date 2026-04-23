<?php

namespace App\Mail;

use App\Data\ContactMessageData;
use App\Models\ContactMessage;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class ContactNotification extends Mailable
{
    private ContactMessageData $data;
    private ?string $avatarUrl = null;
    private ?string $avatarInitials = null;

    public function __construct(ContactMessage $message)
    {
        $this->data = new ContactMessageData(
            id: $message->id,
            client_id: $message->client_id,
            type: $message->type,
            name: $message->name,
            contact_name: $message->contact_name,
            email: $message->email,
            contact_email: $message->contact_email,
            billing_email: $message->billing_email,
            phone: $message->phone,
            subject: $message->subject,
            message: $message->message,
            ip_address: $message->ip_address,
            created_at: $message->created_at,
        );

        if ($message->client_id && $message->client) {
            if ($message->client->avatar) {
                $this->avatarUrl = url('storage/'.$message->client->avatar);
            } else {
                // Generate initials for authenticated client without avatar
                if ($message->type === 'company') {
                    $this->avatarInitials = strtoupper(substr($message->client->company_name ?? '', 0, 1)).
                                           strtoupper(substr($message->client->contact_name ?? '', 0, 1));
                } else {
                    $this->avatarInitials = strtoupper(substr($message->client->first_name ?? '', 0, 1)).
                                           strtoupper(substr($message->client->last_name ?? '', 0, 1));
                }
            }
        }
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'New Contact Message: '.$this->data->subject,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.contact-notification',
            with: [
                'contactMessage' => $this->data,
                'avatarUrl' => $this->avatarUrl,
                'avatarInitials' => $this->avatarInitials,
            ],
        );
    }
}
