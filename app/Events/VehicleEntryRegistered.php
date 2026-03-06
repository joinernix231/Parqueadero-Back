<?php

namespace App\Events;

use App\Domain\Entities\ParkingTicket;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class VehicleEntryRegistered
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public ParkingTicket $ticket
    ) {
    }
}


