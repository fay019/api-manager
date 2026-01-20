<?php

namespace Database\Seeders;

use App\Services\DocumentationScanner;
use Illuminate\Database\Seeder;

class DocumentationSettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Scan and sync all documentation files
        DocumentationScanner::sync();
    }
}
