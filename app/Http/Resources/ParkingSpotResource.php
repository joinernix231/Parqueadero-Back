<?php

namespace App\Http\Resources;

use App\Domain\Entities\ParkingSpot;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ParkingSpotResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        /** @var ParkingSpot $spot */
        $spot = $this->resource instanceof ParkingSpot ? $this->resource : null;

        if (!$spot) {
            return [];
        }

        return [
            'id' => $spot->getId(),
            'parking_lot_id' => $spot->getParkingLotId(),
            'spot_number' => $spot->getSpotNumber(),
            'spot_type' => $spot->getSpotType(),
            'is_occupied' => $spot->isOccupied(),
            'is_active' => $spot->isActive(),
            'is_available' => $spot->isAvailable(),
            'created_at' => $spot->getCreatedAt(),
        ];
    }
}




