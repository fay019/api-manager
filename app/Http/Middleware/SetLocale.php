<?php

namespace App\Http\Middleware;

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
            session(['locale' => $locale]);
        }

        return $next($request);
    }

    private function detectLocale(Request $request): string
    {
        // 1. Check session (user preference) - highest priority
        if (session()->has('locale')) {
            $sessionLocale = session('locale');
            if (in_array($sessionLocale, $this->supportedLocales)) {
                return $sessionLocale;
            }
        }

        // 2. Check cookie
        $cookieLocale = $request->cookie('locale');
        if ($cookieLocale && in_array($cookieLocale, $this->supportedLocales)) {
            return $cookieLocale;
        }

        // 3. Detect from browser Accept-Language header
        $preferred = $request->getPreferredLanguage($this->supportedLocales);
        if ($preferred) {
            return $preferred;
        }

        // 4. Fall back to app config
        return config('app.locale', 'en');
    }
}
