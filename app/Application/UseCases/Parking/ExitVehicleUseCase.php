<?php

namespace App\Application\UseCases\Parking;

use App\Domain\DTOs\ExitVehicleDTO;
use App\Domain\Entities\ParkingTicket;
use App\Domain\Repositories\ParkingLotRepositoryInterface;
use App\Domain\Repositories\ParkingSpotRepositoryInterface;
use App\Domain\Repositories\ParkingTicketRepositoryInterface;
use App\Domain\Repositories\VehicleRepositoryInterface;
use App\Domain\Services\PricingService;
use Illuminate\Support\Facades\DB;

class ExitVehicleUseCase
{
    public function __construct(
        private ParkingTicketRepositoryInterface $parkingTicketRepository,
        private ParkingLotRepositoryInterface $parkingLotRepository,
        private ParkingSpotRepositoryInterface $parkingSpotRepository,
        private VehicleRepositoryInterface $vehicleRepository,
        private PricingService $pricingService
    ) {
    }

    public function execute(ExitVehicleDTO $dto, int $guardId): ParkingTicket
    {
        return DB::transaction(function () use ($dto, $guardId) {
            // Obtener ticket
            $ticket = $this->getTicket($dto);
            if (!$ticket) {
                throw new \Exception('Ticket no encontrado');
            }

            if (!$ticket->isActive()) {
                throw new \Exception('El ticket ya tiene salida registrada');
            }

            // Obtener estacionamiento
            $parkingLot = $this->parkingLotRepository->findById($ticket->getParkingLotId());
            if (!$parkingLot) {
                throw new \Exception('Estacionamiento no encontrado');
            }

            // Registrar salida
            $exitTime = $dto->exitTime ?? date('Y-m-d H:i:s');
            $ticket->setExitTime($exitTime);

            // Calcular horas y precio
            $totalHours = $this->pricingService->calculateTotalHours($ticket);
            $totalAmount = $this->pricingService->calculatePrice($ticket, $parkingLot);

            // Actualizar ticket
            $this->parkingTicketRepository->update($ticket->getId(), [
                'exit_time' => $exitTime,
                'exit_guard_id' => $guardId,
                'total_hours' => $totalHours,
                'total_amount' => $totalAmount,
            ]);

            // Liberar espacio
            $parkingSpot = $this->parkingSpotRepository->findById($ticket->getParkingSpotId());
            if ($parkingSpot) {
                $this->parkingSpotRepository->update($parkingSpot->getId(), ['is_occupied' => false]);
            }

            // Recargar ticket actualizado
            return $this->parkingTicketRepository->findById($ticket->getId());
        });
    }

    private function getTicket(ExitVehicleDTO $dto): ?ParkingTicket
    {
        if ($dto->ticketId) {
            return $this->parkingTicketRepository->findById($dto->ticketId);
        }

        if ($dto->plate) {
            $vehicle = $this->vehicleRepository->findByPlate($dto->plate);
            if (!$vehicle) {
                return null;
            }
            return $this->parkingTicketRepository->findActiveByVehicle($vehicle->getId());
        }

        return null;
    }
}




