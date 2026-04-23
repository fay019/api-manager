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

        Log::info('user.login.attempt', [
            'email' => $credentials['email'],
            'ip' => $request->ip(),
        ]);

        if (Auth::attempt($credentials)) {
            Log::info('user.login.auth_attempt_success', [
                'email' => $credentials['email'],
                'user_id' => Auth::user()->id,
            ]);

            $request->session()->regenerate();

            Log::info('user.login.session_regenerated', [
                'email' => $credentials['email'],
                'session_id' => $request->session()->getId(),
                'is_admin' => Auth::user()->is_admin,
            ]);

            $target = Auth::user()->is_admin ? '/admin' : route('profile.edit');

            Log::info('user.login.redirecting', [
                'email' => $credentials['email'],
                'target' => $target,
            ]);

            return redirect()->intended($target);
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
