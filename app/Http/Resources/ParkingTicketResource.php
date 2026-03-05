<?php

namespace App\Http\Resources;

use App\Domain\Entities\ParkingTicket;
use App\Domain\Repositories\VehicleRepositoryInterface;
use App\Domain\Repositories\ParkingLotRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Log;

class ParkingTicketResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        /** @var ParkingTicket $ticket */
        $ticket = $this->resource instanceof ParkingTicket ? $this->resource : null;

        if (!$ticket) {
            return [];
        }

        $data = [
            'id' => $ticket->getId(),
            'vehicle_id' => $ticket->getVehicleId(),
            'parking_spot_id' => $ticket->getParkingSpotId(),
            'parking_lot_id' => $ticket->getParkingLotId(),
            'entry_time' => $ticket->getEntryTime(),
            'exit_time' => $ticket->getExitTime(),
            'entry_guard_id' => $ticket->getEntryGuardId(),
            'exit_guard_id' => $ticket->getExitGuardId(),
            'total_hours' => $ticket->getTotalHours(),
            'hourly_rate_applied' => $ticket->getHourlyRateApplied(),
            'total_amount' => $ticket->getTotalAmount(),
            'is_paid' => $ticket->isPaid(),
            'payment_method' => $ticket->getPaymentMethod(),
            'payment_time' => $ticket->getPaymentTime(),
            'is_active' => $ticket->isActive(),
            'created_at' => $ticket->getCreatedAt(),
        ];

        // Incluir información del vehículo
        try {
            $vehicleRepository = app(VehicleRepositoryInterface::class);
            $vehicle = $vehicleRepository->findById($ticket->getVehicleId());
            if ($vehicle) {
                $data['vehicle'] = [
                    'id' => $vehicle->getId(),
                    'plate' => $vehicle->getPlate(),
                    'owner_name' => $vehicle->getOwnerName(),
                    'vehicle_type' => $vehicle->getVehicleType(),
                ];
            }
        } catch (\Exception $e) {
            // Log del error pero continuar sin el vehículo
            Log::warning('Error obteniendo vehículo en ParkingTicketResource: ' . $e->getMessage());
        }

        // Incluir información del estacionamiento
        try {
            $parkingLotRepository = app(ParkingLotRepositoryInterface::class);
            $parkingLot = $parkingLotRepository->findById($ticket->getParkingLotId());
            if ($parkingLot) {
                $data['parking_lot'] = [
                    'id' => $parkingLot->getId(),
                    'name' => $parkingLot->getName(),
                ];
            }
        } catch (\Exception $e) {
            // Log del error pero continuar sin el estacionamiento
            Log::warning('Error obteniendo estacionamiento en ParkingTicketResource: ' . $e->getMessage());
        }

        return $data;
    }
}

