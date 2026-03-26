<?php

declare(strict_types=1);

namespace App\DTO;

use Illuminate\Contracts\Support\Arrayable;

class SearchResultDTO implements Arrayable
{
    public function __construct(
        public readonly int $roomTypeId,
        public readonly string $roomTypeName,
        public readonly string $roomTypeSlug,
        public readonly bool $available,
        public readonly ?PriceBreakdownDTO $priceBreakdown,
        public readonly int $availableRooms,
        public readonly string $mealPlan,
    ) {
    }

    public function toArray(): array
    {
        return [
            'room_type_id' => $this->roomTypeId,
            'room_type' => $this->roomTypeName,
            'room_type_slug' => $this->roomTypeSlug,
            'available' => $this->available,
            'available_rooms' => $this->availableRooms,
            'meal_plan' => $this->mealPlan,
            'price_breakdown' => $this->priceBreakdown?->toArray(),
        ];
    }
}
