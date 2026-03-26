<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Property extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'description',
        'address',
        'city',
        'country',
        'star_rating',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'star_rating' => 'integer',
    ];

    public function roomTypes(): HasMany
    {
        return $this->hasMany(RoomType::class);
    }

    public function activeRoomTypes(): HasMany
    {
        return $this->hasMany(RoomType::class)->where('is_active', true);
    }
}
