<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class LoginController extends Controller
{
    public function show()
    {
        if (Auth::check()) {
            return redirect()->route(Auth::user()->is_admin ? 'admin.dashboard' : 'profile.edit');
        }

        return view('auth.login');
    }

    public function store(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            // Invalidate old cookies to fix stale CSRF/session tokens
            $response = redirect()->intended(Auth::user()->is_admin ? '/admin' : route('profile.edit'));
            $response->cookie(cookie()->forget('XSRF-TOKEN'));
            $response->cookie(cookie()->forget(config('session.cookie')));

            return $response;
        }

        // Debug: Log why login failed
        $user = User::where('email', $credentials['email'])->first();
        if (! $user) {
            Log::warning('user.login.failed.user_not_found', ['email' => $credentials['email'], 'ip' => $request->ip()]);
        } else {
            $passwordMatch = Hash::check($credentials['password'], $user->password);
            Log::warning('user.login.failed', [
                'email' => $credentials['email'],
                'ip' => $request->ip(),
                'user_exists' => true,
                'password_match' => $passwordMatch,
                'user_id' => $user->id,
            ]);
        }

        return back()->withErrors([
            'email' => __('auth.failed'),
        ]);
    }
}
