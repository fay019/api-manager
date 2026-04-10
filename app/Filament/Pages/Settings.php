<?php

namespace App\Filament\Pages;

use App\Models\Setting;
use BackedEnum;
use Exception;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Schema;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use UnitEnum;

class Settings extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $slug = 'settings';

    protected static BackedEnum|string|null $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected static string|UnitEnum|null $navigationGroup = 'Settings';

    protected static ?string $title = 'Paramètres de l\'Application';

    public function getTitle(): string
    {
        return 'Paramètres de l\'Application';
    }

    protected string $view = 'filament.pages.settings';

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill([
            'contact_email' => Setting::get('contact_email'),
        ]);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('contact_email')
                    ->label('Email de Contact')
                    ->email()
                    ->required()
                    ->placeholder('admin@example.com'),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        try {
            $state = $this->form->getState();
            Setting::set('contact_email', $state['contact_email'], 'string', 'Email address for contact form');

            Notification::make()
                ->success()
                ->title('Settings saved')
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
