<?php

namespace App\Filament\Pages;

use App\Models\DocumentationSetting;
use App\Services\DocumentationScanner;
use BackedEnum;
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
            $formData['doc_' . $doc->doc_name . '_visible'] = $doc->is_visible;
            $formData['path_' . $doc->doc_name] = $doc->path;
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
                ->title('Documentation scanned')
                ->body('Found ' . count($scanResults) . ' documentation file(s). Please refresh the page.')
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
                if (!file_exists($filePath) && $doc->doc_name !== 'settings') {
                    $deleted[] = $doc->doc_name;
                    $doc->delete();
                }
            });

            if (empty($deleted)) {
                Notification::make()
                    ->info()
                    ->title('No missing files')
                    ->body('All documentation files exist.')
                    ->send();
            } else {
                Notification::make()
                    ->success()
                    ->title('Cleanup complete')
                    ->body('Deleted ' . count($deleted) . ' missing documentation record(s): ' . implode(', ', $deleted))
                    ->send();

                $this->redirect(request()->header('Referer') ?? url()->current());
            }
        } catch (\Exception $e) {
            Notification::make()
                ->danger()
                ->title('Error during cleanup')
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
                $fieldName = 'doc_' . $docName . '_visible';
                $this->data[$fieldName] = $isVisible;

                Notification::make()
                    ->success()
                    ->title('Saved')
                    ->body($doc->doc_name . ' visibility updated.')
                    ->send();
            }
        } catch (\Exception $e) {
            Notification::make()
                ->danger()
                ->title('Error')
                ->body('Failed to update: ' . $e->getMessage())
                ->send();
        }
    }

}
