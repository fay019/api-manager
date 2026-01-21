<?php

namespace App\Modules\Promo;

use App\Modules\BaseModule;

/**
 * Module Promo - Gestion du système de promotions.
 *
 * Ce module fournit:
 * - Gestion des promotions (create, read, update, delete)
 * - Cache et optimisation des performances
 * - Admin panel Filament intégré
 */
class PromoModule extends BaseModule
{
    protected string $moduleName = 'Promo';

    protected string $description = 'Système de gestion des promotions';

    protected string $version = '1.0.0';

    /**
     * Bootstrap le module.
     */
    public function boot(): void
    {
        // Enregistrer les routes du module
        $this->registerPromoRoutes();

        // Enregistrer l'observateur Promo
        $this->registerObservers();

        // Appeler le parent boot
        parent::boot();
    }

    /**
     * Enregistre les routes du module Promo.
     */
    protected function registerPromoRoutes(): void
    {
        // Promo API routes
        $this->app['router']->group([
            'prefix' => 'api/v1',
            'middleware' => ['api', 'throttle:api', 'cors-per-client'],
        ], function ($router) {
            $router->get('/promo/banner.json', 'App\\Modules\\Promo\\Http\\Controllers\\PromoBannerController@show');
        });
    }

    /**
     * Enregistre les observateurs.
     */
    protected function registerObservers(): void
    {
        \App\Models\Promo::observe(\App\Observers\PromoObserver::class);
    }

    /**
     * Hook d'installation du module.
     */
    public function onInstall(): void
    {
        $this->clearPromoCache();
    }

    /**
     * Valide l'installation du module.
     *
     * @return array
     */
    public function validateInstallation(): array
    {
        $errors = [];

        // Vérifier que le modèle existe
        if (!class_exists(\App\Models\Promo::class)) {
            $errors[] = 'Model Promo introuvable';
        }

        return [
            'valid' => empty($errors),
            'errors' => $errors,
        ];
    }

    /**
     * Obtient les prérequis du module.
     *
     * @return array
     */
    public function getRequirements(): array
    {
        return [
            'php' => '8.2+',
            'laravel' => '12.0+',
            'extensions' => ['bcmath', 'json'],
        ];
    }

    /**
     * Vide le cache des promotions.
     */
    public function clearPromoCache(): void
    {
        try {
            \Illuminate\Support\Facades\Cache::forget('active_promos');
            \Illuminate\Support\Facades\Cache::forget('promo_*');
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::warning('Could not clear promo cache: ' . $e->getMessage());
        }
    }
}
