<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class FilamentAuth
{
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();

        if ($user && ! $user->is_admin) {
            auth()->logout();

            return redirect()->route('login.show')->with('error', 'Unauthorized access');
        }

        return $next($request);
    }
}
