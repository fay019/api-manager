<?php

namespace App\Filament\Pages;

use App\Services\AppSettingService;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
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

    public function resetApplicationAction(): Action
    {
        return Action::make('resetApplication')
            ->label('Réinitialiser l\'Application')
            ->color('danger')
            ->icon('heroicon-m-exclamation-triangle')
            ->requiresConfirmation()
            ->modalHeading('Réinitialisation de l\'Application')
            ->modalDescription('ATTENTION: Cette action est DESTRUCTIVE. Elle effacera la base de données (si SQLite), les logs, et remettra l\'application en mode installation. Veuillez saisir "Confirmer" pour valider.')
            ->form([
                TextInput::make('confirmation')
                    ->label('Saisissez "Confirmer"')
                    ->required()
                    ->rules(['in:Confirmer']),
            ])
            ->action(function (AppSettingService $service) {
                if (app()->environment('production') && ! config('installation.wizard.security.allow_production_reset', false)) {
                    Notification::make()
                        ->danger()
                        ->title('Réinitialisation interdite')
                        ->body('La réinitialisation est interdite en environnement de production pour des raisons de sécurité.')
                        ->send();

                    return;
                }

                try {
                    if ($service->resetApplication()) {
                        Notification::make()
                            ->success()
                            ->title('Application réinitialisée')
                            ->body('L\'application a été remise à zéro. Vous allez être redirigé vers l\'installateur.')
                            ->send();

                        $this->redirect('/setup/welcome');
                    } else {
                        throw new \Exception('La réinitialisation a échoué.');
                    }
                } catch (\Exception $e) {
                    Notification::make()
                        ->danger()
                        ->title('Erreur')
                        ->body($e->getMessage())
                        ->send();
                }
            });
    }
}
