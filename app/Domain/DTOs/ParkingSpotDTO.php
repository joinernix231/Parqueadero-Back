<?php

namespace App\Domain\DTOs;

class ParkingSpotDTO
{
    public function __construct(
        public readonly ?int $id = null,
        public readonly int $parkingLotId = 0,
        public readonly string $spotNumber = '',
        public readonly string $spotType = 'regular',
        public readonly bool $isOccupied = false,
        public readonly bool $isActive = true
    ) {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'] ?? null,
            parkingLotId: $data['parking_lot_id'] ?? $data['parkingLotId'] ?? 0,
            spotNumber: $data['spot_number'] ?? $data['spotNumber'] ?? '',
            spotType: $data['spot_type'] ?? $data['spotType'] ?? 'regular',
            isOccupied: $data['is_occupied'] ?? $data['isOccupied'] ?? false,
            isActive: $data['is_active'] ?? $data['isActive'] ?? true
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'parking_lot_id' => $this->parkingLotId,
            'spot_number' => $this->spotNumber,
            'spot_type' => $this->spotType,
            'is_occupied' => $this->isOccupied,
            'is_active' => $this->isActive,
        ];
    }
}





