<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Error - @yield('title')</title>
    @vite('resources/css/app.css')
</head>
<body class="bg-white dark:bg-gray-950">
    <div class="flex min-h-screen items-center justify-center px-4 py-16 sm:px-6 lg:px-8">
        <div class="w-full max-w-md space-y-8 text-center">
            <!-- Error Code -->
            <div class="space-y-4">
                <div class="text-7xl font-black tracking-tight text-indigo-600 dark:text-indigo-500">
                    @yield('code')
                </div>
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white">
                    @yield('title')
                </h1>
                <p class="text-lg text-gray-600 dark:text-gray-400">
                    @yield('message')
                </p>
            </div>

            <!-- Actions -->
            <div class="space-y-3 pt-8">
                <a href="{{ url('/') }}" class="inline-flex w-full items-center justify-center gap-2 rounded-lg bg-indigo-600 px-6 py-3 font-semibold text-white transition-all hover:bg-indigo-700 dark:bg-indigo-700 dark:hover:bg-indigo-600">
                    {{ __('errors.' . ($exception->getStatusCode() ?? '500') . '.back_home') }}
                </a>
                <a href="{{ url()->previous() }}" class="inline-flex w-full items-center justify-center gap-2 rounded-lg border border-gray-300 px-6 py-3 font-semibold text-gray-700 transition-colors hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-900">
                    {{ __('errors.' . ($exception->getStatusCode() ?? '500') . '.back_previous') }}
                </a>
            </div>
        </div>
    </div>

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
