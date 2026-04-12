@php
    $supportedLocales = ['fr', 'en', 'de'];
    $userLocale = null;

    // On cherche le cookie brut via PHP natif
    if (isset($_COOKIE['locale'])) {
        $userLocale = $_COOKIE['locale'];
    }

    // Si pas de cookie, on cherche la langue du navigateur
    if (!$userLocale || !in_array($userLocale, $supportedLocales)) {
        $acceptLang = $_SERVER['HTTP_ACCEPT_LANGUAGE'] ?? '';
        foreach ($supportedLocales as $lang) {
            if (str_starts_with($acceptLang, $lang)) {
                $userLocale = $lang;
                break;
            }
        }
    }

    // Si on a trouvé une langue, on l'applique
    if ($userLocale && in_array($userLocale, $supportedLocales)) {
        app()->setLocale($userLocale);
    }
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') - {{ config('app.name') }}</title>
    @vite('resources/css/app.css')
</head>
<body class="bg-gradient-to-br from-gray-50 to-gray-100 dark:from-gray-950 dark:to-gray-900">
    <div class="flex min-h-screen flex-col items-center justify-center px-4 py-20 sm:px-6 lg:px-8">
        <!-- Optional Header -->
        <div class="mb-16 text-center">
            <a href="{{ route('home') }}" class="inline-flex items-center gap-2 transition-transform hover:scale-105">
                <svg class="h-8 w-8 text-indigo-600 dark:text-indigo-400" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M13 10V3L4 14h7v7l9-11h-7z"/>
                </svg>
                <span class="text-xl font-bold text-gray-900 dark:text-white">{{ config('app.name') }}</span>
            </a>
        </div>

        <!-- Error Container -->
        <div class="w-full max-w-3xl space-y-12 text-center">
            <!-- Error Code & Title -->
            <div class="space-y-6">
                <div class="text-9xl font-black tracking-tighter text-red-600 dark:text-red-500">
                    @yield('code')
                </div>
                <div class="space-y-3">
                    <h1 class="text-5xl font-bold text-gray-900 dark:text-white">
                        @yield('title')
                    </h1>
                    <p class="text-lg text-gray-600 dark:text-gray-400">
                        @yield('message')
                    </p>
                </div>
            </div>

            <!-- Debug Info (if enabled) -->
            @yield('debug', '')

            <!-- Actions -->
            <div class="flex flex-col gap-3 pt-4 sm:flex-row sm:justify-center sm:gap-4">
                <a href="{{ route('home') }}" class="inline-flex w-full items-center justify-center gap-2 rounded-lg bg-indigo-600 px-8 py-3 font-semibold text-white transition-all hover:bg-indigo-700 hover:shadow-lg dark:bg-indigo-700 dark:hover:bg-indigo-600 sm:w-auto">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12a9 9 0 0110-8.9M13.027 12a9 9 0 11-18-4" />
                    </svg>
                    {{ __('errors.' . (isset($exception) ? $exception->getStatusCode() : '503') . '.back_home') }}
                </a>
                <a href="javascript:history.back()" class="inline-flex w-full items-center justify-center gap-2 rounded-lg border border-gray-300 px-8 py-3 font-semibold text-gray-700 transition-colors hover:bg-gray-100 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-800 sm:w-auto">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                    {{ __('errors.' . (isset($exception) ? $exception->getStatusCode() : '503') . '.back_previous') }}
                </a>
            </div>
        </div>
    </div>

    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const html = document.documentElement;

        function getInitialTheme() {
            const saved = localStorage.getItem('theme');
            if (saved) return saved;

            if (window.matchMedia('(prefers-color-scheme: dark)').matches) {
                return 'dark';
            }
            return 'light';
        }

        function applyTheme(theme) {
            if (theme === 'dark') {
                html.classList.add('dark');
            } else {
                html.classList.remove('dark');
            }
            localStorage.setItem('theme', theme);
        }

        const initialTheme = getInitialTheme();
        applyTheme(initialTheme);

        window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', (e) => {
            if (!localStorage.getItem('theme')) {
                applyTheme(e.matches ? 'dark' : 'light');
            }
        });
    });
    </script>
</body>
</html>
