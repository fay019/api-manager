<?php

namespace Database\Seeders;

use App\Models\ApiClient;
use Illuminate\Database\Seeder;

class ApiClientSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Skip factory, create manually to avoid faker issues
        for ($i = 0; $i < 15; $i++) {
            ApiClient::create([
                'name' => 'Test Client ' . ($i + 1),
                'website' => 'https://example' . $i . '.com',
                'client_type' => 'WEB',
                'is_active' => true,
                'allowed_origins' => json_encode(['https://example.com']),
                'rate_limit_per_minute' => 60,
                'monthly_quota' => 10000,
            ]);
        }
    }
}
