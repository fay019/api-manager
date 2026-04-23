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
                ApiKey::create([
                    'api_client_id' => $client->id,
                    'name' => 'API Key ' . ($i + 1),
                    'key_prefix' => 'sk_' . substr(hash('sha256', 'test-key-' . $client->id . '-' . $i), 0, 16),
                    'key_encrypted' => hash('sha256', 'test-key-' . $client->id . '-' . $i),
                    'is_active' => true,
                ]);
            }
        }
    }
}
