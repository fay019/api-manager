<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\DocsController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::prefix('docs')->name('docs.')->group(function () {
    Route::get('/', [DocsController::class, 'index'])->name('index');
    Route::get('/api', [DocsController::class, 'api'])->name('api');
    Route::get('/database', [DocsController::class, 'database'])->name('database');
    Route::get('/deployment', [DocsController::class, 'deployment'])->name('deployment');
    Route::get('/readme', [DocsController::class, 'readme'])->name('readme');
});
