<?php

namespace App\Filament\Pages;

use App\Models\Setting;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use UnitEnum;

class ManageAdsSettings extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $slug = 'manage-ads-settings';

    protected static BackedEnum|string|null $navigationIcon = 'heroicon-o-megaphone';

    protected static string|UnitEnum|null $navigationGroup = 'Settings';

    protected static ?int $navigationSort = 100;

    public ?array $data = [];

    protected string $view = 'filament.pages.manage-ads-settings';

    public function getTitle(): string
    {
        return __('filament.ads.title');
    }

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
                Section::make(__('filament.ads.section'))
                    ->description(__('filament.ads.section_desc'))
                    ->schema([
                        Toggle::make('ads_enabled')
                            ->label(fn (Get $get): string => $get('ads_enabled') ? __('filament.common.enabled') : __('filament.common.disabled'))
                            ->helperText(__('filament.ads.helper') ?? 'Enable or disable Google AdSense across the application')
                            ->live(),
                    ]),

                Actions::make([
                    Action::make('save')
                        ->label(__('filament.actions.save'))
                        ->action('save')
                        ->keyBindings(['mod+s']),
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
                (bool) ($state['ads_enabled'] ?? false),
                'boolean',
                'Enable or disable Google AdSense'
            );

            Notification::make()
                ->success()
                ->title(__('filament.ads.saved'))
                ->body(__('filament.ads.saved') ?? 'Ads settings have been updated successfully.')
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
