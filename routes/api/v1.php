<?php

use App\Http\Controllers\Api\HealthController;
use Illuminate\Support\Facades\Route;

Route::get('/health', [HealthController::class, 'index'])
    ->name('health');

Route::prefix('promo')
    ->group(base_path('routes/api/promo.php'));
