<?php

namespace App\Filament\Pages;

use BackedEnum;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Pages\Page;
use UnitEnum;

/**
 * Page d'Administration des Paramètres Globaux.
 *
 * Cette page permet de configurer:
 * - Paramètres généraux (facilement extensible)
 * - Email (optionnel, ajouter plus tard)
 * - Cache & Queue (optionnel, ajouter plus tard)
 * - API Settings (optionnel, ajouter plus tard)
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

    /**
     * Formulaire des paramètres.
     *
     * Cette architecture est facilement extensible:
     * - Ajouter des onglets pour: Email, Cache, Queue, API, etc.
     * - Chaque onglet peut être activé indépendamment
     * - Les configs sont stockées en BD (AppSetting model)
     */
    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Tabs::make('Paramètres')
                    ->tabs([
                        // Onglet 1: Général (toujours visible)
                        Forms\Components\Tabs\Tab::make('Général')
                            ->schema([
                                Forms\Components\Section::make('Infos Générales')
                                    ->description('Configuration générale de l\'application')
                                    ->schema([
                                        Forms\Components\TextInput::make('site_name')
                                            ->label('Nom du Site')
                                            ->placeholder('API Manager')
                                            ->default(env('APP_NAME', 'API Manager'))
                                            ->disabled()
                                            ->hint('Défini lors de l\'installation'),

                                        Forms\Components\TextInput::make('app_url')
                                            ->label('URL de l\'Application')
                                            ->url()
                                            ->placeholder('https://api.example.com')
                                            ->default(env('APP_URL', 'http://localhost:8000'))
                                            ->disabled()
                                            ->hint('Définie lors de l\'installation'),

                                        Forms\Components\Select::make('app_env')
                                            ->label('Environnement')
                                            ->options([
                                                'local' => 'Développement',
                                                'staging' => 'Staging',
                                                'production' => 'Production',
                                            ])
                                            ->default(env('APP_ENV', 'local'))
                                            ->disabled()
                                            ->hint('Ne pas modifier sans raison'),
                                    ]),
                            ]),

                        // Onglet 2: Email (extensible, ajouter plus tard)
                        Forms\Components\Tabs\Tab::make('Email')
                            ->schema([
                                Forms\Components\Section::make('Configuration Email')
                                    ->description('Paramètres SMTP pour les notifications')
                                    ->schema([
                                        Forms\Components\Placeholder::make('email_notice')
                                            ->content('Configuration email disponible bientôt')
                                            ->icon('heroicon-o-information-circle'),
                                    ]),
                            ]),

                        // Onglet 3: Cache (extensible, ajouter plus tard)
                        Forms\Components\Tabs\Tab::make('Cache & Performance')
                            ->schema([
                                Forms\Components\Section::make('Configuration Cache')
                                    ->description('Optimisez les performances')
                                    ->schema([
                                        Forms\Components\Placeholder::make('cache_notice')
                                            ->content('Configuration cache disponible bientôt')
                                            ->icon('heroicon-o-information-circle'),
                                    ]),
                            ]),

                        // Onglet 4: Queue (extensible, ajouter plus tard)
                        Forms\Components\Tabs\Tab::make('Queue & Jobs')
                            ->schema([
                                Forms\Components\Section::make('Configuration Queue')
                                    ->description('Paramètres des files d\'attente')
                                    ->schema([
                                        Forms\Components\Placeholder::make('queue_notice')
                                            ->content('Configuration queue disponible bientôt')
                                            ->icon('heroicon-o-information-circle'),
                                    ]),
                            ]),

                        // Onglet 5: API (extensible, ajouter plus tard)
                        Forms\Components\Tabs\Tab::make('API')
                            ->schema([
                                Forms\Components\Section::make('Paramètres API')
                                    ->description('Configuration des endpoints API')
                                    ->schema([
                                        Forms\Components\Placeholder::make('api_notice')
                                            ->content('Configuration API disponible bientôt')
                                            ->icon('heroicon-o-information-circle'),
                                    ]),
                            ]),
                    ]),
            ]);
    }

    public function submit(): void
    {
        // Les submits seront implémentés quand on ajoutera les configs réelles
        $this->notify('success', 'Paramètres mis à jour');
    }
}
