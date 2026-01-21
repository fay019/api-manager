<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>API Manager - Diagnostic</title>
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
        .diagnostic-block {
            background: #1a1a1a;
            border: 1px solid #333;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 15px;
        }
        .diagnostic-block h3 {
            color: #667eea;
            margin-bottom: 10px;
        }
        .diagnostic-item {
            display: flex;
            justify-content: space-between;
            padding: 5px 0;
            border-bottom: 1px solid #333;
        }
        .diagnostic-item:last-child {
            border-bottom: none;
        }
        .diagnostic-key {
            color: #aaa;
        }
        .diagnostic-value {
            color: #81C784;
            font-weight: bold;
        }
        .diagnostic-value.error {
            color: #EF5350;
        }
        .footer {
            text-align: center;
            color: #888;
            font-size: 12px;
            margin-top: 20px;
        }
        a {
            color: #667eea;
            text-decoration: none;
        }
        a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🔧 API Manager - Diagnostic</h1>
            <p>System Status Check</p>
        </div>

        @foreach ($checks as $category => $value)
            <div class="diagnostic-block">
                <h3>{{ $category }}</h3>
                @if (is_array($value))
                    @foreach ($value as $key => $status)
                        <div class="diagnostic-item">
                            <span class="diagnostic-key">{{ $key }}</span>
                            <span class="diagnostic-value {{ $status === '✗' ? 'error' : '' }}">{{ $status }}</span>
                        </div>
                    @endforeach
                @else
                    <div class="diagnostic-item">
                        <span class="diagnostic-key">{{ $category }}</span>
                        <span class="diagnostic-value {{ strpos($value, '✗') !== false ? 'error' : '' }}">{{ $value }}</span>
                    </div>
                @endif
            </div>
        @endforeach

        <div class="footer">
            <p><a href="/install.php">← Back to Installation</a></p>
            <p>API Manager - Diagnostic | {{ date('Y-m-d H:i:s') }}</p>
        </div>
    </div>
</body>
</html>
