<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Crypt;

class AiSetting extends Model
{
    protected $fillable = [
        'provider',
        'base_url',
        'default_model',
        'allowed_models',
        'timeout',
        'is_active',
        'ia_token_hash',
    ];

    protected function casts(): array
    {
        return [
            'allowed_models' => 'array',
            'is_active' => 'boolean',
        ];
    }

    public static function getInstance(): self
    {
        return Cache::remember('ai_settings', config('cache.settings_ttl', 3600), function () {
            return self::firstOrCreate([], [
                'provider' => 'ollama',
                'base_url' => config('ai.ollama.url'),
                'default_model' => config('ai.ollama.default_model'),
                'allowed_models' => config('ai.ollama.allowed_models'),
                'timeout' => config('ai.ollama.timeout'),
                'is_active' => true,
            ]);
        });
    }

    public function getAllowedModelsArray(): array
    {
        // Benutze den gecasted Wert von allowed_models
        return $this->allowed_models ?? [];
    }

    protected static function booted(): void
    {
        static::saved(function () {
            Cache::forget('ai_settings');
            // Invalidiere auch den Cache für die Modellisten
            $settings = self::first();
            if ($settings) {
                $cacheKey = self::buildCacheKey($settings);
                Cache::forget($cacheKey);
            }
        });
    }

    public static function buildCacheKey(?self $settings = null): string
    {
        $settings = $settings ?? self::getInstance();

        return 'ai_models:'.$settings->provider.':'.md5($settings->base_url);
    }

    public function setIaTokenHashAttribute(string $value): void
    {
        $this->attributes['ia_token_hash'] = Crypt::encryptString($value);
    }

    public function verifyToken(string $token): bool
    {
        if (! $this->ia_token_hash) {
            return false;
        }

        try {
            return Crypt::decryptString($this->ia_token_hash) === $token;
        } catch (\Exception) {
            return false;
        }
    }
}
