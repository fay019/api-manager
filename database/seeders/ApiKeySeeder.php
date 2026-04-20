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
            ApiKey::factory(rand(2, 4))->for($client)->create();
        }
    }
}
