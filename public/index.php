<?php

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// ⭐ Prepare application BEFORE anything else
// This prevents "No environment file" and directory permission errors
ensureApplicationReady();

// Determine if the application is in maintenance mode...
if (file_exists($maintenance = __DIR__.'/../storage/framework/maintenance.php')) {
    require $maintenance;
}

// Register the Composer autoloader...
require __DIR__.'/../vendor/autoload.php';

/**
 * Prepare the application for bootstrap.
 * This runs BEFORE Laravel loads, preventing critical errors.
 */
function ensureApplicationReady(): void
{
    $basePath = dirname(__DIR__);

    // 1. Ensure required directories exist
    ensureDirectoriesExist($basePath);

    // 2. Ensure .env file exists
    ensureEnvFileExists($basePath);
}

/**
 * Create required directories if they don't exist.
 */
function ensureDirectoriesExist(string $basePath): void
{
    $directories = [
        'storage',
        'storage/framework',
        'storage/framework/cache',
        'storage/framework/sessions',
        'storage/framework/views',
        'storage/logs',
        'bootstrap/cache',
    ];

    foreach ($directories as $dir) {
        $path = $basePath . '/' . $dir;
        if (!is_dir($path)) {
            @mkdir($path, 0755, true);
        }
    }
}

/**
 * Ensure .env file exists by copying from .env.example if needed.
 */
function ensureEnvFileExists(string $basePath): void
{
    $envPath = $basePath . '/.env';
    $envExamplePath = $basePath . '/.env.example';

    // .env already exists, nothing to do
    if (file_exists($envPath)) {
        return;
    }

    // .env.example doesn't exist, can't copy
    if (!file_exists($envExamplePath)) {
        return;
    }

    // Try to copy .env.example to .env
    try {
        @copy($envExamplePath, $envPath);
    } catch (Throwable $e) {
        // Silently ignore - permissions or other issues
        // User will see the maintenance page and can fix manually
    }
}

// Bootstrap Laravel and handle the request...
/** @var Application $app */
$app = require_once __DIR__.'/../bootstrap/app.php';

$app->handleRequest(Request::capture());
