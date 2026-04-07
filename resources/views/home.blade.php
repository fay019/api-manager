@extends('layouts.app')

@section('title', 'Home')

@section('styles')
    <style>
        body {
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

        html.dark .version {
            background: rgba(255, 255, 255, 0.1);
        }

        .content {
            background: white;
            padding: 40px;
            min-height: 400px;
            transition: background-color 0.3s ease, color 0.3s ease;
        }

        html.dark .content {
            background: #1f2937;
            color: #f3f4f6;
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

        html.dark .section h2 {
            color: #818cf8;
            border-bottom-color: #818cf8;
        }

        .quick-nav {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .nav-card {
            background: white;
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
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }

        .nav-card:hover {
            box-shadow: 0 8px 20px rgba(102, 126, 234, 0.2);
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

        html.dark .nav-card {
            background: #374151;
            color: #f3f4f6;
            box-shadow: 0 2px 8px rgba(0,0,0,0.3);
        }

        html.dark .nav-card:hover {
            box-shadow: 0 8px 20px rgba(129, 140, 248, 0.2);
        }

        html.dark .nav-card h3 {
            color: #818cf8;
        }

        html.dark .nav-card p {
            color: #d1d5db;
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

        html.dark .getting-started {
            background: #374151;
            border-left-color: #818cf8;
        }

        html.dark .getting-started h3 {
            color: #f3f4f6;
        }

        html.dark .getting-started ol {
            color: #d1d5db;
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

        html.dark .endpoints-list {
            background: #374151;
            color: #d1d5db;
        }

        html.dark .endpoint {
            border-bottom-color: #4b5563;
        }

        html.dark .method.get {
            background: rgba(129, 140, 248, 0.2);
            color: #a5b4fc;
        }

        html.dark .method.post {
            background: rgba(168, 85, 247, 0.2);
            color: #d8b4fe;
        }

        html.dark .path {
            color: #a5b4fc;
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

        html.dark .feature {
            background: #374151;
            border-color: #4b5563;
        }

        html.dark .feature::before {
            color: #818cf8;
        }

        html.dark .feature h4 {
            color: #f3f4f6;
        }

        html.dark .feature p {
            color: #d1d5db;
        }

        .endpoint-code {
            color: #333;
        }

        html.dark .endpoint-code {
            color: #d1d5db;
        }

        .endpoint-note {
            margin-left: 20px;
            margin-top: 15px;
            color: #666;
        }

        html.dark .endpoint-note {
            color: #d1d5db;
        }

        @media (max-width: 768px) {
            .quick-nav {
                grid-template-columns: 1fr;
            }
        }
    </style>
@endsection

@section('content')
    <div class="container">
        <div class="header">
            <h1>🚀 API Manager</h1>
            <p>Production-Ready API Hub</p>
            <div class="version">Laravel 13 • Filament v5 • Shared Hosting Ready</div>
        </div>

        <div class="content">
            <!-- Quick Navigation -->
            <section class="section">
                <h2>Quick Navigation</h2>
                <div class="quick-nav">
                    @if(auth()->check() && auth()->user()->is_admin)
                        <a href="/admin" class="nav-card">
                            <div class="icon">📊</div>
                            <h3>Admin Panel</h3>
                            <p>Manage clients, keys, and content</p>
                        </a>
                    @endif

                    <a href="{{ route('docs.index') }}" class="nav-card">
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

                <div class="getting-started">
                    <h3>Test the API</h3>
                    <ol>
                        <li>Check the health endpoint:</li>
                    </ol>
                    <div class="endpoints-list">
                        <code class="endpoint-code">curl http://api-manager.test/api/v1/health</code>
                    </div>
                    <p class="endpoint-note">See <strong><a href="{{ route('docs.api') }}">API Docs</a></strong> for authentication and examples</p>
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
    </div>
@endsection