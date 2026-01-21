<?php

namespace App\Filament\Pages;

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
        return 'Health Check API';
    }

    public function getTitle(): string
    {
        return 'Health Check Configuration';
    }

    public function toggleCheck(string $check): void
    {
        $settings = HealthCheckSetting::getInstance();
        $attribute = $check . '_enabled';

        if (property_exists($settings, $attribute)) {
            $settings->{$attribute} = !$settings->{$attribute};
            $settings->save();

            Notification::make()
                ->success()
                ->title('Updated')
                ->body(ucfirst(str_replace('_', ' ', $check)) . ' check ' . ($settings->{$attribute} ? 'enabled' : 'disabled') . '.')
                ->send();
        }
    }

    public function testHealthCheck(): void
    {
        try {
            $controller = new \App\Http\Controllers\Api\HealthController();
            $response = $controller->index();
            $content = json_decode($response->getContent(), true);

            session()->flash('health_check_result', $content);

            Notification::make()
                ->success()
                ->title('Health Check Executed')
                ->body('Scroll down to see the results.')
                ->send();
        } catch (\Exception $e) {
            Notification::make()
                ->danger()
                ->title('Health Check Failed')
                ->body('Error: ' . $e->getMessage())
                ->send();
        }
    }
}
