<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Documentation - API Manager</title>
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
            background: #f5f5f5;
            color: #333;
        }

        .navbar {
            background: white;
            border-bottom: 1px solid #eee;
            padding: 20px 40px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }

        .navbar a {
            color: #667eea;
            text-decoration: none;
            font-weight: 600;
            font-size: 1.1em;
        }

        .navbar a:hover {
            color: #764ba2;
        }

        .container {
            max-width: 1000px;
            margin: 0 auto;
            padding: 40px 20px;
        }

        h1 {
            color: #333;
            margin-bottom: 10px;
            font-size: 2em;
        }

        .subtitle {
            color: #999;
            margin-bottom: 40px;
            font-size: 1.1em;
        }

        .docs-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 30px;
            margin-bottom: 40px;
        }

        @media (max-width: 768px) {
            .docs-grid {
                grid-template-columns: 1fr;
            }
        }

        .doc-card {
            background: white;
            border-radius: 8px;
            padding: 30px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            transition: all 0.3s ease;
            text-decoration: none;
            color: inherit;
            display: block;
        }

        .doc-card:hover {
            box-shadow: 0 8px 20px rgba(102, 126, 234, 0.2);
            transform: translateY(-4px);
        }

        .doc-card h3 {
            color: #667eea;
            margin-bottom: 12px;
            font-size: 1.4em;
        }

        .doc-card p {
            color: #666;
            line-height: 1.6;
            margin-bottom: 15px;
        }

        .doc-card .icon {
            font-size: 2.5em;
            margin-bottom: 15px;
        }

        .doc-card .meta {
            color: #999;
            font-size: 0.9em;
            margin-top: 15px;
            padding-top: 15px;
            border-top: 1px solid #eee;
        }

        .back-link {
            color: #667eea;
            text-decoration: none;
            margin-bottom: 20px;
            display: inline-block;
        }

        .back-link:hover {
            text-decoration: underline;
        }

        .features {
            background: white;
            padding: 30px;
            border-radius: 8px;
            margin-top: 40px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }

        .features h3 {
            color: #333;
            margin-bottom: 20px;
        }

        .features ul {
            list-style: none;
        }

        .features li {
            padding: 8px 0;
            color: #666;
            padding-left: 25px;
            position: relative;
        }

        .features li:before {
            content: "→";
            position: absolute;
            left: 0;
            color: #667eea;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="navbar">
        <a href="/">← Back to Home</a>
    </div>

    <div class="container">
        <h1>📚 Documentation</h1>
        <p class="subtitle">Complete guides for the API Hub system</p>

        @if(empty($visibleDocs))
            <!-- Empty State - Friendly Welcome -->
            <div style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 8px; padding: 60px 30px; text-align: center; box-shadow: 0 8px 16px rgba(102, 126, 234, 0.2); margin: 40px 0; color: white;">
                <div style="font-size: 4rem; margin-bottom: 20px; animation: bounce 2s infinite;">📚</div>
                <h2 style="font-size: 1.8em; color: white; margin-bottom: 15px; font-weight: 600;">Documentation Coming Soon! 🎉</h2>
                <p style="color: rgba(255,255,255,0.95); font-size: 1.05em; margin-bottom: 25px; line-height: 1.8;">
                    We're preparing comprehensive documentation to help you get the most out of this API hub. <br>
                    <strong>Good things are on the way!</strong>
                </p>
                <p style="color: rgba(255,255,255,0.9); font-size: 0.95em; margin-bottom: 30px;">
                    In the meantime, check out the API health status@if(auth()->check() && auth()->user()->is_admin) or explore the admin panel@endif:
                </p>
                <div style="display: flex; gap: 12px; justify-content: center; flex-wrap: wrap;">
                    <a href="/api/v1/health" style="background: rgba(255,255,255,0.2); color: white; padding: 10px 20px; border-radius: 6px; text-decoration: none; font-weight: 600; border: 2px solid rgba(255,255,255,0.3); transition: all 0.3s;">
                        🏥 API Health
                    </a>
                    @if(auth()->check() && auth()->user()->is_admin)
                        <a href="/admin" style="background: white; color: #667eea; padding: 10px 20px; border-radius: 6px; text-decoration: none; font-weight: 600; transition: all 0.3s;">
                            ⚙️ Admin Panel
                        </a>
                    @endif
                </div>
                @if(auth()->check() && auth()->user()->is_admin)
                    <p style="color: rgba(255,255,255,0.7); font-size: 0.85em; margin-top: 30px; padding-top: 20px; border-top: 1px solid rgba(255,255,255,0.2);">
                        <strong>Administrator?</strong> Enable documentation in <a href="/admin" style="color: white; text-decoration: underline;">Documentation Settings</a>
                    </p>
                @endif
            </div>
        @else
            <div class="docs-grid">
                @foreach($visibleDocs as $docName)
                    @php
                        $metadata = \App\Services\DocumentationScanner::getMetadata($docName);
                    @endphp
                    <a href="{{ route('docs.show', $docName) }}" class="doc-card">
                        <div class="icon">{{ $metadata['icon'] }}</div>
                        <h3>{{ $metadata['label'] }}</h3>
                        <p>{{ $metadata['description'] }}</p>
                        <div class="meta">{{ ucfirst($docName) }}</div>
                    </a>
                @endforeach
            </div>
        @endif

        <div class="features">
            <h3>Key Resources</h3>
            <ul>
                @if(auth()->check() && auth()->user()->is_admin)
                    <li><strong>Admin Panel:</strong> <a href="/admin" style="color: #667eea;">/admin</a></li>
                @endif
                <li><strong>API Health:</strong> <a href="/api/v1/health" style="color: #667eea;">/api/v1/health</a></li>
                <li><strong>Promo Banner:</strong> <a href="/api/v1/promo/banner.json" style="color: #667eea;">/api/v1/promo/banner.json</a></li>
                <li><strong>Source Code:</strong> Check the project root directory</li>
            </ul>
        </div>
    </div>
</body>
</html>
