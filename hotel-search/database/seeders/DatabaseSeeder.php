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
            MealPlanSeeder::class,
            PricingSeeder::class,
            InventorySeeder::class,
            DiscountSeeder::class,
        ]);
    }
}
