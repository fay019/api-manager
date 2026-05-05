<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            ClientSeeder::class,
            DocumentationSettingSeeder::class,
            ApiClientSeeder::class,
            ApiKeySeeder::class,
            ApiRequestLogSeeder::class,
            ExternalIconSeeder::class,
            GoogleAdSenseSeeder::class,
        ]);
    }
}
