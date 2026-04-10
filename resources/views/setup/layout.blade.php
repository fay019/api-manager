<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', __('setup.layout.title')) - API Manager</title>
    @vite(['resources/css/app.css'])
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
        }

        html.dark, :root[data-theme="dark"] {
            --bg-gradient: linear-gradient(135deg, #111827 0%, #1f2937 100%);
            --card-bg: #1f2937;
            --text-main: #f9fafb;
            --text-muted: #9ca3af;
            --border: #374151;
        }

        body {
            font-family: 'Instrument Sans', ui-sans-serif, system-ui, sans-serif;
            margin: 0;
            padding: 0;
            background: #f5f7ff;
            background: var(--bg-gradient);
            color: #1f2937;
            color: var(--text-main);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .setup-container {
            width: 100%;
            max-width: 600px;
            padding: 2rem;
            box-sizing: border-box;
        }

        .setup-card {
            background: #ffffff;
            background: var(--card-bg);
            border-radius: 1.5rem;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.15);
            padding: 2.5rem;
            border: 1px solid #e5e7eb;
            border: 1px solid var(--border);
            transition: all 0.3s ease;
        }

        .setup-header {
            text-align: center;
            margin-bottom: 2.5rem;
        }

        .setup-header h1 {
            font-size: 2.25rem;
            font-weight: 800;
            margin: 0 0 0.5rem 0;
            background: linear-gradient(to right, #4f46e5, #818cf8);
            background: linear-gradient(to right, var(--primary), #818cf8);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .setup-header p {
            color: #6b7280;
            color: var(--text-muted);
            font-size: 1.125rem;
            margin: 0;
        }

        .setup-steps {
            display: flex;
            gap: 0.75rem;
            margin-bottom: 2.5rem;
        }

        .setup-step {
            flex: 1;
            height: 6px;
            background: #e5e7eb;
            background: var(--border);
            border-radius: 1rem;
            position: relative;
            overflow: hidden;
        }

        .setup-step.active {
            background: #4f46e5;
            background: var(--primary);
            box-shadow: 0 0 10px rgba(79, 70, 229, 0.3);
        }

        .setup-step.completed {
            background: #10b981;
            background: var(--success);
        }

        .setup-body h2 {
            font-size: 1.2rem;
            font-weight: 700;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-group label {
            display: block;
            font-size: 0.875rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
            color: #1f2937;
            color: var(--text-main);
        }

        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 0.75rem 1rem;
            background: #ffffff;
            background: var(--card-bg);
            color: #1f2937;
            color: var(--text-main);
            border: 2px solid #e5e7eb;
            border: 2px solid var(--border);
            border-radius: 0.75rem;
            font-size: 1rem;
            box-sizing: border-box;
            transition: all 0.2s;
        }

        .form-group input:focus,
        .form-group select:focus {
            outline: none;
            border-color: #4f46e5;
            border-color: var(--primary);
            box-shadow: 0 0 0 4px rgba(79, 70, 229, 0.1);
        }

        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 0.875rem 1.5rem;
            border-radius: 0.75rem;
            font-weight: 700;
            font-size: 1rem;
            cursor: pointer;
            transition: all 0.2s;
            text-decoration: none;
            border: none;
            gap: 0.5rem;
        }

        .btn-primary {
            background: #4f46e5;
            background: var(--primary);
            color: white;
            width: 100%;
        }

        .btn-primary:hover {
            background: #4338ca;
            background: var(--primary-hover);
            transform: translateY(-1px);
            box-shadow: 0 10px 15px -3px rgba(79, 70, 229, 0.4);
        }

        .btn-secondary {
            background: #e5e7eb;
            background: var(--border);
            color: #1f2937;
            color: var(--text-main);
        }

        .btn-secondary:hover {
            background: #6b7280;
            background: var(--text-muted);
            color: white;
        }

        .alert {
            padding: 1rem 1.25rem;
            border-radius: 1rem;
            margin-bottom: 1.5rem;
            font-size: 0.875rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            border-left: 4px solid transparent;
        }

        .alert-success {
            background: rgba(16, 185, 129, 0.1);
            color: #10b981;
            color: var(--success);
            border-left-color: #10b981;
            border-left-color: var(--success);
        }

        .alert-error {
            background: rgba(239, 68, 68, 0.1);
            color: #ef4444;
            color: var(--error);
            border-left-color: #ef4444;
            border-left-color: var(--error);
        }

        .flex { display: flex; }
        .justify-between { justify-content: space-between; }
        .items-center { align-items: center; }
        .mb-0 { margin-bottom: 0; }
        .mt-4 { margin-top: 1rem; }
        .gap-2 { gap: 0.5rem; }
        .text-xs { font-size: 0.75rem; }
        .text-sm { font-size: 0.875rem; }
        .font-semibold { font-weight: 600; }
        .text-gray-500 { color: #6b7280; color: var(--text-muted); }
        .uppercase { text-transform: uppercase; }
        .tracking-wider { letter-spacing: 0.05em; }

        .alert-info {
            background: #dbeafe;
            color: #1e40af;
            border: 1px solid #bfdbfe;
        }

        .info-box {
            background: #f3f4f6;
            border-left: 4px solid #667eea;
            padding: 12px 16px;
            border-radius: 4px;
            margin-bottom: 20px;
            font-size: 13px;
            color: #333;
        }

        .loading {
            display: none;
            text-align: center;
            padding: 20px;
        }

        .spinner {
            display: inline-block;
            width: 20px;
            height: 20px;
            border: 3px solid rgba(102, 126, 234, 0.3);
            border-top-color: #667eea;
            border-radius: 50%;
            animation: spin 0.8s linear infinite;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        .loading.active {
            display: block;
        }

        .password-group {
            position: relative;
        }

        .password-toggle {
            position: absolute;
            right: 12px;
            top: 35px;
            cursor: pointer;
            color: #667eea;
            font-size: 18px;
            user-select: none;
        }

        html.dark .setup-step {
            background: #4b5563;
        }

        html.dark .form-group label {
            color: #f3f4f6;
        }

        html.dark .form-group input,
        html.dark .form-group select,
        html.dark .form-group textarea {
            background: #1f2937;
            border-color: #4b5563;
            color: #f3f4f6;
        }

        html.dark .form-group input:focus,
        html.dark .form-group select:focus,
        html.dark .form-group textarea:focus {
            border-color: #818cf8;
            box-shadow: 0 0 0 3px rgba(129, 140, 248, 0.1);
        }

        html.dark .form-group input::placeholder,
        html.dark .form-group textarea::placeholder {
            color: #9ca3af;
        }

        html.dark .btn-secondary {
            background: #4b5563;
            color: #f3f4f6;
        }

        html.dark .btn-secondary:hover {
            background: #6b7280;
        }

        html.dark .alert-success {
            background: rgba(34, 197, 94, 0.15);
            color: #86efac;
            border-color: rgba(34, 197, 94, 0.3);
        }

        html.dark .alert-error {
            background: rgba(239, 68, 68, 0.15);
            color: #fca5a5;
            border-color: rgba(239, 68, 68, 0.3);
        }

        html.dark .alert-info {
            background: rgba(59, 130, 246, 0.15);
            color: #93c5fd;
            border-color: rgba(59, 130, 246, 0.3);
        }

        html.dark .info-box {
            background: #1f2937;
            border-left-color: #818cf8;
            color: #d1d5db;
        }

        html.dark .spinner {
            border-color: rgba(129, 140, 248, 0.3);
            border-top-color: #818cf8;
        }

        html.dark .password-toggle {
            color: #818cf8;
        }
    </style>
</head>
    <body>
        <div class="setup-container">
            <div class="setup-card">
                <div class="setup-header">
                    <h1>API Manager</h1>
                    <p>{{ __('setup.layout.subtitle') }}</p>
                </div>
                @yield('content')
            </div>
        </div>

    <script>
        // Theme management
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

        // Toggle password visibility
        document.querySelectorAll('.password-toggle').forEach(toggle => {
            toggle.addEventListener('click', function() {
                const input = this.previousElementSibling;
                const type = input.type === 'password' ? 'text' : 'password';
                input.type = type;
                this.textContent = type === 'password' ? '👁️' : '👁️‍🗨️';
            });
        });
    </script>

    @yield('scripts')
</body>
</html>
