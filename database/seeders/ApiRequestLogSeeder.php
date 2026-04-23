<?php

namespace Database\Seeders;

use App\Models\ApiKey;
use App\Models\ApiRequestLog;
use Illuminate\Database\Seeder;

class ApiRequestLogSeeder extends Seeder
{
    public function run(): void
    {
        $keys = ApiKey::all();

        foreach ($keys as $key) {
            for ($i = 0; $i < rand(5, 20); $i++) {
                ApiRequestLog::create([
                    'api_client_id' => $key->apiClient->id,
                    'api_key_id' => $key->id,
                    'method' => 'GET',
                    'path' => '/api/test',
                    'status_code' => 200,
                    'ip' => '127.0.0.1',
                    'duration_ms' => rand(10, 500),
                ]);
            }
        }
    }
}
