<?php

namespace App\Http\Middleware;

use App\Services\Installation\SetupSession;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

class SetLocale
{
    protected array $supportedLocales = ['fr', 'en', 'de'];

    public function handle(Request $request, Closure $next)
    {
        $locale = $this->detectLocale($request);

        if (in_array($locale, $this->supportedLocales)) {
            App::setLocale($locale);
            config(['app.locale' => $locale]);
            setcookie('locale_plain', $locale, time() + (86400 * 30), "/");

            // Persister en session et cookie si nécessaire
            try {
                if (session()->isStarted() && session('locale') !== $locale) {
                    session(['locale' => $locale]);
                }
            } catch (\Exception $e) {
                // Session indisponible (driver DB avant installation)
            }
        }

        return $next($request);
    }

    private function detectLocale(Request $request): string
    {
        // 1. Check if we're in setup mode and have a setup session
        if (! file_exists(storage_path('app/installed.lock'))) {
            $setupSession = app(SetupSession::class);
            $setupLocale = $setupSession->get('locale');

            if ($setupLocale && in_array($setupLocale, $this->supportedLocales)) {
                // \Log::info('Locale detected from SetupSession: ' . $setupLocale);
                return $setupLocale;
            }
        }

        // 2. Check session (user preference) - highest priority after manual switch
        try {
            if (session()->isStarted() && session()->has('locale')) {
                $sessionLocale = session('locale');
                if (in_array($sessionLocale, $this->supportedLocales)) {
                    // \Log::info('Locale detected from Session: ' . $sessionLocale);
                    return $sessionLocale;
                }
            }
        } catch (\Exception $e) {
            // Ignorer si la session n'est pas accessible
        }

        // 3. Check cookie (fallback if session expired but browser remembered)
        $cookieLocale = $request->cookie('locale');
        if ($cookieLocale && in_array($cookieLocale, $this->supportedLocales)) {
            // Update session if it's different and started
            try {
                if (session()->isStarted() && session('locale') !== $cookieLocale) {
                    session(['locale' => $cookieLocale]);
                }
            } catch (\Exception $e) {
            }

            // \Log::info('Locale detected from Cookie: ' . $cookieLocale);
            return $cookieLocale;
        }

        // 4. Detect from browser Accept-Language header
        // ONLY if no user preference is set in session or cookie
        $preferred = $request->getPreferredLanguage($this->supportedLocales);
        if ($preferred) {
            // Persist detected locale so it doesn't flip-flop
            try {
                if (session()->isStarted() && ! session()->has('locale')) {
                    session(['locale' => $preferred]);
                }
            } catch (\Exception $e) {
            }

            // \Log::info('Locale detected from Browser: ' . $preferred);
            return $preferred;
        }

        // 5. Fall back to app config (env)
        $configLocale = config('app.locale', 'en');

        // \Log::info('Locale fallback to Config: ' . $configLocale);
        return $configLocale;
    }
}
