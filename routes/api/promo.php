<?php

use App\Modules\Promo\Http\Controllers\PromoBannerController;
use App\Modules\Promo\Http\Controllers\PromoEventController;
use Illuminate\Support\Facades\Route;

Route::get('/banner.json', [PromoBannerController::class, 'show'])
    ->middleware(['throttle.api.client'])
    ->name('banner');

Route::post('/event', [PromoEventController::class, 'store'])
    ->middleware(['throttle.api.client'])
    ->name('event');
