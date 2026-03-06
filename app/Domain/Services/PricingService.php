<?php

namespace App\Domain\Services;

use App\Domain\Entities\ParkingLot;
use App\Domain\Entities\ParkingTicket;

class PricingService
{
    /**
     * Calcula el precio de un ticket basado en las tarifas del estacionamiento
     */
    public function calculatePrice(ParkingTicket $ticket, ParkingLot $lot): float
    {
        return $ticket->calculatePrice($lot);
    }

    /**
     * Calcula las horas totales entre entry_time y exit_time
     */
    public function calculateTotalHours(ParkingTicket $ticket): float
    {
        return $ticket->calculateTotalHours();
    }
}





