<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;

class NpmDependenciesWidget extends Widget
{
    protected static ?int $sort = 4;

    protected string $view = 'filament.widgets.npm-dependencies';

    public function getDependencies(): array
    {
        $lockPath = base_path('package-lock.json');

        if (! file_exists($lockPath)) {
            return [];
        }

        $lockData = json_decode(file_get_contents($lockPath), true);
        $packages = $lockData['packages'] ?? [];

        $keyDeps = [
            'tailwindcss' => ['name' => 'Tailwind CSS', 'icon' => 'si-tailwindcss', 'color' => '#06b6d4'],
            'vite' => ['name' => 'Vite', 'icon' => 'si-vite', 'color' => '#646cff'],
            '@tailwindcss/vite' => ['name' => 'Tailwind Vite', 'icon' => 'si-tailwindcss', 'color' => '#06b6d4'],
            'laravel-vite-plugin' => ['name' => 'Laravel Vite', 'icon' => 'si-laravel', 'color' => '#dc2626'],
            'axios' => ['name' => 'Axios', 'icon' => 'si-axios', 'color' => '#5a29e4'],
            'alpinejs' => ['name' => 'Alpine.js', 'icon' => 'si-alpinedotjs', 'color' => '#77c1d3'],
        ];

        $result = [];
        foreach ($keyDeps as $packageName => $config) {
            $packageKey = "node_modules/$packageName";
            if (isset($packages[$packageKey])) {
                $package = $packages[$packageKey];
                $result[] = [
                    'name' => $config['name'],
                    'package' => $packageName,
                    'version' => $package['version'] ?? 'N/A',
                    'icon' => $config['icon'],
                    'color' => $config['color'],
                ];
            }
        }

        return $result;
    }
}
