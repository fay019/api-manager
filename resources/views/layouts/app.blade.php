<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'API Manager') - {{ config('app.name') }}</title>

    <!-- Scripts -->
    <script>
        (function() {
            const theme = localStorage.getItem('theme') || (window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light');
            if (theme === 'dark') {
                document.documentElement.classList.add('dark');
            }
        })();
    </script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles

    <!-- Google AdSense -->
    @if(\App\Models\Setting::get('ads_enabled', app()->environment('production')))
        <script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=ca-pub-8411028629670447"
            crossorigin="anonymous"></script>
    @endif

    <style>
        body {
            padding-bottom: 0;
        }

        .main-content {
            width: 100%;
            margin: 0;
            padding: 0;
            min-height: calc(100vh - 3.5rem - 400px); /* Ajustement pour éviter que le footer soit trop haut sur pages vides */
        }
    </style>

    @yield('styles')
</head>
<body>
    @include('layouts.partials.navbar')

    <div class="main-content">
        @yield('content')
    </div>

    @include('layouts.partials.footer')

    @yield('scripts')
    @livewireScripts
</body>
</html>
