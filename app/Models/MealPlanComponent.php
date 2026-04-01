<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MealPlanComponent extends Model
{
    protected $fillable = [
        'code',
        'name',
        'price_per_person_per_night',
        'is_active',
    ];

    protected $casts = [
        'price_per_person_per_night' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function ratePlans(): HasMany
    {
        return $this->hasMany(RatePlanMealPlan::class, 'meal_plan_component_id');
    }
}
