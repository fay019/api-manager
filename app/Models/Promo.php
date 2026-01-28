<?php

namespace App\Models;

use App\Enums\PromoStatus;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

class Promo extends Model
{
    protected $fillable = [
        'slug',
        'author_name',
        'author_role',
        'title',
        'content',
        'image_url',
        'cta_text',
        'cta_url',
        'status',
        'starts_at',
        'ends_at',
        'priority',
        'max_impressions',
        'cooldown_seconds',
        'display_mode',
        'auto_close_timer',
        'show_countdown',
        'animation_style',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'title' => 'array',
            'content' => 'array',
            'cta_text' => 'array',
            'status' => PromoStatus::class,
            'starts_at' => 'datetime',
            'ends_at' => 'datetime',
            'priority' => 'integer',
            'max_impressions' => 'integer',
            'cooldown_seconds' => 'integer',
            'auto_close_timer' => 'integer',
            'show_countdown' => 'boolean',
        ];
    }

    /**
     * Get translation for a field
     */
    public function getTranslation(string $field, ?string $locale = null): ?string
    {
        $locale = $locale ?? app()->getLocale();
        $translations = $this->{$field} ?? [];

        if (! is_array($translations)) {
            return $translations;
        }

        return $translations[$locale] ?? $translations[config('app.fallback_locale')] ?? array_values($translations)[0] ?? null;
    }

    protected function fullImageUrl(): Attribute
    {
        return Attribute::make(
            get: function () {
                $value = $this->getAttributes()['image_url'] ?? null;
                if (! $value) {
                    return null;
                }
                if (str_starts_with($value, 'http')) {
                    return $value;
                }

                return Storage::disk('public')->url($value);
            }
        );
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function versions(): HasMany
    {
        return $this->hasMany(PromoVersion::class);
    }

    public function scopeActive($query)
    {
        return $query
            ->whereIn('status', [PromoStatus::PUBLISHED, PromoStatus::SCHEDULED])
            ->where(function ($q) {
                $q->whereNull('starts_at')
                    ->orWhere('starts_at', '<=', now());
            })
            ->where(function ($q) {
                $q->whereNull('ends_at')
                    ->orWhere('ends_at', '>=', now());
            })
            ->orderByDesc('priority')
            ->orderByDesc('created_at');
    }
}
