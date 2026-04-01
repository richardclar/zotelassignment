<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class RoomType extends Model
{
    protected $fillable = [
        'property_id',
        'name',
        'slug',
        'description',
        'max_occupancy',
        'total_rooms',
        'amenities',
        'images',
        'is_active',
    ];

    protected $casts = [
        'max_occupancy' => 'integer',
        'total_rooms' => 'integer',
        'amenities' => 'array',
        'images' => 'array',
        'is_active' => 'boolean',
    ];

    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }

    public function rooms(): HasMany
    {
        return $this->hasMany(Room::class);
    }

    public function inventories(): HasMany
    {
        return $this->hasMany(Inventory::class);
    }

    public function pricingRules(): HasMany
    {
        return $this->hasMany(PricingRule::class);
    }

    public function ratePlans(): HasMany
    {
        return $this->hasMany(RatePlan::class);
    }

    public function activeRatePlans(): HasMany
    {
        return $this->hasMany(RatePlan::class)->where('is_active', true)->with('ratePlanType', 'mealPlanComponents');
    }
}
