<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MealPlan extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'description',
        'price_per_person_per_night',
        'is_active',
    ];

    protected $casts = [
        'price_per_person_per_night' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function ratePlans(): HasMany
    {
        return $this->hasMany(RatePlan::class);
    }
}
