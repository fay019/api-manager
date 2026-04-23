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

        $totalDeleted = 0;
        $batchSize = 500;

        while (true) {
            $deletedCount = ApiRequestLog::where('created_at', '<', $cutoffDate)
                ->limit($batchSize)
                ->delete();

            if ($deletedCount === 0) {
                break;
            }

            $totalDeleted += $deletedCount;
            $this->info("Pruned {$deletedCount} logs... (Total: {$totalDeleted})");
        }

        $this->info("Pruned {$totalDeleted} API request logs older than {$days} days.");

        return self::SUCCESS;
    }
}
