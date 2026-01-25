<?php

namespace Tests\Feature\Setup;

use Tests\TestCase;

class DatabaseConfigTest extends TestCase
{
    /**
     * Test accessing the database configuration page.
     */
    public function test_can_access_database_page(): void
    {
        $response = $this->get('/setup/database');

        $response->assertStatus(200);
        $response->assertViewIs('setup.steps.database');
    }

    /**
     * Test that database page displays form correctly.
     */
    public function test_database_page_displays_form(): void
    {
        $response = $this->get('/setup/database');

        $response->assertSee('SQLite');
        $response->assertSee('MySQL');
        $response->assertSee('PostgreSQL');
    }

    /**
     * Test storing SQLite database configuration.
     */
    public function test_can_store_sqlite_database_config(): void
    {
        $response = $this->post('/setup/database', [
            'database_driver' => 'sqlite',
            'database_database' => 'database.sqlite',
        ]);

        $response->assertRedirect('/setup/mail');

        $this->assertEquals('sqlite', session('setup.database_driver'));
        $this->assertEquals('database.sqlite', session('setup.database_database'));
    }

    /**
     * Test storing MySQL database configuration.
     */
    public function test_can_store_mysql_database_config(): void
    {
        $response = $this->post('/setup/database', [
            'database_driver' => 'mysql',
            'database_host' => 'localhost',
            'database_port' => '3306',
            'database_database' => 'api_manager',
            'database_username' => 'root',
            'database_password' => 'password',
        ]);

        $response->assertRedirect('/setup/mail');

        $this->assertEquals('mysql', session('setup.database_driver'));
        $this->assertEquals('localhost', session('setup.database_host'));
        $this->assertEquals('3306', session('setup.database_port'));
    }

    /**
     * Test storing PostgreSQL database configuration.
     */
    public function test_can_store_postgresql_database_config(): void
    {
        $response = $this->post('/setup/database', [
            'database_driver' => 'pgsql',
            'database_host' => 'localhost',
            'database_port' => '5432',
            'database_database' => 'api_manager',
            'database_username' => 'postgres',
            'database_password' => 'password',
        ]);

        $response->assertRedirect('/setup/mail');

        $this->assertEquals('pgsql', session('setup.database_driver'));
        $this->assertEquals('postgres', session('setup.database_username'));
    }

    /**
     * Test database validation requires driver.
     */
    public function test_database_validation_requires_driver(): void
    {
        $response = $this->post('/setup/database', [
            'database_driver' => '',
        ]);

        $response->assertSessionHasErrors('database_driver');
    }

    /**
     * Test SQLite validation requires database path.
     */
    public function test_sqlite_validation_requires_database(): void
    {
        $response = $this->post('/setup/database', [
            'database_driver' => 'sqlite',
            'database_database' => '',
        ]);

        $response->assertSessionHasErrors('database_database');
    }

    /**
     * Test MySQL validation requires host and port.
     */
    public function test_mysql_validation_requires_host_and_port(): void
    {
        $response = $this->post('/setup/database', [
            'database_driver' => 'mysql',
            'database_host' => '',
            'database_port' => '',
            'database_database' => 'api_manager',
            'database_username' => 'root',
            'database_password' => 'password',
        ]);

        $response->assertSessionHasErrors(['database_host', 'database_port']);
    }

    /**
     * Test database test endpoint returns success for valid SQLite.
     */
    public function test_database_test_endpoint_sqlite(): void
    {
        $response = $this->post('/setup/database/test', [
            'database_driver' => 'sqlite',
            'database_database' => ':memory:',
        ]);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);
    }

    /**
     * Test database test endpoint returns JSON response.
     */
    public function test_database_test_endpoint_returns_json(): void
    {
        $response = $this->post('/setup/database/test', [
            'database_driver' => 'sqlite',
            'database_database' => ':memory:',
        ]);

        $response->assertHeader('Content-Type', 'application/json');
        $this->assertArrayHasKey('success', $response->json());
    }

    /**
     * Test database test endpoint with invalid credentials fails gracefully.
     */
    public function test_database_test_endpoint_invalid_mysql(): void
    {
        $response = $this->post('/setup/database/test', [
            'database_driver' => 'mysql',
            'database_host' => 'invalid.host',
            'database_port' => '3306',
            'database_database' => 'api_manager',
            'database_username' => 'invalid',
            'database_password' => 'invalid',
        ]);

        $response->assertStatus(200);
        $response->assertJson(['success' => false]);
        $this->assertArrayHasKey('message', $response->json());
    }
}
