<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'API Manager') - {{ config('app.name') }}</title>

    <!-- Google AdSense -->
    <script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=ca-pub-8411028629670447"
        crossorigin="anonymous"></script>

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        @keyframes bounce {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-20px); }
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            color: #333;
            padding-bottom: 250px;
        }

        .admin-link {
            color: #ccc;
            text-decoration: none;
            font-size: 0.9em;
            transition: color 0.3s ease;
        }

        .admin-link:hover {
            color: #667eea;
        }

        @media (max-width: 768px) {
            .header {
                padding: 40px 20px !important;
            }

            .header h1 {
                font-size: 1.8em !important;
            }

            .content {
                padding: 20px !important;
            }
        }
    </style>

    @yield('styles')
</head>
<body>
    <div class="main-content">
        @yield('content')
    </div>

    <!-- Footer partagé -->
    <footer class="footer">
        <p><strong>API Manager</strong> • Production-ready API Hub for Laravel</p>
        <p>Environment: <strong>{{ config('app.env') }}</strong>
            {{ env('APP_DEBUG') === false ? '• Debug: OFF ✓' : '• Debug: ON (Development)' }}</p>
        <p style="margin-top: 15px; font-size: 0.85em; color: #999;">
            <a href="{{ route('docs.index') }}" style="color: #667eea; text-decoration: none;">All Documentation</a> •
            <a href="{{ route('docs.database') }}" style="color: #667eea; text-decoration: none;">Database Schema</a> •
            <a href="{{ route('docs.deployment') }}" style="color: #667eea; text-decoration: none;">Deployment Guide</a>
            @if(!auth()->check())
                • <a href="/admin/login" class="admin-link">admin</a>
            @endif
        </p>
    </footer>

    <style>
        .footer {
            background: transparent;
            border-radius: 0 0 12px 12px;
            padding: 30px 40px;
            text-align: center;
            color: #fff;
            font-size: 0.9em;
            border-top: none;
        }

        .footer p {
            margin: 5px 0;
        }

        .footer a {
            color: #fff;
            text-decoration: none;
        }

        .footer a:hover {
            text-decoration: underline;
        }

        .footer strong {
            color: #fff;
        }
    </style>

    @yield('scripts')
</body>
</html>