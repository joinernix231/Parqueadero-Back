<?php

namespace App\Application\UseCases\Parking;

use App\Domain\Entities\ParkingTicket;
use App\Domain\Repositories\ParkingTicketRepositoryInterface;

class GetCurrentParkedVehiclesUseCase
{
    public function __construct(
        private ParkingTicketRepositoryInterface $parkingTicketRepository
    ) {
    }

    public function execute(?int $parkingLotId = null): array
    {
        return $this->parkingTicketRepository->findCurrentParkedVehicles($parkingLotId);
    }
}




