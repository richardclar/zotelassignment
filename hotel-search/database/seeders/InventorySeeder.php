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
        $endDate = Carbon::today()->addDays(30);

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
            $blockedRooms = mt_rand(0, 1);
            $isClosed = $bookedRooms >= $totalRooms && mt_rand(0, 10) > 7;

            Inventory::create([
                'room_type_id' => $roomType->id,
                'date' => $currentDate->format('Y-m-d'),
                'total_rooms' => $totalRooms,
                'booked_rooms' => $isClosed ? $totalRooms : $bookedRooms,
                'blocked_rooms' => $isClosed ? 0 : $blockedRooms,
                'is_closed' => $isClosed,
            ]);

            $currentDate->addDay();
        }
    }

    private function calculateRandomBookedRooms(Carbon $date, int $totalRooms): int
    {
        $dayOfWeek = $date->dayOfWeek;

        $baseChance = match ($dayOfWeek) {
            Carbon::MONDAY, Carbon::TUESDAY, Carbon::WEDNESDAY, Carbon::THURSDAY => 0.3,
            Carbon::FRIDAY => 0.5,
            Carbon::SATURDAY, Carbon::SUNDAY => 0.7,
            default => 0.3,
        };

        $randomFactor = mt_rand(0, 100) / 100;
        $isHighDemand = mt_rand(0, 10) > 8;

        if ($isHighDemand) {
            $baseChance += 0.2;
        }

        $bookedRatio = min(1, max(0, $baseChance + ($randomFactor - 0.5) * 0.3));

        return (int) round($totalRooms * $bookedRatio);
    }
}
