<?php

namespace App\Filament\Pages;

use App\Models\DocumentationSetting;
use App\Models\Setting;
use App\Services\AppSettingService;
use App\Services\DocumentationScanner;
use BackedEnum;
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

    protected static ?string $title = null;

    public function getTitle(): string
    {
        return __('filament.manage_app.title');
    }

    protected string $view = 'filament.pages.manage-app-settings';

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill($this->getInitialFormData());
    }

    public function save(): void
    {
        try {
            $state = $this->form->getState();

            Setting::set(
                'contact_email',
                $state['contact_email'],
                'string',
                'Email address for contact form'
            );

            Notification::make()
                ->success()
                ->title(__('filament.manage_app.saved_title'))
                ->send();
        } catch (\Exception $e) {
            Notification::make()
                ->danger()
                ->title(__('filament.common.error'))
                ->body($e->getMessage())
                ->send();
        }
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
        $formData = [
            'contact_email' => Setting::get('contact_email'),
        ];

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
                ->title(__('filament.manage_app.documentation_scanned'))
                ->body(str_replace('{count}', count($scanResults), __('filament.manage_app.documentation_scan_body')))
                ->send();

            $this->redirect(request()->header('Referer') ?? url()->current());
        } catch (\Exception $e) {
            Notification::make()
                ->danger()
                ->title(__('filament.manage_app.error_scanning'))
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
                    ->title(__('filament.manage_app.no_missing_files'))
                    ->body(__('filament.manage_app.no_missing_files_body'))
                    ->send();
            } else {
                Notification::make()
                    ->success()
                    ->title(__('filament.manage_app.cleanup_done'))
                    ->body(str_replace('{count}', count($deleted), __('filament.manage_app.cleanup_done_body')).' '.implode(', ', $deleted))
                    ->send();

                $this->redirect(request()->header('Referer') ?? url()->current());
            }
        } catch (\Exception $e) {
            Notification::make()
                ->danger()
                ->title(__('filament.manage_app.cleanup_error_title'))
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
                    ->title(__('filament.manage_app.saved_title'))
                    ->body($doc->doc_name.' '.__('filament.manage_app.saved_body'))
                    ->send();
            }
        } catch (\Exception $e) {
            Notification::make()
                ->danger()
                ->title(__('filament.common.error'))
                ->body(__('filament.manage_app.error_update').' '.$e->getMessage())
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
                    ->title(__('filament.manage_app.icon_updated'))
                    ->body(str_replace('{docName}', $doc->doc_name, __('filament.manage_app.icon_updated_body')))
                    ->send();
            }
        } catch (\Exception $e) {
            Notification::make()
                ->danger()
                ->title(__('filament.common.error'))
                ->body(__('filament.manage_app.error_update_icon').' '.$e->getMessage())
                ->send();
        }
    }

    public function resetApplication(): void
    {
        if (app()->environment('production') && ! config('installation.wizard.security.allow_production_reset', false)) {
            Notification::make()
                ->danger()
                ->title(__('filament.manage_app.production_forbidden'))
                ->body(__('filament.manage_app.production_forbidden_body'))
                ->send();

            return;
        }

        try {
            $service = app(AppSettingService::class);
            if ($service->resetApplication()) {
                Notification::make()
                    ->success()
                    ->title(__('filament.manage_app.app_reset'))
                    ->body(__('filament.manage_app.app_reset_body'))
                    ->send();

                $this->redirect('/setup/welcome', navigate: true);

                return;
            }

            Notification::make()
                ->danger()
                ->title(__('filament.manage_app.reset_failed'))
                ->body(__('filament.manage_app.reset_failed_body'))
                ->send();
        } catch (\Exception $e) {
            Notification::make()
                ->danger()
                ->title(__('filament.common.error'))
                ->body($e->getMessage())
                ->send();
        }
    }

    public function resetAction(): Action
    {
        return Action::make('reset')
            ->label(__('filament.manage_app.reset_action_label'))
            ->color('danger')
            ->icon('heroicon-m-exclamation-triangle')
            ->requiresConfirmation()
            ->modalHeading(__('filament.manage_app.reset_modal_heading'))
            ->modalDescription(__('filament.manage_app.reset_modal_description'))
            ->form([
                TextInput::make('confirm')
                    ->label(__('filament.manage_app.reset_confirm_label'))
                    ->placeholder('Confirm')
                    ->required()
                    ->rules(['in:Confirm']),
            ])
            ->action(function (AppSettingService $service) {
                if (app()->environment('production') && ! config('installation.wizard.security.allow_production_reset', false)) {
                    Notification::make()
                        ->danger()
                        ->title(__('filament.manage_app.production_forbidden'))
                        ->body(__('filament.manage_app.production_forbidden_body'))
                        ->send();

                    return;
                }

                if ($service->resetApplication()) {
                    Notification::make()
                        ->success()
                        ->title(__('filament.manage_app.app_reset'))
                        ->body(__('filament.manage_app.app_reset_body'))
                        ->send();

                    return redirect()->to('/setup/welcome');
                }

                Notification::make()
                    ->danger()
                    ->title(__('filament.manage_app.reset_failed'))
                    ->body(__('filament.manage_app.reset_failed_body'))
                    ->send();
            });
    }
}
