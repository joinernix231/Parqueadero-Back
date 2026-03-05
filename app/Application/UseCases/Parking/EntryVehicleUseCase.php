<?php

namespace App\Application\UseCases\Parking;

use App\Application\Actions\Vehicle\CreateVehicleAction;
use App\Domain\DTOs\EntryVehicleDTO;
use App\Domain\Entities\ParkingTicket;
use App\Domain\Entities\Vehicle;
use App\Domain\Repositories\ParkingLotRepositoryInterface;
use App\Domain\Repositories\ParkingSpotRepositoryInterface;
use App\Domain\Repositories\ParkingTicketRepositoryInterface;
use App\Domain\Repositories\VehicleRepositoryInterface;
use App\Domain\Services\ParkingAvailabilityService;
use App\Domain\Services\VehicleValidationService;
use Illuminate\Support\Facades\DB;

class EntryVehicleUseCase
{
    public function __construct(
        private VehicleRepositoryInterface $vehicleRepository,
        private ParkingLotRepositoryInterface $parkingLotRepository,
        private ParkingSpotRepositoryInterface $parkingSpotRepository,
        private ParkingTicketRepositoryInterface $parkingTicketRepository,
        private VehicleValidationService $vehicleValidationService,
        private ParkingAvailabilityService $availabilityService,
        private CreateVehicleAction $createVehicleAction
    ) {
    }

    public function execute(EntryVehicleDTO $dto, int $guardId): ParkingTicket
    {
        return DB::transaction(function () use ($dto, $guardId) {
            // Obtener o crear vehículo
            $vehicle = $this->getOrCreateVehicle($dto);

            // Verificar que el vehículo no tenga un ticket activo
            $activeTicket = $this->parkingTicketRepository->findActiveByVehicle($vehicle->getId());
            if ($activeTicket) {
                throw new \Exception('El vehículo ya tiene un ticket activo');
            }

            // Obtener estacionamiento
            $parkingLot = $this->parkingLotRepository->findById($dto->parkingLotId);
            if (!$parkingLot) {
                throw new \Exception('Estacionamiento no encontrado');
            }

            if (!$parkingLot->isActive()) {
                throw new \Exception('El estacionamiento no está activo');
            }

            // Obtener espacio
            $parkingSpot = $this->parkingSpotRepository->findById($dto->parkingSpotId);
            if (!$parkingSpot) {
                throw new \Exception('Espacio no encontrado');
            }

            if (!$this->availabilityService->canOccupySpot($parkingSpot)) {
                throw new \Exception('El espacio no está disponible');
            }

            // Verificar que el espacio pertenezca al estacionamiento
            if ($parkingSpot->getParkingLotId() !== $parkingLot->getId()) {
                throw new \Exception('El espacio no pertenece al estacionamiento');
            }

            // Ocupar espacio
            $this->parkingSpotRepository->update($parkingSpot->getId(), ['is_occupied' => true]);

            // Crear ticket
            $entryTime = $dto->entryTime ?? date('Y-m-d H:i:s');
            $ticketData = [
                'vehicle_id' => $vehicle->getId(),
                'parking_spot_id' => $parkingSpot->getId(),
                'parking_lot_id' => $parkingLot->getId(),
                'entry_time' => $entryTime,
                'entry_guard_id' => $guardId,
            ];

            $ticket = $this->parkingTicketRepository->create($ticketData);

            return $ticket;
        });
    }

    private function getOrCreateVehicle(EntryVehicleDTO $dto): Vehicle
    {
        if ($dto->vehicleId) {
            $vehicle = $this->vehicleRepository->findById($dto->vehicleId);
            if (!$vehicle) {
                throw new \Exception('Vehículo no encontrado');
            }
            return $vehicle;
        }

        // Obtener la placa del nivel superior o de vehicleData
        $plate = $dto->plate ?? $dto->vehicleData?->plate;

        if ($plate) {
            $vehicle = $this->vehicleRepository->findByPlate($plate);
            if ($vehicle) {
                return $vehicle;
            }

            // Crear vehículo si no existe y se proporcionaron los datos
            if ($dto->vehicleData) {
                $vehicleDto = $dto->vehicleData;
                
                // Asegurar que la placa esté en el VehicleDTO
                // Si no tiene placa, crear uno nuevo con la placa obtenida
                if (empty($vehicleDto->plate)) {
                    $vehicleDto = new \App\Domain\DTOs\VehicleDTO(
                        plate: $plate,
                        ownerName: $vehicleDto->ownerName,
                        phone: $vehicleDto->phone,
                        vehicleType: $vehicleDto->vehicleType
                    );
                }
                
                // Validar datos requeridos
                if (empty($vehicleDto->ownerName) || empty($vehicleDto->phone) || empty($vehicleDto->plate)) {
                    throw new \Exception('Se requieren los datos completos del vehículo (placa, propietario y teléfono)');
                }
                
                return $this->createVehicleAction->execute($vehicleDto);
            }

            throw new \Exception('Vehículo no encontrado. Se requieren los datos del vehículo para registrarlo.');
        }

        throw new \Exception('Se requiere vehicle_id o plate (dentro de vehicle_data)');
    }
}

