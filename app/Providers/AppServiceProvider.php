<?php

namespace App\Providers;

use App\Contracts\Installation\EnvManagerInterface;
use App\Contracts\Installation\InstallationCheckInterface;
use App\Contracts\Installation\RequirementsCheckerInterface;
use App\Models\Promo;
use App\Observers\PromoObserver;
use App\Services\Installation\EnvManager;
use App\Services\Installation\InstallationCheck;
use App\Services\Installation\RequirementsChecker;
use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Register Installation Services
        $this->app->bind(RequirementsCheckerInterface::class, RequirementsChecker::class);
        $this->app->bind(EnvManagerInterface::class, EnvManager::class);
        $this->app->bind(InstallationCheckInterface::class, InstallationCheck::class);

        // Setup Session Singleton
        $this->app->singleton(\App\Services\Installation\SetupSession::class, function ($app) {
            return new \App\Services\Installation\SetupSession;
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Promo::observe(PromoObserver::class);

        // Force standard Livewire routes to avoid 404s
        // This is necessary because Livewire v3 uses hashed routes by default
        // which can mismatch with Filament expectations.
        if (class_exists(Livewire::class)) {
            Livewire::setUpdateRoute(function ($handle) {
                return \Illuminate\Support\Facades\Route::post('/livewire/update', $handle)->middleware('web');
            });

            Livewire::setScriptRoute(function ($handle) {
                return \Illuminate\Support\Facades\Route::get('/livewire/livewire.js', $handle);
            });

            // Also register common asset routes to avoid 404s if Filament/Livewire tries to use standard paths
            \Illuminate\Support\Facades\Route::post('/livewire/upload-file', [\Livewire\Features\SupportFileUploads\FileUploadController::class, 'handle'])
                ->middleware('web')
                ->name('livewire.upload-file.custom');

            \Illuminate\Support\Facades\Route::get('/livewire/preview-file/{filename}', [\Livewire\Features\SupportFileUploads\FilePreviewController::class, 'handle'])
                ->middleware('web')
                ->name('livewire.preview-file.custom');
        }
    }
}
