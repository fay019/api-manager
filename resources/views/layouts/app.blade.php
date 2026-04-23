<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    @include('layouts.partials.seo')
    @include('partials.favicon')

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
    @php
        $adsEnabled = \App\Models\Setting::get('ads_enabled', app()->environment('production'));
        $clientId = \App\Models\Setting::get('ads_client_id', 'ca-pub-8411028629670447');
    @endphp
    @if($adsEnabled && $clientId)
        <script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client={{ $clientId }}"
            crossorigin="anonymous"></script>
    @endif

    <style>
        :root {
            --primary: #4f46e5;
            --primary-hover: #4338ca;
            --bg-gradient: linear-gradient(135deg, #f5f7ff 0%, #e0e7ff 100%);
            --card-bg: #ffffff;
            --text-main: #1f2937;
            --text-muted: #6b7280;
            --border: #e5e7eb;
            --success: #10b981;
            --error: #ef4444;
            --nav-bg: #ffffff;
            --nav-border: rgba(0, 0, 0, 0.1);
        }

        html.dark, :root[data-theme="dark"] {
            --bg-gradient: linear-gradient(135deg, #111827 0%, #1f2937 100%);
            --card-bg: #1f2937;
            --text-main: #f9fafb;
            --text-muted: #9ca3af;
            --border: #374151;
            --nav-bg: #18181b;
            --nav-border: rgba(255, 255, 255, 0.1);
        }

        body {
            padding-bottom: 0;
            padding-top: 3.5rem; /* Space for fixed navbar */
            color: var(--text-main);
            background: var(--card-bg); /* Use card-bg or similar for consistent background */
        }

        .main-content {
            width: 100%;
            margin: 0;
            padding: 0;
            min-height: calc(100vh - 7rem - 400px); /* Adjust min-height for padding-top/bottom */
            position: relative;
            z-index: 1;
        }

        /* Google AdSense fix: Prevent ads from overlapping content */
        ins.adsbygoogle {
            display: block !important;
            margin: 1rem auto !important;
            text-align: center !important;
        }

        /* Prevent injected fixed position ads from overlapping */
        [style*="position: fixed"] {
            z-index: 10 !important;
        }
    </style>

    @yield('styles')
</head>
<body>
    @include('layouts.partials.navbar')

    <!-- Main Content -->
    <div class="main-content">
        @yield('content')
    </div>

    @include('layouts.partials.footer')

    @yield('scripts')
    @livewireScripts

    <!-- Push ads script to reload after page load -->
    <script>
        if (typeof window.adsbygoogle !== 'undefined') {
            window.addEventListener('load', function() {
                (adsbygoogle = window.adsbygoogle || []).push({});
            });
        }

        // Copy to clipboard utility
        function copyToClipboard(text) {
            if (navigator.clipboard && window.isSecureContext) {
                navigator.clipboard.writeText(text).then(() => {
                    alert('Copié!');
                }).catch(() => {
                    fallbackCopy(text);
                });
            } else {
                fallbackCopy(text);
            }
        }

        function fallbackCopy(text) {
            const textarea = document.createElement('textarea');
            textarea.value = text;
            document.body.appendChild(textarea);
            textarea.select();
            document.execCommand('copy');
            document.body.removeChild(textarea);
            alert('Copié!');
        }
    </script>
</body>
</html>
