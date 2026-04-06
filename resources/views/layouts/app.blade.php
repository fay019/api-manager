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

        html, body {
            height: 100%;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            color: #333;
            display: flex;
            flex-direction: column;
            padding-bottom: 150px;
        }

        .main-content {
            flex: 1;
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
            background: white;
            border-radius: 0 0 12px 12px;
            padding: 30px 40px;
            text-align: center;
            color: #666;
            font-size: 0.9em;
            border-top: 1px solid #eee;
        }

        .footer p {
            margin: 5px 0;
        }
    </style>

    <script>
        // Detect Google AdSense ad height and adjust body padding
        function adjustFooterPadding() {
            let maxHeight = 0;

            // Method 1: Detect fixed/sticky positioned ads
            const allElements = document.querySelectorAll('[style*="position: fixed"], [style*="position: sticky"], [style*="bottom: 0"]');

            allElements.forEach(el => {
                const rect = el.getBoundingClientRect();
                const computedStyle = window.getComputedStyle(el);
                const position = computedStyle.position;

                // Check if element is at the bottom and has height
                if ((position === 'fixed' || position === 'sticky') && rect.bottom > 0 && rect.height > 0) {
                    // Don't count elements above the fold (like top nav)
                    if (rect.top > window.innerHeight / 2) {
                        maxHeight = Math.max(maxHeight, rect.height + 30);
                    }
                }
            });

            // Fallback to at least 100px padding
            const padding = Math.max(100, maxHeight);
            document.body.style.paddingBottom = padding + 'px';
            console.log('Ad height detected:', maxHeight, 'px | Body padding:', padding, 'px');
        }

        // Check on page load
        window.addEventListener('load', () => {
            setTimeout(adjustFooterPadding, 500);
        });

        // Monitor DOM changes (ads might load dynamically)
        const observer = new MutationObserver(adjustFooterPadding);
        observer.observe(document.body, { childList: true, subtree: true });

        // Also check periodically
        setInterval(adjustFooterPadding, 1000);
    </script>

    @yield('scripts')
</body>
</html>