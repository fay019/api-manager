<?php

namespace App\Filament\Resources\ApiRequestLogResource\Pages;

use App\Filament\Resources\ApiRequestLogResource;
use App\Jobs\ArchiveApiRequestLogsJob;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\DB;

class ListApiRequestLogs extends ListRecords
{
    protected static string $resource = ApiRequestLogResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('archive')
                ->label(__('filament.log.archive_button') ?? 'Archive Old Logs')
                ->color('warning')
                ->icon('heroicon-o-archive-box')
                ->disabled(fn () => DB::table('jobs')->where('payload', 'like', '%ArchiveApiRequestLogsJob%')->exists())
                ->action(function () {
                    $jobInProgress = DB::table('jobs')
                        ->where('payload', 'like', '%ArchiveApiRequestLogsJob%')
                        ->exists();

                    if ($jobInProgress) {
                        Notification::make()
                            ->title(__('filament.log.archive_already_running_title') ?? 'Archiving in Progress')
                            ->body(__('filament.log.archive_already_running_message') ?? 'An archival process is already running. Please be patient.')
                            ->warning()
                            ->send();

                        return;
                    }

                    ArchiveApiRequestLogsJob::dispatch();

                    Notification::make()
                        ->title(__('filament.log.archive_queued_title') ?? 'Archiving Started')
                        ->body(__('filament.log.archive_queued_message') ?? 'Log archival has been queued. This may take a few minutes to complete.')
                        ->info()
                        ->send();
                }),
        ];
    }
}
