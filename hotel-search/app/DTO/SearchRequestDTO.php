<?php

declare(strict_types=1);

namespace App\DTO;

use App\Enums\MealPlanType;
use Carbon\Carbon;
use Illuminate\Contracts\Support\Arrayable;

class SearchRequestDTO implements Arrayable
{
    public function __construct(
        public readonly Carbon $checkInDate,
        public readonly Carbon $checkOutDate,
        public readonly int $adults,
        public readonly MealPlanType $mealPlan,
    ) {
    }

    public function getNights(): int
    {
        return (int) $this->checkInDate->diffInDays($this->checkOutDate);
    }

    public function getDaysBeforeCheckin(): int
    {
        return (int) Carbon::today()->diffInDays($this->checkInDate, false);
    }

    public function toArray(): array
    {
        return [
            'check_in_date' => $this->checkInDate->format('Y-m-d'),
            'check_out_date' => $this->checkOutDate->format('Y-m-d'),
            'adults' => $this->adults,
            'meal_plan' => $this->mealPlan->value,
        ];
    }
}
