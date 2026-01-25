<?php

namespace Tests\Feature;

use App\Enums\PromoStatus;
use App\Models\ApiClient;
use App\Models\Promo;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PromoApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_promo_api_returns_new_display_fields(): void
    {
        $user = User::factory()->create();
        $apiClient = ApiClient::factory()->create();

        $promo = Promo::create([
            'slug' => 'test-promo',
            'author_name' => 'John Doe',
            'author_role' => 'Designer',
            'title' => 'Test Promo',
            'content' => 'Test Content',
            'status' => PromoStatus::PUBLISHED,
            'starts_at' => now()->subDay(),
            'ends_at' => now()->addDay(),
            'priority' => 5,
            'max_impressions' => 10,
            'cooldown_seconds' => 3600,
            'display_mode' => 'once_per_day',
            'created_by' => $user->id,
        ]);

        $response = $this->withHeaders([
            'X-API-KEY' => $apiClient->api_key,
        ])->getJson('/api/v1/promo/banner.json');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    'id',
                    'author_name',
                    'author_role',
                    'title',
                    'content',
                    'max_impressions',
                    'cooldown_seconds',
                    'display_mode',
                    'start_date',
                    'end_date',
                ],
            ])
            ->assertJson([
                'data' => [
                    'author_name' => 'John Doe',
                    'author_role' => 'Designer',
                    'max_impressions' => 10,
                    'cooldown_seconds' => 3600,
                    'display_mode' => 'once_per_day',
                    'start_date' => $promo->starts_at->format('Y-m-d'),
                    'end_date' => $promo->ends_at->format('Y-m-d'),
                ],
            ]);
    }
}
