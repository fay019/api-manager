<?php

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// ⭐ Prepare application BEFORE anything else
// This prevents "No environment file" and directory permission errors
ensureApplicationReady();

// ⭐ Ensure APP_KEY is set BEFORE Laravel's encryption service loads
// This prevents "No application encryption key has been specified" error
ensureAppKeyExists();

// ⭐ Ensure database tables exist BEFORE Laravel's service providers run
// This prevents cache/session/queue table errors on fresh deployment
try {
    ensureRequiredDatabaseTables();
} catch (Throwable $e) {
    // Silently ignore - tables will be created by migrations
}

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
 * Ensure APP_KEY is set in .env file.
 * Generates a random key if not present or empty, preventing encryption service errors.
 */
function ensureAppKeyExists(): void
{
    $basePath = dirname(__DIR__);
    $envPath = $basePath.'/.env';

    // .env must exist first
    if (! file_exists($envPath)) {
        return;
    }

    // Read .env file
    $content = file_get_contents($envPath);

    // Check if APP_KEY is already set with a non-empty value
    if (preg_match('/^APP_KEY=base64:/', $content)) {
        // APP_KEY already has a base64 value
        return;
    }

    // Generate a new APP_KEY (base64 encoded random string)
    $key = 'base64:'.base64_encode(random_bytes(32));

    // Replace existing APP_KEY= line (even if empty) with the new key
    if (preg_match('/^APP_KEY=.*$/m', $content)) {
        $content = preg_replace('/^APP_KEY=.*$/m', 'APP_KEY='.$key, $content);
    } else {
        // APP_KEY doesn't exist, add it after APP_ENV
        $content = preg_replace('/^(APP_ENV=.*?)$/m', '$1'."\nAPP_KEY=".$key, $content);
    }

    // Write back to .env
    try {
        @file_put_contents($envPath, $content);
    } catch (Throwable $e) {
        // Silently ignore - user can set manually
    }
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
        $path = $basePath.'/'.$dir;
        if (! is_dir($path)) {
            @mkdir($path, 0755, true);
        }
    }
}

/**
 * Ensure .env file exists by copying from .env.example if needed.
 */
function ensureEnvFileExists(string $basePath): void
{
    $envPath = $basePath.'/.env';
    $envExamplePath = $basePath.'/.env.example';

    // .env already exists, nothing to do
    if (file_exists($envPath)) {
        return;
    }

    // .env.example doesn't exist, can't copy
    if (! file_exists($envExamplePath)) {
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

/**
 * Ensure required database tables exist before Laravel boots.
 * Creates tables needed for sessions, cache, and queue operations.
 * Creates the database file if it doesn't exist yet.
 */
function ensureRequiredDatabaseTables(): void
{
    $basePath = dirname(__DIR__);
    $dbPath = $basePath.'/database/database.sqlite';
    $dbDir = dirname($dbPath);

    // Ensure database directory exists
    if (! is_dir($dbDir)) {
        @mkdir($dbDir, 0755, true);
    }

    // Create database file if it doesn't exist
    if (! file_exists($dbPath)) {
        @touch($dbPath);
    }

    try {
        $pdo = new PDO("sqlite:{$dbPath}");
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Always create sessions table (default SESSION_DRIVER=database)
        $pdo->exec('CREATE TABLE IF NOT EXISTS sessions (
            id TEXT PRIMARY KEY,
            user_id INTEGER,
            ip_address VARCHAR(45),
            user_agent TEXT,
            payload LONGTEXT,
            last_activity INTEGER
        )');

        // Create cache tables (used by Livewire and other components)
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

        // Create jobs tables (for QUEUE_CONNECTION=database)
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
    } catch (Throwable $e) {
        // Silently ignore - tables will be created by migrations
        // or the bootstrap page will handle it
    }
}

// Bootstrap Laravel and handle the request...
/** @var Application $app */
$app = require_once __DIR__.'/../bootstrap/app.php';

$app->handleRequest(Request::capture());
