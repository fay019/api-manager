<?php

namespace App\Filament\Resources\ExternalIconResource\Widgets;

use Filament\Widgets\Widget;

class ExternalIconHelper extends Widget
{
    protected string $view = 'filament.resources.external-icon.helper';

    protected int|string|array $columnSpan = 'full';
}
