<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ApiRequestLog extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'api_client_id',
        'api_key_id',
        'method',
        'path',
        'status_code',
        'ip',
        'hostname',
        'domain',
        'site_name',
        'page_path',
        'full_url',
        'client_request_time',
        'client_user_agent',
        'user_agent',
        'origin',
        'referer',
        'duration_ms',
        'created_at',
    ];

    protected function casts(): array
    {
        return [
            'created_at' => 'datetime',
        ];
    }

    public function apiClient(): BelongsTo
    {
        return $this->belongsTo(ApiClient::class);
    }

    public function apiKey(): BelongsTo
    {
        return $this->belongsTo(ApiKey::class);
    }
}
