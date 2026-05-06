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
            $client = ApiClient::create([
                'name' => 'Test Client ' . ($i + 1),
                'website' => 'https://example' . $i . '.com',
                'client_type' => 'WEB',
                'is_active' => true,
                'allowed_origins' => json_encode(['https://example.com']),
                'rate_limit_per_minute' => 60,
                'monthly_quota' => 10000,
            ]);

            // Ajouter les colonnes IA si elles existent (après migration Phase 1)
            if ($i % 3 === 0 && \Schema::hasColumn('api_clients', 'type')) {
                $client->update([
                    'type' => 'ia',
                    'allowed_endpoints' => json_encode(['api/v1/ai/generate', 'api/v1/ai/models']),
                ]);
            } elseif (\Schema::hasColumn('api_clients', 'type')) {
                $client->update([
                    'type' => 'ia',
                    'allowed_endpoints' => null,
                ]);
            }
        }
    }
}
