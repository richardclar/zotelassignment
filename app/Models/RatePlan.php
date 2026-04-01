<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class RatePlan extends Model
{
    protected $fillable = [
        'room_type_id',
        'rate_plan_type_id',
        'name',
        'slug',
        'description',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function roomType(): BelongsTo
    {
        return $this->belongsTo(RoomType::class);
    }

    public function ratePlanType(): BelongsTo
    {
        return $this->belongsTo(RatePlanType::class);
    }

    public function mealPlanComponents(): BelongsToMany
    {
        return $this->belongsToMany(MealPlanComponent::class, 'rate_plan_meal_plan_component')
            ->withPivot('price_per_person_per_night')
            ->withTimestamps();
    }
}
