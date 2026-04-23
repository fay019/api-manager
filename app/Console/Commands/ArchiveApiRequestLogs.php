<?php

namespace App\Console\Commands;

use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

#[Signature('api:archive-logs {--archive-days=15} {--delete-days=90}')]
#[Description('Archive logs older than N days and delete archives older than M days')]
class ArchiveApiRequestLogs extends Command
{
    public function handle(): int
    {
        $archiveDays = (int) ($this->option('archive-days') ?: config('logging.archive_days', 15));
        $deleteDays = (int) ($this->option('delete-days') ?: config('logging.delete_days', 90));

        $this->info('Starting API request logs archival process...');
        $this->info("Archive threshold: {$archiveDays} days");
        $this->info("Delete threshold: {$deleteDays} days");

        $cutoffArchive = now()->subDays($archiveDays);
        $cutoffDelete = now()->subDays($deleteDays);

        $archivedCount = $this->archiveLogs($cutoffArchive);
        $deletedCount = $this->deleteOldArchives($cutoffDelete);

        $this->info("✓ Archived {$archivedCount} logs older than {$archiveDays} days.");
        $this->info("✓ Deleted {$deletedCount} archived logs older than {$deleteDays} days.");

        return self::SUCCESS;
    }

    private function archiveLogs($cutoffDate): int
    {
        $totalArchived = 0;
        $batchSize = 500;

        while (true) {
            $logsToArchive = DB::table('api_request_logs')
                ->where('created_at', '<', $cutoffDate)
                ->limit($batchSize)
                ->get();

            if ($logsToArchive->isEmpty()) {
                break;
            }

            DB::table('api_request_logs_archive')->insert(
                $logsToArchive->map(fn ($log) => (array) $log)->toArray()
            );

            DB::table('api_request_logs')
                ->where('created_at', '<', $cutoffDate)
                ->limit($batchSize)
                ->delete();

            $totalArchived += $logsToArchive->count();
            $this->info("Archived {$logsToArchive->count()} logs... (Total: {$totalArchived})");
        }

        return $totalArchived;
    }

    private function deleteOldArchives($cutoffDate): int
    {
        return DB::table('api_request_logs_archive')
            ->where('created_at', '<', $cutoffDate)
            ->delete();
    }
}
