<?php

namespace App\Domain\Services;

use App\Domain\Entities\ParkingSpot;

class ParkingAvailabilityService
{
    /**
     * Verifica si un espacio está disponible
     */
    public function isSpotAvailable(ParkingSpot $spot): bool
    {
        return $spot->isAvailable();
    }

    /**
     * Verifica si un espacio puede ser ocupado
     */
    public function canOccupySpot(ParkingSpot $spot): bool
    {
        return $spot->isActive() && !$spot->isOccupied();
    }
}





