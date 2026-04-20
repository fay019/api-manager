<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;

class DependenciesWidget extends Widget
{
    protected static ?int $sort = 3;

    protected string $view = 'filament.widgets.dependencies';

    public function getDependencies(): array
    {
        $lockPath = base_path('composer.lock');

        if (! file_exists($lockPath)) {
            return [];
        }

        $lockData = json_decode(file_get_contents($lockPath), true);
        $packages = array_merge(
            $lockData['packages'] ?? [],
            $lockData['packages-dev'] ?? []
        );

        $keyDeps = [
            'filament/filament' => ['name' => 'Filament', 'icon' => 'si-filament', 'color' => '#f59e0b'],
            'livewire/livewire' => ['name' => 'Livewire', 'icon' => 'si-livewire', 'color' => '#4f46e5'],
            'blade-ui-kit/blade-icons' => ['name' => 'Blade Icons', 'icon' => 'heroicon-o-sparkles', 'color' => '#8b5cf6'],
            'blade-ui-kit/blade-heroicons' => ['name' => 'Blade Heroicons', 'icon' => 'heroicon-o-star', 'color' => '#ec4899'],
            'postare/blade-mdi' => ['name' => 'blade-mdi', 'icon' => 'mdi-simple-icons', 'color' => '#ec4899'],
            'mallardduck/blade-lucide-icons' => ['name' => 'Lucide Icons', 'icon' => 'lucide-box', 'color' => '#06b6d4'],
            'codeat3/blade-devicons' => ['name' => 'Devicons', 'icon' => 'si-icon', 'color' => '#ea580c'],
            'tailwindcss/tailwindcss' => ['name' => 'Tailwind CSS', 'icon' => 'heroicon-o-paint-brush', 'color' => '#0ea5e9'],
        ];

        $result = [];
        foreach ($packages as $package) {
            $name = $package['name'];
            if (isset($keyDeps[$name])) {
                $config = $keyDeps[$name];
                $result[] = [
                    'name' => $config['name'],
                    'package' => $name,
                    'version' => $package['version'],
                    'icon' => $config['icon'],
                    'color' => $config['color'],
                ];
            }
        }

        return $result;
    }
}
