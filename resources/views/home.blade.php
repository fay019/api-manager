<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>API Manager - {{ config('app.name') }}</title>
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
            flex-direction: column;
            padding: 20px;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            width: 100%;
        }

        .header {
            background: white;
            border-radius: 12px 12px 0 0;
            padding: 60px 40px;
            text-align: center;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }

        .header h1 {
            font-size: 2.5em;
            margin-bottom: 10px;
            font-weight: 700;
        }

        .header p {
            font-size: 1.1em;
            opacity: 0.95;
            margin-bottom: 5px;
        }

        .version {
            display: inline-block;
            background: rgba(255, 255, 255, 0.2);
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.9em;
            margin-top: 10px;
        }

        .content {
            background: white;
            padding: 40px;
            min-height: 400px;
        }

        .section {
            margin-bottom: 50px;
        }

        .section h2 {
            color: #667eea;
            font-size: 1.8em;
            margin-bottom: 25px;
            padding-bottom: 10px;
            border-bottom: 2px solid #667eea;
        }

        .quick-nav {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .nav-card {
            background: white;
            border: 2px solid #f0f0f0;
            border-radius: 8px;
            padding: 25px;
            text-align: center;
            transition: all 0.3s ease;
            text-decoration: none;
            color: inherit;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 15px;
        }

        .nav-card:hover {
            border-color: #667eea;
            box-shadow: 0 5px 20px rgba(102, 126, 234, 0.15);
            transform: translateY(-3px);
        }

        .nav-card .icon {
            font-size: 2.5em;
        }

        .nav-card h3 {
            color: #667eea;
            font-size: 1.1em;
            margin: 0;
        }

        .nav-card p {
            color: #666;
            font-size: 0.9em;
            margin: 0;
            line-height: 1.5;
        }

        .getting-started {
            background: #f9f9f9;
            border-left: 4px solid #667eea;
            padding: 25px;
            border-radius: 6px;
            margin-bottom: 30px;
        }

        .getting-started h3 {
            color: #333;
            margin-bottom: 15px;
            font-size: 1.2em;
        }

        .getting-started ol {
            margin-left: 20px;
            color: #666;
            line-height: 2;
        }

        .getting-started li {
            margin-bottom: 10px;
        }

        .credentials-box {
            background: #fff3cd;
            border-left: 4px solid #ffc107;
            padding: 15px;
            border-radius: 4px;
            margin-top: 10px;
            font-size: 0.9em;
            color: #333;
        }

        .credentials-box strong {
            color: #333;
            display: block;
            margin-bottom: 8px;
        }

        .endpoints-list {
            background: #f5f5f5;
            padding: 20px;
            border-radius: 6px;
            font-family: 'Monaco', 'Menlo', 'Ubuntu Mono', monospace;
            font-size: 0.9em;
            line-height: 2;
            color: #333;
            margin-bottom: 30px;
        }

        .endpoint {
            margin-bottom: 10px;
            padding-bottom: 10px;
            border-bottom: 1px solid #ddd;
        }

        .endpoint:last-child {
            border-bottom: none;
            margin-bottom: 0;
            padding-bottom: 0;
        }

        .method {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 3px;
            font-weight: 600;
            margin-right: 10px;
            font-size: 0.85em;
        }

        .method.get {
            background: #e3f2fd;
            color: #1976d2;
        }

        .method.post {
            background: #f3e5f5;
            color: #7b1fa2;
        }

        .path {
            color: #667eea;
            font-weight: 600;
        }

        .features-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
        }

        .feature {
            background: white;
            border: 1px solid #f0f0f0;
            padding: 20px;
            border-radius: 6px;
            text-align: center;
        }

        .feature::before {
            content: "✓";
            display: block;
            font-size: 1.8em;
            color: #667eea;
            margin-bottom: 10px;
            font-weight: bold;
        }

        .feature h4 {
            color: #333;
            margin-bottom: 8px;
            font-size: 1em;
        }

        .feature p {
            color: #666;
            font-size: 0.9em;
            line-height: 1.5;
        }

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

        @media (max-width: 768px) {
            .header {
                padding: 40px 20px;
            }

            .header h1 {
                font-size: 1.8em;
            }

            .content {
                padding: 20px;
            }

            .quick-nav {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🚀 API Manager</h1>
            <p>Production-Ready API Hub</p>
            <div class="version">Laravel 12 • Filament v4 • Shared Hosting Ready</div>
        </div>

        <div class="content">
            <!-- Quick Navigation -->
            <section class="section">
                <h2>Quick Navigation</h2>
                <div class="quick-nav">
                    <a href="/admin" class="nav-card">
                        <div class="icon">📊</div>
                        <h3>Admin Panel</h3>
                        <p>Manage clients, keys, and content</p>
                    </a>

                    <a href="{{ route('docs.index') }}" class="nav-card" style="border-color: #667eea; box-shadow: 0 5px 20px rgba(102, 126, 234, 0.15);">
                        <div class="icon" style="font-size: 3em;">📚</div>
                        <h3 style="color: #764ba2; font-weight: 700;">All Documentation</h3>
                        <p style="font-weight: 600;">Browse all guides, API docs & schema</p>
                    </a>

                    <a href="/api/v1/promo/banner.json" class="nav-card">
                        <div class="icon">📡</div>
                        <h3>API Test</h3>
                        <p>View live promo banner</p>
                    </a>
                </div>
            </section>

            <!-- Getting Started -->
            <section class="section">
                <h2>Getting Started</h2>

                @if($showCredentials)
                    <div class="getting-started">
                        <h3>Admin Access</h3>
                        <ol>
                            <li>Go to <strong><a href="/admin">/admin</a></strong></li>
                            <li>Login with default credentials:</li>
                        </ol>
                        <div class="credentials-box">
                            <strong>Email:</strong> admin@moussouni.dev<br>
                            <strong>Password:</strong> password
                        </div>
                    </div>
                @endif

                <div class="getting-started">
                    <h3>Test the API</h3>
                    <ol>
                        <li>Check the health endpoint:</li>
                    </ol>
                    <div class="endpoints-list">
                        <code style="color: #333;">curl http://api-manager.test/api/v1/health</code>
                    </div>
                    <li style="margin-left: 20px; color: #666;">See <strong><a href="{{ route('docs.api') }}">API Docs</a></strong> for authentication and examples</li>
                </div>
            </section>

            <!-- API Endpoints -->
            <section class="section">
                <h2>Available Endpoints</h2>
                <div class="endpoints-list">
                    <div class="endpoint">
                        <span class="method get">GET</span>
                        <span class="path">/api/v1/health</span>
                        <br><small style="color: #999;">Check API status</small>
                    </div>
                    <div class="endpoint">
                        <span class="method get">GET</span>
                        <span class="path">/api/v1/promo/banner.json</span>
                        <br><small style="color: #999;">Get active promo banner</small>
                    </div>
                    <div class="endpoint">
                        <span class="method post">POST</span>
                        <span class="path">/api/v1/promo/event</span>
                        <br><small style="color: #999;">Track promo events</small>
                    </div>
                </div>
            </section>

            <!-- Core Features -->
            <section class="section">
                <h2>Core Features</h2>
                <div class="features-grid">
                    <div class="feature">
                        <h4>Modular Architecture</h4>
                        <p>Organized API structure with versioning</p>
                    </div>
                    <div class="feature">
                        <h4>Secure API Keys</h4>
                        <p>Bcrypt hashed authentication</p>
                    </div>
                    <div class="feature">
                        <h4>CORS Control</h4>
                        <p>Per-client CORS configuration</p>
                    </div>
                    <div class="feature">
                        <h4>Rate Limiting</h4>
                        <p>60 requests/minute by default</p>
                    </div>
                    <div class="feature">
                        <h4>Request Logging</h4>
                        <p>Track all API requests</p>
                    </div>
                    <div class="feature">
                        <h4>Event Tracking</h4>
                        <p>Monitor promo interactions</p>
                    </div>
                </div>
            </section>
        </div>

        <div class="footer">
            <p><strong>API Manager</strong> • Production-ready API Hub for Laravel</p>
            <p>Environment: <strong>{{ config('app.env') }}</strong>
                {{ env('APP_DEBUG') === false ? '• Debug: OFF ✓' : '• Debug: ON (Development)' }}</p>
            <p style="margin-top: 15px; font-size: 0.85em; color: #999;">
                <a href="{{ route('docs.index') }}" style="color: #667eea; text-decoration: none;">All Documentation</a> •
                <a href="{{ route('docs.database') }}" style="color: #667eea; text-decoration: none;">Database Schema</a> •
                <a href="{{ route('docs.deployment') }}" style="color: #667eea; text-decoration: none;">Deployment Guide</a>
            </p>
        </div>
    </div>
</body>
</html>