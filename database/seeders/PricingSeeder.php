<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\PricingRule;
use App\Models\RoomType;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class PricingSeeder extends Seeder
{
    private array $basePrices = [
        'standard' => [
            1 => 8000.00,
            2 => 12000.00,
            3 => 16000.00,
        ],
        'deluxe' => [
            1 => 14500.00,
            2 => 20000.00,
            3 => 26000.00,
        ],
    ];

    public function run(): void
    {
        $startDate = Carbon::today();
        $endDate = Carbon::today()->addDays(60);

        $roomTypes = RoomType::all();

        foreach ($roomTypes as $roomType) {
            $this->seedPricingForRoomType($roomType, $startDate, $endDate);
        }
    }

    private function seedPricingForRoomType(RoomType $roomType, Carbon $startDate, Carbon $endDate): void
    {
        $prices = $this->basePrices[$roomType->slug] ?? $this->basePrices['standard'];
        $currentDate = $startDate->copy();

        while ($currentDate->lte($endDate)) {
            foreach ([1, 2, 3] as $occupancy) {
                $price = $this->calculateDynamicPrice($prices[$occupancy], $currentDate);

                PricingRule::create([
                    'room_type_id' => $roomType->id,
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

        $multiplier = $isWeekend ? 1.2 : 1.0;

        $dayOfYear = $date->dayOfYear;
        $seasonalMultiplier = $this->getSeasonalMultiplier($dayOfYear);

        $randomVariation = (mt_rand(-10, 10) / 100) + 1;

        return round($basePrice * $multiplier * $seasonalMultiplier * $randomVariation, 2);
    }

    private function getSeasonalMultiplier(int $dayOfYear): float
    {
        if ($dayOfYear >= 80 && $dayOfYear <= 151) {
            return 0.9;
        }
        if ($dayOfYear >= 152 && $dayOfYear <= 243) {
            return 1.3;
        }
        if ($dayOfYear >= 244 && $dayOfYear <= 334) {
            return 1.0;
        }

        return 1.1;
    }
}
