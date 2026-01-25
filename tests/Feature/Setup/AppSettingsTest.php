<?php

namespace Tests\Feature\Setup;

use Tests\TestCase;

class AppSettingsTest extends TestCase
{
    /**
     * Test accessing the app settings page.
     */
    public function test_can_access_app_settings_page(): void
    {
        $response = $this->get('/setup/app-settings');

        $response->assertStatus(200);
        $response->assertViewIs('setup.steps.app-settings');
    }

    /**
     * Test app settings page displays form fields.
     */
    public function test_app_settings_page_displays_form(): void
    {
        $response = $this->get('/setup/app-settings');

        $response->assertSee('Nom de l\'application');
        $response->assertSee('URL');
        $response->assertSee('Environnement');
        $response->assertSee('Fuseau horaire');
        $response->assertSee('Langue');
    }

    /**
     * Test storing app settings with valid data.
     */
    public function test_can_store_valid_app_settings(): void
    {
        $response = $this->post('/setup/app-settings', [
            'app_name' => 'Test App',
            'app_url' => 'https://test.local',
            'app_env' => 'local',
            'timezone' => 'UTC',
            'locale' => 'en',
        ]);

        $response->assertRedirect('/setup/database');

        $this->assertEquals('Test App', session('setup.app_name'));
        $this->assertEquals('https://test.local', session('setup.app_url'));
        $this->assertEquals('local', session('setup.app_env'));
        $this->assertEquals('UTC', session('setup.timezone'));
        $this->assertEquals('en', session('setup.locale'));
    }

    /**
     * Test app settings validation requires app name.
     */
    public function test_app_settings_validation_requires_name(): void
    {
        $response = $this->post('/setup/app-settings', [
            'app_name' => '',
            'app_url' => 'https://test.local',
            'app_env' => 'local',
            'timezone' => 'UTC',
            'locale' => 'en',
        ]);

        $response->assertSessionHasErrors('app_name');
    }

    /**
     * Test app settings validation requires URL.
     */
    public function test_app_settings_validation_requires_url(): void
    {
        $response = $this->post('/setup/app-settings', [
            'app_name' => 'Test App',
            'app_url' => '',
            'app_env' => 'local',
            'timezone' => 'UTC',
            'locale' => 'en',
        ]);

        $response->assertSessionHasErrors('app_url');
    }

    /**
     * Test app settings validation requires valid URL format.
     */
    public function test_app_settings_validation_requires_valid_url(): void
    {
        $response = $this->post('/setup/app-settings', [
            'app_name' => 'Test App',
            'app_url' => 'not-a-valid-url',
            'app_env' => 'local',
            'timezone' => 'UTC',
            'locale' => 'en',
        ]);

        $response->assertSessionHasErrors('app_url');
    }

    /**
     * Test app settings validation requires environment.
     */
    public function test_app_settings_validation_requires_env(): void
    {
        $response = $this->post('/setup/app-settings', [
            'app_name' => 'Test App',
            'app_url' => 'https://test.local',
            'app_env' => '',
            'timezone' => 'UTC',
            'locale' => 'en',
        ]);

        $response->assertSessionHasErrors('app_env');
    }

    /**
     * Test app settings validation requires timezone.
     */
    public function test_app_settings_validation_requires_timezone(): void
    {
        $response = $this->post('/setup/app-settings', [
            'app_name' => 'Test App',
            'app_url' => 'https://test.local',
            'app_env' => 'local',
            'timezone' => '',
            'locale' => 'en',
        ]);

        $response->assertSessionHasErrors('timezone');
    }

    /**
     * Test app settings validation requires valid timezone.
     */
    public function test_app_settings_validation_requires_valid_timezone(): void
    {
        $response = $this->post('/setup/app-settings', [
            'app_name' => 'Test App',
            'app_url' => 'https://test.local',
            'app_env' => 'local',
            'timezone' => 'Invalid/Timezone',
            'locale' => 'en',
        ]);

        $response->assertSessionHasErrors('timezone');
    }

    /**
     * Test app settings validation requires locale.
     */
    public function test_app_settings_validation_requires_locale(): void
    {
        $response = $this->post('/setup/app-settings', [
            'app_name' => 'Test App',
            'app_url' => 'https://test.local',
            'app_env' => 'local',
            'timezone' => 'UTC',
            'locale' => '',
        ]);

        $response->assertSessionHasErrors('locale');
    }

    /**
     * Test valid timezones are accepted.
     */
    public function test_valid_timezones_accepted(): void
    {
        $validTimezones = ['UTC', 'America/New_York', 'Europe/Paris', 'Asia/Tokyo'];

        foreach ($validTimezones as $timezone) {
            $response = $this->post('/setup/app-settings', [
                'app_name' => 'Test App',
                'app_url' => 'https://test.local',
                'app_env' => 'local',
                'timezone' => $timezone,
                'locale' => 'en',
            ]);

            $response->assertSessionDoesntHaveErrors('timezone');
        }
    }

    /**
     * Test app settings with production environment.
     */
    public function test_can_store_production_environment(): void
    {
        $response = $this->post('/setup/app-settings', [
            'app_name' => 'Production App',
            'app_url' => 'https://example.com',
            'app_env' => 'production',
            'timezone' => 'UTC',
            'locale' => 'en',
        ]);

        $response->assertRedirect('/setup/database');

        $this->assertEquals('production', session('setup.app_env'));
    }

    /**
     * Test app settings persists in session.
     */
    public function test_app_settings_persist_in_session(): void
    {
        $this->post('/setup/app-settings', [
            'app_name' => 'Test App',
            'app_url' => 'https://test.local',
            'app_env' => 'local',
            'timezone' => 'UTC',
            'locale' => 'en',
        ]);

        $response = $this->get('/setup/app-settings');

        $this->assertEquals('Test App', session('setup.app_name'));
        $this->assertEquals('https://test.local', session('setup.app_url'));
    }

    /**
     * Test app settings page displays form.
     */
    public function test_app_settings_page_loads(): void
    {
        $response = $this->get('/setup/app-settings');

        $response->assertStatus(200);
        $response->assertViewIs('setup.steps.app-settings');
    }

    /**
     * Test auto-detection of app name from folder.
     */
    public function test_auto_detects_app_name(): void
    {
        $response = $this->get('/setup/app-settings');

        // Should show detected app name in the view
        $this->assertNotNull($response->viewData('appName'));
    }

    /**
     * Test auto-detection of app URL.
     */
    public function test_auto_detects_app_url(): void
    {
        $response = $this->get('/setup/app-settings');

        // Should show detected app URL in the view
        $this->assertNotNull($response->viewData('appUrl'));
    }

    /**
     * Test auto-detection of environment (localhost detection).
     */
    public function test_auto_detects_environment(): void
    {
        $response = $this->get('/setup/app-settings');

        // Should show detected environment in the view
        $this->assertNotNull($response->viewData('appEnv'));
    }
}
