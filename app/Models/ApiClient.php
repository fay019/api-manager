<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ApiClient extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'client_id',
        'website',
        'client_type',
        'is_active',
        'allowed_origins',
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

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function apiKeys(): HasMany
    {
        return $this->hasMany(ApiKey::class);
    }

    public function requestLogs(): HasMany
    {
        return $this->hasMany(ApiRequestLog::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeWithMetrics($query)
    {
        return $query->withCount([
            'apiKeys as active_keys' => fn ($q) => $q->where('is_active', true),
            'requestLogs as total_requests',
            'requestLogs as success_requests' => fn ($q) => $q->whereBetween('status_code', [200, 299]),
        ]);
    }
}
