<?php

namespace App\Filament\Pages;

use App\Models\SiteSetting;
use App\Services\FaviconService;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\ViewField;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class SiteSettings extends Page
{
    use InteractsWithForms;

    protected static ?string $slug = 'favicon-manager';

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-photo';

    protected static string|\UnitEnum|null $navigationGroup = 'Settings';

    protected static ?string $title = 'Gestionnaire de Favicons';

    protected string $view = 'filament.pages.site-settings';

    public ?array $data = [];

    public function mount(): void
    {
        $settings = SiteSetting::first();
        if ($settings) {
            $this->form->fill($settings->toArray());
        }
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->statePath('data')
            ->components([
                ViewField::make('helper')
                    ->view('filament.pages.site-settings-helper'),
                Section::make('Configuration des Images')
                    ->schema([
                        FileUpload::make('favicon_source')
                            ->label('Source Favicon (PNG/SVG)')
                            ->image()
                            ->directory('settings')
                            ->disk('public')
                            ->visibility('public'),
                        Toggle::make('is_rounded')
                            ->label('Option : Favicons ronds')
                            ->helperText('Si activé, les favicons générés seront découpés en cercle.')
                            ->default(false),
                        FileUpload::make('og_image')
                            ->label('Image OpenGraph (SEO)')
                            ->image()
                            ->directory('settings')
                            ->disk('public')
                            ->visibility('public'),
                    ]),
                ViewField::make('preview')
                    ->view('filament.pages.site-settings-preview'),
            ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('save')
                ->label('Enregistrer')
                ->action('save'),
            Action::make('restoreDefaults')
                ->label('Utiliser les images par défaut')
                ->color('gray')
                ->icon('heroicon-o-arrow-path')
                ->requiresConfirmation()
                ->modalHeading('Restaurer les images par défaut')
                ->modalDescription('Cela remplacera vos images actuelles par les images par défaut du site. Êtes-vous sûr ?')
                ->action('restoreDefaults'),
            Action::make('generateFavicons')
                ->label('Générer les Favicons')
                ->color('success')
                ->requiresConfirmation()
                ->action('generateFavicons'),
        ];
    }

    public function restoreDefaults(): void
    {
        $faviconPath = 'settings/default-favicon.png';
        $ogImagePath = 'settings/default-og-image.png';

        // Copie des ressources vers le stockage public
        if (! Storage::disk('public')->exists('settings')) {
            Storage::disk('public')->makeDirectory('settings');
        }

        File::copy(resource_path('images/defaults/default-favicon.png'), Storage::disk('public')->path($faviconPath));
        File::copy(resource_path('images/defaults/default-og-image.png'), Storage::disk('public')->path($ogImagePath));

        $settings = SiteSetting::firstOrNew([]);
        $settings->favicon_source = $faviconPath;
        $settings->og_image = $ogImagePath;
        $settings->save();

        $this->form->fill($settings->toArray());

        Notification::make()
            ->title('Images par défaut restaurées')
            ->success()
            ->send();
    }

    protected function getFormActions(): array
    {
        return [
            Action::make('save')
                ->label('Enregistrer')
                ->submit('save'),
        ];
    }

    public function save(): void
    {
        $settings = SiteSetting::first();
        $oldFavicon = $settings?->favicon_source;
        $oldOgImage = $settings?->og_image;

        $data = $this->form->getState();

        if ($settings) {
            $settings->update($data);
        } else {
            $settings = SiteSetting::create($data);
        }

        // Nettoyage des anciens fichiers sources s'ils ont été remplacés
        if ($oldFavicon && $oldFavicon !== $settings->favicon_source) {
            Storage::disk('public')->delete($oldFavicon);
        }
        if ($oldOgImage && $oldOgImage !== $settings->og_image) {
            Storage::disk('public')->delete($oldOgImage);
        }

        $this->form->fill($settings->toArray());

        Notification::make()
            ->title('Paramètres enregistrés')
            ->success()
            ->send();
    }

    public function generateFavicons(FaviconService $faviconService): void
    {
        $settings = SiteSetting::first();

        if (! $settings || ! $settings->favicon_source) {
            Notification::make()
                ->title('Source favicon manquante')
                ->danger()
                ->send();

            return;
        }

        try {
            $faviconService->generateFromSource($settings->favicon_source, $settings->is_rounded ?? false);
            Notification::make()
                ->title('Favicons générés avec succès')
                ->success()
                ->send();
        } catch (\Exception $e) {
            Notification::make()
                ->title('Erreur lors de la génération')
                ->body($e->getMessage())
                ->danger()
                ->send();
        }
    }
}
