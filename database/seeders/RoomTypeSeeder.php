<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\RoomType;
use Illuminate\Database\Seeder;

class RoomTypeSeeder extends Seeder
{
    public function run(): void
    {
        $propertyId = 1;

        RoomType::create([
            'property_id' => $propertyId,
            'name' => 'Standard Room',
            'slug' => 'standard',
            'description' => 'Comfortable room with essential amenities for a pleasant stay.',
            'max_occupancy' => 3,
            'total_rooms' => 5,
            'amenities' => ['wifi', 'tv', 'air_conditioning', 'minibar'],
            'is_active' => true,
        ]);

        RoomType::create([
            'property_id' => $propertyId,
            'name' => 'Deluxe Room',
            'slug' => 'deluxe',
            'description' => 'Spacious room with premium amenities and beautiful views.',
            'max_occupancy' => 3,
            'total_rooms' => 5,
            'amenities' => ['wifi', 'tv', 'air_conditioning', 'minibar', 'balcony', 'jacuzzi'],
            'is_active' => true,
        ]);
    }
}
