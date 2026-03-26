<?php

declare(strict_types=1);

namespace App\Services;

use App\DTO\SearchRequestDTO;
use App\DTO\SearchResultDTO;
use App\Enums\MealPlanType;
use App\Interfaces\PricingServiceInterface;
use App\Interfaces\SearchServiceInterface;
use App\Models\Inventory;
use App\Models\MealPlan;
use App\Models\PricingRule;
use App\Models\RoomType;
use Illuminate\Support\Collection;
use InvalidArgumentException;

class SearchService implements SearchServiceInterface
{
    public function __construct(
        private readonly PricingServiceInterface $pricingService
    ) {
    }

    public function search(SearchRequestDTO $request): Collection
    {
        $this->validateRequest($request);

        $roomTypes = RoomType::with('property')
            ->where('is_active', true)
            ->whereHas('property', fn($q) => $q->where('is_active', true))
            ->get();

        $results = collect();

        foreach ($roomTypes as $roomType) {
            $result = $this->processRoomType($request, $roomType);
            $results->push($result);
        }

        return $results->sortBy('price_breakdown.final_price');
    }

    public function validateRequest(SearchRequestDTO $request): void
    {
        if ($request->adults < 1 || $request->adults > 3) {
            throw new InvalidArgumentException('Adults must be between 1 and 3');
        }

        if ($request->checkInDate->lt(today())) {
            throw new InvalidArgumentException('Check-in date cannot be in the past');
        }

        if ($request->checkOutDate->lte($request->checkInDate)) {
            throw new InvalidArgumentException('Check-out date must be after check-in date');
        }

        if ($request->getNights() > 30) {
            throw new InvalidArgumentException('Maximum stay is 30 nights');
        }
    }

    private function processRoomType(SearchRequestDTO $request, RoomType $roomType): SearchResultDTO
    {
        $inventories = $this->getInventoriesForDateRange($request, $roomType);

        $isAvailable = $this->checkAvailability($inventories);
        $availableRooms = $this->calculateAvailableRooms($inventories);

        $dailyPrices = $this->getDailyPrices($request, $roomType);

        $priceBreakdown = $isAvailable
            ? $this->pricingService->calculatePrice($request, $roomType->id, $dailyPrices)
            : null;

        return new SearchResultDTO(
            roomTypeId: $roomType->id,
            roomTypeName: $roomType->name,
            roomTypeSlug: $roomType->slug,
            available: $isAvailable,
            priceBreakdown: $priceBreakdown,
            availableRooms: $availableRooms,
            mealPlan: $this->getMealPlanDisplayName($request->mealPlan)
        );
    }

    private function getInventoriesForDateRange(SearchRequestDTO $request, RoomType $roomType): Collection
    {
        $startDate = $request->checkInDate->format('Y-m-d');
        $endDate = $request->checkOutDate->copy()->subDay()->format('Y-m-d');

        return Inventory::where('room_type_id', $roomType->id)
            ->whereBetween('date', [$startDate, $endDate])
            ->get()
            ->keyBy(fn($item) => $item->date->format('Y-m-d'));
    }

    private function checkAvailability(Collection $inventories): bool
    {
        if ($inventories->isEmpty()) {
            return false;
        }

        return !$inventories->contains(fn($inv) => $inv->is_sold_out);
    }

    private function calculateAvailableRooms(Collection $inventories): int
    {
        if ($inventories->isEmpty()) {
            return 0;
        }

        return $inventories->min('available_rooms');
    }

    private function getDailyPrices(SearchRequestDTO $request, RoomType $roomType): array
    {
        $startDate = $request->checkInDate->copy();
        $endDate = $request->checkOutDate->copy()->subDay();
        $adults = $request->adults;

        $prices = [];
        $current = $startDate->copy();

        while ($current->lte($endDate)) {
            $pricingRule = PricingRule::where('room_type_id', $roomType->id)
                ->whereDate('date', $current->format('Y-m-d'))
                ->where('occupancy', $adults)
                ->first();

            $prices[] = $pricingRule?->base_price ?? 0;
            $current->addDay();
        }

        return $prices;
    }

    private function getMealPlanDisplayName(MealPlanType $mealPlan): string
    {
        return match ($mealPlan) {
            MealPlanType::ROOM_ONLY => 'Room Only',
            MealPlanType::BREAKFAST => 'Bed & Breakfast',
        };
    }
}
