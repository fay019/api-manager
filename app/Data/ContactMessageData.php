<?php

namespace App\Data;

use Illuminate\Support\Carbon;

class ContactMessageData
{
    public function __construct(
        public int $id,
        public string $name,
        public string $email,
        public string $subject,
        public string $message,
        public string $ip_address,
        public Carbon $created_at,
    ) {}
}
