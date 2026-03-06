<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ParkingLot extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'address',
        'total_spots',
        'hourly_rate_day',
        'hourly_rate_night',
        'day_start_time',
        'day_end_time',
        'is_active',
    ];

    protected $casts = [
        'total_spots' => 'integer',
        'hourly_rate_day' => 'decimal:2',
        'hourly_rate_night' => 'decimal:2',
        'is_active' => 'boolean',
        'day_start_time' => 'datetime:H:i',
        'day_end_time' => 'datetime:H:i',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Relación con ParkingSpots
     */
    public function parkingSpots(): HasMany
    {
        return $this->hasMany(ParkingSpot::class);
    }

    /**
     * Relación con ParkingTickets
     */
    public function parkingTickets(): HasMany
    {
        return $this->hasMany(ParkingTicket::class);
    }
}





