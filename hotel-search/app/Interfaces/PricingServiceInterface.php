<?php

declare(strict_types=1);

namespace App\Interfaces;

use App\DTO\PriceBreakdownDTO;
use App\DTO\SearchRequestDTO;

interface PricingServiceInterface
{
    public function calculatePrice(
        SearchRequestDTO $request,
        int $roomTypeId,
        array $dailyPrices
    ): PriceBreakdownDTO;

    public function calculateMealPlanPrice(
        SearchRequestDTO $request,
        float $pricePerPersonPerNight
    ): float;
}
