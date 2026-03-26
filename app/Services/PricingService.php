<?php

declare(strict_types=1);

namespace App\Services;

use App\DTO\PriceBreakdownDTO;
use App\DTO\SearchRequestDTO;
use App\Interfaces\DiscountServiceInterface;
use App\Interfaces\PricingServiceInterface;
use App\Models\MealPlan;

class PricingService implements PricingServiceInterface
{
    public function __construct(
        private readonly DiscountServiceInterface $discountService
    ) {
    }

    public function calculatePrice(
        SearchRequestDTO $request,
        int $roomTypeId,
        array $dailyPrices
    ): PriceBreakdownDTO {
        $nights = $request->getNights();
        $adults = $request->adults;

        $basePrice = array_sum($dailyPrices);
        $mealPrice = $this->calculateMealPlanPrice($request, $this->getMealPlanPricePerPerson($request));
        $subtotal = $basePrice + $mealPrice;

        $discountResult = $this->discountService->calculateDiscounts($request, $subtotal, $roomTypeId);
        $totalDiscount = $discountResult['total_discount'];
        $appliedDiscounts = $discountResult['discounts'];

        $finalPrice = max(0, $subtotal - $totalDiscount);
        $pricePerNight = $nights > 0 ? $finalPrice / $nights : 0;

        return new PriceBreakdownDTO(
            basePrice: $basePrice,
            mealPrice: $mealPrice,
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

    private function getMealPlanPricePerPerson(SearchRequestDTO $request): float
    {
        $mealPlan = MealPlan::where('slug', $request->mealPlan->value)->first();

        return (float) ($mealPlan?->price_per_person_per_night ?? 0);
    }
}
