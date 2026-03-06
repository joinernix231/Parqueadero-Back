<?php

namespace App\Application\UseCases\Vehicle;

use App\Domain\Entities\Vehicle;
use App\Domain\Repositories\VehicleRepositoryInterface;
use App\Domain\Services\VehicleValidationService;

class FindVehicleByPlateUseCase
{
    public function __construct(
        private VehicleRepositoryInterface $vehicleRepository,
        private VehicleValidationService $validationService
    ) {
    }

    public function execute(string $plate): ?Vehicle
    {
        $normalizedPlate = $this->validationService->normalizePlate($plate);
        return $this->vehicleRepository->findByPlate($normalizedPlate);
    }
}





