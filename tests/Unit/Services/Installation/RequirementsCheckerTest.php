<?php

namespace Tests\Unit\Services\Installation;

use App\Services\Installation\RequirementsChecker;
use Tests\TestCase;

class RequirementsCheckerTest extends TestCase
{
    private RequirementsChecker $checker;

    protected function setUp(): void
    {
        parent::setUp();
        $this->checker = new RequirementsChecker;
    }

    /**
     * Test that check() method returns array with required keys.
     */
    public function test_check_returns_array_with_required_keys(): void
    {
        $result = $this->checker->check();

        $this->assertIsArray($result);
        $this->assertArrayHasKey('passed', $result);
        $this->assertArrayHasKey('errors', $result);
        $this->assertArrayHasKey('warnings', $result);
        $this->assertArrayHasKey('checks', $result);
    }

    /**
     * Test that check returns boolean for passed key.
     */
    public function test_check_passed_is_boolean(): void
    {
        $result = $this->checker->check();

        $this->assertIsBool($result['passed']);
    }

    /**
     * Test that errors array contains strings.
     */
    public function test_check_errors_are_strings(): void
    {
        $result = $this->checker->check();

        foreach ($result['errors'] as $error) {
            $this->assertIsString($error);
        }
    }

    /**
     * Test that warnings array contains strings.
     */
    public function test_check_warnings_are_strings(): void
    {
        $result = $this->checker->check();

        foreach ($result['warnings'] as $warning) {
            $this->assertIsString($warning);
        }
    }

    /**
     * Test PHP version check returns expected format.
     */
    public function test_check_php_version_format(): void
    {
        $result = $this->checker->checkPhpVersion();

        $this->assertIsArray($result);
        $this->assertArrayHasKey('passed', $result);
        $this->assertArrayHasKey('current', $result);
        $this->assertArrayHasKey('required', $result);
    }

    /**
     * Test PHP version check should pass (running on 8.3+).
     */
    public function test_check_php_version_passes(): void
    {
        $result = $this->checker->checkPhpVersion();

        // Current environment runs PHP 8.3.30
        $this->assertTrue($result['passed']);
    }

    /**
     * Test required extensions check returns array.
     */
    public function test_check_required_extensions_returns_array(): void
    {
        $result = $this->checker->checkRequiredExtensions();

        $this->assertIsArray($result);
        $this->assertArrayHasKey('passed', $result);
        $this->assertArrayHasKey('installed', $result);
        $this->assertArrayHasKey('missing', $result);
    }

    /**
     * Test required extensions should include PDO in required list.
     */
    public function test_required_extensions_includes_pdo(): void
    {
        $result = $this->checker->checkRequiredExtensions();

        $this->assertArrayHasKey('required', $result);
        $this->assertContains('pdo', $result['required']);
    }

    /**
     * Test required extensions should include mbstring in required list.
     */
    public function test_required_extensions_includes_mbstring(): void
    {
        $result = $this->checker->checkRequiredExtensions();

        $this->assertArrayHasKey('required', $result);
        $this->assertContains('mbstring', $result['required']);
    }

    /**
     * Test required extensions installed list is array.
     */
    public function test_required_extensions_installed_list(): void
    {
        $result = $this->checker->checkRequiredExtensions();

        $this->assertIsArray($result['installed']);
        $this->assertIsArray($result['missing']);
    }

    /**
     * Test optional extensions check returns array.
     */
    public function test_check_optional_extensions_returns_array(): void
    {
        $result = $this->checker->checkOptionalExtensions();

        $this->assertIsArray($result);
        $this->assertArrayHasKey('installed', $result);
        $this->assertArrayHasKey('missing', $result);
    }

    /**
     * Test optional extensions installed and missing are arrays.
     */
    public function test_optional_extensions_arrays(): void
    {
        $result = $this->checker->checkOptionalExtensions();

        $this->assertIsArray($result['installed']);
        $this->assertIsArray($result['missing']);
    }

    /**
     * Test permissions check returns array.
     */
    public function test_check_permissions_returns_array(): void
    {
        $result = $this->checker->checkPermissions();

        $this->assertIsArray($result);
        $this->assertArrayHasKey('passed', $result);
        $this->assertArrayHasKey('writable', $result);
        $this->assertArrayHasKey('not_writable', $result);
    }

    /**
     * Test permissions check has details for directories.
     */
    public function test_permissions_check_has_details(): void
    {
        $result = $this->checker->checkPermissions();

        $this->assertArrayHasKey('details', $result);
        $this->assertIsArray($result['details']);
    }

    /**
     * Test permissions check each directory details has writable status.
     */
    public function test_permissions_details_have_writable_status(): void
    {
        $result = $this->checker->checkPermissions();

        foreach ($result['details'] as $directory) {
            $this->assertArrayHasKey('writable', $directory);
            $this->assertIsBool($directory['writable']);
        }
    }

    /**
     * Test env file check returns array.
     */
    public function test_check_env_file_returns_array(): void
    {
        $result = $this->checker->checkEnvFile();

        $this->assertIsArray($result);
        $this->assertArrayHasKey('passed', $result);
        $this->assertArrayHasKey('env_exists', $result);
        $this->assertArrayHasKey('env_example_exists', $result);
    }

    /**
     * Test server info returns array with expected keys.
     */
    public function test_get_server_info_returns_array(): void
    {
        $result = $this->checker->getServerInfo();

        $this->assertIsArray($result);
        $this->assertArrayHasKey('host', $result);
        $this->assertArrayHasKey('sapi', $result);
        $this->assertArrayHasKey('memory_limit', $result);
    }

    /**
     * Test server info contains expected value types.
     */
    public function test_server_info_contains_expected_types(): void
    {
        $result = $this->checker->getServerInfo();

        $this->assertIsString($result['host']);
        $this->assertIsBool($result['is_https']);
        $this->assertIsString($result['scheme']);
        $this->assertIsString($result['sapi']);
        $this->assertIsString($result['memory_limit']);
    }

    /**
     * Test enable cache method accepts integer.
     */
    public function test_enable_cache_accepts_integer(): void
    {
        // Should not throw exception
        $this->checker->enableCache(10);
        $this->assertTrue(true);
    }

    /**
     * Test enable cache with default value.
     */
    public function test_enable_cache_default_value(): void
    {
        // Should not throw exception
        $this->checker->enableCache();
        $this->assertTrue(true);
    }

    /**
     * Test check is cacheable.
     */
    public function test_check_is_cacheable(): void
    {
        $this->checker->enableCache(5);
        $result1 = $this->checker->check();

        // Second call should use cache
        $result2 = $this->checker->check();

        $this->assertEquals($result1, $result2);
    }

    /**
     * Test multiple required extensions are checked.
     */
    public function test_multiple_required_extensions_checked(): void
    {
        $result = $this->checker->checkRequiredExtensions();

        // Should have required extensions list
        $this->assertNotEmpty($result['required']);
        $this->assertContains('pdo', $result['required']);
    }

    /**
     * Test passed flag matches requirements status.
     */
    public function test_passed_flag_reflects_status(): void
    {
        $result = $this->checker->check();

        // If there are errors, passed should be false
        if (count($result['errors']) > 0) {
            $this->assertFalse($result['passed']);
        }

        // If no errors, passed should be true
        if (count($result['errors']) === 0) {
            $this->assertTrue($result['passed']);
        }
    }
}
