<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RatePlanMealPlan extends Model
{
    protected $fillable = [
        'rate_plan_id',
        'meal_plan_component_id',
    ];

    public function ratePlan(): BelongsTo
    {
        return $this->belongsTo(RatePlan::class);
    }

    public function mealPlanComponent(): BelongsTo
    {
        return $this->belongsTo(MealPlanComponent::class, 'meal_plan_component_id');
    }
}
