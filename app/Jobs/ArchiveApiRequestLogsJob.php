<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class ArchiveApiRequestLogsJob implements ShouldQueue
{
    use Queueable;

    public function handle(): void
    {
        try {
            Log::info('Starting archive API request logs job...');

            Artisan::call('api:archive-logs');
            $output = Artisan::output();

            Log::info('Completed archive API request logs job: '.$output);

            Cache::put('archive_logs_result', [
                'status' => 'completed',
                'output' => $output,
                'timestamp' => now(),
            ], 3600); // Cache for 1 hour
        } catch (\Exception $e) {
            Log::error('Failed to archive API request logs: '.$e->getMessage());

            Cache::put('archive_logs_result', [
                'status' => 'failed',
                'error' => $e->getMessage(),
                'timestamp' => now(),
            ], 3600);

            throw $e;
        }
    }
}
