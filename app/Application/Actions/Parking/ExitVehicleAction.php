<?php

namespace App\Application\Actions\Parking;

use App\Application\UseCases\Parking\ExitVehicleUseCase;
use App\Domain\DTOs\ExitVehicleDTO;
use App\Domain\Entities\ParkingTicket;

class ExitVehicleAction
{
    public function __construct(
        private ExitVehicleUseCase $exitVehicleUseCase
    ) {
    }

    public function execute(ExitVehicleDTO $dto, int $guardId): ParkingTicket
    {
        return $this->exitVehicleUseCase->execute($dto, $guardId);
    }
}





