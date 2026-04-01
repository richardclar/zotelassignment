<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PricingRule extends Model
{
    protected $fillable = [
        'room_type_id',
        'rate_plan_id',
        'date',
        'occupancy',
        'base_price',
        'is_active',
    ];

    protected $casts = [
        'date' => 'date',
        'occupancy' => 'integer',
        'base_price' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function roomType(): BelongsTo
    {
        return $this->belongsTo(RoomType::class);
    }

    public function ratePlan(): BelongsTo
    {
        return $this->belongsTo(RatePlan::class);
    }
}
