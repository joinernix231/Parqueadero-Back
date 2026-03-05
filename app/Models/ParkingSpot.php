<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ParkingSpot extends Model
{
    use HasFactory;

    protected $fillable = [
        'parking_lot_id',
        'spot_number',
        'spot_type',
        'is_occupied',
        'is_active',
    ];

    protected $casts = [
        'is_occupied' => 'boolean',
        'is_active' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Relación con ParkingLot
     */
    public function parkingLot(): BelongsTo
    {
        return $this->belongsTo(ParkingLot::class);
    }

    /**
     * Relación con ParkingTickets
     */
    public function parkingTickets(): HasMany
    {
        return $this->hasMany(ParkingTicket::class);
    }
}




