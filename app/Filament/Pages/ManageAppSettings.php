<?php

namespace App\Filament\Pages;

use App\Models\DocumentationSetting;
use App\Services\AppSettingService;
use App\Services\DocumentationScanner;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Schema;
use UnitEnum;

class ManageAppSettings extends Page implements HasForms
{
    use InteractsWithForms;

    protected static BackedEnum|string|null $navigationIcon = 'heroicon-o-book-open';

    protected static string|UnitEnum|null $navigationGroup = 'System';

    protected static ?int $navigationSort = 99;

    protected static ?string $title = 'Documentation Settings';

    protected string $view = 'filament.pages.manage-app-settings';

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill($this->getInitialFormData());
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components($this->getFormSchema())
            ->statePath('data');
    }

    protected function getFormSchema(): array
    {
        return [];
    }

    protected function getInitialFormData(): array
    {
        $formData = [];

        DocumentationSetting::all()->each(function (DocumentationSetting $doc) use (&$formData) {
            $formData['doc_'.$doc->doc_name.'_visible'] = $doc->is_visible;
            $formData['path_'.$doc->doc_name] = $doc->path;
        });

        return $formData;
    }

    public function scanDocumentation(): void
    {
        try {
            $scanResults = DocumentationScanner::scan();
            DocumentationScanner::sync();

            Notification::make()
                ->success()
                ->title('Documentation scannée')
                ->body('Trouvé '.count($scanResults).' fichier(s) de documentation. Les nouveaux documents sont masqués par défaut.')
                ->send();

            $this->redirect(request()->header('Referer') ?? url()->current());
        } catch (\Exception $e) {
            Notification::make()
                ->danger()
                ->title('Error scanning documentation')
                ->body($e->getMessage())
                ->send();
        }
    }

    public function cleanupMissing(): void
    {
        try {
            $deleted = [];

            DocumentationSetting::all()->each(function (DocumentationSetting $doc) use (&$deleted) {
                $filePath = base_path($doc->path);
                if (! file_exists($filePath) && $doc->doc_name !== 'settings') {
                    $deleted[] = $doc->doc_name;
                    $doc->delete();
                }
            });

            if (empty($deleted)) {
                Notification::make()
                    ->info()
                    ->title('Aucun fichier manquant')
                    ->body('Tous les fichiers de documentation existent.')
                    ->send();
            } else {
                Notification::make()
                    ->success()
                    ->title('Nettoyage terminé')
                    ->body('Supprimé '.count($deleted).' enregistrement(s) manquant(s) : '.implode(', ', $deleted))
                    ->send();

                $this->redirect(request()->header('Referer') ?? url()->current());
            }
        } catch (\Exception $e) {
            Notification::make()
                ->danger()
                ->title('Erreur lors du nettoyage')
                ->body($e->getMessage())
                ->send();
        }
    }

    public function toggleDocVisibility(string $docName, bool $isVisible): void
    {
        try {
            $doc = DocumentationSetting::where('doc_name', $docName)->first();

            if ($doc) {
                $valueToStore = $isVisible ? 1 : 0;
                $doc->getConnection()->table('documentation_settings')
                    ->where('id', $doc->id)
                    ->update(['is_visible' => $valueToStore]);

                // Update local data to reflect the change
                $fieldName = 'doc_'.$docName.'_visible';
                $this->data[$fieldName] = $isVisible;

                Notification::make()
                    ->success()
                    ->title('Saved')
                    ->body($doc->doc_name.' visibility updated.')
                    ->send();
            }
        } catch (\Exception $e) {
            Notification::make()
                ->danger()
                ->title('Error')
                ->body('Failed to update: '.$e->getMessage())
                ->send();
        }
    }

    public function updateDocumentationIcon(string $docName, string $icon): void
    {
        try {
            $doc = DocumentationSetting::where('doc_name', $docName)->first();

            if ($doc) {
                $doc->update(['icon' => $icon]);

                Notification::make()
                    ->success()
                    ->title('Icon updated')
                    ->body('Icon for '.$doc->doc_name.' has been updated.')
                    ->send();
            }
        } catch (\Exception $e) {
            Notification::make()
                ->danger()
                ->title('Error')
                ->body('Failed to update icon: '.$e->getMessage())
                ->send();
        }
    }

    public function resetAction(): Action
    {
        return Action::make('reset')
            ->label('Réinitialiser l\'application')
            ->color('danger')
            ->icon('heroicon-m-exclamation-triangle')
            ->requiresConfirmation()
            ->modalHeading('Réinitialisation Complète')
            ->modalDescription('ATTENTION: Cette action est DESTRUCTIVE. Elle effacera la base de données (si SQLite), supprimera le verrouillage de l\'installation et vous redirigera vers l\'installateur.')
            ->form([
                TextInput::make('confirm')
                    ->label('Saisissez "Confirmer"')
                    ->placeholder('Confirmer')
                    ->required()
                    ->rules(['in:Confirmer']),
            ])
            ->action(function (AppSettingService $service) {
                if (app()->environment('production') && ! config('installation.wizard.security.allow_production_reset', false)) {
                    Notification::make()
                        ->danger()
                        ->title('Action interdite')
                        ->body('La réinitialisation est interdite en production.')
                        ->send();

                    return;
                }

                if ($service->resetApplication()) {
                    Notification::make()
                        ->success()
                        ->title('Application réinitialisée')
                        ->body('L\'application a été remise à son état initial.')
                        ->send();

                    return redirect()->to('/setup/welcome');
                }

                Notification::make()
                    ->danger()
                    ->title('Échec de la réinitialisation')
                    ->body('Une erreur est survenue lors de la réinitialisation.')
                    ->send();
            });
    }
}
