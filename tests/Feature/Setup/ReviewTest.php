<?php

namespace Tests\Feature\Setup;

use Tests\TestCase;

class ReviewTest extends TestCase
{
    /**
     * Test accessing the review page without complete data shows warnings.
     */
    public function test_review_page_shows_incomplete_warning(): void
    {
        $response = $this->get('/setup/review');

        $response->assertStatus(200);
        $response->assertViewIs('setup.steps.review');
        $response->assertViewHas('isComplete', false);
    }

    /**
     * Test review page displays configuration summary.
     */
    public function test_review_page_displays_summary(): void
    {
        // Set up session data
        $this->withSession([
            'setup.app_name' => 'Test App',
            'setup.app_url' => 'https://test.local',
            'setup.app_env' => 'local',
            'setup.timezone' => 'UTC',
            'setup.locale' => 'en',
            'setup.database_driver' => 'sqlite',
            'setup.database_database' => 'database.sqlite',
            'setup.mail_driver' => 'smtp',
            'setup.mail_host' => 'smtp.example.com',
            'setup.mail_port' => '587',
            'setup.mail_from_address' => 'noreply@example.com',
            'setup.mail_from_name' => 'Test App',
            'setup.admin_name' => 'Admin User',
            'setup.admin_email' => 'admin@example.com',
            'setup.admin_password' => 'SecurePassword123!',
        ]);

        $response = $this->get('/setup/review');

        $response->assertSee('Test App');
        $response->assertSee('https://test.local');
        $response->assertSee('Admin User');
        $response->assertSee('admin@example.com');
    }

    /**
     * Test review page masks passwords.
     */
    public function test_review_page_masks_passwords(): void
    {
        $this->withSession([
            'setup.app_name' => 'Test App',
            'setup.app_url' => 'https://test.local',
            'setup.app_env' => 'local',
            'setup.timezone' => 'UTC',
            'setup.locale' => 'en',
            'setup.database_driver' => 'sqlite',
            'setup.database_database' => 'database.sqlite',
            'setup.mail_driver' => 'smtp',
            'setup.mail_host' => 'smtp.example.com',
            'setup.mail_port' => '587',
            'setup.mail_from_address' => 'noreply@example.com',
            'setup.mail_from_name' => 'Test App',
            'setup.admin_name' => 'Admin User',
            'setup.admin_email' => 'admin@example.com',
            'setup.admin_password' => 'SecurePassword123!',
        ]);

        $response = $this->get('/setup/review');

        $response->assertDontSee('SecurePassword123!');
    }

    /**
     * Test review page indicates missing app settings.
     */
    public function test_review_page_warns_missing_app_settings(): void
    {
        $response = $this->get('/setup/review');

        $response->assertViewHas('isComplete', false);
        $this->assertContains('incomplets', $response->viewData('warnings') ?? []);
    }

    /**
     * Test review page shows all configuration sections.
     */
    public function test_review_page_displays_all_sections(): void
    {
        $this->withSession([
            'setup.app_name' => 'Test App',
            'setup.app_url' => 'https://test.local',
            'setup.app_env' => 'local',
            'setup.timezone' => 'UTC',
            'setup.locale' => 'en',
            'setup.database_driver' => 'sqlite',
            'setup.database_database' => 'database.sqlite',
            'setup.mail_driver' => 'smtp',
            'setup.mail_host' => 'smtp.example.com',
            'setup.mail_port' => '587',
            'setup.mail_from_address' => 'noreply@example.com',
            'setup.mail_from_name' => 'Test App',
            'setup.admin_name' => 'Admin User',
            'setup.admin_email' => 'admin@example.com',
            'setup.admin_password' => 'SecurePassword123!',
        ]);

        $response = $this->get('/setup/review');

        // Check that all config arrays are passed to view
        $this->assertNotNull($response->viewData('appSettings'));
        $this->assertNotNull($response->viewData('database'));
        $this->assertNotNull($response->viewData('mail'));
        $this->assertNotNull($response->viewData('admin'));
    }

    /**
     * Test review displays database driver type.
     */
    public function test_review_displays_database_driver(): void
    {
        $this->withSession([
            'setup.app_name' => 'Test App',
            'setup.app_url' => 'https://test.local',
            'setup.app_env' => 'local',
            'setup.timezone' => 'UTC',
            'setup.locale' => 'en',
            'setup.database_driver' => 'mysql',
            'setup.database_host' => 'localhost',
            'setup.database_port' => '3306',
            'setup.database_database' => 'api_manager',
            'setup.mail_driver' => 'smtp',
            'setup.mail_host' => 'smtp.example.com',
            'setup.mail_port' => '587',
            'setup.mail_from_address' => 'noreply@example.com',
            'setup.mail_from_name' => 'Test App',
            'setup.admin_name' => 'Admin User',
            'setup.admin_email' => 'admin@example.com',
            'setup.admin_password' => 'SecurePassword123!',
        ]);

        $response = $this->get('/setup/review');

        $database = $response->viewData('database');
        $this->assertEquals('mysql', $database['driver']);
        $this->assertEquals('localhost', $database['host']);
    }

    /**
     * Test review page shows N/A for missing optional fields.
     */
    public function test_review_shows_na_for_missing_fields(): void
    {
        $response = $this->get('/setup/review');

        $response->assertSee('N/A');
    }

    /**
     * Test review page passes step information to view.
     */
    public function test_review_passes_step_info(): void
    {
        $response = $this->get('/setup/review');

        $this->assertEquals(6, $response->viewData('currentStep'));
        $this->assertEquals(7, $response->viewData('totalSteps'));
    }

    /**
     * Test review shows complete status when all data present.
     */
    public function test_review_shows_complete_when_all_data_present(): void
    {
        $this->withSession([
            'setup.app_name' => 'Test App',
            'setup.app_url' => 'https://test.local',
            'setup.app_env' => 'local',
            'setup.timezone' => 'UTC',
            'setup.locale' => 'en',
            'setup.database_driver' => 'sqlite',
            'setup.database_database' => 'database.sqlite',
            'setup.mail_driver' => 'log',
            'setup.mail_from_address' => 'noreply@example.com',
            'setup.mail_from_name' => 'Test App',
            'setup.admin_name' => 'Admin User',
            'setup.admin_email' => 'admin@example.com',
            'setup.admin_password' => 'SecurePassword123!',
        ]);

        $response = $this->get('/setup/review');

        $response->assertViewHas('isComplete', true);
    }
}
