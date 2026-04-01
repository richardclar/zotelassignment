<?php

declare(strict_types=1);

namespace Tests\Unit\Services;

use App\DTO\SearchRequestDTO;
use App\Models\MealPlan;
use App\Services\DiscountService;
use App\Services\PricingService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PricingServiceTest extends TestCase
{
    use RefreshDatabase;

    private PricingService $pricingService;

    protected function setUp(): void
    {
        parent::setUp();

        MealPlan::create([
            'name' => 'Room Only',
            'slug' => 'room_only',
            'price_per_person_per_night' => 0,
            'is_active' => true,
        ]);

        MealPlan::create([
            'name' => 'Bed & Breakfast',
            'slug' => 'breakfast',
            'price_per_person_per_night' => 35.00,
            'is_active' => true,
        ]);

        $discountService = new DiscountService;
        $this->pricingService = new PricingService($discountService);
    }

    public function test_calculates_base_price_correctly(): void
    {
        $request = new SearchRequestDTO(
            checkInDate: Carbon::parse('2026-04-01'),
            checkOutDate: Carbon::parse('2026-04-03'),
            adults: 2,
        );

        $dailyPrices = [150.00, 150.00];

        $result = $this->pricingService->calculatePrice($request, 1, $dailyPrices);

        $this->assertEquals(300.00, $result->baseRoomPrice);
        $this->assertEquals(300.00, $result->subtotal);
        $this->assertEquals(2, $result->nights);
    }

    public function test_calculates_meal_plan_price_correctly(): void
    {
        $request = new SearchRequestDTO(
            checkInDate: Carbon::parse('2026-04-01'),
            checkOutDate: Carbon::parse('2026-04-03'),
            adults: 2,
        );

        $dailyPrices = [150.00, 150.00];
        $mealPlanPricePerPersonPerNight = 70.00;

        $result = $this->pricingService->calculatePrice(
            $request,
            1,
            $dailyPrices,
            null,
            $mealPlanPricePerPersonPerNight
        );

        $this->assertEquals(280.00, $result->mealPlanPrice);
        $this->assertEquals(580.00, $result->subtotal);
    }

    public function test_calculates_price_per_night(): void
    {
        $request = new SearchRequestDTO(
            checkInDate: Carbon::parse('2026-04-01'),
            checkOutDate: Carbon::parse('2026-04-04'),
            adults: 2,
        );

        $dailyPrices = [100.00, 100.00, 100.00];

        $result = $this->pricingService->calculatePrice($request, 1, $dailyPrices);

        $this->assertEquals(100.00, $result->pricePerNight);
        $this->assertEquals(300.00, $result->finalPrice);
    }
}
