<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $fillable = ['key', 'value', 'type', 'description'];

    public static function get(string $key, mixed $default = null): mixed
    {
        try {
            $setting = static::where('key', $key)->first();

            if (! $setting) {
                return $default;
            }

            return match ($setting->type) {
                'boolean' => in_array(strtolower($setting->value), ['true', '1', 'yes'], true),
                'integer' => (int) $setting->value,
                default => $setting->value,
            };
        } catch (\Exception) {
            return $default;
        }
    }

    public static function set(string $key, mixed $value, string $type = 'boolean', ?string $description = null): void
    {
        static::updateOrCreate(
            ['key' => $key],
            [
                'value' => (string) $value,
                'type' => $type,
                'description' => $description,
            ]
        );
    }
}
