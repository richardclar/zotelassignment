<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\PricingRule;
use App\Models\RatePlan;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class PricingSeeder extends Seeder
{
    private array $basePrices = [
        'standard-ep' => [
            1 => 7000.00,
            2 => 10000.00,
            3 => 13000.00,
            4 => 16000.00,
        ],
        'standard-cp' => [
            1 => 7800.00,
            2 => 10800.00,
            3 => 13800.00,
            4 => 16800.00,
        ],
        'deluxe-cp' => [
            1 => 13500.00,
            2 => 18000.00,
            3 => 23000.00,
            4 => 28000.00,
        ],
        'deluxe-map' => [
            1 => 15500.00,
            2 => 20000.00,
            3 => 25000.00,
            4 => 30000.00,
        ],
    ];

    public function run(): void
    {
        $startDate = Carbon::today();
        $endDate = Carbon::today()->addDays(60);

        $ratePlans = RatePlan::with('ratePlanType')->get();

        foreach ($ratePlans as $ratePlan) {
            $this->seedPricingForRatePlan($ratePlan, $startDate, $endDate);
        }
    }

    private function seedPricingForRatePlan(RatePlan $ratePlan, Carbon $startDate, Carbon $endDate): void
    {
        $prices = $this->basePrices[$ratePlan->slug] ?? $this->basePrices['standard-ep'];
        $currentDate = $startDate->copy();

        while ($currentDate->lte($endDate)) {
            foreach ([1, 2, 3, 4] as $occupancy) {
                if (! isset($prices[$occupancy])) {
                    continue;
                }

                $price = $this->calculateDynamicPrice($prices[$occupancy], $currentDate);

                PricingRule::create([
                    'room_type_id' => $ratePlan->room_type_id,
                    'rate_plan_id' => $ratePlan->id,
                    'date' => $currentDate->format('Y-m-d'),
                    'occupancy' => $occupancy,
                    'base_price' => $price,
                    'is_active' => true,
                ]);
            }

            $currentDate->addDay();
        }
    }

    private function calculateDynamicPrice(float $basePrice, Carbon $date): float
    {
        $dayOfWeek = $date->dayOfWeek;
        $isWeekend = in_array($dayOfWeek, [Carbon::SATURDAY, Carbon::SUNDAY]);

        $multiplier = $isWeekend ? 1.15 : 1.0;

        $dayOfYear = $date->dayOfYear;
        $seasonalMultiplier = $this->getSeasonalMultiplier($dayOfYear);

        $randomVariation = (mt_rand(-5, 5) / 100) + 1;

        return round($basePrice * $multiplier * $seasonalMultiplier * $randomVariation, 2);
    }

    private function getSeasonalMultiplier(int $dayOfYear): float
    {
        if ($dayOfYear >= 80 && $dayOfYear <= 151) {
            return 0.95;
        }
        if ($dayOfYear >= 152 && $dayOfYear <= 243) {
            return 1.2;
        }
        if ($dayOfYear >= 244 && $dayOfYear <= 334) {
            return 1.0;
        }

        return 1.05;
    }
}
