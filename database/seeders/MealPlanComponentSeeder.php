<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\MealPlanComponent;
use Illuminate\Database\Seeder;

class MealPlanComponentSeeder extends Seeder
{
    public function run(): void
    {
        MealPlanComponent::create([
            'code' => 'ROOM',
            'name' => 'Room Only',
            'price_per_person_per_night' => 0,
            'is_active' => true,
        ]);

        MealPlanComponent::create([
            'code' => 'BREAKFAST',
            'name' => 'Breakfast',
            'price_per_person_per_night' => 800,
            'is_active' => true,
        ]);

        MealPlanComponent::create([
            'code' => 'DINNER',
            'name' => 'Dinner',
            'price_per_person_per_night' => 1200,
            'is_active' => true,
        ]);
    }
}
