<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Responses\ApiResponse;
use App\Models\HealthCheckSetting;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class HealthController extends Controller
{
    public function index()
    {
        $health = [
            'status' => 'ok',
            'timestamp' => now()->toIso8601String(),
            'checks' => $this->performHealthChecks(),
        ];

        return ApiResponse::success($health);
    }

    /**
     * Perform various health checks based on settings
     */
    private function performHealthChecks(): array
    {
        $settings = HealthCheckSetting::getInstance();
        $checks = [];

        // Check 1: Cache functionality
        if ($settings->cache_enabled) {
            $checks['cache'] = $this->checkCache();
        }

        // Check 2: Logs writable
        if ($settings->logs_enabled) {
            $checks['logs'] = $this->checkLogs();
        }

        // Check 3: Disk space
        if ($settings->disk_space_enabled) {
            $checks['disk_space'] = $this->checkDiskSpace();
        }

        // Check 4: Storage writable
        if ($settings->storage_enabled) {
            $checks['storage'] = $this->checkStorage();
        }

        // Check 5: Mail configuration
        if ($settings->mail_enabled) {
            $checks['mail'] = $this->checkMailConfiguration();
        }

        // Check 6: Database connection
        if ($settings->database_enabled) {
            $checks['database'] = $this->checkDatabaseConnection();
        }

        // Check 7: PHP extensions
        if ($settings->php_extensions_enabled) {
            $checks['php_extensions'] = $this->checkPhpExtensions();
        }

        // Check 8: API response time
        if ($settings->api_response_time_enabled) {
            $checks['api_response_time'] = $this->checkApiResponseTime();
        }

        // Check 9: Environment variables
        if ($settings->environment_variables_enabled) {
            $checks['environment_variables'] = $this->checkEnvironmentVariables();
        }

        return $checks;
    }

    /**
     * Check cache is working
     */
    private function checkCache(): array
    {
        try {
            $testKey = 'health_check_'.microtime(true);
            Cache::put($testKey, 'ok', 5);
            $value = Cache::get($testKey);
            Cache::forget($testKey);

            return [
                'status' => $value === 'ok' ? 'ok' : 'error',
                'message' => $value === 'ok' ? __('filament.health.cache_working') : __('filament.health.cache_failed'),
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'message' => __('filament.health.cache_error').' '.$e->getMessage(),
            ];
        }
    }

    /**
     * Check logs directory is writable
     */
    private function checkLogs(): array
    {
        try {
            $logsPath = storage_path('logs');

            if (! is_dir($logsPath)) {
                return [
                    'status' => 'error',
                    'message' => __('filament.health.logs_not_found'),
                ];
            }

            if (! is_writable($logsPath)) {
                return [
                    'status' => 'error',
                    'message' => __('filament.health.logs_not_writable'),
                ];
            }

            $logFile = $logsPath.'/laravel.log';
            if (file_exists($logFile) && ! is_writable($logFile)) {
                return [
                    'status' => 'error',
                    'message' => __('filament.health.logs_file_not_writable'),
                ];
            }

            return [
                'status' => 'ok',
                'message' => __('filament.health.logs_writable'),
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'message' => __('filament.health.logs_error').' '.$e->getMessage(),
            ];
        }
    }

    /**
     * Check available disk space
     */
    private function checkDiskSpace(): array
    {
        try {
            $diskFree = disk_free_space(base_path());
            $diskTotal = disk_total_space(base_path());

            if ($diskFree === false || $diskTotal === false) {
                return [
                    'status' => 'warning',
                    'message' => __('filament.health.disk_space_unable'),
                ];
            }

            $percentUsed = round(((($diskTotal - $diskFree) / $diskTotal) * 100), 2);
            $freeGB = round($diskFree / (1024 ** 3), 2);
            $totalGB = round($diskTotal / (1024 ** 3), 2);

            // Warning if less than 10% free space
            if ($percentUsed > 90) {
                return [
                    'status' => 'warning',
                    'message' => __('filament.health.disk_space_low').$percentUsed.'% used',
                    'free_gb' => $freeGB,
                    'total_gb' => $totalGB,
                    'percent_used' => $percentUsed,
                ];
            }

            return [
                'status' => 'ok',
                'message' => __('filament.health.disk_space_healthy'),
                'free_gb' => $freeGB,
                'total_gb' => $totalGB,
                'percent_used' => $percentUsed,
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'warning',
                'message' => __('filament.health.disk_space_error').' '.$e->getMessage(),
            ];
        }
    }

    /**
     * Check storage directories are writable
     */
    private function checkStorage(): array
    {
        try {
            $directories = [
                'logs' => storage_path('logs'),
                'app' => storage_path('app'),
                'cache' => storage_path('framework/cache'),
                'sessions' => storage_path('framework/sessions'),
            ];

            $issues = [];
            foreach ($directories as $name => $path) {
                if (! is_dir($path)) {
                    $issues[] = "{$name} directory not found";

                    continue;
                }

                if (! is_writable($path)) {
                    $issues[] = "{$name} directory not writable";
                }
            }

            if (! empty($issues)) {
                return [
                    'status' => 'error',
                    'message' => __('filament.health.storage_issues').implode(', ', $issues),
                    'issues' => $issues,
                ];
            }

            return [
                'status' => 'ok',
                'message' => __('filament.health.storage_all_writable'),
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'message' => __('filament.health.storage_error').' '.$e->getMessage(),
            ];
        }
    }

    /**
     * Check mail configuration
     */
    private function checkMailConfiguration(): array
    {
        try {
            $mailer = config('mail.default');
            $host = config('mail.mailers.'.$mailer.'.host');

            if (! $mailer || ! $host) {
                return [
                    'status' => 'warning',
                    'message' => __('filament.health.mail_incomplete'),
                ];
            }

            return [
                'status' => 'ok',
                'message' => __('filament.health.mail_configured').' ('.$mailer.')',
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'message' => __('filament.health.mail_error').' '.$e->getMessage(),
            ];
        }
    }

    /**
     * Check database connection
     */
    private function checkDatabaseConnection(): array
    {
        try {
            DB::connection()->getPdo();

            return [
                'status' => 'ok',
                'message' => __('filament.health.database_working'),
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'message' => __('filament.health.database_failed').' '.$e->getMessage(),
            ];
        }
    }

    /**
     * Check required PHP extensions
     */
    private function checkPhpExtensions(): array
    {
        $requiredExtensions = [
            'pdo',
            'json',
            'openssl',
            'mbstring',
            'tokenizer',
            'xml',
            'ctype',
        ];

        $missing = [];
        foreach ($requiredExtensions as $ext) {
            if (! extension_loaded($ext)) {
                $missing[] = $ext;
            }
        }

        if (! empty($missing)) {
            return [
                'status' => 'error',
                'message' => __('filament.health.php_extensions_missing').implode(', ', $missing),
                'missing' => $missing,
            ];
        }

        return [
            'status' => 'ok',
            'message' => __('filament.health.php_extensions_loaded'),
        ];
    }

    /**
     * Check API response time
     */
    private function checkApiResponseTime(): array
    {
        try {
            $start = microtime(true);

            // Simple DB query to measure response time
            DB::connection()->select('SELECT 1');

            $duration = round((microtime(true) - $start) * 1000, 2);

            $status = $duration < 100 ? 'ok' : ($duration < 500 ? 'warning' : 'error');
            $message = __('filament.health.api_response_time')."{$duration}ms";

            return [
                'status' => $status,
                'message' => $message,
                'response_time_ms' => $duration,
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'message' => __('filament.health.api_response_error').' '.$e->getMessage(),
            ];
        }
    }

    /**
     * Check critical configuration values
     */
    private function checkEnvironmentVariables(): array
    {
        $checks = [
            'APP_KEY' => config('app.key'),
            'DB_HOST' => config('database.connections.'.config('database.default').'.host'),
            'DB_DATABASE' => config('database.connections.'.config('database.default').'.database'),
            'DB_USERNAME' => config('database.connections.'.config('database.default').'.username'),
        ];

        $missing = [];
        foreach ($checks as $name => $value) {
            if (! $value) {
                $missing[] = $name;
            }
        }

        if (! empty($missing)) {
            return [
                'status' => 'error',
                'message' => __('filament.health.config_missing').' '.implode(', ', $missing),
                'missing' => $missing,
            ];
        }

        return [
            'status' => 'ok',
            'message' => __('filament.health.config_all_set'),
        ];
    }
}
