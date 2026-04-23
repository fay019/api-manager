<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ContactMessage extends Model
{
    protected $fillable = [
        'client_id',
        'name',
        'contact_name',
        'email',
        'contact_email',
        'billing_email',
        'phone',
        'subject',
        'message',
        'type',
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

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }
}
