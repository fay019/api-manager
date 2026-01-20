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
        ApiClient::factory()->count(10)->create();
    }
}
