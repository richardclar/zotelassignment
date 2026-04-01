<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\RatePlanType;
use Illuminate\Database\Seeder;

class RatePlanTypeSeeder extends Seeder
{
    public function run(): void
    {
        RatePlanType::create([
            'code' => 'EP',
            'name' => 'European Plan',
            'description' => 'Room Only - No meals included',
            'is_active' => true,
        ]);

        RatePlanType::create([
            'code' => 'CP',
            'name' => 'Continental Plan',
            'description' => 'Room with Breakfast',
            'is_active' => true,
        ]);

        RatePlanType::create([
            'code' => 'MAP',
            'name' => 'Modified American Plan',
            'description' => 'Room with Breakfast and Dinner',
            'is_active' => true,
        ]);
    }
}
