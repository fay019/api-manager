<?php

namespace App\Filament\Resources\ApiRequestLogResource\Pages;

use App\Filament\Resources\ApiRequestLogResource;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\Artisan;

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
                    try {
                        Artisan::call('api:archive-logs');
                        $output = Artisan::output();

                        Notification::make()
                            ->title('Success')
                            ->body('Logs archived successfully: '.trim($output))
                            ->success()
                            ->send();
                    } catch (\Exception $e) {
                        Notification::make()
                            ->title('Error')
                            ->body($e->getMessage())
                            ->danger()
                            ->send();
                    }
                }),
        ];
    }
}
