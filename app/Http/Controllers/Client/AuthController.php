<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Http\Requests\Client\ForgotPasswordRequest;
use App\Http\Requests\Client\LoginRequest;
use App\Http\Requests\Client\RegisterRequest;
use App\Http\Requests\Client\ResendActivationRequest;
use App\Http\Requests\Client\ResetPasswordRequest;
use App\Models\Client;
use App\Notifications\ClientActivation;
use App\Notifications\ClientPasswordReset;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function showRegister(): View
    {
        return view('client.auth.register');
    }

    public function showLogin(): View
    {
        return view('client.auth.login');
    }

    public function register(RegisterRequest $request): RedirectResponse
    {
        $rawToken = Str::random(64);

        $client = Client::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => $request->password,
            'activation_token' => hash('sha256', $rawToken),
            'activation_expires_at' => now()->addHours(24),
            'is_active' => false,
        ]);

        $client->notify(new ClientActivation($rawToken));

        Log::info('client.register', ['email' => $request->email, 'ip' => $request->ip()]);

        return redirect()->route('client.login')
            ->with('success', __('client.client_auth.register_success'));
    }

    public function activate(string $token): RedirectResponse
    {
        $hashedToken = hash('sha256', $token);
        $client = Client::where('activation_token', $hashedToken)->first();

        if (! $client) {
            Log::warning('client.activate.invalid', ['ip' => request()->ip()]);

            return redirect()->route('client.login')
                ->withErrors(['activation' => __('client.client_auth.invalid_token')]);
        }

        if ($client->is_active) {
            return redirect()->route('client.login')
                ->with('info', __('client.client_auth.already_activated'));
        }

        if (! $client->activation_expires_at || $client->activation_expires_at->isPast()) {
            Log::warning('client.activate.expired', ['email' => $client->email, 'ip' => request()->ip()]);

            return redirect()->route('client.login')
                ->withErrors(['activation' => __('client.client_auth.expired_token')])
                ->with('resend_email', $client->email);
        }

        $client->update([
            'is_active' => true,
            'activated_at' => now(),
            'activation_token' => null,
            'activation_expires_at' => null,
        ]);

        Log::info('client.activated', ['email' => $client->email]);

        return redirect()->route('client.login')
            ->with('success', __('client.client_auth.activation_success'));
    }

    public function resendActivation(ResendActivationRequest $request): RedirectResponse
    {
        $client = Client::where('email', $request->email)->where('is_active', false)->first();

        if ($client) {
            $rawToken = Str::random(64);
            $client->update([
                'activation_token' => hash('sha256', $rawToken),
                'activation_expires_at' => now()->addHours(24),
            ]);
            $client->notify(new ClientActivation($rawToken));
            Log::info('client.activate.resend', ['email' => $request->email, 'ip' => $request->ip()]);
        }

        return redirect()->route('client.login')
            ->with('success', __('client.client_auth.resend_success'));
    }

    public function login(LoginRequest $request): RedirectResponse
    {
        if (! auth('client')->attempt($request->only('email', 'password'))) {
            Log::warning('client.login.failed', ['email' => $request->email, 'ip' => $request->ip()]);

            return redirect()->back()
                ->withErrors(['email' => __('client.client_auth.invalid_credentials')])
                ->withInput($request->only('email'));
        }

        $client = auth('client')->user();

        if (! $client->is_active) {
            auth('client')->logout();
            Log::warning('client.login.inactive', ['email' => $client->email, 'ip' => $request->ip()]);

            return redirect()->back()
                ->withErrors(['email' => __('client.client_auth.inactive_account')])
                ->withInput($request->only('email'));
        }

        $client->update(['last_login_at' => now()]);
        $request->session()->regenerate();
        auth('client')->logoutOtherDevices($request->password);

        Log::info('client.login.success', ['id' => $client->id, 'ip' => $request->ip()]);

        return redirect()->route('client.dashboard');
    }

    public function logout(Request $request): RedirectResponse
    {
        auth('client')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('client.login');
    }

    public function showForgotPassword(): View
    {
        return view('client.auth.forgot-password');
    }

    public function sendPasswordReset(ForgotPasswordRequest $request): RedirectResponse
    {
        $client = Client::where('email', $request->email)
            ->where('is_active', true)
            ->first();

        if ($client) {
            try {
                $rawToken = Str::random(64);
                $client->update([
                    'password_reset_token' => hash('sha256', $rawToken),
                    'password_reset_expires_at' => now()->addHour(),
                ]);
                $client->notify(new ClientPasswordReset($rawToken));
                Log::info('client.password.forgot', ['email' => $request->email, 'ip' => $request->ip()]);
            } catch (\Exception $e) {
                Log::error('client.password.forgot.error', ['error' => $e->getMessage(), 'email' => $request->email]);
            }
        }

        return redirect()->route('client.login')
            ->with('success', __('client.client_auth.password_reset_sent'));
    }

    public function showResetPassword(string $token, Request $request): View
    {
        return view('client.auth.reset-password', [
            'token' => $token,
            'email' => $request->query('email'),
        ]);
    }

    public function resetPassword(ResetPasswordRequest $request): RedirectResponse
    {
        $client = Client::where('email', $request->email)
            ->where('is_active', true)
            ->whereNotNull('password_reset_token')
            ->first();

        if (! $client) {
            return redirect()->back()
                ->withErrors(['email' => __('client.client_auth.password_reset_invalid')]);
        }

        if (! hash_equals($client->password_reset_token, hash('sha256', $request->token))) {
            Log::warning('client.password.reset.invalid', ['email' => $request->email, 'ip' => $request->ip()]);

            return redirect()->back()
                ->withErrors(['token' => __('client.client_auth.password_reset_invalid')]);
        }

        if ($client->password_reset_expires_at->isPast()) {
            Log::warning('client.password.reset.expired', ['email' => $request->email, 'ip' => $request->ip()]);

            return redirect()->route('client.password.forgot')
                ->withErrors(['token' => __('client.client_auth.password_reset_expired')]);
        }

        $client->update([
            'password' => $request->password,
            'password_reset_token' => null,
            'password_reset_expires_at' => null,
        ]);

        Log::info('client.password.reset', ['id' => $client->id]);

        return redirect()->route('client.login')
            ->with('success', __('client.client_auth.password_reset_success'));
    }
}
