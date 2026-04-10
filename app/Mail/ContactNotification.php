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

    public function __construct(ContactMessage $message)
    {
        $this->data = new ContactMessageData(
            id: $message->id,
            name: $message->name,
            email: $message->email,
            subject: $message->subject,
            message: $message->message,
            ip_address: $message->ip_address,
            created_at: $message->created_at,
        );
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
            ],
        );
    }
}
