<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            PropertySeeder::class,
            RoomTypeSeeder::class,
            RatePlanTypeSeeder::class,
            MealPlanComponentSeeder::class,
            RatePlanSeeder::class,
            PricingSeeder::class,
            InventorySeeder::class,
            DiscountSeeder::class,
        ]);
    }
}
