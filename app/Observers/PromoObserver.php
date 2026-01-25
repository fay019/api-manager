<?php

namespace App\Observers;

use App\Models\Promo;
use App\Services\PromoService;

class PromoObserver
{
    public function creating(Promo $promo): void
    {
        if (auth()->check() && ! $promo->created_by) {
            $promo->created_by = auth()->id();
        }

        // Si toujours vide (ex: production/seeder), mettre le premier admin par défaut
        if (! $promo->created_by) {
            try {
                $promo->created_by = \App\Models\User::admins()->first()?->id ?? \App\Models\User::first()?->id ?? 1;
            } catch (\Exception $e) {
                $promo->created_by = 1;
            }
        }
    }

    public function created(Promo $promo): void
    {
        $this->createVersion($promo, 1);
        app(PromoService::class)->clearActivePromoCache();
    }

    public function updated(Promo $promo): void
    {
        \Log::info('PromoObserver@updated triggered', [
            'promo_id' => $promo->id,
            'dirty_attributes' => $promo->getDirty(),
        ]);

        $latestVersion = $promo->versions()->latest('version')->first();
        $nextVersion = ($latestVersion?->version ?? 0) + 1;

        $this->createVersion($promo, $nextVersion);
        app(PromoService::class)->clearActivePromoCache();
    }

    private function createVersion(Promo $promo, int $version): void
    {
        try {
            $promo->versions()->create([
                'version' => $version,
                'payload_json' => [
                    'title' => $promo->title,
                    'content' => $promo->content,
                    'image_url' => $promo->image_url,
                    'cta_text' => $promo->cta_text,
                    'cta_url' => $promo->cta_url,
                    'status' => $promo->status->value,
                    'starts_at' => $promo->starts_at?->toIso8601String(),
                    'ends_at' => $promo->ends_at?->toIso8601String(),
                    'priority' => $promo->priority,
                    'display_mode' => $promo->display_mode,
                ],
                'created_by' => $promo->created_by,
                'created_at' => now(),
            ]);
        } catch (\Exception $e) {
            \Log::error('Failed to create promo version', [
                'promo_id' => $promo->id,
                'version' => $version,
                'error' => $e->getMessage(),
            ]);
            // On ne bloque pas la création/édition de la promo si la version échoue
        }
    }
}
