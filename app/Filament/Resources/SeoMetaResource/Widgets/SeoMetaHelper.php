<?php

namespace App\Filament\Resources\SeoMetaResource\Widgets;

use Filament\Widgets\Widget;

class SeoMetaHelper extends Widget
{
    protected string $view = 'filament.resources.seo-meta.helper';

    protected int|string|array $columnSpan = 'full';
}
