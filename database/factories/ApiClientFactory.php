<?php

namespace Database\Factories;

use App\Models\ApiClient;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ApiClient>
 */
class ApiClientFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = ApiClient::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->company(),
            'contact_email' => $this->faker->companyEmail(),
            'contact_name' => $this->faker->name(),
            'website' => $this->faker->url(),
            'client_type' => $this->faker->randomElement(['MOBILE', 'WEB', 'PARTNER', 'INTERNAL']),
            'description' => $this->faker->sentence(),
            'is_active' => $this->faker->boolean(80),
            'allowed_origins' => [$this->faker->url(), $this->faker->url()],
            'notes' => $this->faker->paragraph(),
            'rate_limit_per_minute' => $this->faker->numberBetween(30, 120),
            'monthly_quota' => $this->faker->randomElement([null, 1000, 5000, 10000, 50000]),
            'webhook_url' => $this->faker->url(),
            'activated_at' => $this->faker->dateTimeBetween('-1 year', 'now'),
        ];
    }
}
