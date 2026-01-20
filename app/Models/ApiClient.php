<?php

namespace App\Models;

use App\Enums\ApiClientStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ApiClient extends Model
{
    protected $fillable = [
        'name',
        'status',
        'allowed_origins',
        'notes',
        'rate_limit_per_minute',
    ];

    protected function casts(): array
    {
        return [
            'allowed_origins' => 'array',
            'status' => ApiClientStatus::class,
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
