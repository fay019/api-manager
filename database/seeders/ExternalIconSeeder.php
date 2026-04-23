<?php

namespace Database\Seeders;

use App\Models\ExternalIcon;
use Illuminate\Database\Seeder;

class ExternalIconSeeder extends Seeder
{
    public function run(): void
    {
        $icons = [
            [
                'name' => 'Home',
                'slug' => 'home',
                'provider' => 'heroicons',
                'type' => 'svg',
                'source' => '<path stroke-linecap="round" stroke-linejoin="round" d="m2.25 12 8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25" />',
                'color' => '#3b82f6',
                'tags' => ['navigation', 'main'],
                'is_active' => true,
            ],
            [
                'name' => 'User',
                'slug' => 'user',
                'provider' => 'heroicons',
                'type' => 'svg',
                'source' => '<path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" />',
                'color' => '#10b981',
                'tags' => ['auth', 'profile'],
                'is_active' => true,
            ],
            [
                'name' => 'Settings',
                'slug' => 'settings',
                'provider' => 'heroicons',
                'type' => 'svg',
                'source' => '<path stroke-linecap="round" stroke-linejoin="round" d="M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.324.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 0 1 1.37.49l1.296 2.247a1.125 1.125 0 0 1-.26 1.431l-1.003.827c-.293.24-.438.613-.431.992a6.759 6.759 0 0 1 0 .255c-.007.378.138.75.43.99l1.005.828c.424.35.534.954.26 1.43l-1.298 2.247a1.125 1.125 0 0 1-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.57 6.57 0 0 1-.22.128c-.331.183-.581.495-.644.869l-.213 1.28c-.09.543-.56.941-1.11.941h-2.594c-.55 0-1.02-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 0 1-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 0 1-1.369-.49l-1.297-2.247a1.125 1.125 0 0 1 .26-1.431l1.004-.827c.292-.24.437-.613.43-.992a6.932 6.932 0 0 1 0-.255c.007-.378-.138-.75-.43-.99l-1.004-.828a1.125 1.125 0 0 1-.26-1.43l1.297-2.247a1.125 1.125 0 0 1 1.37-.491l1.246.467c.33.123.711.074 1.03-.114.073-.044.147-.086.22-.127.332-.183.582-.495.644-.869l.214-1.281Z" /><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />',
                'color' => '#6366f1',
                'tags' => ['config', 'admin'],
                'is_active' => true,
            ],
            [
                'name' => 'Laravel',
                'slug' => 'laravel',
                'provider' => 'devicon',
                'type' => 'cdn',
                'source' => 'https://cdn.jsdelivr.net/gh/devicons/devicon/icons/laravel/laravel-original.svg',
                'color' => '#ff2d20',
                'tags' => ['framework', 'php'],
                'is_active' => true,
            ],
            [
                'name' => 'Tailwind CSS',
                'slug' => 'tailwind',
                'provider' => 'devicon',
                'type' => 'cdn',
                'source' => 'https://cdn.jsdelivr.net/gh/devicons/devicon/icons/tailwindcss/tailwindcss-original.svg',
                'color' => '#38b2ac',
                'tags' => ['css', 'styling'],
                'is_active' => true,
            ],
            [
                'name' => 'PHP',
                'slug' => 'php',
                'provider' => 'devicon',
                'type' => 'cdn',
                'source' => 'https://cdn.jsdelivr.net/gh/devicons/devicon/icons/php/php-original.svg',
                'color' => '#777bb4',
                'tags' => ['language', 'backend'],
                'is_active' => true,
            ],
        ];

        foreach ($icons as $icon) {
            ExternalIcon::updateOrCreate(['slug' => $icon['slug']], $icon);
        }
    }
}
