<?php

declare(strict_types=1);

namespace App\Services;

use App\DTO\RatePlanResultDTO;
use App\DTO\SearchRequestDTO;
use App\DTO\SearchResultDTO;
use App\Interfaces\PricingServiceInterface;
use App\Interfaces\SearchServiceInterface;
use App\Models\Inventory;
use App\Models\PricingRule;
use App\Models\RatePlan;
use App\Models\RoomType;
use Illuminate\Support\Collection;
use InvalidArgumentException;

class SearchService implements SearchServiceInterface
{
    public function __construct(
        private readonly PricingServiceInterface $pricingService
    ) {}

    public function search(SearchRequestDTO $request): Collection
    {
        $this->validateRequest($request);

        $roomTypes = RoomType::with(['property', 'activeRatePlans.ratePlanType', 'activeRatePlans.mealPlanComponents'])
            ->where('is_active', true)
            ->whereHas('property', fn ($q) => $q->where('is_active', true))
            ->get();

        $results = collect();

        foreach ($roomTypes as $roomType) {
            $result = $this->processRoomType($request, $roomType);
            $results->push($result);
        }

        return $results->sortBy(fn ($r) => $r->ratePlans[0]->priceBreakdown?->finalPrice ?? PHP_INT_MAX);
    }

    public function validateRequest(SearchRequestDTO $request): void
    {
        $maxOccupancy = RoomType::max('max_occupancy') ?? 4;

        if ($request->adults < 1 || $request->adults > $maxOccupancy) {
            throw new InvalidArgumentException("Adults must be between 1 and {$maxOccupancy}");
        }

        if ($request->checkInDate->lt(today())) {
            throw new InvalidArgumentException('Check-in date cannot be in the past');
        }

        if ($request->checkOutDate->lte($request->checkInDate)) {
            throw new InvalidArgumentException('Check-out date must be after check-in date');
        }

        if ($request->getNights() > 60) {
            throw new InvalidArgumentException('Maximum stay is 60 nights');
        }
    }

    private function processRoomType(SearchRequestDTO $request, RoomType $roomType): SearchResultDTO
    {
        $inventories = $this->getInventoriesForDateRange($request, $roomType);
        $isAvailable = $this->checkAvailability($inventories);
        $availableRooms = $this->calculateAvailableRooms($inventories);

        $ratePlans = collect();

        foreach ($roomType->activeRatePlans as $ratePlan) {
            $ratePlanResult = $this->processRatePlan($request, $roomType, $ratePlan, $inventories);
            $ratePlans->push($ratePlanResult);
        }

        return new SearchResultDTO(
            roomTypeId: $roomType->id,
            roomTypeName: $roomType->name,
            roomTypeSlug: $roomType->slug,
            maxOccupancy: $roomType->max_occupancy,
            ratePlans: $ratePlans->toArray()
        );
    }

    private function processRatePlan(
        SearchRequestDTO $request,
        RoomType $roomType,
        RatePlan $ratePlan,
        Collection $inventories
    ): RatePlanResultDTO {
        $isAvailable = $this->checkAvailability($inventories);
        $availableRooms = $this->calculateAvailableRooms($inventories);

        $dailyPrices = $this->getDailyPricesForRatePlan($request, $roomType, $ratePlan);
        $mealPlanPricePerNight = $ratePlan->mealPlanComponents->sum('price_per_person_per_night');

        $priceBreakdown = $isAvailable
            ? $this->pricingService->calculatePrice(
                $request,
                $roomType->id,
                $dailyPrices,
                $ratePlan,
                $mealPlanPricePerNight
            )
            : null;

        $mealPlansIncluded = $ratePlan->mealPlanComponents->pluck('name')->implode(', ');

        return new RatePlanResultDTO(
            ratePlanId: $ratePlan->id,
            ratePlanName: $ratePlan->name,
            ratePlanCode: $ratePlan->ratePlanType->code ?? 'EP',
            mealPlansIncluded: $mealPlansIncluded,
            mealPlanPricePerNight: $mealPlanPricePerNight,
            available: $isAvailable,
            priceBreakdown: $priceBreakdown,
            availableRooms: $availableRooms
        );
    }

    private function getInventoriesForDateRange(SearchRequestDTO $request, RoomType $roomType): Collection
    {
        $startDate = $request->checkInDate->format('Y-m-d');
        $endDate = $request->checkOutDate->copy()->subDay()->format('Y-m-d');

        return Inventory::where('room_type_id', $roomType->id)
            ->whereBetween('date', [$startDate, $endDate])
            ->get()
            ->keyBy(fn ($item) => $item->date->format('Y-m-d'));
    }

    private function checkAvailability(Collection $inventories): bool
    {
        if ($inventories->isEmpty()) {
            return false;
        }

        return ! $inventories->contains(fn ($inv) => $inv->is_sold_out);
    }

    private function calculateAvailableRooms(Collection $inventories): int
    {
        if ($inventories->isEmpty()) {
            return 0;
        }

        return $inventories->min('available_rooms');
    }

    private function getDailyPricesForRatePlan(
        SearchRequestDTO $request,
        RoomType $roomType,
        RatePlan $ratePlan
    ): array {
        $startDate = $request->checkInDate->copy();
        $endDate = $request->checkOutDate->copy()->subDay();
        $adults = $request->adults;

        $prices = [];
        $current = $startDate->copy();

        while ($current->lte($endDate)) {
            $pricingRule = PricingRule::where('room_type_id', $roomType->id)
                ->where('rate_plan_id', $ratePlan->id)
                ->whereDate('date', $current->format('Y-m-d'))
                ->where('occupancy', $adults)
                ->first();

            if (! $pricingRule) {
                $pricingRule = PricingRule::where('room_type_id', $roomType->id)
                    ->whereNull('rate_plan_id')
                    ->whereDate('date', $current->format('Y-m-d'))
                    ->where('occupancy', $adults)
                    ->first();
            }

            $prices[] = $pricingRule?->base_price ?? 0;
            $current->addDay();
        }

        return $prices;
    }
}
