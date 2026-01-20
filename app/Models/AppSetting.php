<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class AppSetting extends Model
{
    protected $table = 'app_settings';

    protected $fillable = [
        'show_admin_credentials',
        'visible_docs',
    ];

    protected $casts = [
        'show_admin_credentials' => 'boolean',
        'visible_docs' => 'array',
    ];

    /**
     * Get the singleton instance from cache or database
     */
    public static function get(): self
    {
        $cacheKey = 'app_settings';
        $cacheTtl = config('cache.settings_ttl', 3600);

        return Cache::remember($cacheKey, $cacheTtl, function () {
            return self::find(1) ?? self::create([
                'id' => 1,
                'show_admin_credentials' => true,
                'visible_docs' => ['readme', 'api', 'database', 'deployment'],
            ]);
        });
    }

    /**
     * Clear the cache when saving
     */
    protected static function booted(): void
    {
        static::saved(function () {
            Cache::forget('app_settings');
        });
    }

    /**
     * Check if credentials should be shown (only in local environment AND if setting enabled)
     */
    public function shouldShowCredentials(): bool
    {
        return config('app.env') === 'local' && $this->show_admin_credentials;
    }

    /**
     * Check if a specific documentation page is visible
     */
    public function isDocVisible(string $docName): bool
    {
        $visibleDocs = $this->visible_docs ?? [];
        return in_array($docName, $visibleDocs);
    }

    /**
     * Get list of visible documentation names
     */
    public function getVisibleDocs(): array
    {
        return $this->visible_docs ?? [];
    }

    /**
     * Set visible documentation list
     */
    public function setVisibleDocs(array $docs): self
    {
        $this->visible_docs = $docs;
        return $this;
    }

    /**
     * Check if docs index should be visible (true if at least one doc is visible)
     */
    public function isDocsIndexVisible(): bool
    {
        return count($this->getVisibleDocs()) > 0;
    }
}
