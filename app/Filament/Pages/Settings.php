<?php

namespace App\Filament\Pages;

use App\Models\Setting;
use BackedEnum;
use Exception;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Artisan;
use UnitEnum;

class Settings extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $slug = 'settings';

    protected static BackedEnum|string|null $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected static string|UnitEnum|null $navigationGroup = 'Settings';

    protected static ?string $title = 'Paramètres Généraux';

    public function getTitle(): string
    {
        return 'Paramètres Généraux';
    }

    protected string $view = 'filament.pages.settings';

    public ?array $data = [];

    public function mount(): void
    {
        $siteSettings = \App\Models\SiteSetting::first();
        $this->form->fill([
            'site_name' => $siteSettings?->site_name ?? config('app.name'),
            'contact_email' => Setting::get('contact_email'),
        ]);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                \Filament\Schemas\Components\Section::make('Identité Visuelle & SEO')
                    ->schema([
                        TextInput::make('site_name')
                            ->label('Nom du Site (Public)')
                            ->helperText('Ce nom sera utilisé pour les balises SEO, le titre de l\'onglet et les favicons. Il peut être différent du nom système.')
                            ->required(),
                    ]),
                \Filament\Schemas\Components\Section::make('Contact')
                    ->schema([
                        TextInput::make('contact_email')
                            ->label('Email de Contact')
                            ->email()
                            ->required()
                            ->placeholder('admin@example.com'),
                    ]),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        try {
            $state = $this->form->getState();

            // Sauvegarde dans SiteSetting (pour SEO/Favicons)
            $siteSettings = \App\Models\SiteSetting::firstOrNew([]);
            $siteSettings->site_name = $state['site_name'];
            $siteSettings->save();

            // Sauvegarde dans Setting (Email)
            Setting::set('contact_email', $state['contact_email'], 'string', 'Email address for contact form');

            Notification::make()
                ->success()
                ->title('Paramètres enregistrés')
                ->send();
        } catch (Exception $e) {
            Notification::make()
                ->danger()
                ->title('Error')
                ->body($e->getMessage())
                ->send();
        }
    }

    public function resetApplication(): void
    {
        try {
            Artisan::call('app:reset');
            Notification::make()
                ->success()
                ->title('Application reset successfully')
                ->send();
        } catch (Exception $e) {
            Notification::make()
                ->danger()
                ->title('Reset failed')
                ->body($e->getMessage())
                ->send();
        }
    }
}
