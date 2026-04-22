<?php

namespace App\Filament\Pages;

use App\Models\Setting;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\Actions;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Utilities\Get;
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
            'ads_client_id' => Setting::get('ads_client_id', ''),
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
                            ->helperText(__('filament.ads.helper'))
                            ->live(),
                    ]),

                Section::make(__('filament.ads.config_section'))
                    ->visible(fn (Get $get): bool => $get('ads_enabled'))
                    ->description(__('filament.ads.config_description'))
                    ->schema([
                        TextInput::make('ads_client_id')
                            ->label(__('filament.ads.client_id_label'))
                            ->placeholder(__('filament.ads.client_id_placeholder'))
                            ->helperText(__('filament.ads.client_id_help'))
                            ->required(fn (Get $get): bool => $get('ads_enabled')),
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

            Setting::set(
                'ads_client_id',
                (string) ($state['ads_client_id'] ?? ''),
                'string',
                'Google AdSense Client ID'
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
