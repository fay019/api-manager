<?php

namespace Database\Factories;

use App\Models\ApiClient;
use App\Models\ApiKey;
use App\Services\ApiKeyService;
use Illuminate\Database\Eloquent\Factories\Factory;

class ApiKeyFactory extends Factory
{
    protected $model = ApiKey::class;

    public function definition(): array
    {
        $keyService = new ApiKeyService;
        $generatedKey = $keyService->generateKey();

        return [
            'api_client_id' => ApiClient::factory(),
            'key_encrypted' => $generatedKey['encrypted'],
            'key_prefix' => $generatedKey['prefix'],
            'name' => $this->faker->words(3, true),
            'starts_at' => $this->faker->dateTimeBetween('-3 months', 'now'),
            'expires_at' => $this->faker->boolean(70) ? $this->faker->dateTimeBetween('now', '+1 year') : null,
            'is_active' => $this->faker->boolean(85),
        ];
    }
}
