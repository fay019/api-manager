<?php

namespace App\Filament\Pages;

use App\Http\Controllers\Api\HealthController;
use App\Models\HealthCheckSetting;
use BackedEnum;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use UnitEnum;

class HealthCheckSettings extends Page
{
    protected static BackedEnum|string|null $navigationIcon = 'heroicon-o-heart';

    protected string $view = 'filament.pages.health-check-settings';

    protected static string|UnitEnum|null $navigationGroup = 'System';

    protected static ?int $navigationSort = 100;

    public static function getNavigationLabel(): string
    {
        return __('filament.health.nav_label') ?? 'Health Check API';
    }

    public function getTitle(): string
    {
        return __('filament.health.title') ?? 'Health Check Configuration';
    }

    public function toggleCheck(string $check): void
    {
        $settings = HealthCheckSetting::getInstance();
        $attribute = $check.'_enabled';

        if (property_exists($settings, $attribute)) {
            $settings->{$attribute} = ! $settings->{$attribute};
            $settings->save();

            Notification::make()
                ->success()
                ->title(__('filament.health.updated'))
                ->body(ucfirst(str_replace('_', ' ', $check)).' check '.($settings->{$attribute} ? __('filament.health.check_enabled') : __('filament.health.check_disabled')).'.')
                ->send();
        }
    }

    public function testHealthCheck(): void
    {
        try {
            $controller = new HealthController;
            $response = $controller->index();
            $content = json_decode($response->getContent(), true);

            session()->flash('health_check_result', $content);

            Notification::make()
                ->success()
                ->title(__('filament.health.executed_title'))
                ->body(__('filament.health.executed_body'))
                ->send();
        } catch (\Exception $e) {
            Notification::make()
                ->danger()
                ->title(__('filament.health.failed_title'))
                ->body(__('filament.health.failed_body').' '.$e->getMessage())
                ->send();
        }
    }
}
