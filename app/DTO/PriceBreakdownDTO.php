<?php

declare(strict_types=1);

namespace App\DTO;

use Illuminate\Contracts\Support\Arrayable;

class PriceBreakdownDTO implements Arrayable
{
    public function __construct(
        public readonly float $baseRoomPrice,
        public readonly float $mealPlanPrice,
        public readonly float $subtotal,
        public readonly float $discount,
        public readonly float $finalPrice,
        public readonly array $appliedDiscounts,
        public readonly int $nights,
        public readonly float $pricePerNight,
    ) {}

    public function toArray(): array
    {
        return [
            'room_price' => round($this->baseRoomPrice, 2),
            'meal_plan_price' => round($this->mealPlanPrice, 2),
            'subtotal' => round($this->subtotal, 2),
            'discount' => round($this->discount, 2),
            'final_price' => round($this->finalPrice, 2),
            'applied_discounts' => array_map(fn ($d) => $d->toArray(), $this->appliedDiscounts),
            'nights' => $this->nights,
            'price_per_night' => round($this->pricePerNight, 2),
        ];
    }
}
