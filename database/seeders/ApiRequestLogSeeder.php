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
            ApiRequestLog::factory(rand(5, 20))
                ->for($key->apiClient)
                ->for($key)
                ->create();
        }
    }
}
