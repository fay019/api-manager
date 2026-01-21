<?php
/**
 * 🔧 API Manager - Quick Diagnostic
 *
 * Ultra-simple text-based diagnostic
 * Works even if HTML rendering fails
 *
 * Access: /diagnostic.php
 */

// Force plain text
header('Content-Type: text/plain; charset=utf-8');

// Force error display
ini_set('display_errors', '1');
error_reporting(E_ALL);

echo "=== API Manager Diagnostic ===\n";
echo "Date: " . date('Y-m-d H:i:s') . "\n\n";

$basePath = dirname(__DIR__);

// 1. Basic checks
echo "1. Basic Environment:\n";
echo "   PHP Version: " . phpversion() . "\n";
echo "   SAPI: " . php_sapi_name() . "\n";
echo "   Base Path: " . $basePath . "\n";
echo "   CWD: " . getcwd() . "\n";
echo "   Script: " . __FILE__ . "\n\n";

// 2. Directory checks
echo "2. Required Directories:\n";
$dirs = [
    'storage',
    'storage/framework',
    'storage/framework/cache',
    'storage/logs',
    'bootstrap/cache',
    'database',
];

foreach ($dirs as $dir) {
    $path = $basePath . '/' . $dir;
    $exists = is_dir($path) ? "✓" : "✗";
    echo "   $exists $dir\n";
    if (!is_dir($path)) {
        if (@mkdir($path, 0755, true)) {
            echo "     → Created\n";
        } else {
            echo "     → Failed to create\n";
        }
    }
}
echo "\n";

// 3. File checks
echo "3. Critical Files:\n";
$files = [
    '.env' => $basePath . '/.env',
    '.env.example' => $basePath . '/.env.example',
    'composer.json' => $basePath . '/composer.json',
    'vendor/autoload.php' => $basePath . '/vendor/autoload.php',
    'bootstrap/app.php' => $basePath . '/bootstrap/app.php',
];

foreach ($files as $name => $path) {
    $exists = file_exists($path) ? "✓" : "✗";
    echo "   $exists $name\n";
}

// Create .env if missing
if (!file_exists($basePath . '/.env') && file_exists($basePath . '/.env.example')) {
    echo "\n   → Copying .env.example to .env...\n";
    if (@copy($basePath . '/.env.example', $basePath . '/.env')) {
        echo "     ✓ Created .env\n";
    } else {
        echo "     ✗ Failed to create .env\n";
    }
}

echo "\n";

// 4. Database check
echo "4. Database Status:\n";
$dbPath = $basePath . '/database/database.sqlite';
if (file_exists($dbPath)) {
    echo "   ✓ database.sqlite exists\n";
} else {
    echo "   ✗ database.sqlite missing\n";
    if (@touch($dbPath)) {
        echo "     → Created database file\n";
    }
}

echo "\n";

// 5. Permissions check
echo "5. Filesystem Permissions:\n";
$testFile = $basePath . '/.write-test-' . time() . '.txt';
if (@file_put_contents($testFile, 'test')) {
    echo "   ✓ Base path is writable\n";
    @unlink($testFile);
} else {
    echo "   ✗ Base path is NOT writable\n";
}

if (is_writable($basePath . '/storage')) {
    echo "   ✓ storage/ is writable\n";
} else {
    echo "   ✗ storage/ is NOT writable\n";
}

echo "\n";

// 6. Composer status
echo "6. Composer Status:\n";
if (is_dir($basePath . '/vendor')) {
    echo "   ✓ vendor/ directory exists\n";
} else {
    echo "   ✗ vendor/ directory missing\n";
    echo "   → Run: composer install\n";
}

echo "\n";

// 7. Check for errors
echo "7. Diagnostics Complete\n";
echo "   Check this output against the HTML version at /install.php\n";
echo "   Check logs at storage/logs/install-diagnostic.log\n";

echo "\n=== End Diagnostic ===\n";
