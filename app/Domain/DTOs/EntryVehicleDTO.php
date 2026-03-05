<?php

namespace App\Domain\DTOs;

class EntryVehicleDTO
{
    public function __construct(
        public readonly ?int $vehicleId = null,
        public readonly ?string $plate = null,
        public readonly ?VehicleDTO $vehicleData = null,
        public readonly int $parkingLotId = 0,
        public readonly int $parkingSpotId = 0,
        public readonly ?string $entryTime = null
    ) {
    }

    public static function fromArray(array $data): self
    {
        $vehicleData = null;
        if (isset($data['vehicle_data']) && is_array($data['vehicle_data'])) {
            $vehicleDataArray = $data['vehicle_data'];
            // Asegurar que la placa esté incluida en vehicle_data
            if (!isset($vehicleDataArray['plate']) && isset($data['plate'])) {
                $vehicleDataArray['plate'] = $data['plate'];
            }
            $vehicleData = VehicleDTO::fromArray($vehicleDataArray);
        }

        return new self(
            vehicleId: $data['vehicle_id'] ?? $data['vehicleId'] ?? null,
            plate: $data['plate'] ?? null,
            vehicleData: $vehicleData,
            parkingLotId: $data['parking_lot_id'] ?? $data['parkingLotId'] ?? 0,
            parkingSpotId: $data['parking_spot_id'] ?? $data['parkingSpotId'] ?? 0,
            entryTime: $data['entry_time'] ?? $data['entryTime'] ?? null
        );
    }

    public function toArray(): array
    {
        return [
            'vehicle_id' => $this->vehicleId,
            'plate' => $this->plate,
            'parking_lot_id' => $this->parkingLotId,
            'parking_spot_id' => $this->parkingSpotId,
            'entry_time' => $this->entryTime,
        ];
    }
}

