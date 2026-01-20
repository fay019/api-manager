<?php

namespace App\Modules\Promo\Http\Controllers;

use App\Http\Responses\ApiResponse;
use App\Services\PromoService;

class PromoBannerController
{
    public function show(PromoService $promoService)
    {
        $promo = $promoService->getPromoForApi();

        if (empty($promo)) {
            return ApiResponse::notFound('No active promo available');
        }

        return ApiResponse::success($promo);
    }
}
