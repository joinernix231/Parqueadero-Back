<?php

namespace App\Http\Resources;

use App\Domain\Entities\Vehicle;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class VehicleResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        /** @var Vehicle $vehicle */
        $vehicle = $this->resource instanceof Vehicle ? $this->resource : null;

        if (! $vehicle) {
            return [];
        }

        return [
            'id' => $vehicle->getId(),
            'plate' => $vehicle->getPlate(),
            'owner_name' => $vehicle->getOwnerName(),
            'phone' => $vehicle->getPhone(),
            'vehicle_type' => $vehicle->getVehicleType(),
            'created_at' => $vehicle->getCreatedAt(),
        ];
    }
}
