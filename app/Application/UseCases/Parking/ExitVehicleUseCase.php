<?php

namespace App\Application\UseCases\Parking;

use App\Domain\DTOs\ExitVehicleDTO;
use App\Domain\Entities\ParkingTicket;
use App\Domain\Repositories\ParkingLotRepositoryInterface;
use App\Domain\Repositories\ParkingSpotRepositoryInterface;
use App\Domain\Repositories\ParkingTicketRepositoryInterface;
use App\Domain\Repositories\VehicleRepositoryInterface;
use App\Domain\Services\DateTimeService;
use App\Domain\Services\PricingService;
use Illuminate\Support\Facades\DB;

class ExitVehicleUseCase
{
    public function __construct(
        private ParkingTicketRepositoryInterface $parkingTicketRepository,
        private ParkingLotRepositoryInterface $parkingLotRepository,
        private ParkingSpotRepositoryInterface $parkingSpotRepository,
        private VehicleRepositoryInterface $vehicleRepository,
        private PricingService $pricingService,
        private DateTimeService $dateTimeService
    ) {}

    public function execute(ExitVehicleDTO $dto, int $guardId): ParkingTicket
    {
        return DB::transaction(function () use ($dto, $guardId) {
            $ticket = $this->getTicket($dto);
            if (! $ticket) {
                throw new \Exception('Ticket not found');
            }

            if (! $ticket->isActive()) {
                throw new \Exception('El ticket ya tiene salida registrada');
            }
            $parkingLot = $this->parkingLotRepository->findById($ticket->getParkingLotId());
            if (! $parkingLot) {
                throw new \Exception('Parking lot not found');
            }

            // Normalizar exit_time de UTC a zona horaria local si viene del frontend
            $exitTime = $this->dateTimeService->normalizeFromUtc($dto->exitTime);
            $ticket->setExitTime($exitTime);

            // Calcular horas y precio
            $totalHours = $this->pricingService->calculateTotalHours($ticket);
            $totalAmount = $this->pricingService->calculatePrice($ticket, $parkingLot);
            $exitHour = $this->dateTimeService->createCarbonFromLocal($exitTime)->format('H:i');
            $hourlyRateApplied = $parkingLot->isDayTime($exitHour)
                ? $parkingLot->getHourlyRateDay()
                : $parkingLot->getHourlyRateNight();

            // Actualizar ticket
            $this->parkingTicketRepository->update($ticket->getId(), [
                'exit_time' => $exitTime,
                'exit_guard_id' => $guardId,
                'total_hours' => $totalHours,
                'hourly_rate_applied' => $hourlyRateApplied,
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
            if (! $vehicle) {
                return null;
            }

            return $this->parkingTicketRepository->findActiveByVehicle($vehicle->getId());
        }

        return null;
    }
}
