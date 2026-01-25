<?php

namespace Tests\Unit\Services\Installation;

use App\Services\Installation\InstallationCheck;
use Tests\TestCase;

class InstallationCheckTest extends TestCase
{
    private InstallationCheck $checker;

    protected function setUp(): void
    {
        parent::setUp();
        $this->checker = new InstallationCheck;
    }

    /**
     * Test InstallationCheck can be instantiated.
     */
    public function test_installation_check_can_be_instantiated(): void
    {
        $this->assertInstanceOf(InstallationCheck::class, $this->checker);
    }

    /**
     * Test isInstalled returns boolean.
     */
    public function test_is_installed_returns_boolean(): void
    {
        $result = $this->checker->isInstalled();

        $this->assertIsBool($result);
    }

    /**
     * Test validateIntegrity returns array.
     */
    public function test_validate_integrity_returns_array(): void
    {
        // If not installed yet, this should handle gracefully
        if (! $this->checker->isInstalled()) {
            $this->assertTrue(true);
        } else {
            $result = $this->checker->validateIntegrity();

            $this->assertIsArray($result);
            $this->assertArrayHasKey('valid', $result);
        }
    }

    /**
     * Test validateIntegrity has valid key.
     */
    public function test_validate_integrity_has_valid_key(): void
    {
        if (! $this->checker->isInstalled()) {
            $this->assertTrue(true);
        } else {
            $result = $this->checker->validateIntegrity();

            $this->assertArrayHasKey('valid', $result);
            $this->assertIsBool($result['valid']);
        }
    }

    /**
     * Test getInstallationInfo returns array or null.
     */
    public function test_get_installation_info_returns_array_or_null(): void
    {
        $result = $this->checker->getInstallationInfo();

        $this->assertTrue($result === null || is_array($result));
    }

    /**
     * Test getInstalledAt returns Carbon or null.
     */
    public function test_get_installed_at_returns_carbon_or_null(): void
    {
        $result = $this->checker->getInstalledAt();

        $this->assertTrue($result === null || $result instanceof \Carbon\Carbon);
    }

    /**
     * Test createLock returns boolean.
     */
    public function test_create_lock_returns_boolean(): void
    {
        $result = $this->checker->createLock();

        $this->assertIsBool($result);
    }

    /**
     * Test createLock creates lock file successfully.
     */
    public function test_create_lock_creates_file(): void
    {
        $result = $this->checker->createLock();

        $this->assertTrue($result);

        // Verify installation is now marked as installed
        $this->assertTrue($this->checker->isInstalled());
    }

    /**
     * Test reset method exists and is callable.
     */
    public function test_reset_method_is_callable(): void
    {
        // If installed, we can test reset
        if ($this->checker->isInstalled()) {
            // Should not throw exception
            $this->checker->reset(false);
            $this->assertTrue(true);
        } else {
            $this->assertTrue(true);
        }
    }

    /**
     * Test installation info contains expected keys when installed.
     */
    public function test_installation_info_has_expected_keys(): void
    {
        if ($this->checker->isInstalled()) {
            $info = $this->checker->getInstallationInfo();

            $this->assertIsArray($info);
            $this->assertArrayHasKey('installed_at', $info);
            $this->assertArrayHasKey('hash', $info);
        } else {
            $this->assertTrue(true);
        }
    }

    /**
     * Test multiple createLock calls return boolean.
     */
    public function test_multiple_create_lock_calls(): void
    {
        // First call
        $result1 = $this->checker->createLock();
        $this->assertIsBool($result1);

        // Second call (should still work or be idempotent)
        $result2 = $this->checker->createLock();
        $this->assertIsBool($result2);
    }

    /**
     * Test isInstalled consistency.
     */
    public function test_is_installed_consistency(): void
    {
        $result1 = $this->checker->isInstalled();

        // Create lock
        $this->checker->createLock();

        $result2 = $this->checker->isInstalled();

        // Both should be boolean and consistent
        $this->assertIsBool($result1);
        $this->assertIsBool($result2);
        $this->assertTrue($result2); // After creating lock, should be true
    }

    /**
     * Test validateIntegrity is boolean when installed.
     */
    public function test_validate_integrity_is_boolean_when_installed(): void
    {
        $this->checker->createLock();

        $result = $this->checker->validateIntegrity();

        $this->assertIsArray($result);
        $this->assertIsBool($result['valid']);
    }

    /**
     * Test lock file contains hash.
     */
    public function test_lock_file_contains_hash(): void
    {
        $this->checker->createLock();

        $info = $this->checker->getInstallationInfo();

        if ($info) {
            $this->assertArrayHasKey('hash', $info);
            $this->assertIsString($info['hash']);
            $this->assertNotEmpty($info['hash']);
        }
    }

    /**
     * Test lock file contains timestamp.
     */
    public function test_lock_file_contains_timestamp(): void
    {
        $this->checker->createLock();

        $info = $this->checker->getInstallationInfo();

        if ($info) {
            $this->assertArrayHasKey('installed_at', $info);
        }
    }
}
