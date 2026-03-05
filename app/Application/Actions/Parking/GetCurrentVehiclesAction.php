<?php

namespace App\Application\Actions\Parking;

use App\Application\UseCases\Parking\GetCurrentParkedVehiclesUseCase;

class GetCurrentVehiclesAction
{
    public function __construct(
        private GetCurrentParkedVehiclesUseCase $getCurrentParkedVehiclesUseCase
    ) {
    }

    public function execute(?int $parkingLotId = null): array
    {
        return $this->getCurrentParkedVehiclesUseCase->execute($parkingLotId);
    }
}




