<?php

declare(strict_types=1);

namespace App\Services;

use App\DTO\PriceBreakdownDTO;
use App\DTO\SearchRequestDTO;
use App\Interfaces\DiscountServiceInterface;
use App\Interfaces\PricingServiceInterface;
use App\Models\RatePlan;

class PricingService implements PricingServiceInterface
{
    public function __construct(
        private readonly DiscountServiceInterface $discountService
    ) {}

    public function calculatePrice(
        SearchRequestDTO $request,
        int $roomTypeId,
        array $dailyPrices,
        ?RatePlan $ratePlan = null,
        float $mealPlanPricePerPersonPerNight = 0
    ): PriceBreakdownDTO {
        $nights = $request->getNights();
        $adults = $request->adults;

        $baseRoomPrice = array_sum($dailyPrices);
        $mealPlanPrice = $mealPlanPricePerPersonPerNight * $adults * $nights;
        $subtotal = $baseRoomPrice + $mealPlanPrice;

        $ratePlanTypeId = $ratePlan?->ratePlanType?->id;
        $discountResult = $this->discountService->calculateDiscounts(
            $request,
            $subtotal,
            $roomTypeId,
            $ratePlanTypeId
        );
        $totalDiscount = $discountResult['total_discount'];
        $appliedDiscounts = $discountResult['discounts'];

        $finalPrice = max(0, $subtotal - $totalDiscount);
        $pricePerNight = $nights > 0 ? $finalPrice / $nights : 0;

        return new PriceBreakdownDTO(
            baseRoomPrice: $baseRoomPrice,
            mealPlanPrice: $mealPlanPrice,
            subtotal: $subtotal,
            discount: $totalDiscount,
            finalPrice: $finalPrice,
            appliedDiscounts: $appliedDiscounts,
            nights: $nights,
            pricePerNight: $pricePerNight
        );
    }

    public function calculateMealPlanPrice(
        SearchRequestDTO $request,
        float $pricePerPersonPerNight
    ): float {
        return $pricePerPersonPerNight * $request->adults * $request->getNights();
    }
}
