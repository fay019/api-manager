<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Erreur - @yield('title')</title>
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
        .error-code {
            font-size: 72px;
            font-weight: bold;
            color: #dc2626;
            margin-bottom: 10px;
        }
        .error-title {
            font-size: 24px;
            font-weight: 600;
            color: #111827;
            margin-bottom: 15px;
        }
        .error-description {
            font-size: 14px;
            color: #6b7280;
            line-height: 1.6;
            margin-bottom: 30px;
        }
        .actions {
            display: flex;
            gap: 10px;
            justify-content: center;
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

        html.dark .container {
            background: #374151;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.5);
        }

        html.dark .error-code {
            color: #f87171;
        }

        html.dark .error-title {
            color: #f3f4f6;
        }

        html.dark .error-description {
            color: #d1d5db;
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
        <div class="error-code">@yield('code')</div>
        <div class="error-title">@yield('title')</div>
        <div class="error-description">@yield('message')</div>
        <div class="actions">
            <a href="{{ url('/') }}" class="btn btn-primary">{{ __('errors.' . ($exception->getStatusCode() ?? '500') . '.back') }}</a>
            <a href="{{ url()->previous() }}" class="btn btn-secondary">{{ __('errors.' . ($exception->getStatusCode() ?? '500') . '.back') }}</a>
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
