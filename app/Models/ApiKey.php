<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class ApiKey extends Model
{
    use HasFactory;

    protected $fillable = [
        'api_client_id',
        'key_encrypted',
        'key_prefix',
        'name',
        'slug',
        'starts_at',
        'expires_at',
        'is_active',
    ];

    protected $hidden = ['key_encrypted'];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'last_used_at' => 'datetime',
            'starts_at' => 'datetime',
            'expires_at' => 'datetime',
        ];
    }

    public function apiClient(): BelongsTo
    {
        return $this->belongsTo(ApiClient::class);
    }

    public function requestLogs(): HasMany
    {
        return $this->hasMany(ApiRequestLog::class);
    }

    public function getIsExpiredAttribute(): bool
    {
        return $this->expires_at && $this->expires_at->isPast();
    }

    protected static function booted(): void
    {
        static::creating(function (self $model) {
            if (! $model->slug && $model->name) {
                $model->slug = Str::slug($model->name);
            }
        });

        static::updating(function (self $model) {
            if ($model->isDirty('name') && $model->name) {
                $model->slug = Str::slug($model->name);
            }
        });
    }

    public function getIsValidAttribute(): bool
    {
        $now = now();

        return $this->is_active
            && (! $this->starts_at || $this->starts_at->isPast())
            && (! $this->expires_at || $this->expires_at->isFuture())
            && $this->apiClient->is_active;
    }

    public function scopeValid($query)
    {
        return $query->where('is_active', true)
            ->where(fn ($q) => $q->whereNull('starts_at')->orWhere('starts_at', '<=', now()))
            ->where(fn ($q) => $q->whereNull('expires_at')->orWhere('expires_at', '>', now()));
    }

    public function scopeExpired($query)
    {
        return $query->where('expires_at', '<', now())->where('is_active', true);
    }
}
