<?php

namespace Tests\Unit\Services\Installation;

use App\Services\Installation\EnvManager;
use Tests\TestCase;

class EnvManagerTest extends TestCase
{
    private EnvManager $manager;

    protected function setUp(): void
    {
        parent::setUp();
        $this->manager = new EnvManager;
    }

    /**
     * Test that EnvManager can be instantiated.
     */
    public function test_env_manager_can_be_instantiated(): void
    {
        $this->assertInstanceOf(EnvManager::class, $this->manager);
    }

    /**
     * Test envExists returns boolean.
     */
    public function test_env_exists_returns_boolean(): void
    {
        $result = $this->manager->envExists();

        $this->assertIsBool($result);
    }

    /**
     * Test envExists should return true for app in testing.
     */
    public function test_env_exists_is_true(): void
    {
        $result = $this->manager->envExists();

        // .env should exist in test environment
        $this->assertTrue($result);
    }

    /**
     * Test get method returns value.
     */
    public function test_get_returns_value(): void
    {
        $result = $this->manager->get('APP_NAME');

        $this->assertNotNull($result);
        $this->assertIsString($result);
    }

    /**
     * Test get method returns default for missing key.
     */
    public function test_get_returns_default_for_missing_key(): void
    {
        $result = $this->manager->get('NONEXISTENT_KEY', 'default_value');

        $this->assertEquals('default_value', $result);
    }

    /**
     * Test update method returns boolean.
     */
    public function test_update_returns_boolean(): void
    {
        $result = $this->manager->update(['TEST_KEY' => 'test_value']);

        $this->assertIsBool($result);
    }

    /**
     * Test update method accepts array of values.
     */
    public function test_update_accepts_array(): void
    {
        $values = [
            'TEST_APP_NAME' => 'Test Application',
            'TEST_APP_ENV' => 'testing',
        ];

        $result = $this->manager->update($values);

        $this->assertTrue($result);
    }

    /**
     * Test all method returns array.
     */
    public function test_all_returns_array(): void
    {
        $result = $this->manager->all();

        $this->assertIsArray($result);
    }

    /**
     * Test all method contains APP_NAME.
     */
    public function test_all_contains_app_name(): void
    {
        $result = $this->manager->all();

        $this->assertArrayHasKey('APP_NAME', $result);
    }

    /**
     * Test all method contains DATABASE config.
     */
    public function test_all_contains_database_config(): void
    {
        $result = $this->manager->all();

        $this->assertArrayHasKey('DB_CONNECTION', $result);
    }

    /**
     * Test validate method returns array.
     */
    public function test_validate_returns_array(): void
    {
        $result = $this->manager->validate();

        $this->assertIsArray($result);
    }

    /**
     * Test validate method has valid key.
     */
    public function test_validate_has_valid_key(): void
    {
        $result = $this->manager->validate();

        $this->assertArrayHasKey('valid', $result);
    }

    /**
     * Test backup method returns string filename.
     */
    public function test_backup_returns_filename(): void
    {
        $result = $this->manager->backup();

        $this->assertIsString($result);
        $this->assertNotEmpty($result);
    }

    /**
     * Test backup filename has correct extension.
     */
    public function test_backup_filename_extension(): void
    {
        $result = $this->manager->backup();

        $this->assertStringEndsWith('.backup', $result);
    }

    /**
     * Test list backups returns array.
     */
    public function test_list_backups_returns_array(): void
    {
        $result = $this->manager->listBackups();

        $this->assertIsArray($result);
    }

    /**
     * Test reload method exists and is callable.
     */
    public function test_reload_method_is_callable(): void
    {
        // Should not throw exception
        $this->manager->reload();
        $this->assertTrue(true);
    }

    /**
     * Test flush cache method exists and is callable.
     */
    public function test_flush_cache_is_callable(): void
    {
        // Should not throw exception
        $this->manager->flushCache();
        $this->assertTrue(true);
    }

    /**
     * Test update stores value that can be retrieved.
     */
    public function test_update_value_can_be_retrieved(): void
    {
        $this->manager->update(['TEST_STORAGE_KEY' => 'test_storage_value']);

        // Reload to ensure value is read from file
        $this->manager->reload();

        $value = $this->manager->get('TEST_STORAGE_KEY');

        $this->assertEquals('test_storage_value', $value);
    }

    /**
     * Test update with multiple values.
     */
    public function test_update_with_multiple_values(): void
    {
        $values = [
            'TEST_VAR_1' => 'value1',
            'TEST_VAR_2' => 'value2',
            'TEST_VAR_3' => 'value3',
        ];

        $result = $this->manager->update($values);

        $this->assertTrue($result);

        // Verify each value
        $this->manager->reload();
        $this->assertEquals('value1', $this->manager->get('TEST_VAR_1'));
        $this->assertEquals('value2', $this->manager->get('TEST_VAR_2'));
        $this->assertEquals('value3', $this->manager->get('TEST_VAR_3'));
    }

    /**
     * Test update handles special characters.
     */
    public function test_update_handles_special_characters(): void
    {
        $this->manager->update(['TEST_SPECIAL' => 'value with spaces and !@#$%']);

        $this->manager->reload();

        $value = $this->manager->get('TEST_SPECIAL');

        $this->assertNotEmpty($value);
    }

    /**
     * Test all method returns complete env configuration.
     */
    public function test_all_returns_complete_config(): void
    {
        $result = $this->manager->all();

        // Should contain common Laravel config keys
        $commonKeys = ['APP_NAME', 'APP_URL', 'APP_KEY', 'DB_CONNECTION'];

        foreach ($commonKeys as $key) {
            $this->assertArrayHasKey($key, $result, "Missing key: $key");
        }
    }

    /**
     * Test get method with nested dot notation.
     */
    public function test_get_retrieves_existing_keys(): void
    {
        $appName = $this->manager->get('APP_NAME');

        $this->assertNotNull($appName);
    }
}
