<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('errors.500.title') }} - {{ config('app.name') }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            transition: background-color 0.3s ease;
        }

        html.dark body {
            background: linear-gradient(135deg, #1f2937 0%, #111827 100%);
        }

        .container {
            background: white;
            border-radius: 8px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            max-width: 600px;
            width: 100%;
            padding: 40px;
            text-align: center;
        }

        html.dark .container {
            background: #374151;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.5);
        }

        .error-code {
            font-size: 72px;
            font-weight: bold;
            color: #dc2626;
            margin-bottom: 10px;
        }

        html.dark .error-code {
            color: #f87171;
        }

        .error-title {
            font-size: 24px;
            font-weight: 600;
            color: #111827;
            margin-bottom: 15px;
        }

        html.dark .error-title {
            color: #f3f4f6;
        }

        .error-description {
            font-size: 14px;
            color: #6b7280;
            line-height: 1.6;
            margin-bottom: 25px;
        }

        html.dark .error-description {
            color: #d1d5db;
        }

        .log-section {
            background: #f3f4f6;
            border: 1px solid #d1d5db;
            border-radius: 6px;
            padding: 15px;
            margin-bottom: 20px;
            max-height: 300px;
            overflow-y: auto;
            font-family: 'Courier New', monospace;
            font-size: 12px;
            color: #374151;
            line-height: 1.4;
        }

        html.dark .log-section {
            background: #4b5563;
            border-color: #6b7280;
            color: #e5e7eb;
        }

        .log-section h4 {
            color: #111827;
            margin-bottom: 10px;
            font-size: 13px;
        }

        html.dark .log-section h4 {
            color: #f3f4f6;
        }

        .log-entry {
            margin-bottom: 10px;
            padding: 8px;
            background: white;
            border-left: 3px solid #dc2626;
            padding-left: 12px;
        }

        html.dark .log-entry {
            background: #374151;
            border-color: #f87171;
            color: #e5e7eb;
        }

        .info-box {
            background: #dbeafe;
            border: 1px solid #93c5fd;
            border-radius: 6px;
            padding: 15px;
            margin-bottom: 20px;
            color: #1e40af;
            font-size: 13px;
        }

        html.dark .info-box {
            background: #1e3a8a;
            border-color: #3b82f6;
            color: #93c5fd;
        }

        .info-box strong {
            display: block;
            margin-bottom: 5px;
        }

        .info-box-danger {
            background: #fef2f2;
            border-color: #fecaca;
            color: #991b1b;
        }

        html.dark .info-box-danger {
            background: #7f1d1d;
            border-color: #dc2626;
            color: #fecaca;
        }

        .actions {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 6px;
            text-decoration: none;
            font-size: 14px;
            cursor: pointer;
            transition: all 0.3s;
        }

        .btn-primary {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
        }

        .btn-primary:hover {
            opacity: 0.9;
        }

        .btn-secondary {
            background: #e5e7eb;
            color: #111827;
        }

        .btn-secondary:hover {
            background: #d1d5db;
        }

        html.dark .btn-secondary {
            background: #4b5563;
            color: #f3f4f6;
        }

        html.dark .btn-secondary:hover {
            background: #6b7280;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="error-code">500</div>
        <div class="error-title">{{ __('errors.500.title') }}</div>
        <div class="error-description">
            {{ __('errors.500.message') }}
        </div>

        @if(config('app.debug'))
            <div class="info-box">
                <strong>⚠️ {{ __('errors.500.debug_enabled') }}</strong>
                {{ __('errors.500.recent_logs') }}
                <br><small style="opacity: 0.8;">{{ __('errors.500.full_log') }}</small>
            </div>

            <div class="log-section">
                <h4>📋 {{ __('errors.500.recent_logs') }} ({{ __('errors.500.full_log') }})</h4>
                @php
                    $logFile = storage_path('logs/laravel.log');
                    if (file_exists($logFile)) {
                        $lines = array_reverse(file($logFile));
                        $displayed = 0;
                        foreach ($lines as $line) {
                            if ($displayed >= 20) break;
                            $trimmed = trim($line);
                            if (!empty($trimmed)) {
                                echo '<div class="log-entry">' . htmlspecialchars($trimmed) . '</div>';
                                $displayed++;
                            }
                        }
                    } else {
                        echo '<p style="opacity: 0.7;">'. __('errors.500.no_logs') .'</p>';
                    }
                @endphp
            </div>

            <div class="info-box info-box-danger">
                <strong>📁 {{ __('errors.500.full_log') }}</strong>
                {{ $logFile ?? storage_path('logs/laravel.log') }}
                <br><small style="opacity: 0.8;">{{ __('errors.500.recent_logs') }}</small>
            </div>
        @else
            <div class="info-box">
                <strong>💡 {{ __('errors.500.enable_debug') }}</strong>
                {{ __('errors.500.debug_enabled') }}
            </div>
        @endif

        <div class="actions">
            <a href="{{ url('/') }}" class="btn btn-primary">← {{ __('errors.500.back') }}</a>
            <a href="{{ url()->previous() }}" class="btn btn-secondary">← {{ __('errors.500.back') }}</a>
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
