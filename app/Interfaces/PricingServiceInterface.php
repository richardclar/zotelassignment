<?php

declare(strict_types=1);

namespace App\Interfaces;

use App\DTO\PriceBreakdownDTO;
use App\DTO\SearchRequestDTO;
use App\Models\RatePlan;

interface PricingServiceInterface
{
    public function calculatePrice(
        SearchRequestDTO $request,
        int $roomTypeId,
        array $dailyPrices,
        ?RatePlan $ratePlan = null,
        float $mealPlanPricePerPersonPerNight = 0
    ): PriceBreakdownDTO;

    public function calculateMealPlanPrice(
        SearchRequestDTO $request,
        float $pricePerPersonPerNight
    ): float;
}
