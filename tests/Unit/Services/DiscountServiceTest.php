<?php

declare(strict_types=1);

namespace Tests\Unit\Services;

use App\DTO\SearchRequestDTO;
use App\Models\DiscountRule;
use App\Models\DiscountType;
use App\Services\DiscountService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DiscountServiceTest extends TestCase
{
    use RefreshDatabase;

    private DiscountService $discountService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->discountService = new DiscountService;
    }

    public function test_calculates_long_stay_discount_for_3_nights(): void
    {
        $longStayType = DiscountType::create([
            'name' => 'Long Stay Discount',
            'slug' => 'long_stay',
            'discount_type' => 'percentage',
            'discount_value' => 15,
            'is_stackable' => true,
            'is_active' => true,
        ]);

        DiscountRule::create([
            'discount_type_id' => $longStayType->id,
            'min_nights' => 3,
            'is_active' => true,
        ]);

        $request = new SearchRequestDTO(
            checkInDate: Carbon::parse('2026-04-01'),
            checkOutDate: Carbon::parse('2026-04-04'),
            adults: 2,
        );

        $result = $this->discountService->calculateDiscounts($request, 1000.00, 1);

        $this->assertCount(1, $result['discounts']);
        $this->assertEquals(150.00, $result['total_discount']);
        $this->assertEquals('long_stay', $result['discounts'][0]->slug);
    }

    public function test_calculates_last_minute_discount_within_3_days(): void
    {
        $lastMinuteType = DiscountType::create([
            'name' => 'Last Minute Discount',
            'slug' => 'last_minute',
            'discount_type' => 'percentage',
            'discount_value' => 10,
            'is_stackable' => true,
            'is_active' => true,
        ]);

        DiscountRule::create([
            'discount_type_id' => $lastMinuteType->id,
            'min_days_before_checkin' => 0,
            'max_days_before_checkin' => 3,
            'is_active' => true,
        ]);

        $checkInDate = Carbon::today()->addDays(2);

        $request = new SearchRequestDTO(
            checkInDate: $checkInDate,
            checkOutDate: $checkInDate->copy()->addDay(),
            adults: 2,
        );

        $result = $this->discountService->calculateDiscounts($request, 500.00, 1);

        $this->assertCount(1, $result['discounts']);
        $this->assertEquals(50.00, $result['total_discount']);
    }

    public function test_no_discount_for_short_stay_under_3_nights(): void
    {
        $longStayType = DiscountType::create([
            'name' => 'Long Stay Discount',
            'slug' => 'long_stay',
            'discount_type' => 'percentage',
            'discount_value' => 15,
            'is_stackable' => true,
            'is_active' => true,
        ]);

        DiscountRule::create([
            'discount_type_id' => $longStayType->id,
            'min_nights' => 3,
            'is_active' => true,
        ]);

        $request = new SearchRequestDTO(
            checkInDate: Carbon::parse('2026-04-01'),
            checkOutDate: Carbon::parse('2026-04-03'),
            adults: 2,
        );

        $result = $this->discountService->calculateDiscounts($request, 500.00, 1);

        $this->assertCount(0, $result['discounts']);
        $this->assertEquals(0.00, $result['total_discount']);
    }
}
