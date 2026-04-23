<?php

namespace App\Filament\Resources\ExternalIconResource\Pages;

use App\Filament\Resources\ExternalIconResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListExternalIcons extends ListRecords
{
    protected static string $resource = ExternalIconResource::class;

    public function getHeaderWidgetsColumns(): int|array
    {
        return 1;
    }

    public function getHeaderWidgets(): array
    {
        return [
            ExternalIconResource\Widgets\ExternalIconHelper::class,
        ];
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
