<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\MealPlan;
use Illuminate\Database\Seeder;

class MealPlanSeeder extends Seeder
{
    public function run(): void
    {
        MealPlan::create([
            'name' => 'Room Only',
            'slug' => 'room_only',
            'description' => 'Room accommodation only, without meals.',
            'price_per_person_per_night' => 0,
            'is_active' => true,
        ]);

        MealPlan::create([
            'name' => 'Bed & Breakfast',
            'slug' => 'breakfast',
            'description' => 'Room accommodation with daily breakfast buffet.',
            'price_per_person_per_night' => 2800.00,
            'is_active' => true,
        ]);
    }
}
