<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;

class ArchiveApiRequestLogsJob implements ShouldQueue
{
    use Queueable;

    public function handle(): void
    {
        try {
            Log::info('Starting archive API request logs job...');
            Artisan::call('api:archive-logs');
            Log::info('Completed archive API request logs job: '.Artisan::output());
        } catch (\Exception $e) {
            Log::error('Failed to archive API request logs: '.$e->getMessage());
            throw $e;
        }
    }
}
