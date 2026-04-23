<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ApiRequestLogArchive extends Model
{
    protected $table = 'api_request_logs_archive';

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
        'request_size',
        'response_size',
        'error_message',
        'cached',
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
