<?php

use App\Http\Controllers\Api\AiController;
use Illuminate\Support\Facades\Route;

Route::middleware(['api.key:true', 'cors.client', 'throttle.api.client', 'ia.token'])
    ->prefix('ai')
    ->name('ai.')
    ->group(function () {
        Route::get('/health', [AiController::class, 'health'])->name('health');
        Route::get('/models', [AiController::class, 'models'])->name('models');
        Route::post('/generate', [AiController::class, 'generate'])->name('generate');
        Route::get('/test', [AiController::class, 'test'])->name('test');
    });
