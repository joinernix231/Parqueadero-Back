<?php

namespace App\Application\UseCases\Vehicle;

use App\Domain\DTOs\VehicleDTO;
use App\Domain\Entities\Vehicle;
use App\Domain\Repositories\VehicleRepositoryInterface;
use App\Domain\Services\VehicleValidationService;

class RegisterVehicleUseCase
{
    public function __construct(
        private VehicleRepositoryInterface $vehicleRepository,
        private VehicleValidationService $validationService
    ) {
    }

    public function execute(VehicleDTO $dto): Vehicle
    {
        // Validar formato de placa
        if (!$this->validationService->validatePlateFormat($dto->plate)) {
            throw new \InvalidArgumentException('Formato de placa inválido');
        }

        // Normalizar placa
        $normalizedPlate = $this->validationService->normalizePlate($dto->plate);

        // Verificar si la placa ya existe
        $existingVehicle = $this->vehicleRepository->findByPlate($normalizedPlate);
        if ($existingVehicle) {
            throw new \Exception('La placa ya está registrada');
        }

        // Crear vehículo
        $data = [
            'plate' => $normalizedPlate,
            'owner_name' => $dto->ownerName,
            'phone' => $dto->phone,
            'vehicle_type' => $dto->vehicleType,
        ];

        return $this->vehicleRepository->create($data);
    }
}





