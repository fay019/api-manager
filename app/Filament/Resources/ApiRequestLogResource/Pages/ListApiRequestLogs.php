<?php

namespace App\Filament\Resources\ApiRequestLogResource\Pages;

use App\Filament\Resources\ApiRequestLogResource;
use App\Jobs\ArchiveApiRequestLogsJob;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;

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
                ->action(function () {
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
