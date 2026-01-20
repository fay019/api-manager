<?php

namespace Database\Seeders;

use App\Models\AppSetting;
use Illuminate\Database\Seeder;

class AppSettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        AppSetting::updateOrCreate(
            ['id' => 1],
            [
                'show_admin_credentials' => true,
                'visible_docs' => ['readme', 'api', 'database', 'deployment'],
            ]
        );
    }
}
