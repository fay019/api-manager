<?php

namespace Database\Seeders;

use App\Models\ApiClient;
use App\Models\ApiKey;
use Illuminate\Database\Seeder;

class ApiKeySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $clients = ApiClient::all();

        foreach ($clients as $client) {
            for ($i = 0; $i < rand(2, 4); $i++) {
                $keyData = [
                    'api_client_id' => $client->id,
                    'name' => 'API Key ' . ($i + 1),
                    'key_prefix' => 'sk_' . substr(hash('sha256', $client->id . $i), 0, 5),
                    'key_encrypted' => hash('sha256', 'test-key-' . $client->id . '-' . $i),
                    'is_active' => true,
                ];

                // Ajouter colonnes IA si elles existent (après migration Phase 1)
                if (\Schema::hasColumn('api_keys', 'ip_whitelist')) {
                    $keyData['ip_whitelist'] = $i === 0 ? json_encode(['192.168.1.100', '10.0.0.0/8']) : null;
                }
                if (\Schema::hasColumn('api_keys', 'allowed_endpoints')) {
                    $keyData['allowed_endpoints'] = $i === 1 ? json_encode(['api/v1/ai/generate']) : null;
                }

                ApiKey::create($keyData);
            }
        }
    }
}
