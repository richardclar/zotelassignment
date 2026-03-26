<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Inventory;
use App\Models\RoomType;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class InventorySeeder extends Seeder
{
    public function run(): void
    {
        $startDate = Carbon::today();
        $endDate = Carbon::today()->addDays(60);

        $roomTypes = RoomType::all();

        foreach ($roomTypes as $roomType) {
            $this->seedInventoryForRoomType($roomType, $startDate, $endDate);
        }
    }

    private function seedInventoryForRoomType(RoomType $roomType, Carbon $startDate, Carbon $endDate): void
    {
        $totalRooms = $roomType->total_rooms;
        $currentDate = $startDate->copy();

        while ($currentDate->lte($endDate)) {
            $bookedRooms = $this->calculateRandomBookedRooms($currentDate, $totalRooms);
            $blockedRooms = 0;
            $isClosed = false;

            Inventory::create([
                'room_type_id' => $roomType->id,
                'date' => $currentDate->format('Y-m-d'),
                'total_rooms' => $totalRooms,
                'booked_rooms' => $bookedRooms,
                'blocked_rooms' => $blockedRooms,
                'is_closed' => $isClosed,
            ]);

            $currentDate->addDay();
        }
    }

    private function calculateRandomBookedRooms(Carbon $date, int $totalRooms): int
    {
        $baseChance = 0.15;

        $randomFactor = mt_rand(0, 100) / 100;
        $bookedRatio = min(0.4, $baseChance + ($randomFactor - 0.5) * 0.1);

        return (int) round($totalRooms * $bookedRatio);
    }
}
