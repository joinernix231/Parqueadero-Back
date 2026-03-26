<?php

namespace App\Http\Resources;

use App\Domain\Entities\ParkingTicket;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ParkingTicketResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        /** @var ParkingTicket $ticket */
        $ticket = $this->resource instanceof ParkingTicket ? $this->resource : null;

        if (! $ticket) {
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

        $vehicle = $ticket->getVehicle();
        if ($vehicle) {
            $data['vehicle'] = [
                'id' => $vehicle->getId(),
                'plate' => $vehicle->getPlate(),
                'owner_name' => $vehicle->getOwnerName(),
                'vehicle_type' => $vehicle->getVehicleType(),
            ];
        }

        $parkingLot = $ticket->getParkingLot();
        if ($parkingLot) {
            $data['parking_lot'] = [
                'id' => $parkingLot->getId(),
                'name' => $parkingLot->getName(),
            ];
        }

        $parkingSpot = $ticket->getParkingSpot();
        if ($parkingSpot) {
            $data['parking_spot'] = [
                'id' => $parkingSpot->getId(),
                'spot_number' => $parkingSpot->getSpotNumber(),
                'spot_type' => $parkingSpot->getSpotType(),
            ];
        }

        return $data;
    }
}
