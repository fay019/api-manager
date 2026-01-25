<?php

namespace Tests\Feature\Setup;

use Tests\TestCase;

class MailConfigTest extends TestCase
{
    /**
     * Test accessing the mail configuration page.
     */
    public function test_can_access_mail_page(): void
    {
        $response = $this->get('/setup/mail');

        $response->assertStatus(200);
        $response->assertViewIs('setup.steps.mail');
    }

    /**
     * Test that mail page displays all driver options.
     */
    public function test_mail_page_displays_all_drivers(): void
    {
        $response = $this->get('/setup/mail');

        $response->assertSee('SMTP');
        $response->assertSee('SendMail');
        $response->assertSee('Log');
        $response->assertSee('Mailgun');
    }

    /**
     * Test storing SMTP mail configuration.
     */
    public function test_can_store_smtp_config(): void
    {
        $response = $this->post('/setup/mail', [
            'mail_driver' => 'smtp',
            'mail_host' => 'smtp.gmail.com',
            'mail_port' => '587',
            'mail_username' => 'user@gmail.com',
            'mail_password' => 'apppassword',
            'mail_encryption' => 'tls',
            'mail_from_address' => 'noreply@example.com',
            'mail_from_name' => 'API Manager',
        ]);

        $response->assertRedirect('/setup/admin');

        $this->assertEquals('smtp', session('setup.mail_driver'));
        $this->assertEquals('smtp.gmail.com', session('setup.mail_host'));
        $this->assertEquals('587', session('setup.mail_port'));
        $this->assertEquals('noreply@example.com', session('setup.mail_from_address'));
    }

    /**
     * Test storing SendMail configuration.
     */
    public function test_can_store_sendmail_config(): void
    {
        $response = $this->post('/setup/mail', [
            'mail_driver' => 'sendmail',
            'mail_path' => '/usr/sbin/sendmail -t -i',
            'mail_from_address' => 'noreply@example.com',
            'mail_from_name' => 'API Manager',
        ]);

        $response->assertRedirect('/setup/admin');

        $this->assertEquals('sendmail', session('setup.mail_driver'));
        $this->assertEquals('/usr/sbin/sendmail -t -i', session('setup.mail_path'));
    }

    /**
     * Test storing Log driver configuration.
     */
    public function test_can_store_log_config(): void
    {
        $response = $this->post('/setup/mail', [
            'mail_driver' => 'log',
            'mail_from_address' => 'noreply@example.com',
            'mail_from_name' => 'API Manager',
        ]);

        $response->assertRedirect('/setup/admin');

        $this->assertEquals('log', session('setup.mail_driver'));
    }

    /**
     * Test storing Mailgun configuration.
     */
    public function test_can_store_mailgun_config(): void
    {
        $response = $this->post('/setup/mail', [
            'mail_driver' => 'mailgun',
            'mail_from_address' => 'noreply@example.com',
            'mail_from_name' => 'API Manager',
        ]);

        $response->assertRedirect('/setup/admin');

        $this->assertEquals('mailgun', session('setup.mail_driver'));
    }

    /**
     * Test mail validation requires driver.
     */
    public function test_mail_validation_requires_driver(): void
    {
        $response = $this->post('/setup/mail', [
            'mail_driver' => '',
        ]);

        $response->assertSessionHasErrors('mail_driver');
    }

    /**
     * Test SMTP validation requires host and port.
     */
    public function test_smtp_validation_requires_host_and_port(): void
    {
        $response = $this->post('/setup/mail', [
            'mail_driver' => 'smtp',
            'mail_host' => '',
            'mail_port' => '',
            'mail_from_address' => 'test@example.com',
            'mail_from_name' => 'Test',
        ]);

        $response->assertSessionHasErrors(['mail_host', 'mail_port']);
    }

    /**
     * Test SendMail validation requires path.
     */
    public function test_sendmail_validation_requires_path(): void
    {
        $response = $this->post('/setup/mail', [
            'mail_driver' => 'sendmail',
            'mail_path' => '',
            'mail_from_address' => 'test@example.com',
            'mail_from_name' => 'Test',
        ]);

        $response->assertSessionHasErrors('mail_path');
    }

    /**
     * Test mail validation requires from address.
     */
    public function test_mail_validation_requires_from_address(): void
    {
        $response = $this->post('/setup/mail', [
            'mail_driver' => 'smtp',
            'mail_host' => 'smtp.example.com',
            'mail_port' => '587',
            'mail_from_address' => '',
            'mail_from_name' => 'Test',
        ]);

        $response->assertSessionHasErrors('mail_from_address');
    }

    /**
     * Test mail test endpoint returns JSON response.
     */
    public function test_mail_test_endpoint_returns_json(): void
    {
        $response = $this->post('/setup/mail/test', [
            'mail_driver' => 'log',
        ]);

        $response->assertHeader('Content-Type', 'application/json');
        $this->assertArrayHasKey('success', $response->json());
    }

    /**
     * Test mail test endpoint rejects non-SMTP drivers.
     */
    public function test_mail_test_endpoint_only_tests_smtp(): void
    {
        $response = $this->post('/setup/mail/test', [
            'mail_driver' => 'log',
        ]);

        $response->assertJson(['success' => false]);
    }

    /**
     * Test SMTP connection test with invalid host fails.
     */
    public function test_smtp_test_with_invalid_host(): void
    {
        $response = $this->post('/setup/mail/test', [
            'mail_driver' => 'smtp',
            'mail_host' => 'invalid.nonexistent.host.example',
            'mail_port' => '587',
            'mail_username' => 'user@example.com',
            'mail_password' => 'password',
            'mail_encryption' => 'tls',
        ]);

        $response->assertJson(['success' => false]);
        $this->assertArrayHasKey('message', $response->json());
    }

    /**
     * Test mail configuration persists in session.
     */
    public function test_mail_config_persists_in_session(): void
    {
        $this->post('/setup/mail', [
            'mail_driver' => 'smtp',
            'mail_host' => 'smtp.example.com',
            'mail_port' => '587',
            'mail_username' => 'user@example.com',
            'mail_password' => 'password',
            'mail_encryption' => 'tls',
            'mail_from_address' => 'noreply@example.com',
            'mail_from_name' => 'API Manager',
        ]);

        $response = $this->get('/setup/mail');

        $this->assertEquals('smtp', session('setup.mail_driver'));
        $this->assertEquals('smtp.example.com', session('setup.mail_host'));
    }
}
