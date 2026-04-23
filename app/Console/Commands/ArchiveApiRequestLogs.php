<?php

namespace App\Console\Commands;

use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

#[Signature('api:archive-logs')]
#[Description('Archive logs older than 30 days and delete archives older than 6 months')]
class ArchiveApiRequestLogs extends Command
{
    public function handle(): int
    {
        $this->info('Starting API request logs archival process...');

        $cutoffArchive = now()->subDays(30);
        $cutoffDelete = now()->subMonths(6);

        // Archive logs older than 30 days
        $archivedCount = $this->archiveLogs($cutoffArchive);

        // Delete archived logs older than 6 months
        $deletedCount = $this->deleteOldArchives($cutoffDelete);

        $this->info("Archived {$archivedCount} logs older than 30 days.");
        $this->info("Deleted {$deletedCount} archived logs older than 6 months.");

        return self::SUCCESS;
    }

    private function archiveLogs($cutoffDate): int
    {
        $logsToArchive = DB::table('api_request_logs')
            ->where('created_at', '<', $cutoffDate)
            ->get();

        if ($logsToArchive->isEmpty()) {
            return 0;
        }

        DB::table('api_request_logs_archive')->insert(
            $logsToArchive->map(fn ($log) => (array) $log)->toArray()
        );

        DB::table('api_request_logs')
            ->where('created_at', '<', $cutoffDate)
            ->delete();

        return $logsToArchive->count();
    }

    private function deleteOldArchives($cutoffDate): int
    {
        return DB::table('api_request_logs_archive')
            ->where('created_at', '<', $cutoffDate)
            ->delete();
    }
}
