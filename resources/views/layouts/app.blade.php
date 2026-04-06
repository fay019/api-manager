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

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            color: #333;
            padding-bottom: 250px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
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
        <p><strong>🚀 API Manager</strong> • Production-ready API Hub for Laravel</p>
        <p>Environment: <strong>{{ config('app.env') }}</strong>
            {{ env('APP_DEBUG') === false ? '• Debug: OFF ✓' : '• Debug: ON (Development)' }}</p>

        <p style="margin-top: 20px; font-size: 0.85em;">
            <a href="{{ route('docs.index') }}">📚 Documentation</a> •
            <a href="{{ route('docs.database') }}">🗄️ Database Schema</a> •
            <a href="{{ route('docs.deployment') }}">🚢 Deployment Guide</a>
            @if(!auth()->check())
                • <a href="/admin/login" class="admin-link">🔐 Admin</a>
            @endif
        </p>

        <p style="margin-top: 20px; font-size: 0.85em; padding-top: 15px; border-top: 1px solid rgba(255, 255, 255, 0.1);">
            📧 <span id="admin-email"></span>
        </p>

        <p style="margin-top: 20px; font-size: 0.85em;">
            🌐 <a href="https://moussouni.dev" target="_blank" rel="noopener noreferrer">Moussouni.dev</a> •
            🎬 <a href="https://kdrama.moussouni.dev" target="_blank" rel="noopener noreferrer">K-Drama</a>
        </p>

        <p style="margin-top: 20px; font-size: 0.8em; opacity: 0.9; padding-top: 15px; border-top: 1px solid rgba(255, 255, 255, 0.05);">
            © {{ date('Y') }} API Manager. All rights reserved.
        </p>
    </footer>

    <style>
        .footer {
            background: transparent;
            border-radius: 0 0 12px 12px;
            padding: 40px 40px;
            text-align: center;
            color: #fff;
            font-size: 0.9em;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            margin-top: 20px;
        }

        .footer p {
            margin: 8px 0;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
            line-height: 1.6;
        }

        .footer a {
            color: #fff;
            text-decoration: none;
            font-weight: 500;
            transition: opacity 0.3s ease;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
        }

        .footer a:hover {
            opacity: 0.8;
            text-decoration: underline;
        }

        .footer strong {
            color: #fff;
            font-weight: 600;
        }

        .admin-link {
            color: #fff;
            text-decoration: none;
            font-weight: 500;
            transition: opacity 0.3s ease;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
        }

        .admin-link:hover {
            opacity: 0.8;
        }
    </style>

    <script>
        // Protect email from bots - build dynamically
        document.addEventListener('DOMContentLoaded', function() {
            const adminEmail = document.getElementById('admin-email');
            if (adminEmail) {
                const email = 'admin' + '@' + 'moussouni.dev';
                adminEmail.innerHTML = '<a href="mailto:' + email + '">' + email + '</a>';
            }
        });
    </script>

    @yield('scripts')
</body>
</html>