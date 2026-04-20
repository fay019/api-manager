<?php

namespace Database\Factories;

use App\Models\ApiRequestLog;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ApiRequestLog>
 */
class ApiRequestLogFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $methods = ['GET', 'POST', 'PUT', 'DELETE', 'PATCH'];
        $statusCode = $this->faker->randomElement([
            $this->faker->numberBetween(200, 299), // 2xx (success)
            $this->faker->numberBetween(200, 299),
            $this->faker->numberBetween(200, 299),
            $this->faker->numberBetween(400, 499), // 4xx (client error)
            $this->faker->numberBetween(500, 599), // 5xx (server error)
        ]);

        $domainName = $this->faker->domainName();

        return [
            'method' => $this->faker->randomElement($methods),
            'path' => $this->faker->url(),
            'status_code' => $statusCode,
            'ip' => $this->faker->ipv4(),
            'hostname' => 'host-'.$this->faker->word(),
            'domain' => $domainName,
            'site_name' => $this->faker->word(),
            'page_path' => '/'.$this->faker->slug(),
            'full_url' => 'https://'.$domainName.'/'.$this->faker->slug(),
            'client_request_time' => now()->subDays(rand(0, 30))->format('Y-m-d H:i:s'),
            'client_user_agent' => $this->faker->userAgent(),
            'user_agent' => $this->faker->userAgent(),
            'origin' => 'https://'.$this->faker->domainName(),
            'referer' => 'https://'.$this->faker->domainName().'/'.$this->faker->slug(),
            'duration_ms' => $this->faker->numberBetween(10, 5000),
            'created_at' => now()->subDays(rand(0, 30))->format('Y-m-d H:i:s'),
        ];
    }
}
