<?php

declare(strict_types=1);

namespace App\DTO;

use Illuminate\Contracts\Support\Arrayable;

class RatePlanResultDTO implements Arrayable
{
    public function __construct(
        public readonly int $ratePlanId,
        public readonly string $ratePlanName,
        public readonly string $ratePlanCode,
        public readonly string $mealPlansIncluded,
        public readonly float $mealPlanPricePerNight,
        public readonly bool $available,
        public readonly ?PriceBreakdownDTO $priceBreakdown,
        public readonly int $availableRooms,
    ) {}

    public function toArray(): array
    {
        return [
            'rate_plan_id' => $this->ratePlanId,
            'rate_plan' => $this->ratePlanName,
            'rate_plan_code' => $this->ratePlanCode,
            'meal_plans_included' => $this->mealPlansIncluded,
            'meal_plan_price_per_night' => round($this->mealPlanPricePerNight, 2),
            'available' => $this->available,
            'available_rooms' => $this->availableRooms,
            'price_breakdown' => $this->priceBreakdown?->toArray(),
        ];
    }
}
