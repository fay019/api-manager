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
        'email',
        'password',
        'avatar',
        'contact_name',
        'contact_email',
        'description',
        'notes',
        'is_active',
        'activated_at',
        'last_login_at',
        'pending_email',
        'activation_token',
        'activation_expires_at',
        'password_reset_token',
        'password_reset_expires_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'activation_token',
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
        ];
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
}
