<?php

namespace App\Modules\Promo\Http\Controllers;

use App\Http\Responses\ApiResponse;
use App\Services\PromoService;

class PromoBannerController
{
    public function show(PromoService $promoService)
    {
        $promoData = $promoService->getPromoForApi();

        if (empty($promoData)) {
            return ApiResponse::notFound('No active promo available');
        }

        return ApiResponse::success($promoData);
    }

    public function showBySlug(string $slug, PromoService $promoService)
    {
        $promo = $promoService->getPromoBySlug($slug);

        if (! $promo) {
            return ApiResponse::notFound("No active promo found for slug: {$slug}");
        }

        return ApiResponse::success($promoService->getPromoForApi($promo));
    }
}
