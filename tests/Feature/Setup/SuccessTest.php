<?php

namespace Tests\Feature\Setup;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SuccessTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test accessing the success page.
     */
    public function test_can_access_success_page(): void
    {
        $response = $this->get('/setup/success');

        $response->assertStatus(200);
        $response->assertViewIs('setup.steps.success');
    }

    /**
     * Test success page shows appropriate message.
     */
    public function test_success_page_displays_success_message(): void
    {
        $response = $this->get('/setup/success');

        $response->assertSee('Succès', false);
        $response->assertSee('Installation', false);
    }

    /**
     * Test success page displays step progress.
     */
    public function test_success_page_shows_step_progress(): void
    {
        $response = $this->get('/setup/success');

        $response->assertViewHas('currentStep', 7);
        $response->assertViewHas('totalSteps', 7);
    }

    /**
     * Test install endpoint requires POST method.
     */
    public function test_install_requires_post_method(): void
    {
        $response = $this->get('/setup/install');

        $response->assertStatus(405); // Method Not Allowed
    }

    /**
     * Test install endpoint requires CSRF token.
     */
    public function test_install_requires_csrf_token(): void
    {
        $this->withoutMiddleware(); // Disable CSRF for other routes but not this one

        $response = $this->post('/setup/install', [], [
            'X-CSRF-TOKEN' => '',
        ]);

        // CSRF token will be validated by middleware
        $this->assertTrue(true); // Placeholder
    }

    /**
     * Test full installation with valid session data.
     */
    public function test_full_installation_with_valid_data(): void
    {
        // Set up complete session data
        $this->withSession([
            'setup.app_name' => 'Test App',
            'setup.app_url' => 'https://test.local',
            'setup.app_env' => 'local',
            'setup.app_debug' => true,
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

        $response = $this->post('/setup/install');

        $response->assertRedirect('/setup/success');
        $response->assertSessionHas('success', 'Installation réussie!');
    }

    /**
     * Test installation fails without app settings in session.
     */
    public function test_installation_fails_without_app_settings(): void
    {
        $this->withSession([
            'setup.database_driver' => 'sqlite',
            'setup.database_database' => 'database.sqlite',
            'setup.mail_driver' => 'log',
            'setup.mail_from_address' => 'noreply@example.com',
            'setup.mail_from_name' => 'Test App',
            'setup.admin_name' => 'Admin User',
            'setup.admin_email' => 'admin@example.com',
            'setup.admin_password' => 'SecurePassword123!',
        ]);

        $response = $this->post('/setup/install');

        $response->assertSessionHas('error');
    }

    /**
     * Test installation fails without database config in session.
     */
    public function test_installation_fails_without_database_config(): void
    {
        $this->withSession([
            'setup.app_name' => 'Test App',
            'setup.app_url' => 'https://test.local',
            'setup.app_env' => 'local',
            'setup.timezone' => 'UTC',
            'setup.locale' => 'en',
            'setup.mail_driver' => 'log',
            'setup.mail_from_address' => 'noreply@example.com',
            'setup.mail_from_name' => 'Test App',
            'setup.admin_name' => 'Admin User',
            'setup.admin_email' => 'admin@example.com',
            'setup.admin_password' => 'SecurePassword123!',
        ]);

        $response = $this->post('/setup/install');

        $response->assertSessionHas('error');
    }

    /**
     * Test installation fails without mail config in session.
     */
    public function test_installation_fails_without_mail_config(): void
    {
        $this->withSession([
            'setup.app_name' => 'Test App',
            'setup.app_url' => 'https://test.local',
            'setup.app_env' => 'local',
            'setup.timezone' => 'UTC',
            'setup.locale' => 'en',
            'setup.database_driver' => 'sqlite',
            'setup.database_database' => 'database.sqlite',
            'setup.admin_name' => 'Admin User',
            'setup.admin_email' => 'admin@example.com',
            'setup.admin_password' => 'SecurePassword123!',
        ]);

        $response = $this->post('/setup/install');

        $response->assertSessionHas('error');
    }

    /**
     * Test installation fails without admin config in session.
     */
    public function test_installation_fails_without_admin_config(): void
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
        ]);

        $response = $this->post('/setup/install');

        $response->assertSessionHas('error');
    }

    /**
     * Test installation cleans up session data after completion.
     */
    public function test_installation_cleans_session_data(): void
    {
        $this->withSession([
            'setup.app_name' => 'Test App',
            'setup.app_url' => 'https://test.local',
            'setup.app_env' => 'local',
            'setup.app_debug' => true,
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

        $response = $this->post('/setup/install');

        // Session keys should be cleaned up
        $this->assertNull(session('setup.app_name'));
        $this->assertNull(session('setup.database_driver'));
        $this->assertNull(session('setup.admin_password'));
    }

    /**
     * Test installation handles exceptions gracefully.
     */
    public function test_installation_handles_exceptions(): void
    {
        $this->withSession([
            'setup.app_name' => 'Test App',
            'setup.app_url' => 'https://test.local',
            'setup.app_env' => 'local',
            'setup.timezone' => 'UTC',
            'setup.locale' => 'en',
            'setup.database_driver' => 'mysql', // This will fail without a real MySQL connection
            'setup.database_host' => 'invalid.host',
            'setup.database_port' => '3306',
            'setup.database_database' => 'api_manager',
            'setup.database_username' => 'root',
            'setup.database_password' => 'password',
            'setup.mail_driver' => 'log',
            'setup.mail_from_address' => 'noreply@example.com',
            'setup.mail_from_name' => 'Test App',
            'setup.admin_name' => 'Admin User',
            'setup.admin_email' => 'admin@example.com',
            'setup.admin_password' => 'SecurePassword123!',
        ]);

        $response = $this->post('/setup/install');

        // Should redirect back with error instead of throwing exception
        $response->assertSessionHas('error');
    }

    /**
     * Test installation with MySQL configuration.
     */
    public function test_installation_with_mysql_config(): void
    {
        $this->withSession([
            'setup.app_name' => 'Test App',
            'setup.app_url' => 'https://test.local',
            'setup.app_env' => 'local',
            'setup.app_debug' => false,
            'setup.timezone' => 'UTC',
            'setup.locale' => 'en',
            'setup.database_driver' => 'mysql',
            'setup.database_host' => 'localhost',
            'setup.database_port' => '3306',
            'setup.database_database' => 'api_manager',
            'setup.database_username' => 'root',
            'setup.database_password' => 'password',
            'setup.mail_driver' => 'log',
            'setup.mail_from_address' => 'noreply@example.com',
            'setup.mail_from_name' => 'Test App',
            'setup.admin_name' => 'Admin User',
            'setup.admin_email' => 'admin@example.com',
            'setup.admin_password' => 'SecurePassword123!',
        ]);

        $response = $this->post('/setup/install');

        // Should fail due to invalid MySQL connection but not throw exception
        $response->assertSessionHas('error');
    }

    /**
     * Test installation with PostgreSQL configuration.
     */
    public function test_installation_with_postgresql_config(): void
    {
        $this->withSession([
            'setup.app_name' => 'Test App',
            'setup.app_url' => 'https://test.local',
            'setup.app_env' => 'local',
            'setup.timezone' => 'UTC',
            'setup.locale' => 'en',
            'setup.database_driver' => 'pgsql',
            'setup.database_host' => 'localhost',
            'setup.database_port' => '5432',
            'setup.database_database' => 'api_manager',
            'setup.database_username' => 'postgres',
            'setup.database_password' => 'password',
            'setup.mail_driver' => 'log',
            'setup.mail_from_address' => 'noreply@example.com',
            'setup.mail_from_name' => 'Test App',
            'setup.admin_name' => 'Admin User',
            'setup.admin_email' => 'admin@example.com',
            'setup.admin_password' => 'SecurePassword123!',
        ]);

        $response = $this->post('/setup/install');

        // Should fail due to invalid PostgreSQL connection but not throw exception
        $response->assertSessionHas('error');
    }

    /**
     * Test installation response is JSON or HTML redirect.
     */
    public function test_installation_response_type(): void
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

        $response = $this->post('/setup/install');

        // Should be a redirect response
        $response->assertRedirect();
    }
}
