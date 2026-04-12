<?php

use App\Http\Controllers\ContactController;
use App\Http\Controllers\DocsController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\ProfileController;
use App\Models\Setting;
use App\Services\Installation\EnvManager;
use Illuminate\Http\Request;
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

// Language switching
Route::get('/locale/{locale}', function (string $locale) {
    $locale = strtolower($locale);
    if (in_array($locale, ['fr', 'en', 'de'])) {
        session(['locale' => $locale]);
        App::setLocale($locale);

        // Update .env if the service is available
        try {
            $envManager = app(EnvManager::class);
            $envManager->update(['APP_LOCALE' => $locale]);
            $envManager->flushCache();
        } catch (Exception $e) {
            // Service not available or .env not writable, continue
        }

        // Permanent cookie for non-authenticated users, expires in 1 year
        $cookie = cookie('locale', $locale, 60 * 24 * 365, '/', null, false, false);

        return back()->withCookie($cookie);
    }

    return back();
})->name('locale.switch')->where('locale', '[a-z]{2}');

// Logout
Route::post('/logout', function () {
    Auth::logout();

    return redirect('/');
})->middleware('auth')->name('logout');

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
