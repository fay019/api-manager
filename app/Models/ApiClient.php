<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ApiClient extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'contact_email',
        'contact_name',
        'website',
        'client_type',
        'description',
        'is_active',
        'allowed_origins',
        'notes',
        'rate_limit_per_minute',
        'monthly_quota',
        'webhook_url',
        'activated_at',
    ];

    protected function casts(): array
    {
        return [
            'allowed_origins' => 'array',
            'is_active' => 'boolean',
            'activated_at' => 'datetime',
        ];
    }

    public function apiKeys(): HasMany
    {
        return $this->hasMany(ApiKey::class);
    }

    public function requestLogs(): HasMany
    {
        return $this->hasMany(ApiRequestLog::class);
    }
}
