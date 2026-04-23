<?php

use App\Http\Controllers\Api\HealthController;
use App\Http\Controllers\Client\AuthController;
use App\Http\Controllers\Client\DashboardController;
use App\Http\Controllers\Client\ProfileController as ClientProfileController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\DocsController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\ProfileController;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');

// Authentication routes
Route::get('/login', [LoginController::class, 'show'])->name('login.show')->middleware('guest');
Route::post('/login', [LoginController::class, 'store'])->name('login.store')->middleware('guest');

// User profile (non-admin)
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
});

// Contact form
Route::get('/contact', [ContactController::class, 'show'])->name('contact.show');
Route::post('/contact', [ContactController::class, 'store'])->name('contact.store');

// Language switching - session only, no .env modifications
Route::post('/locale/{locale}', function (string $locale) {
    $locale = strtolower($locale);
    if (in_array($locale, ['fr', 'en', 'de'])) {
        session(['locale' => $locale]);
        app()->setLocale($locale);

        // Store in cookie as fallback
        $cookie = cookie('locale', $locale, 60 * 24 * 365, '/', null, false, false);

        return back()->withCookie($cookie);
    }

    return back();
})->name('locale.switch')->where('locale', '[a-z]{2}')->middleware('web');

// Logout
Route::post('/logout', function () {
    Auth::logout();

    return redirect('/');
})->middleware('auth')->name('logout');

// Client authentication (external)
Route::prefix('client')->name('client.')->group(function () {
    Route::middleware('guest:client')->group(function () {
        Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
        Route::post('/register', [AuthController::class, 'register'])
            ->middleware('throttle:client-register');

        Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
        Route::post('/login', [AuthController::class, 'login'])
            ->middleware('throttle:client-login');

        Route::get('/password/forgot', [AuthController::class, 'showForgotPassword'])->name('password.forgot');
        Route::post('/password/forgot', [AuthController::class, 'sendPasswordReset'])
            ->middleware('throttle:client-password-forgot');

        Route::get('/password/reset/{token}', [AuthController::class, 'showResetPassword'])->name('password.reset');
        Route::post('/password/reset', [AuthController::class, 'resetPassword'])
            ->middleware('throttle:client-password-reset');
    });

    Route::get('/activate/{token}', [AuthController::class, 'activate'])
        ->name('activate')
        ->middleware('throttle:client-activate');

    Route::post('/activate/resend', [AuthController::class, 'resendActivation'])
        ->name('activate.resend')
        ->middleware('throttle:client-resend');

    Route::middleware('auth:client')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
        Route::get('/profile', [ClientProfileController::class, 'edit'])->name('profile.edit');
        Route::put('/profile', [ClientProfileController::class, 'update'])->name('profile.update');
        Route::post('/profile/avatar', [ClientProfileController::class, 'updateAvatar'])->name('profile.update-avatar');
        Route::get('/api-keys/{id}/key', [ClientProfileController::class, 'getApiKey'])->name('api-keys.get-key');
    });
});

// Admin routes
Route::middleware('auth')->prefix('admin')->name('admin.')->group(function () {
    Route::post('/settings/save-contact-email', function (Request $request) {
        $email = $request->input('contact_email');

        if (! filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return response()->json(['success' => false, 'message' => 'Invalid email address']);
        }

        try {
            Setting::set('contact_email', $email, 'string', 'Email address for contact form');

            return response()->json(['success' => true]);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    })->name('settings.save-contact-email');

    Route::post('/health-check/test', function (Request $request) {
        try {
            $controller = new HealthController;
            $response = $controller->index();

            return response()->json(json_decode($response->getContent(), true));
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    })->name('health-check.test');

    Route::post('/api-request-logs/archive', function (Request $request) {
        try {
            Artisan::call('api:archive-logs');
            $output = Artisan::output();

            return response()->json(['success' => true, 'message' => $output]);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    })->name('api-request-logs.archive');
});

Route::prefix('docs')->name('docs.')->group(function () {
    Route::get('/', [DocsController::class, 'index'])->name('index');
    // Keep legacy routes for backwards compatibility
    Route::get('/api', [DocsController::class, 'api'])->name('api');
    Route::get('/database', [DocsController::class, 'database'])->name('database');
    Route::get('/deployment', [DocsController::class, 'deployment'])->name('deployment');
    Route::get('/readme', [DocsController::class, 'readme'])->name('readme');
    // Dynamic route for any documentation (must come last)
    Route::get('/{docName}', [DocsController::class, 'show'])->name('show');
});

// Test error routes (remove after testing)
if (config('app.debug')) {
    Route::get('/test-error-401', function () {
        abort(401);
    });
    Route::get('/test-error-403', function () {
        abort(403);
    });
    Route::get('/test-error-404', function () {
        abort(404);
    });
    Route::get('/test-error-419', function () {
        abort(419);
    });
    Route::get('/test-error-500', function () {
        abort(500);
    });
    Route::get('/test-error-503', function () {
        abort(503);
    });
}
