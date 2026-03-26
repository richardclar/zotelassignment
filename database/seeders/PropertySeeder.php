<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Property;
use Illuminate\Database\Seeder;

class PropertySeeder extends Seeder
{
    public function run(): void
    {
        Property::create([
            'name' => 'Zotel Project Hotel',
            'slug' => 'zotel-project',
            'description' => 'A luxurious 5-star hotel in the heart of the city with stunning views and world-class amenities.',
            'address' => '123 Main Street',
            'city' => 'Mumbai',
            'country' => 'India',
            'star_rating' => 5,
            'is_active' => true,
        ]);
    }
}
