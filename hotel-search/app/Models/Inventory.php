<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Inventory extends Model
{
    protected $fillable = [
        'room_type_id',
        'date',
        'total_rooms',
        'booked_rooms',
        'blocked_rooms',
        'is_closed',
    ];

    protected $casts = [
        'date' => 'date',
        'total_rooms' => 'integer',
        'booked_rooms' => 'integer',
        'blocked_rooms' => 'integer',
        'is_closed' => 'boolean',
    ];

    public function roomType(): BelongsTo
    {
        return $this->belongsTo(RoomType::class);
    }

    public function getAvailableRoomsAttribute(): int
    {
        return $this->total_rooms - $this->booked_rooms - $this->blocked_rooms;
    }

    public function getIsSoldOutAttribute(): bool
    {
        return $this->available_rooms <= 0 || $this->is_closed;
    }
}
