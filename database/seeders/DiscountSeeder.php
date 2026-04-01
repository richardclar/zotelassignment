<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\DiscountRule;
use App\Models\DiscountType;
use App\Models\RatePlanType;
use Illuminate\Database\Seeder;

class DiscountSeeder extends Seeder
{
    public function run(): void
    {
        $lastMinute = DiscountType::create([
            'name' => 'Last Minute Discount',
            'slug' => 'last_minute',
            'description' => 'Special discount for last-minute bookings (within 3 days)',
            'discount_type' => DiscountType::TYPE_PERCENTAGE,
            'discount_value' => 10,
            'is_stackable' => true,
            'is_active' => true,
        ]);

        DiscountRule::create([
            'discount_type_id' => $lastMinute->id,
            'rate_plan_type_id' => null,
            'room_type_id' => null,
            'valid_from' => null,
            'valid_to' => null,
            'min_nights' => 1,
            'max_nights' => null,
            'min_days_before_checkin' => 0,
            'max_days_before_checkin' => 3,
            'is_active' => true,
        ]);

        $longStay = DiscountType::create([
            'name' => 'Long Stay Discount',
            'slug' => 'long_stay',
            'description' => 'Discount for stays of 3 or more nights',
            'discount_type' => DiscountType::TYPE_PERCENTAGE,
            'discount_value' => 15,
            'is_stackable' => true,
            'is_active' => true,
        ]);

        DiscountRule::create([
            'discount_type_id' => $longStay->id,
            'rate_plan_type_id' => null,
            'room_type_id' => null,
            'valid_from' => null,
            'valid_to' => null,
            'min_nights' => 3,
            'max_nights' => null,
            'min_days_before_checkin' => null,
            'max_days_before_checkin' => null,
            'is_active' => true,
        ]);

        $epType = RatePlanType::where('code', 'EP')->first();
        $cpType = RatePlanType::where('code', 'CP')->first();
        $mapType = RatePlanType::where('code', 'MAP')->first();

        $earlyBird = DiscountType::create([
            'name' => 'Early Bird Discount',
            'slug' => 'early_bird',
            'description' => 'Discount for early bookings (7+ days in advance)',
            'discount_type' => DiscountType::TYPE_PERCENTAGE,
            'discount_value' => 5,
            'is_stackable' => true,
            'is_active' => true,
        ]);

        if ($epType) {
            DiscountRule::create([
                'discount_type_id' => $earlyBird->id,
                'rate_plan_type_id' => $epType->id,
                'room_type_id' => null,
                'valid_from' => null,
                'valid_to' => null,
                'min_nights' => 1,
                'max_nights' => null,
                'min_days_before_checkin' => 7,
                'max_days_before_checkin' => null,
                'is_active' => true,
            ]);
        }

        $earlyBirdCP = DiscountType::create([
            'name' => 'Early Bird Discount (CP/MAP)',
            'slug' => 'early_bird_cp_map',
            'description' => 'Higher discount for early bookings on CP/MAP plans',
            'discount_type' => DiscountType::TYPE_PERCENTAGE,
            'discount_value' => 10,
            'is_stackable' => true,
            'is_active' => true,
        ]);

        if ($cpType) {
            DiscountRule::create([
                'discount_type_id' => $earlyBirdCP->id,
                'rate_plan_type_id' => $cpType->id,
                'room_type_id' => null,
                'valid_from' => null,
                'valid_to' => null,
                'min_nights' => 1,
                'max_nights' => null,
                'min_days_before_checkin' => 7,
                'max_days_before_checkin' => null,
                'is_active' => true,
            ]);
        }

        if ($mapType) {
            DiscountRule::create([
                'discount_type_id' => $earlyBirdCP->id,
                'rate_plan_type_id' => $mapType->id,
                'room_type_id' => null,
                'valid_from' => null,
                'valid_to' => null,
                'min_nights' => 1,
                'max_nights' => null,
                'min_days_before_checkin' => 7,
                'max_days_before_checkin' => null,
                'is_active' => true,
            ]);
        }
    }
}
