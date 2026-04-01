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
        public readonly int $maxOccupancy,
        public readonly array $ratePlans,
    ) {}

    public function hasAvailableRatePlans(): bool
    {
        return collect($this->ratePlans)->contains(fn ($rp) => $rp->available);
    }

    public function getLowestPrice(): ?float
    {
        $available = collect($this->ratePlans)
            ->where('available', true)
            ->map(fn ($rp) => $rp->priceBreakdown?->finalPrice);

        return $available->filter()->min();
    }

    public function toArray(): array
    {
        return [
            'room_type_id' => $this->roomTypeId,
            'room_type' => $this->roomTypeName,
            'room_type_slug' => $this->roomTypeSlug,
            'max_occupancy' => $this->maxOccupancy,
            'rate_plans' => array_map(fn ($rp) => is_array($rp) ? $rp : $rp->toArray(), $this->ratePlans),
        ];
    }
}
