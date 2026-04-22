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
use App\Services\Installation\SetupSession;
use Filament\Support\Facades\FilamentView;
use Filament\View\PanelsRenderHook;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Livewire\Features\SupportFileUploads\FilePreviewController;
use Livewire\Features\SupportFileUploads\FileUploadController;
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
        $this->app->singleton(SetupSession::class, function ($app) {
            return new SetupSession;
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Register locale switcher in Filament topbar
        FilamentView::registerRenderHook(
            PanelsRenderHook::TOPBAR_END,
            fn () => view('components.filament-locale-switcher')
        );

        Promo::observe(PromoObserver::class);

        $this->registerClientRateLimiters();

        // Force standard Livewire routes to avoid 404s
        // This is necessary because Livewire v3 uses hashed routes by default
        // which can mismatch with Filament expectations.
        if (class_exists(Livewire::class)) {
            Livewire::setUpdateRoute(function ($handle) {
                return Route::post('/livewire/update', $handle)->middleware('web');
            });

            Livewire::setScriptRoute(function ($handle) {
                return Route::get('/livewire/livewire.js', $handle);
            });

            // Also register common asset routes to avoid 404s if Filament/Livewire tries to use standard paths
            Route::post('/livewire/upload-file', [FileUploadController::class, 'handle'])
                ->middleware('web')
                ->name('livewire.upload-file.custom');

            Route::get('/livewire/preview-file/{filename}', [FilePreviewController::class, 'handle'])
                ->middleware('web')
                ->name('livewire.preview-file.custom');
        }
    }

    private function registerClientRateLimiters(): void
    {
        RateLimiter::for('client-login', function (Request $request) {
            return [
                Limit::perMinute(5)->by('login-email:'.$request->input('email')),
                Limit::perMinute(10)->by('login-ip:'.$request->ip()),
                Limit::perMinute(20)->by('login-global:'.$request->ip()),
            ];
        });

        RateLimiter::for('client-register', function (Request $request) {
            return Limit::perMinute(5)->by('register:'.$request->ip());
        });

        RateLimiter::for('client-activate', function (Request $request) {
            return Limit::perMinute(20)->by('activate:'.$request->ip());
        });

        RateLimiter::for('client-resend', function (Request $request) {
            return Limit::perMinute(3)->by('resend:'.$request->ip());
        });

        RateLimiter::for('client-password-forgot', function (Request $request) {
            return Limit::perMinute(3)->by('pwd-forgot:'.$request->ip());
        });

        RateLimiter::for('client-password-reset', function (Request $request) {
            return Limit::perMinute(5)->by('pwd-reset:'.$request->ip());
        });
    }
}
