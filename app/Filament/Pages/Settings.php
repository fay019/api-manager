<?php

namespace App\Filament\Pages;

use BackedEnum;
use Filament\Pages\Page;
use UnitEnum;

/**
 * Page d'Administration des Paramètres Globaux.
 *
 * Cette page affiche les paramètres de l'application.
 * Architecture extensible pour ajouter plus de configurations plus tard.
 */
class Settings extends Page
{
    protected static BackedEnum|string|null $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected string $view = 'filament.pages.settings';

    protected static string|UnitEnum|null $navigationGroup = 'Administration';

    protected static ?int $navigationSort = 10;

    public static function getNavigationLabel(): string
    {
        return 'Paramètres';
    }

    public function getTitle(): string
    {
        return 'Paramètres de l\'Application';
    }
}
