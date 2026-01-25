<?php

namespace Tests\Feature\Setup;

use Tests\TestCase;

class AdminConfigTest extends TestCase
{
    /**
     * Test accessing the admin configuration page.
     */
    public function test_can_access_admin_page(): void
    {
        $response = $this->get('/setup/admin');

        $response->assertStatus(200);
        $response->assertViewIs('setup.steps.admin');
    }

    /**
     * Test admin page displays form fields.
     */
    public function test_admin_page_displays_form(): void
    {
        $response = $this->get('/setup/admin');

        $response->assertSee('Nom complet');
        $response->assertSee('Email');
        $response->assertSee('Mot de passe');
    }

    /**
     * Test storing valid admin configuration.
     */
    public function test_can_store_valid_admin_config(): void
    {
        $response = $this->post('/setup/admin', [
            'admin_name' => 'John Doe',
            'admin_email' => 'admin@example.com',
            'password' => 'SecurePassword123!',
            'password_confirmation' => 'SecurePassword123!',
        ]);

        $response->assertRedirect('/setup/review');

        $this->assertEquals('John Doe', session('setup.admin_name'));
        $this->assertEquals('admin@example.com', session('setup.admin_email'));
        $this->assertEquals('SecurePassword123!', session('setup.admin_password'));
    }

    /**
     * Test admin validation requires name.
     */
    public function test_admin_validation_requires_name(): void
    {
        $response = $this->post('/setup/admin', [
            'admin_name' => '',
            'admin_email' => 'admin@example.com',
            'password' => 'SecurePassword123!',
            'password_confirmation' => 'SecurePassword123!',
        ]);

        $response->assertSessionHasErrors('admin_name');
    }

    /**
     * Test admin validation requires email.
     */
    public function test_admin_validation_requires_email(): void
    {
        $response = $this->post('/setup/admin', [
            'admin_name' => 'John Doe',
            'admin_email' => '',
            'password' => 'SecurePassword123!',
            'password_confirmation' => 'SecurePassword123!',
        ]);

        $response->assertSessionHasErrors('admin_email');
    }

    /**
     * Test admin validation requires valid email format.
     */
    public function test_admin_validation_requires_valid_email(): void
    {
        $response = $this->post('/setup/admin', [
            'admin_name' => 'John Doe',
            'admin_email' => 'not-an-email',
            'password' => 'SecurePassword123!',
            'password_confirmation' => 'SecurePassword123!',
        ]);

        $response->assertSessionHasErrors('admin_email');
    }

    /**
     * Test password validation requires minimum length (8 characters).
     */
    public function test_password_must_be_at_least_8_characters(): void
    {
        $response = $this->post('/setup/admin', [
            'admin_name' => 'John Doe',
            'admin_email' => 'admin@example.com',
            'password' => 'Pass1!',
            'password_confirmation' => 'Pass1!',
        ]);

        $response->assertSessionHasErrors('password');
    }

    /**
     * Test password validation requires uppercase letter.
     */
    public function test_password_must_have_uppercase(): void
    {
        $response = $this->post('/setup/admin', [
            'admin_name' => 'John Doe',
            'admin_email' => 'admin@example.com',
            'password' => 'password123!',
            'password_confirmation' => 'password123!',
        ]);

        $response->assertSessionHasErrors('password');
    }

    /**
     * Test password validation requires lowercase letter.
     */
    public function test_password_must_have_lowercase(): void
    {
        $response = $this->post('/setup/admin', [
            'admin_name' => 'John Doe',
            'admin_email' => 'admin@example.com',
            'password' => 'PASSWORD123!',
            'password_confirmation' => 'PASSWORD123!',
        ]);

        $response->assertSessionHasErrors('password');
    }

    /**
     * Test password validation requires digit.
     */
    public function test_password_must_have_digit(): void
    {
        $response = $this->post('/setup/admin', [
            'admin_name' => 'John Doe',
            'admin_email' => 'admin@example.com',
            'password' => 'Password!',
            'password_confirmation' => 'Password!',
        ]);

        $response->assertSessionHasErrors('password');
    }

    /**
     * Test password validation requires special character.
     */
    public function test_password_must_have_special_character(): void
    {
        $response = $this->post('/setup/admin', [
            'admin_name' => 'John Doe',
            'admin_email' => 'admin@example.com',
            'password' => 'Password123',
            'password_confirmation' => 'Password123',
        ]);

        $response->assertSessionHasErrors('password');
    }

    /**
     * Test valid special characters in password.
     */
    public function test_password_accepts_valid_special_characters(): void
    {
        $specialChars = ['@', '$', '!', '%', '*', '?', '&'];

        foreach ($specialChars as $char) {
            $password = "SecurePassword123{$char}";

            $response = $this->post('/setup/admin', [
                'admin_name' => 'John Doe',
                'admin_email' => 'admin@example.com',
                'password' => $password,
                'password_confirmation' => $password,
            ]);

            $response->assertSessionDoesntHaveErrors('password');
        }
    }

    /**
     * Test password confirmation must match.
     */
    public function test_password_confirmation_must_match(): void
    {
        $response = $this->post('/setup/admin', [
            'admin_name' => 'John Doe',
            'admin_email' => 'admin@example.com',
            'password' => 'SecurePassword123!',
            'password_confirmation' => 'DifferentPassword123!',
        ]);

        $response->assertSessionHasErrors('password');
    }

    /**
     * Test admin configuration persists in session.
     */
    public function test_admin_config_persists_in_session(): void
    {
        $this->post('/setup/admin', [
            'admin_name' => 'John Doe',
            'admin_email' => 'admin@example.com',
            'password' => 'SecurePassword123!',
            'password_confirmation' => 'SecurePassword123!',
        ]);

        $response = $this->get('/setup/admin');

        $this->assertEquals('John Doe', session('setup.admin_name'));
        $this->assertEquals('admin@example.com', session('setup.admin_email'));
    }

    /**
     * Test password is stored in session as plain text (for now).
     */
    public function test_password_stored_in_session(): void
    {
        $password = 'SecurePassword123!';

        $this->post('/setup/admin', [
            'admin_name' => 'John Doe',
            'admin_email' => 'admin@example.com',
            'password' => $password,
            'password_confirmation' => $password,
        ]);

        $this->assertEquals($password, session('setup.admin_password'));
    }

    /**
     * Test very strong password is accepted.
     */
    public function test_very_strong_password_accepted(): void
    {
        $response = $this->post('/setup/admin', [
            'admin_name' => 'John Doe',
            'admin_email' => 'admin@example.com',
            'password' => 'MyStr0ng!P@ssw0rdWith123Special&Chars',
            'password_confirmation' => 'MyStr0ng!P@ssw0rdWith123Special&Chars',
        ]);

        $response->assertSessionDoesntHaveErrors('password');
        $response->assertRedirect('/setup/review');
    }
}
