<?php

declare(strict_types=1);

namespace Tests\Feature\Api;

use App\Models\DiscountRule;
use App\Models\DiscountType;
use App\Models\Inventory;
use App\Models\MealPlan;
use App\Models\PricingRule;
use App\Models\Property;
use App\Models\RoomType;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SearchControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $property = Property::create([
            'name' => 'Test Hotel',
            'slug' => 'test-hotel',
            'is_active' => true,
        ]);

        RoomType::create([
            'property_id' => $property->id,
            'name' => 'Standard Room',
            'slug' => 'standard',
            'max_occupancy' => 3,
            'total_rooms' => 5,
            'is_active' => true,
        ]);

        MealPlan::create([
            'name' => 'Room Only',
            'slug' => 'room_only',
            'price_per_person_per_night' => 0,
            'is_active' => true,
        ]);

        MealPlan::create([
            'name' => 'Bed & Breakfast',
            'slug' => 'breakfast',
            'price_per_person_per_night' => 2800.00,
            'is_active' => true,
        ]);

        $roomType = RoomType::first();
        $dates = ['2026-04-01', '2026-04-02', '2026-04-03'];

        foreach ($dates as $date) {
            Inventory::create([
                'room_type_id' => $roomType->id,
                'date' => $date,
                'total_rooms' => 5,
                'booked_rooms' => 0,
                'blocked_rooms' => 0,
                'is_closed' => false,
            ]);

            foreach ([1, 2, 3] as $occupancy) {
                PricingRule::create([
                    'room_type_id' => $roomType->id,
                    'date' => $date,
                    'occupancy' => $occupancy,
                    'base_price' => 8000.00 * $occupancy,
                    'is_active' => true,
                ]);
            }
        }

        $discountType = DiscountType::create([
            'name' => 'Long Stay Discount',
            'slug' => 'long_stay',
            'discount_type' => 'percentage',
            'discount_value' => 15,
            'is_stackable' => true,
            'is_active' => true,
        ]);

        DiscountRule::create([
            'discount_type_id' => $discountType->id,
            'min_nights' => 3,
            'is_active' => true,
        ]);
    }

    public function test_search_returns_available_rooms(): void
    {
        $response = $this->getJson('/api/search?' . http_build_query([
            'check_in' => '2026-04-01',
            'check_out' => '2026-04-03',
            'adults' => 2,
            'meal_plan' => 'room_only',
        ]));

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'meta' => [
                    'nights' => 2,
                    'total_results' => 1,
                ],
            ]);
    }

    public function test_search_calculates_long_stay_discount(): void
    {
        $response = $this->getJson('/api/search?' . http_build_query([
            'check_in' => '2026-04-01',
            'check_out' => '2026-04-04',
            'adults' => 2,
            'meal_plan' => 'room_only',
        ]));

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'meta' => [
                    'nights' => 3,
                ],
            ]);

        $data = $response->json('data.0.price_breakdown');
        $this->assertGreaterThan(0, $data['discount']);
        $this->assertNotEmpty($data['applied_discounts']);
    }

    public function test_search_validates_adults_range(): void
    {
        $response = $this->getJson('/api/search?' . http_build_query([
            'check_in' => '2026-04-01',
            'check_out' => '2026-04-03',
            'adults' => 5,
            'meal_plan' => 'room_only',
        ]));

        $response->assertStatus(422);
    }

    public function test_search_validates_checkout_after_checkin(): void
    {
        $response = $this->getJson('/api/search?' . http_build_query([
            'check_in' => '2026-04-03',
            'check_out' => '2026-04-01',
            'adults' => 2,
            'meal_plan' => 'room_only',
        ]));

        $response->assertStatus(422);
    }
}
