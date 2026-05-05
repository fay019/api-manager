<?php

use App\Http\Controllers\Api\HealthController;
use Illuminate\Support\Facades\Route;

Route::get('/health', [HealthController::class, 'index'])
    ->name('health');

Route::prefix('promo')
    ->name('promo.')
    ->group(base_path('routes/api/promo.php'));

include base_path('routes/api/ai.php');
