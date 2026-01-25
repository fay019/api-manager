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
        'icon',
    ];

    protected $casts = [
        'is_visible' => 'boolean',
    ];

    /**
     * Get a documentation setting by name
     */
    public static function getByName(string $name): ?self
    {
        try {
            return self::where('doc_name', $name)->first();
        } catch (\Exception $e) {
            // Table doesn't exist yet (fresh database before migrations)
            return null;
        }
    }

    /**
     * Get all visible documentation
     */
    public static function visible(): array
    {
        try {
            return self::where('is_visible', true)
                ->pluck('doc_name')
                ->toArray();
        } catch (\Exception $e) {
            // Table doesn't exist yet (fresh database before migrations)
            return [];
        }
    }

    /**
     * Check if a specific doc is visible
     */
    public static function isDocVisible(string $docName): bool
    {
        try {
            $doc = self::where('doc_name', $docName)->first();

            return $doc ? (bool) $doc->is_visible : false;
        } catch (\Exception $e) {
            // Table doesn't exist yet (fresh database before migrations)
            return true; // Default to visible until table is created
        }
    }

    /**
     * Get all docs with visibility info
     */
    public static function getAllDocs(): array
    {
        try {
            return self::all()->map(function ($doc) {
                return [
                    'doc_name' => $doc->doc_name,
                    'path' => $doc->path,
                    'is_visible' => $doc->is_visible,
                    'icon' => $doc->icon,
                ];
            })->toArray();
        } catch (\Exception $e) {
            // Table doesn't exist yet (fresh database before migrations)
            return [];
        }
    }

    /**
     * Get curated list of available icons
     */
    public static function getCuratedIcons(): array
    {
        return config('documentation-icons.curated', []);
    }

    /**
     * Get default icon for a documentation name
     */
    public static function getDefaultIcon(string $docName): string
    {
        $defaults = config('documentation-icons.defaults', []);

        return $defaults[$docName] ?? config('documentation-icons.fallback', '📄');
    }

    /**
     * Check if admin credentials should be shown
     */
    public static function shouldShowCredentials(): bool
    {
        try {
            // Only show credentials in local environment
            if (! app()->environment('local')) {
                return false;
            }

            // Default to false if table doesn't exist yet
            return false;
        } catch (\Exception $e) {
            return false;
        }
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
