<?php

namespace App\Data;

use Illuminate\Support\Carbon;

class ContactMessageData
{
    public function __construct(
        public int $id,
        public ?int $client_id,
        public string $type,
        public string $name,
        public ?string $contact_name,
        public string $email,
        public ?string $contact_email,
        public ?string $billing_email,
        public ?string $phone,
        public string $subject,
        public string $message,
        public string $ip_address,
        public Carbon $created_at,
    ) {}
}
