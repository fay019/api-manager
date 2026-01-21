<?php
/**
 * 🚀 API Manager - Installation Bootstrap
 *
 * This file is completely independent of Laravel.
 * It can run even if Laravel crashes.
 *
 * Access: /install.php
 */

// Start output buffering to handle headers safely
ob_start();

// Error reporting (don't display in headers)
ini_set('display_errors', '0');
error_reporting(E_ALL);

// Set error and exception handlers to log (not display)
set_error_handler(function($errno, $errstr, $errfile, $errline) {
    global $output, $errors;
    $msg = "[$errno] $errstr in $errfile:$errline";
    $errors[] = $msg;
    $output[] = [
        'time' => date('H:i:s'),
        'message' => "⚠️ PHP Error: " . $msg,
        'type' => 'error'
    ];
    return true;
});

set_exception_handler(function($exception) {
    global $output, $errors;
    $msg = "Exception: " . $exception->getMessage();
    $errors[] = $msg;
    $output[] = [
        'time' => date('H:i:s'),
        'message' => "❌ " . $msg,
        'type' => 'error'
    ];
});

// Try to set cache headers, ignore if already sent
if (!headers_sent()) {
    header('Cache-Control: no-cache, no-store, must-revalidate');
    header('Pragma: no-cache');
    header('Expires: 0');
    header('Content-Type: text/html; charset=utf-8');
}

$basePath = dirname(__DIR__);
$output = [];
$errors = [];
$success = false;
$startTime = microtime(true);

// Simple logging function
function log_step($message) {
    global $output;
    $output[] = [
        'time' => date('H:i:s'),
        'message' => $message,
        'type' => 'info'
    ];
}

function log_error($message) {
    global $output, $errors;
    $errors[] = $message;
    $output[] = [
        'time' => date('H:i:s'),
        'message' => $message,
        'type' => 'error'
    ];
}

function log_success($message) {
    global $output;
    $output[] = [
        'time' => date('H:i:s'),
        'message' => $message,
        'type' => 'success'
    ];
}

// Start installation
log_step('🚀 Installation Bootstrap Started');
log_step('PHP Version: ' . phpversion());
log_step('PHP SAPI: ' . php_sapi_name());
log_step('Base Path: ' . $basePath);
log_step('Current Working Directory: ' . getcwd());
log_step('Script Filename: ' . __FILE__);
log_step('Server Software: ' . ($_SERVER['SERVER_SOFTWARE'] ?? 'unknown'));
log_step('Loaded PHP Extensions: ' . implode(', ', array_slice(get_loaded_extensions(), 0, 10)) . '...');

// Check file system permissions
$testFile = $basePath . '/.install-test-' . time() . '.txt';
if (@file_put_contents($testFile, 'test')) {
    log_success('File system is writable');
    @unlink($testFile);
} else {
    log_error('⚠️ File system is NOT writable - permissions issues expected');
}

// Step 1: Create directories
log_step('📁 Creating required directories...');
$directories = [
    'storage',
    'storage/framework',
    'storage/framework/cache',
    'storage/framework/sessions',
    'storage/framework/views',
    'storage/logs',
    'storage/app',
    'bootstrap/cache',
    'database',
];

foreach ($directories as $dir) {
    $path = $basePath . '/' . $dir;
    if (!is_dir($path)) {
        if (@mkdir($path, 0755, true)) {
            log_success("Created: $dir");
        } else {
            log_error("Failed to create: $dir");
        }
    } else {
        log_step("Already exists: $dir");
    }
}

// Step 2: Create .env
log_step('📝 Setting up .env file...');
$envPath = $basePath . '/.env';
$envExamplePath = $basePath . '/.env.example';

if (!file_exists($envPath)) {
    if (file_exists($envExamplePath)) {
        if (@copy($envExamplePath, $envPath)) {
            log_success('.env created from .env.example');
        } else {
            log_error('Failed to create .env from .env.example');
        }
    } else {
        log_error('.env.example not found!');
    }
} else {
    log_step('.env already exists');
}

// Step 3: Check Composer
log_step('🎵 Checking Composer...');
$vendorPath = $basePath . '/vendor';
$composerPath = $basePath . '/composer.json';

if (!is_dir($vendorPath)) {
    log_step('vendor/ directory not found, attempting composer install...');

    if (!file_exists($composerPath)) {
        log_error('composer.json not found!');
    } else {
        log_step('Running: composer install');

        // Try to run composer
        $output_var = [];
        $return_var = 0;

        // Try different composer paths
        $composer_cmd = null;
        $possible_paths = [
            '/usr/local/bin/composer',
            '/usr/bin/composer',
            'composer',
            'php composer.phar',
        ];

        foreach ($possible_paths as $cmd) {
            $test_output = [];
            exec("{$cmd} --version 2>&1", $test_output, $test_code);
            if ($test_code === 0) {
                $composer_cmd = $cmd;
                log_success("Found Composer: {$cmd}");
                break;
            }
        }

        if ($composer_cmd) {
            $cmd = "cd {$basePath} && {$composer_cmd} install --no-interaction 2>&1";
            log_step("Executing: {$cmd}");

            $result = shell_exec($cmd);
            if ($result) {
                log_step('Composer output: ' . substr($result, 0, 500));
            }

            if (is_dir($vendorPath)) {
                log_success('✅ Composer install completed successfully');
            } else {
                log_error('❌ vendor/ directory still not found after composer install');
            }
        } else {
            log_error('⚠️ Composer not found in common locations');
            log_error('Please run: composer install');
        }
    }
} else {
    log_success('vendor/ directory exists');
}

// Step 4: Create SQLite database
log_step('🗄️ Preparing database...');
$dbPath = $basePath . '/database/database.sqlite';

if (!file_exists($dbPath)) {
    if (@touch($dbPath)) {
        log_success('SQLite database file created');

        // Create sessions table
        try {
            $pdo = new PDO("sqlite:{$dbPath}");
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            $pdo->exec('CREATE TABLE IF NOT EXISTS sessions (
                id TEXT PRIMARY KEY,
                user_id INTEGER,
                ip_address VARCHAR(45),
                user_agent TEXT,
                payload LONGTEXT,
                last_activity INTEGER
            )');

            $pdo->exec('CREATE TABLE IF NOT EXISTS cache (
                key TEXT PRIMARY KEY,
                value LONGTEXT,
                expiration INTEGER
            )');

            $pdo->exec('CREATE TABLE IF NOT EXISTS cache_locks (
                key TEXT PRIMARY KEY,
                owner TEXT,
                expiration INTEGER
            )');

            $pdo->exec('CREATE TABLE IF NOT EXISTS jobs (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                queue TEXT,
                payload LONGTEXT,
                attempts INTEGER,
                reserved_at INTEGER,
                available_at INTEGER,
                created_at INTEGER
            )');

            $pdo->exec('CREATE TABLE IF NOT EXISTS failed_jobs (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                connection TEXT,
                queue TEXT,
                payload LONGTEXT,
                exception LONGTEXT,
                failed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )');

            log_success('Required tables created (sessions, cache, cache_locks, jobs, failed_jobs)');
        } catch (Exception $e) {
            log_error('Failed to create sessions table: ' . $e->getMessage());
        }
    } else {
        log_error('Failed to create database file');
    }
} else {
    log_step('Database file already exists');
}

// Step 5: Check Laravel bootstrap
log_step('🔧 Checking Laravel bootstrap...');
$bootstrapApp = $basePath . '/bootstrap/app.php';
$autoload = $basePath . '/vendor/autoload.php';

if (file_exists($bootstrapApp)) {
    log_success('bootstrap/app.php exists');
} else {
    log_error('bootstrap/app.php not found');
}

if (file_exists($autoload)) {
    log_success('vendor/autoload.php exists');
} else {
    log_error('vendor/autoload.php not found');
}

// Determine success
$success = count($errors) === 0 && is_dir($vendorPath) && file_exists($envPath);

$elapsedTime = microtime(true) - $startTime;
log_step('');
log_step(sprintf('Execution time: %.2f seconds', $elapsedTime));

if ($success) {
    log_success('✅ Installation bootstrap completed successfully!');
    log_step('You can now visit: ' . $_SERVER['HTTP_HOST']);
} else {
    log_error('❌ Installation has issues. Please fix the errors above.');
}

// Write diagnostic log to file for server debugging
try {
    $logDir = $basePath . '/storage/logs';
    if (!is_dir($logDir)) {
        @mkdir($logDir, 0755, true);
    }

    $logFile = $logDir . '/install-diagnostic.log';
    $logContent = date('Y-m-d H:i:s') . " - Installation Bootstrap Diagnostic\n";
    $logContent .= "URL: " . ($_SERVER['HTTP_HOST'] ?? 'unknown') . "\n";
    $logContent .= "Success: " . ($success ? 'yes' : 'no') . "\n";
    $logContent .= "Execution Time: " . sprintf('%.2f seconds', $elapsedTime) . "\n";
    $logContent .= "Errors: " . count($errors) . "\n";
    $logContent .= "PHP Version: " . phpversion() . "\n";
    $logContent .= "\n--- Log Output ---\n";

    foreach ($output as $log) {
        $logContent .= "[{$log['time']}] [{$log['type']}] {$log['message']}\n";
    }

    file_put_contents($logFile, $logContent, FILE_APPEND);
} catch (Exception $e) {
    // Silently ignore log file write errors
}

?>
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
        .spinner {
            display: inline-block;
            width: 10px;
            height: 10px;
            border: 2px solid #f3f3f3;
            border-top: 2px solid #667eea;
            border-radius: 50%;
            animation: spin 0.6s linear infinite;
        }
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🚀 API Manager Installation Bootstrap</h1>
            <p>Automatic environment setup</p>
            <span class="status <?php echo $success ? 'success' : 'error'; ?>">
                <?php echo $success ? '✅ Ready' : '⚠️ Needs Attention'; ?>
            </span>
        </div>

        <div class="logs">
            <?php foreach ($output as $log): ?>
                <div class="log-entry <?php echo $log['type']; ?>">
                    <span class="log-time"><?php echo $log['time']; ?></span>
                    <?php echo htmlspecialchars($log['message']); ?>
                </div>
            <?php endforeach; ?>
        </div>

        <?php if ($success): ?>
            <div class="next-steps">
                <h3>✅ Bootstrap Complete!</h3>
                <p>Your application is ready. Click the link below to start the installation wizard:</p>
                <ol>
                    <li><a href="/" style="color: #667eea;">Visit your application</a> to launch the Setup Wizard</li>
                    <li>Follow the on-screen steps to complete installation</li>
                    <li>Access the admin panel at <code>/admin</code></li>
                </ol>
            </div>
        <?php else: ?>
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
        <?php endif; ?>

        <div class="footer">
            <p>API Manager - Installation Bootstrap | <?php echo date('Y-m-d H:i:s'); ?></p>
            <p><a href="/install.php" style="color: #667eea;">Refresh this page</a> to check status again</p>
        </div>
    </div>
</body>
</html>
