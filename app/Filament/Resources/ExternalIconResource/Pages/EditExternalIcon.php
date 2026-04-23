<?php

namespace App\Filament\Resources\ExternalIconResource\Pages;

use App\Filament\Resources\ExternalIconResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditExternalIcon extends EditRecord
{
    protected static string $resource = ExternalIconResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
