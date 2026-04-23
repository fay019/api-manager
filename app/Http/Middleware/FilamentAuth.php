<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class FilamentAuth
{
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();

        Log::info('FilamentAuth middleware check', [
            'path' => $request->path(),
            'user_exists' => $user !== null,
            'user_id' => $user?->id,
            'user_email' => $user?->email,
            'is_admin' => $user?->is_admin,
            'session_id' => $request->session()->getId(),
        ]);

        if ($user && ! $user->is_admin) {
            Log::warning('FilamentAuth: User is not admin, logging out', [
                'user_id' => $user->id,
                'user_email' => $user->email,
                'is_admin' => $user->is_admin,
            ]);

            auth()->logout();

            return redirect()->route('login.show')->with('error', 'Unauthorized access');
        }

        if (!$user) {
            Log::warning('FilamentAuth: No user authenticated');
        }

        return $next($request);
    }
}
