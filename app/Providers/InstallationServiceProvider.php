<?php

namespace App\Providers;

use App\Http\Middleware\SetupStateful;
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
     * Charge les routes du wizard d'installation + routes publiques.
     */
    protected function loadSetupRoutes(): void
    {
        // Redirect root to setup during installation
        Route::get('/', function () {
            return redirect('/setup/welcome');
        });

        // Load web routes (public pages, docs, etc.)
        Route::middleware('web')
            ->group(base_path('routes/web.php'));

        // Load API routes
        Route::middleware('api')
            ->prefix('api')
            ->name('api.')
            ->group(base_path('routes/api.php'));

        // Load setup routes
        Route::middleware([SetupStateful::class])
            ->group(base_path('routes/setup.php'));
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
            ->name('api.')
            ->group(base_path('routes/api.php'));
    }
}
