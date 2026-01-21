<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\DocsController;
use App\Http\Controllers\SetupController;
use App\Http\Controllers\BootstrapController;
use App\Http\Controllers\InstallerController;
use Illuminate\Support\Facades\Route;

// Bootstrap/Diagnostic Routes (Accessible before setup)
Route::get('/install.php', [InstallerController::class, 'install'])->name('bootstrap.install');
Route::get('/diagnostic.php', [InstallerController::class, 'diagnostic'])->name('bootstrap.diagnostic');
Route::get('/setup.php', [BootstrapController::class, 'setup'])->name('bootstrap.setup');

// Setup Routes (Installation Wizard)
Route::prefix('setup')->name('setup.')->group(function () {
    Route::get('/', [SetupController::class, 'index'])->name('index');
    Route::get('/general', [SetupController::class, 'stepGeneral'])->name('general');
    Route::post('/save-general', [SetupController::class, 'saveGeneral'])->name('save-general');
    Route::get('/database', [SetupController::class, 'stepDatabase'])->name('database');
    Route::post('/test-database', [SetupController::class, 'testDatabase'])->name('test-database');
    Route::post('/save-database', [SetupController::class, 'saveDatabase'])->name('save-database');
    Route::get('/confirm', [SetupController::class, 'stepConfirm'])->name('confirm');
    Route::post('/finish', [SetupController::class, 'finish'])->name('finish');
});

Route::get('/', [HomeController::class, 'index'])->name('home');

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
