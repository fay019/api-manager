<?php

namespace App\Filament\Pages;

use App\Models\Setting;
use BackedEnum;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use UnitEnum;

class ManageAdsSettings extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $slug = 'manage-ads-settings';

    protected static BackedEnum|string|null $navigationIcon = 'heroicon-o-megaphone';

    protected static string|UnitEnum|null $navigationGroup = 'Settings';

    protected static ?int $navigationSort = 100;

    protected static ?string $title = 'Ads Settings';

    protected string $view = 'filament.pages.manage-ads-settings';

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill([
            'ads_enabled' => Setting::get('ads_enabled', app()->environment('production')),
        ]);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Google AdSense Configuration')
                    ->description('Manage AdSense settings for your application')
                    ->extraAttributes(['class' => 'mb-6'])
                    ->schema([
                        Toggle::make('ads_enabled')
                            ->label('Enable AdSense')
                            ->helperText('Enable or disable Google AdSense across the application'),
                    ]),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        try {
            $state = $this->form->getState();

            Setting::set(
                'ads_enabled',
                (bool) $state['ads_enabled'],
                'boolean',
                'Enable or disable Google AdSense'
            );

            Notification::make()
                ->success()
                ->title('Settings saved')
                ->body('Ads settings have been updated successfully.')
                ->send();
        } catch (\Exception $e) {
            Notification::make()
                ->danger()
                ->title('Error saving settings')
                ->body($e->getMessage())
                ->send();
        }
    }
}
