<?php

declare(strict_types=1);

namespace Tests\Unit\Services;

use App\DTO\SearchRequestDTO;
use App\Enums\MealPlanType;
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

        $discountService = new DiscountService();
        $this->pricingService = new PricingService($discountService);
    }

    public function test_calculates_base_price_correctly(): void
    {
        $request = new SearchRequestDTO(
            checkInDate: Carbon::parse('2026-04-01'),
            checkOutDate: Carbon::parse('2026-04-03'),
            adults: 2,
            mealPlan: MealPlanType::ROOM_ONLY
        );

        $dailyPrices = [150.00, 150.00];

        $result = $this->pricingService->calculatePrice($request, 1, $dailyPrices);

        $this->assertEquals(300.00, $result->basePrice);
        $this->assertEquals(300.00, $result->subtotal);
        $this->assertEquals(2, $result->nights);
    }

    public function test_calculates_meal_plan_price_correctly(): void
    {
        $request = new SearchRequestDTO(
            checkInDate: Carbon::parse('2026-04-01'),
            checkOutDate: Carbon::parse('2026-04-03'),
            adults: 2,
            mealPlan: MealPlanType::BREAKFAST
        );

        $dailyPrices = [150.00, 150.00];

        $result = $this->pricingService->calculatePrice($request, 1, $dailyPrices);

        $this->assertEquals(140.00, $result->mealPrice);
        $this->assertEquals(440.00, $result->subtotal);
    }

    public function test_calculates_price_per_night(): void
    {
        $request = new SearchRequestDTO(
            checkInDate: Carbon::parse('2026-04-01'),
            checkOutDate: Carbon::parse('2026-04-04'),
            adults: 2,
            mealPlan: MealPlanType::ROOM_ONLY
        );

        $dailyPrices = [100.00, 100.00, 100.00];

        $result = $this->pricingService->calculatePrice($request, 1, $dailyPrices);

        $this->assertEquals(100.00, $result->pricePerNight);
        $this->assertEquals(300.00, $result->finalPrice);
    }
}
