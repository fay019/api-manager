<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>API Manager - Installation Bootstrap</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Monaco', 'Courier New', monospace;
            background: #0f0f0f;
            color: #e0e0e0;
            padding: 20px;
            line-height: 1.6;
        }
        .container {
            max-width: 900px;
            margin: 0 auto;
        }
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            text-align: center;
        }
        .header h1 {
            font-size: 24px;
            margin-bottom: 10px;
        }
        .status {
            display: inline-block;
            padding: 8px 16px;
            border-radius: 4px;
            font-weight: bold;
            margin-top: 10px;
        }
        .status.success {
            background: #4CAF50;
            color: white;
        }
        .status.error {
            background: #f44336;
            color: white;
        }
        .logs {
            background: #1a1a1a;
            border: 1px solid #333;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 20px;
            max-height: 500px;
            overflow-y: auto;
        }
        .log-entry {
            margin-bottom: 8px;
            padding: 8px;
            border-left: 3px solid #666;
            border-radius: 3px;
        }
        .log-entry.info {
            border-left-color: #667eea;
            color: #90CAF9;
        }
        .log-entry.success {
            border-left-color: #4CAF50;
            color: #81C784;
        }
        .log-entry.error {
            border-left-color: #f44336;
            color: #EF5350;
        }
        .log-time {
            color: #888;
            font-size: 12px;
            margin-right: 10px;
        }
        .next-steps {
            background: #1a1a1a;
            border-left: 4px solid #667eea;
            padding: 15px;
            border-radius: 4px;
            margin-bottom: 20px;
        }
        .next-steps h3 {
            color: #667eea;
            margin-bottom: 10px;
        }
        .next-steps ol {
            margin-left: 20px;
        }
        .next-steps li {
            margin-bottom: 8px;
        }
        .footer {
            text-align: center;
            color: #888;
            font-size: 12px;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🚀 API Manager Installation Bootstrap</h1>
            <p>Automatic environment setup</p>
            <span class="status {{ $success ? 'success' : 'error' }}">
                {{ $success ? '✅ Ready' : '⚠️ Needs Attention' }}
            </span>
        </div>

        <div class="logs">
            @foreach ($output as $log)
                <div class="log-entry {{ $log['type'] }}">
                    <span class="log-time">{{ $log['time'] }}</span>
                    {{ $log['message'] }}
                </div>
            @endforeach
        </div>

        @if ($success)
            <div class="next-steps">
                <h3>✅ Bootstrap Complete!</h3>
                <p>Your application is ready. Click the link below to start the installation wizard:</p>
                <ol>
                    <li><a href="/" style="color: #667eea;">Visit your application</a> to launch the Setup Wizard</li>
                    <li>Follow the on-screen steps to complete installation</li>
                    <li>Access the admin panel at <code>/admin</code></li>
                </ol>
            </div>
        @else
            <div class="next-steps">
                <h3>❌ Bootstrap Issues Detected</h3>
                <p>Please fix the errors shown above. Common solutions:</p>
                <ol>
                    <li>
                        <strong>If "Composer not found":</strong>
                        <pre style="background: #0f0f0f; padding: 10px; margin-top: 5px; overflow-x: auto;">composer install</pre>
                    </li>
                    <li>
                        <strong>If "Failed to create" directory:</strong>
                        <pre style="background: #0f0f0f; padding: 10px; margin-top: 5px; overflow-x: auto;">chmod -R 755 storage bootstrap/cache</pre>
                    </li>
                    <li>
                        <strong>If ".env.example not found":</strong>
                        Check that you cloned the full repository with all files
                    </li>
                    <li>
                        <strong>For other issues:</strong>
                        Check the logs above and ensure all file permissions are correct
                    </li>
                </ol>
            </div>
        @endif

        <div class="footer">
            <p>API Manager - Installation Bootstrap | {{ date('Y-m-d H:i:s') }}</p>
            <p><a href="/install.php" style="color: #667eea;">Refresh this page</a> to check status again</p>
        </div>
    </div>
</body>
</html>
