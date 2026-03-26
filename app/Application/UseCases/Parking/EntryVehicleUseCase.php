<?php

namespace App\Application\UseCases\Parking;

use App\Application\UseCases\Vehicle\RegisterVehicleUseCase;
use App\Domain\DTOs\EntryVehicleDTO;
use App\Domain\Entities\ParkingTicket;
use App\Domain\Entities\Vehicle;
use App\Domain\Repositories\ParkingLotRepositoryInterface;
use App\Domain\Repositories\ParkingSpotRepositoryInterface;
use App\Domain\Repositories\ParkingTicketRepositoryInterface;
use App\Domain\Repositories\VehicleRepositoryInterface;
use App\Domain\Services\DateTimeService;
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
        private RegisterVehicleUseCase $registerVehicleUseCase,
        private DateTimeService $dateTimeService
    ) {}

    public function execute(EntryVehicleDTO $dto, int $guardId): ParkingTicket
    {
        return DB::transaction(function () use ($dto, $guardId) {
            // Obtener o crear vehículo
            $vehicle = $this->getOrCreateVehicle($dto);

            // Verificar que el vehículo no tenga un ticket activo
            $activeTicket = $this->parkingTicketRepository->findActiveByVehicle($vehicle->getId());
            if ($activeTicket) {
                throw new \Exception('This vehicle already has an active ticket');
            }

            // Obtener estacionamiento
            $parkingLot = $this->parkingLotRepository->findById($dto->parkingLotId);
            if (! $parkingLot) {
                throw new \Exception('Parking lot not found');
            }

            if (! $parkingLot->isActive()) {
                throw new \Exception('The parking lot is not active');
            }

            // Obtener espacio
            $parkingSpot = $this->parkingSpotRepository->findById($dto->parkingSpotId);
            if (! $parkingSpot) {
                throw new \Exception('Parking spot not found');
            }

            if (! $parkingSpot->isAvailable()) {
                throw new \Exception('The parking spot is not available');
            }

            // Verificar que el espacio pertenezca al estacionamiento
            if ($parkingSpot->getParkingLotId() !== $parkingLot->getId()) {
                throw new \Exception('The parking spot does not belong to this parking lot');
            }

            // Ocupar espacio
            $this->parkingSpotRepository->update($parkingSpot->getId(), ['is_occupied' => true]);

            // Crear ticket
            // Normalizar entry_time de UTC a zona horaria local si viene del frontend
            $entryTime = $this->dateTimeService->normalizeFromUtc($dto->entryTime);
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
            if (! $vehicle) {
                throw new \Exception('Vehicle not found');
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
                    throw new \Exception('Complete vehicle data is required (license plate, owner, and phone)');
                }

                return $this->registerVehicleUseCase->execute($vehicleDto);
            }

            throw new \Exception('Vehicle not found. Provide vehicle data to register a new vehicle.');
        }

        throw new \Exception('vehicle_id or plate (inside vehicle_data) is required');
    }
}
