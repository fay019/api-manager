<?php

namespace App\Console\Commands;

use App\Models\ApiRequestLog;
use Illuminate\Console\Command;

class PruneApiRequestLogs extends Command
{
    protected $signature = 'api:prune-logs {--days=90}';

    protected $description = 'Prune API request logs older than N days';

    public function handle()
    {
        $days = $this->option('days');
        $cutoffDate = now()->subDays($days);

        $deletedCount = ApiRequestLog::where('created_at', '<', $cutoffDate)->delete();

        $this->info("Pruned {$deletedCount} API request logs older than {$days} days.");

        return self::SUCCESS;
    }
}
