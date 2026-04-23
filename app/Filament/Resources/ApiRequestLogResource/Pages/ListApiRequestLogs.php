<?php

namespace App\Filament\Resources\ApiRequestLogResource\Pages;

use App\Filament\Resources\ApiRequestLogResource;
use Filament\Actions\Action;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\Http;

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
                        $response = Http::post(route('admin.api-request-logs.archive'));

                        if ($response->json('success')) {
                            $this->notify('success', $response->json('message'));
                        } else {
                            $this->notify('danger', $response->json('message'));
                        }
                    } catch (\Exception $e) {
                        $this->notify('danger', $e->getMessage());
                    }
                }),
        ];
    }
}
