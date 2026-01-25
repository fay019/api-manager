<?php

namespace App\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class InstallationServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // On ne fait rien ici pour l'instant
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $isInstalled = file_exists(storage_path('app/installed.lock'));

        if (! $isInstalled) {
            $this->loadSetupRoutes();
        } else {
            $this->loadAppRoutes();
        }
    }

    /**
     * Charge uniquement les routes du wizard d'installation.
     */
    protected function loadSetupRoutes(): void
    {
        Route::middleware([\App\Http\Middleware\SetupStateful::class])
            ->group(base_path('routes/setup.php'));

        // Rediriger la racine vers le setup si non installé
        Route::get('/', function () {
            return redirect('/setup/welcome');
        });
    }

    /**
     * Charge les routes normales de l'application.
     */
    protected function loadAppRoutes(): void
    {
        Route::middleware('web')
            ->group(base_path('routes/web.php'));

        Route::middleware('api')
            ->prefix('api')
            ->group(base_path('routes/api.php'));
    }
}
