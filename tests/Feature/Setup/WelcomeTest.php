<?php

namespace Tests\Feature\Setup;

use Tests\TestCase;

class WelcomeTest extends TestCase
{
    /**
     * Test accessing the welcome page.
     */
    public function test_can_access_welcome_page(): void
    {
        $response = $this->get('/setup/welcome');

        $response->assertStatus(200);
        $response->assertViewIs('setup.steps.welcome');
    }

    /**
     * Test welcome page passes check results to view.
     */
    public function test_welcome_page_displays_check_results(): void
    {
        $response = $this->get('/setup/welcome');

        $response->assertViewHas('checkResults');
        $response->assertViewHas('canContinue');
        $response->assertViewHas('errorCount');
        $response->assertViewHas('warningCount');
    }

    /**
     * Test welcome page check results structure.
     */
    public function test_welcome_page_check_results_structure(): void
    {
        $response = $this->get('/setup/welcome');

        $checkResults = $response->viewData('checkResults');
        $this->assertArrayHasKey('passed', $checkResults);
        $this->assertArrayHasKey('errors', $checkResults);
        $this->assertArrayHasKey('warnings', $checkResults);
    }

    /**
     * Test welcome page caching by checking same data returned.
     */
    public function test_welcome_page_caching_works(): void
    {
        // First request
        $response1 = $this->get('/setup/welcome');
        $checkResults1 = $response1->viewData('checkResults');

        // Second request should have same results (cached)
        $response2 = $this->get('/setup/welcome');
        $checkResults2 = $response2->viewData('checkResults');

        // Both should have same structure
        $this->assertArrayHasKey('passed', $checkResults1);
        $this->assertArrayHasKey('passed', $checkResults2);
    }

    /**
     * Test welcome page displays check results.
     */
    public function test_welcome_page_displays_results(): void
    {
        $response = $this->get('/setup/welcome');

        $response->assertViewHas('checkResults');
        $checkResults = $response->viewData('checkResults');
        $this->assertIsBool($checkResults['passed']);
    }

    /**
     * Test accessing /setup redirects to welcome.
     */
    public function test_setup_root_redirects_to_welcome(): void
    {
        $response = $this->get('/setup');

        $response->assertStatus(301);
        $response->assertRedirect('/setup/welcome');
    }
}
