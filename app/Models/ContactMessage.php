<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ContactMessage extends Model
{
    protected $fillable = [
        'name',
        'email',
        'subject',
        'message',
        'status',
        'ip_address',
        'user_agent',
        'language',
        'honeypot_triggered',
        'timestamp_check_valid',
        'admin_notes',
        'reply_message',
        'replied_at',
        'replied_by',
    ];

    protected function casts(): array
    {
        return [
            'honeypot_triggered' => 'boolean',
            'timestamp_check_valid' => 'boolean',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }
}
