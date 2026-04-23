<?php

namespace App\Filament\Resources\SeoMetaResource\Pages;

use App\Filament\Resources\SeoMetaResource;
use App\Models\SeoMeta;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

class ListSeoMetas extends ListRecords
{
    protected static string $resource = SeoMetaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('scanRoutes')
                ->label(__('filament.seo_meta.scan_routes'))
                ->icon('heroicon-o-magnifying-glass')
                ->color('info')
                ->action(function () {
                    $routes = Route::getRoutes();
                    $newEntriesCount = 0;
                    $ignoredPatterns = [
                        'admin*',
                        'filament*',
                        'livewire*',
                        'sanctum*',
                        '_boost*',
                        'api*',
                        'test*',
                        'horizon*',
                        'telescope*',
                        '_debugbar*',
                    ];

                    foreach ($routes as $route) {
                        $name = $route->getName();
                        $uri = $route->uri();

                        // Ignorer les routes sans nom ou qui correspondent aux patterns ignorés
                        if (! $name || Str::is($ignoredPatterns, $name) || Str::is($ignoredPatterns, $uri)) {
                            continue;
                        }

                        // Ignorer les méthodes autres que GET
                        if (! in_array('GET', $route->methods())) {
                            continue;
                        }

                        // Vérifier si elle existe déjà
                        $exists = SeoMeta::query()
                            ->where('route_name', $name)
                            ->orWhere('url', $uri)
                            ->exists();

                        if (! $exists) {
                            SeoMeta::create([
                                'route_name' => $name,
                                'url' => '/'.ltrim($uri, '/'),
                                'locale' => config('app.locale', 'fr'),
                                'title' => ucfirst(str_replace(['.', '-', '_'], ' ', $name)),
                                'description' => '',
                                'is_auto_generated' => true,
                                'robots' => 'index,follow',
                            ]);
                            $newEntriesCount++;
                        }
                    }

                    if ($newEntriesCount > 0) {
                        Notification::make()
                            ->title(__('filament.seo_meta.scan_success', ['count' => $newEntriesCount]))
                            ->success()
                            ->send();
                    } else {
                        Notification::make()
                            ->title(__('filament.seo_meta.scan_no_new'))
                            ->info()
                            ->send();
                    }
                }),
            Actions\CreateAction::make(),
        ];
    }
}
