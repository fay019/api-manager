<?php

namespace App\Mail;

use App\Models\ContactMessage;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class ContactReply extends Mailable
{
    private ?string $avatarUrl = null;
    private ?string $avatarInitials = null;

    public function __construct(
        public ContactMessage $message,
        public string $recipientEmail = '',
        public string $language = '',
    ) {
        if (! $this->recipientEmail) {
            $this->recipientEmail = $message->email;
        }

        if (! $this->language) {
            $this->language = $message->language ?? 'en';
        }

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
            to: $this->recipientEmail,
            subject: 'Re: '.$this->message->subject,
        );
    }

    public function content(): Content
    {
        // Set locale to message's language for email content
        app()->setLocale($this->language);

        return new Content(
            view: 'emails.contact-reply',
            with: [
                'type' => $this->message->type,
                'contactName' => $this->message->type === 'company' && $this->message->contact_name ? $this->message->contact_name : $this->message->name,
                'companyName' => $this->message->type === 'company' ? $this->message->name : null,
                'replyMessage' => $this->message->reply_message,
                'originalMessage' => $this->message->message,
                'avatarUrl' => $this->avatarUrl,
                'avatarInitials' => $this->avatarInitials,
            ],
        );
    }
}
