<?php

namespace App\Mail;

use App\Models\ContactMessage;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class ContactReply extends Mailable
{
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
                'name' => $this->message->name,
                'replyMessage' => $this->message->reply_message,
                'originalMessage' => $this->message->message,
            ],
        );
    }
}
