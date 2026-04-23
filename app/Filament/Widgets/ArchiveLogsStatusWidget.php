<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class ArchiveLogsStatusWidget extends Widget
{
    protected static ?int $sort = -1;

    protected int $pollingInterval = 2000;

    private ?array $previousResult = null;

    public function getJobStatus(): array
    {
        $isRunning = DB::table('jobs')
            ->where('payload', 'like', '%ArchiveApiRequestLogsJob%')
            ->exists();

        $result = Cache::get('archive_logs_result');

        // Show notification when job completes
        if (!$isRunning && $result && (!$this->previousResult || $this->previousResult !== $result)) {
            if ($result['status'] === 'completed') {
                Notification::make()
                    ->title('✓ Archivage Terminé!')
                    ->body($result['output'] ?? 'Logs archivés avec succès')
                    ->success()
                    ->send();
            } elseif ($result['status'] === 'failed') {
                Notification::make()
                    ->title('✗ Erreur lors de l\'archivage')
                    ->body($result['error'] ?? 'Une erreur est survenue')
                    ->danger()
                    ->send();
            }

            $this->previousResult = $result;
        }

        return [
            'isRunning' => $isRunning,
            'result' => $result,
        ];
    }
}
