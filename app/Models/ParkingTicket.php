<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ParkingTicket extends Model
{
    use HasFactory;

    protected $fillable = [
        'vehicle_id',
        'parking_spot_id',
        'parking_lot_id',
        'entry_time',
        'exit_time',
        'entry_guard_id',
        'exit_guard_id',
        'total_hours',
        'hourly_rate_applied',
        'total_amount',
        'is_paid',
        'payment_method',
        'payment_time',
    ];

    protected $casts = [
        'entry_time' => 'datetime',
        'exit_time' => 'datetime',
        'payment_time' => 'datetime',
        'total_hours' => 'decimal:2',
        'hourly_rate_applied' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'is_paid' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Relación con Vehicle
     */
    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }

    /**
     * Relación con ParkingSpot
     */
    public function parkingSpot(): BelongsTo
    {
        return $this->belongsTo(ParkingSpot::class);
    }

    /**
     * Relación con ParkingLot
     */
    public function parkingLot(): BelongsTo
    {
        return $this->belongsTo(ParkingLot::class);
    }

    /**
     * Relación con User (entry guard)
     */
    public function entryGuard(): BelongsTo
    {
        return $this->belongsTo(User::class, 'entry_guard_id');
    }

    /**
     * Relación con User (exit guard)
     */
    public function exitGuard(): BelongsTo
    {
        return $this->belongsTo(User::class, 'exit_guard_id');
    }
}
