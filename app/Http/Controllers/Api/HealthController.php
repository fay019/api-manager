<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Responses\ApiResponse;
use Illuminate\Support\Facades\Cache;

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
        $settings = \App\Models\HealthCheckSetting::getInstance();
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
                'message' => $value === 'ok' ? 'Cache is working' : 'Cache write/read failed',
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'message' => 'Cache error: '.$e->getMessage(),
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
                    'message' => 'Logs directory not found',
                ];
            }

            if (! is_writable($logsPath)) {
                return [
                    'status' => 'error',
                    'message' => 'Logs directory not writable',
                ];
            }

            $logFile = $logsPath.'/laravel.log';
            if (file_exists($logFile) && ! is_writable($logFile)) {
                return [
                    'status' => 'error',
                    'message' => 'Log file not writable',
                ];
            }

            return [
                'status' => 'ok',
                'message' => 'Logs directory is writable',
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'message' => 'Logs check error: '.$e->getMessage(),
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
                    'message' => 'Unable to determine disk space',
                ];
            }

            $percentUsed = round(((($diskTotal - $diskFree) / $diskTotal) * 100), 2);
            $freeGB = round($diskFree / (1024 ** 3), 2);
            $totalGB = round($diskTotal / (1024 ** 3), 2);

            // Warning if less than 10% free space
            if ($percentUsed > 90) {
                return [
                    'status' => 'warning',
                    'message' => 'Low disk space: '.$percentUsed.'% used',
                    'free_gb' => $freeGB,
                    'total_gb' => $totalGB,
                    'percent_used' => $percentUsed,
                ];
            }

            return [
                'status' => 'ok',
                'message' => 'Disk space is healthy',
                'free_gb' => $freeGB,
                'total_gb' => $totalGB,
                'percent_used' => $percentUsed,
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'warning',
                'message' => 'Disk space check error: '.$e->getMessage(),
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
                    'message' => 'Storage issues: '.implode(', ', $issues),
                    'issues' => $issues,
                ];
            }

            return [
                'status' => 'ok',
                'message' => 'All storage directories are writable',
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'message' => 'Storage check error: '.$e->getMessage(),
            ];
        }
    }
}
