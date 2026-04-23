<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class GoogleAdSenseSeeder extends Seeder
{
    public function run(): void
    {
        Setting::updateOrCreate(
            ['key' => 'ads_enabled'],
            ['value' => 'false', 'type' => 'boolean']
        );
    }
}
