<?php

namespace App\Application\Actions\Vehicle;

use App\Application\UseCases\Vehicle\RegisterVehicleUseCase;
use App\Domain\DTOs\VehicleDTO;
use App\Domain\Entities\Vehicle;

class CreateVehicleAction
{
    public function __construct(
        private RegisterVehicleUseCase $registerVehicleUseCase
    ) {
    }

    public function execute(VehicleDTO $dto): Vehicle
    {
        return $this->registerVehicleUseCase->execute($dto);
    }
}





