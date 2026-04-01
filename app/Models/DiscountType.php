<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DiscountType extends Model
{
    public const TYPE_PERCENTAGE = 'percentage';

    public const TYPE_FIXED = 'fixed';

    protected $fillable = [
        'name',
        'slug',
        'description',
        'discount_type',
        'discount_value',
        'is_stackable',
        'is_active',
    ];

    protected $casts = [
        'discount_value' => 'decimal:2',
        'is_stackable' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function discountRules(): HasMany
    {
        return $this->hasMany(DiscountRule::class);
    }

    public function isPercentage(): bool
    {
        return $this->discount_type === self::TYPE_PERCENTAGE;
    }

    public function isFixed(): bool
    {
        return $this->discount_type === self::TYPE_FIXED;
    }
}
