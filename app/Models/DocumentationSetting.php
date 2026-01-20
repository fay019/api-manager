<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class DocumentationSetting extends Model
{
    protected $table = 'documentation_settings';

    protected $fillable = [
        'doc_name',
        'path',
        'is_visible',
        'show_admin_credentials',
    ];

    protected $casts = [
        'is_visible' => 'boolean',
        'show_admin_credentials' => 'boolean',
    ];

    /**
     * Get a documentation setting by name
     */
    public static function getByName(string $name): ?self
    {
        return self::where('doc_name', $name)->first();
    }

    /**
     * Get all visible documentation
     */
    public static function visible(): array
    {
        return self::where('is_visible', true)
            ->pluck('doc_name')
            ->toArray();
    }

    /**
     * Get admin credentials visibility setting
     */
    public static function shouldShowCredentials(): bool
    {
        // Check if in local environment first
        if (config('app.env') !== 'local') {
            return false;
        }

        // Then check the setting (use first record or default to true)
        $setting = self::first();
        return $setting ? (bool) $setting->show_admin_credentials : true;
    }

    /**
     * Check if a specific doc is visible
     */
    public static function isDocVisible(string $docName): bool
    {
        $doc = self::where('doc_name', $docName)->first();
        return $doc ? (bool) $doc->is_visible : false;
    }

    /**
     * Get all docs with visibility info
     */
    public static function getAllDocs(): array
    {
        return self::all()->map(function ($doc) {
            return [
                'doc_name' => $doc->doc_name,
                'path' => $doc->path,
                'is_visible' => $doc->is_visible,
            ];
        })->toArray();
    }

    /**
     * Clear cache when updating
     */
    protected static function booted(): void
    {
        static::saved(function () {
            Cache::forget('documentation_settings');
        });

        static::deleted(function () {
            Cache::forget('documentation_settings');
        });
    }
}
