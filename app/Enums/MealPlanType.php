<?php

declare(strict_types=1);

namespace App\Enums;

enum MealPlanType: string
{
    case ROOM_ONLY = 'room_only';
    case BREAKFAST = 'breakfast';
}
