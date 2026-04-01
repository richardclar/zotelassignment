<?php

declare(strict_types=1);

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DiscountRule extends Model
{
    protected $fillable = [
        'discount_type_id',
        'rate_plan_type_id',
        'room_type_id',
        'valid_from',
        'valid_to',
        'min_nights',
        'max_nights',
        'min_days_before_checkin',
        'max_days_before_checkin',
        'is_active',
    ];

    protected $casts = [
        'valid_from' => 'date',
        'valid_to' => 'date',
        'min_nights' => 'integer',
        'max_nights' => 'integer',
        'min_days_before_checkin' => 'integer',
        'max_days_before_checkin' => 'integer',
        'is_active' => 'boolean',
    ];

    public function discountType(): BelongsTo
    {
        return $this->belongsTo(DiscountType::class);
    }

    public function ratePlanType(): BelongsTo
    {
        return $this->belongsTo(RatePlanType::class);
    }

    public function roomType(): BelongsTo
    {
        return $this->belongsTo(RoomType::class);
    }

    public function isValidForDateRange(Carbon $checkIn, Carbon $checkOut, int $nights): bool
    {
        if (! $this->is_active) {
            return false;
        }

        if ($nights < $this->min_nights) {
            return false;
        }

        if ($this->max_nights !== null && $nights > $this->max_nights) {
            return false;
        }

        if ($this->valid_from !== null && $checkIn->lt($this->valid_from)) {
            return false;
        }

        if ($this->valid_to !== null && $checkIn->gt($this->valid_to)) {
            return false;
        }

        $daysBeforeCheckin = Carbon::today()->diffInDays($checkIn, false);

        if ($this->min_days_before_checkin !== null && $daysBeforeCheckin < $this->min_days_before_checkin) {
            return false;
        }

        if ($this->max_days_before_checkin !== null && $daysBeforeCheckin > $this->max_days_before_checkin) {
            return false;
        }

        return true;
    }
}
