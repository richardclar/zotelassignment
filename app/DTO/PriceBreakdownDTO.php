<?php

declare(strict_types=1);

namespace App\DTO;

use Illuminate\Contracts\Support\Arrayable;

class PriceBreakdownDTO implements Arrayable
{
    public function __construct(
        public readonly float $basePrice,
        public readonly float $mealPrice,
        public readonly float $subtotal,
        public readonly float $discount,
        public readonly float $finalPrice,
        public readonly array $appliedDiscounts,
        public readonly int $nights,
        public readonly float $pricePerNight,
    ) {
    }

    public function toArray(): array
    {
        return [
            'base_price' => round($this->basePrice, 2),
            'meal_price' => round($this->mealPrice, 2),
            'subtotal' => round($this->subtotal, 2),
            'discount' => round($this->discount, 2),
            'final_price' => round($this->finalPrice, 2),
            'applied_discounts' => array_map(fn($d) => $d->toArray(), $this->appliedDiscounts),
            'nights' => $this->nights,
            'price_per_night' => round($this->pricePerNight, 2),
        ];
    }
}
