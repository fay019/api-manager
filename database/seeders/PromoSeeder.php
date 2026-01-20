<?php

namespace Database\Seeders;

use App\Models\Promo;
use App\Models\User;
use App\Enums\PromoStatus;
use Illuminate\Database\Seeder;

class PromoSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::first();

        if (!$admin) {
            return;
        }

        Promo::create([
            'title' => 'Bienvenue sur l\'API Manager',
            'content' => 'Découvrez toutes les fonctionnalités de notre nouvelle interface de gestion d\'API.',
            'image_url' => null,
            'cta_text' => 'Voir la doc',
            'cta_url' => '/docs',
            'status' => PromoStatus::PUBLISHED,
            'starts_at' => now(),
            'ends_at' => now()->addMonths(1),
            'priority' => 10,
            'created_by' => $admin->id,
        ]);

        Promo::create([
            'title' => 'Maintenance prévue',
            'content' => 'Une maintenance aura lieu ce dimanche entre 2h et 4h du matin.',
            'status' => PromoStatus::SCHEDULED,
            'starts_at' => now()->addDays(5),
            'priority' => 10,
            'created_by' => $admin->id,
        ]);
    }
}
