<?php

namespace App\Domain\DTOs;

class VehicleDTO
{
    public function __construct(
        public readonly ?int $id = null,
        public readonly string $plate = '',
        public readonly string $ownerName = '',
        public readonly string $phone = '',
        public readonly string $vehicleType = 'car'
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'] ?? null,
            plate: $data['plate'] ?? '',
            ownerName: $data['owner_name'] ?? $data['ownerName'] ?? '',
            phone: $data['phone'] ?? '',
            vehicleType: $data['vehicle_type'] ?? $data['vehicleType'] ?? 'car'
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'plate' => $this->plate,
            'owner_name' => $this->ownerName,
            'phone' => $this->phone,
            'vehicle_type' => $this->vehicleType,
        ];
    }
}
