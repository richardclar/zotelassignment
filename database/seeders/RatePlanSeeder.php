<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\MealPlanComponent;
use App\Models\RatePlan;
use App\Models\RatePlanType;
use App\Models\RoomType;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RatePlanSeeder extends Seeder
{
    public function run(): void
    {
        $roomTypes = RoomType::all();
        $mealComponents = MealPlanComponent::all()->keyBy('code');

        foreach ($roomTypes as $roomType) {
            if ($roomType->slug === 'standard') {
                $this->createStandardRoomRatePlans($roomType, $mealComponents);
            } else {
                $this->createDeluxeRoomRatePlans($roomType, $mealComponents);
            }
        }
    }

    private function createStandardRoomRatePlans(RoomType $roomType, $mealComponents): void
    {
        $epType = RatePlanType::where('code', 'EP')->first();
        $cpType = RatePlanType::where('code', 'CP')->first();

        $ep = RatePlan::create([
            'room_type_id' => $roomType->id,
            'rate_plan_type_id' => $epType->id,
            'name' => 'Standard Room Only',
            'slug' => 'standard-ep',
            'description' => 'Room only - European Plan',
            'is_active' => true,
        ]);

        DB::table('rate_plan_meal_plan_component')->insert([
            'rate_plan_id' => $ep->id,
            'meal_plan_component_id' => $mealComponents['ROOM']->id,
            'price_per_person_per_night' => 0,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $cp = RatePlan::create([
            'room_type_id' => $roomType->id,
            'rate_plan_type_id' => $cpType->id,
            'name' => 'Standard with Breakfast',
            'slug' => 'standard-cp',
            'description' => 'Room with Breakfast - Continental Plan',
            'is_active' => true,
        ]);

        DB::table('rate_plan_meal_plan_component')->insert([
            'rate_plan_id' => $cp->id,
            'meal_plan_component_id' => $mealComponents['ROOM']->id,
            'price_per_person_per_night' => 0,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('rate_plan_meal_plan_component')->insert([
            'rate_plan_id' => $cp->id,
            'meal_plan_component_id' => $mealComponents['BREAKFAST']->id,
            'price_per_person_per_night' => 400,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    private function createDeluxeRoomRatePlans(RoomType $roomType, $mealComponents): void
    {
        $cpType = RatePlanType::where('code', 'CP')->first();
        $mapType = RatePlanType::where('code', 'MAP')->first();

        $cp = RatePlan::create([
            'room_type_id' => $roomType->id,
            'rate_plan_type_id' => $cpType->id,
            'name' => 'Deluxe with Breakfast',
            'slug' => 'deluxe-cp',
            'description' => 'Deluxe Room with Breakfast - Continental Plan',
            'is_active' => true,
        ]);

        DB::table('rate_plan_meal_plan_component')->insert([
            'rate_plan_id' => $cp->id,
            'meal_plan_component_id' => $mealComponents['ROOM']->id,
            'price_per_person_per_night' => 0,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('rate_plan_meal_plan_component')->insert([
            'rate_plan_id' => $cp->id,
            'meal_plan_component_id' => $mealComponents['BREAKFAST']->id,
            'price_per_person_per_night' => 500,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $map = RatePlan::create([
            'room_type_id' => $roomType->id,
            'rate_plan_type_id' => $mapType->id,
            'name' => 'Deluxe All Meals',
            'slug' => 'deluxe-map',
            'description' => 'Deluxe Room with Breakfast and Dinner - Modified American Plan',
            'is_active' => true,
        ]);

        DB::table('rate_plan_meal_plan_component')->insert([
            'rate_plan_id' => $map->id,
            'meal_plan_component_id' => $mealComponents['ROOM']->id,
            'price_per_person_per_night' => 0,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('rate_plan_meal_plan_component')->insert([
            'rate_plan_id' => $map->id,
            'meal_plan_component_id' => $mealComponents['BREAKFAST']->id,
            'price_per_person_per_night' => 500,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('rate_plan_meal_plan_component')->insert([
            'rate_plan_id' => $map->id,
            'meal_plan_component_id' => $mealComponents['DINNER']->id,
            'price_per_person_per_night' => 600,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
