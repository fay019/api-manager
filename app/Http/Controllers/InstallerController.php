<?php

namespace App\Http\Controllers;

use Illuminate\Routing\Controller;

class InstallerController extends Controller
{
    /**
     * Run installation diagnostics and show status
     */
    public function install()
    {
        $basePath = base_path();
        $output = [];
        $errors = [];
        $success = false;

        // Start
        $this->log($output, 'info', '🚀 Installation Bootstrap Started');
        $this->log($output, 'info', 'PHP Version: ' . phpversion());
        $this->log($output, 'info', 'PHP SAPI: ' . php_sapi_name());
        $this->log($output, 'info', 'Base Path: ' . $basePath);
        $this->log($output, 'info', 'Server Software: ' . ($_SERVER['SERVER_SOFTWARE'] ?? 'unknown'));

        // Check file system
        $testFile = $basePath . '/.install-test-' . time() . '.txt';
        if (@file_put_contents($testFile, 'test')) {
            $this->log($output, 'success', 'File system is writable');
            @unlink($testFile);
        } else {
            $this->log($output, 'error', '⚠️ File system is NOT writable - permissions issues expected');
            $errors[] = 'Not writable';
        }

        // Step 1: Create directories
        $this->log($output, 'info', '📁 Creating required directories...');
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
                    $this->log($output, 'success', "Created: $dir");
                } else {
                    $this->log($output, 'error', "Failed to create: $dir");
                    $errors[] = "Cannot create $dir";
                }
            } else {
                $this->log($output, 'info', "Already exists: $dir");
            }
        }

        // Step 2: Create .env
        $this->log($output, 'info', '📝 Setting up .env file...');
        $envPath = $basePath . '/.env';
        $envExamplePath = $basePath . '/.env.example';

        if (!file_exists($envPath)) {
            if (file_exists($envExamplePath)) {
                if (@copy($envExamplePath, $envPath)) {
                    $this->log($output, 'success', '.env created from .env.example');
                } else {
                    $this->log($output, 'error', 'Failed to create .env from .env.example');
                    $errors[] = 'Cannot create .env';
                }
            } else {
                $this->log($output, 'error', '.env.example not found!');
                $errors[] = 'No .env.example';
            }
        } else {
            $this->log($output, 'info', '.env already exists');
        }

        // Step 3: Check Composer
        $this->log($output, 'info', '🎵 Checking Composer...');
        $vendorPath = $basePath . '/vendor';
        $composerPath = $basePath . '/composer.json';

        if (!is_dir($vendorPath)) {
            if (file_exists($composerPath)) {
                $this->log($output, 'info', 'vendor/ directory not found, attempting composer install...');
                $this->log($output, 'info', 'Running: composer install');

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
                        $this->log($output, 'success', "Found Composer: {$cmd}");
                        break;
                    }
                }

                if ($composer_cmd) {
                    $cmd = "cd {$basePath} && {$composer_cmd} install --no-interaction 2>&1";
                    $result = shell_exec($cmd);
                    if ($result) {
                        $this->log($output, 'info', 'Composer output: ' . substr($result, 0, 500));
                    }

                    if (is_dir($vendorPath)) {
                        $this->log($output, 'success', '✅ Composer install completed successfully');
                    } else {
                        $this->log($output, 'error', '❌ vendor/ directory still not found after composer install');
                        $errors[] = 'Composer failed';
                    }
                } else {
                    $this->log($output, 'error', '⚠️ Composer not found in common locations');
                    $this->log($output, 'error', 'Please run: composer install');
                    $errors[] = 'Composer not found';
                }
            } else {
                $this->log($output, 'error', 'composer.json not found!');
                $errors[] = 'No composer.json';
            }
        } else {
            $this->log($output, 'success', 'vendor/ directory exists');
        }

        // Step 4: Create database
        $this->log($output, 'info', '🗄️ Preparing database...');
        $dbPath = $basePath . '/database/database.sqlite';

        if (!file_exists($dbPath)) {
            if (@touch($dbPath)) {
                $this->log($output, 'success', 'SQLite database file created');

                try {
                    $pdo = new \PDO("sqlite:{$dbPath}");
                    $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

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

                    $this->log($output, 'success', 'Required tables created');
                } catch (\Exception $e) {
                    $this->log($output, 'error', 'Failed to create tables: ' . $e->getMessage());
                    $errors[] = 'Database creation failed';
                }
            } else {
                $this->log($output, 'error', 'Failed to create database file');
                $errors[] = 'Cannot create database';
            }
        } else {
            $this->log($output, 'info', 'Database file already exists');
        }

        // Step 5: Check Laravel
        $this->log($output, 'info', '🔧 Checking Laravel bootstrap...');
        $bootstrapApp = $basePath . '/bootstrap/app.php';
        $autoload = $basePath . '/vendor/autoload.php';

        if (file_exists($bootstrapApp)) {
            $this->log($output, 'success', 'bootstrap/app.php exists');
        } else {
            $this->log($output, 'error', 'bootstrap/app.php not found');
            $errors[] = 'No bootstrap/app.php';
        }

        if (file_exists($autoload)) {
            $this->log($output, 'success', 'vendor/autoload.php exists');
        } else {
            $this->log($output, 'error', 'vendor/autoload.php not found');
            $errors[] = 'No autoload.php';
        }

        // Summary
        $success = count($errors) === 0 && is_dir($vendorPath) && file_exists($envPath);

        $this->log($output, 'info', '');
        if ($success) {
            $this->log($output, 'success', '✅ Installation bootstrap completed successfully!');
            $this->log($output, 'info', 'You can now visit: ' . request()->getHost());
        } else {
            $this->log($output, 'error', '❌ Installation has issues. Please fix the errors above.');
        }

        return view('installer.install', compact('output', 'errors', 'success'));
    }

    /**
     * Show diagnostic page
     */
    public function diagnostic()
    {
        $basePath = base_path();
        $checks = [];

        // Basic
        $checks['PHP Version'] = phpversion();
        $checks['SAPI'] = php_sapi_name();
        $checks['Base Path'] = $basePath;

        // Directories
        $dirs = [];
        foreach (['storage', 'bootstrap/cache', 'database'] as $dir) {
            $dirs[$dir] = is_dir($basePath . '/' . $dir) ? '✓' : '✗';
        }
        $checks['Directories'] = $dirs;

        // Files
        $files = [];
        foreach (['.env', '.env.example', 'composer.json', 'vendor/autoload.php'] as $file) {
            $files[$file] = file_exists($basePath . '/' . $file) ? '✓' : '✗';
        }
        $checks['Files'] = $files;

        // Database
        $dbPath = $basePath . '/database/database.sqlite';
        $checks['Database'] = file_exists($dbPath) ? '✓ exists' : '✗ missing';

        // Permissions
        $testFile = $basePath . '/.write-test-' . time() . '.txt';
        $writable = @file_put_contents($testFile, 'test');
        if ($writable) {
            @unlink($testFile);
        }
        $checks['Writable'] = $writable ? '✓ yes' : '✗ no';

        return view('installer.diagnostic', compact('checks'));
    }

    /**
     * Log a message
     */
    private function log(&$output, $type, $message)
    {
        $output[] = [
            'time' => date('H:i:s'),
            'message' => $message,
            'type' => $type
        ];
    }
}
