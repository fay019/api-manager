<?php
/**
 * 🚀 API Manager - Setup Bootstrap
 *
 * Completely independent of Laravel.
 * No dependencies, works even when everything is broken.
 *
 * Access: /setup.php
 */

// Force error display
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

header('Content-Type: text/html; charset=utf-8');

$basePath = dirname(__DIR__);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>API Manager - Setup</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: #333;
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }
        .container {
            background: white;
            border-radius: 8px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.1);
            max-width: 600px;
            width: 100%;
            padding: 40px;
            text-align: center;
        }
        h1 {
            color: #667eea;
            margin-bottom: 20px;
            font-size: 32px;
        }
        p {
            color: #666;
            margin-bottom: 30px;
            line-height: 1.6;
        }
        .buttons {
            display: flex;
            gap: 15px;
            flex-direction: column;
        }
        .button {
            display: inline-block;
            padding: 15px 30px;
            border-radius: 5px;
            text-decoration: none;
            font-weight: bold;
            font-size: 16px;
            transition: all 0.3s;
            border: none;
            cursor: pointer;
        }
        .button-primary {
            background: #667eea;
            color: white;
        }
        .button-primary:hover {
            background: #5568d3;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
        }
        .button-secondary {
            background: #f0f0f0;
            color: #333;
            border: 2px solid #667eea;
        }
        .button-secondary:hover {
            background: #667eea;
            color: white;
        }
        .step {
            background: #f9f9f9;
            padding: 20px;
            margin: 20px 0;
            border-radius: 5px;
            text-align: left;
        }
        .step h3 {
            color: #667eea;
            margin-bottom: 10px;
        }
        .step p {
            text-align: left;
            margin: 0;
        }
        code {
            background: #f0f0f0;
            padding: 2px 6px;
            border-radius: 3px;
            font-family: 'Monaco', monospace;
        }
        .success {
            color: #4CAF50;
            font-weight: bold;
        }
        .error {
            color: #f44336;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🚀 API Manager Setup</h1>
        <p>Your application needs to be set up. Choose an option:</p>

        <div class="buttons">
            <a href="/" class="button button-primary">
                Go to Web Setup Wizard →
            </a>
            <a href="/diagnostic.php" class="button button-secondary">
                View Diagnostics (Plain Text)
            </a>
            <a href="/install.php" class="button button-secondary">
                View Diagnostics (HTML)
            </a>
        </div>

        <div style="margin-top: 40px; border-top: 2px solid #eee; padding-top: 30px;">
            <h2 style="color: #333; font-size: 20px; margin-bottom: 20px;">📋 What to do:</h2>

            <div class="step">
                <h3>Step 1: Check System</h3>
                <p>Click "View Diagnostics" to check if your system is properly configured.</p>
            </div>

            <div class="step">
                <h3>Step 2: Bootstrap (if needed)</h3>
                <p>If there are issues, the diagnostics page will help you fix them:</p>
                <p>
                    • Creates required directories<br>
                    • Creates .env file<br>
                    • Creates database tables<br>
                    • Checks Composer dependencies
                </p>
            </div>

            <div class="step">
                <h3>Step 3: Run Setup Wizard</h3>
                <p>Click "Go to Web Setup Wizard" to complete the installation through the web interface.</p>
            </div>
        </div>

        <p style="margin-top: 40px; color: #999; font-size: 12px;">
            API Manager v1.0 | Running on PHP <?php echo phpversion(); ?>
        </p>
    </div>
</body>
</html>
