<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Client extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'type',
        'first_name',
        'last_name',
        'email',
        'password',
        'avatar',
        'contact_email',
        'description',
        'notes',
        'company_name',
        'phone',
        'country',
        'timezone',
        'language',
        'billing_email',
        'address_json',
        'is_active',
        'activated_at',
        'last_login_at',
        'last_failed_login_at',
        'locked_until_at',
        'pending_email',
        'activation_token',
        'activation_expires_at',
        'password_reset_token',
        'password_reset_expires_at',
        'failed_login_attempts',
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'activation_token',
        'password_reset_token',
    ];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
            'is_active' => 'boolean',
            'activated_at' => 'datetime',
            'activation_expires_at' => 'datetime',
            'password_reset_expires_at' => 'datetime',
            'last_login_at' => 'datetime',
            'last_failed_login_at' => 'datetime',
            'locked_until_at' => 'datetime',
            'address_json' => 'array',
        ];
    }

    protected static function booted(): void
    {
        static::saving(function ($client) {
            $client->name = $client->type === 'company'
                ? $client->company_name
                : trim("{$client->first_name} {$client->last_name}");
        });
    }

    public function apiClients(): HasMany
    {
        return $this->hasMany(ApiClient::class, 'client_id');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeWithApiMetrics($query)
    {
        return $query->with([
            'apiClients' => fn ($q) => $q->where('is_active', true),
            'apiClients.apiKeys',
        ]);
    }

    public function getDisplayEmailAttribute(): string
    {
        return $this->type === 'company'
            ? $this->contact_email ?? $this->email
            : $this->email;
    }
}
