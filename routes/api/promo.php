<?php

use App\Modules\Promo\Http\Controllers\PromoBannerController;
use Illuminate\Support\Facades\Route;

Route::get('/banner.json', [PromoBannerController::class, 'show'])
    ->middleware(['throttle.api.client'])
    ->name('banner');

Route::get('/{slug}.json', [PromoBannerController::class, 'showBySlug'])
    ->middleware(['throttle.api.client'])
    ->name('by-slug');
