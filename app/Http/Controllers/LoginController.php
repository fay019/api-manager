<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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

            $user = Auth::user();
            if ($user->is_admin) {
                return redirect()->intended('/admin');
            }

            return redirect()->intended(route('profile.edit'));
        }

        return back()->withErrors([
            'email' => __('auth.failed'),
        ]);
    }
}
