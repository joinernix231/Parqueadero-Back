<?php

namespace App\Application\UseCases\ParkingLot;

use App\Domain\Entities\ParkingSpot;
use App\Domain\Repositories\ParkingSpotRepositoryInterface;

class GetAvailableSpotsUseCase
{
    public function __construct(
        private ParkingSpotRepositoryInterface $parkingSpotRepository
    ) {
    }

    public function execute(int $parkingLotId): array
    {
        return $this->parkingSpotRepository->findAvailableByLot($parkingLotId);
    }
}





