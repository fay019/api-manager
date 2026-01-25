<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HealthCheckSetting extends Model
{
    protected $table = 'health_check_settings';

    protected $fillable = [
        'cache_enabled',
        'logs_enabled',
        'disk_space_enabled',
        'storage_enabled',
    ];

    protected $casts = [
        'cache_enabled' => 'boolean',
        'logs_enabled' => 'boolean',
        'disk_space_enabled' => 'boolean',
        'storage_enabled' => 'boolean',
    ];

    /**
     * Get the singleton instance (only one row in table)
     */
    public static function getInstance(): self
    {
        return self::first() ?? self::create([
            'cache_enabled' => true,
            'logs_enabled' => true,
            'disk_space_enabled' => true,
            'storage_enabled' => true,
        ]);
    }

    /**
     * Check if a specific check is enabled
     */
    public function isCheckEnabled(string $checkName): bool
    {
        $attribute = $checkName.'_enabled';

        return $this->{$attribute} ?? true;
    }
}
