<?php

namespace App\Domain\Entities;

class Vehicle
{
    public function __construct(
        private ?int $id = null,
        private string $plate = '',
        private string $ownerName = '',
        private string $phone = '',
        private string $vehicleType = 'car',
        private ?string $createdAt = null,
        private ?string $updatedAt = null
    ) {}

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPlate(): string
    {
        return $this->plate;
    }

    public function getOwnerName(): string
    {
        return $this->ownerName;
    }

    public function getPhone(): string
    {
        return $this->phone;
    }

    public function getVehicleType(): string
    {
        return $this->vehicleType;
    }

    public function getCreatedAt(): ?string
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): ?string
    {
        return $this->updatedAt;
    }

    /**
     * Normaliza el formato de la placa a mayúsculas sin espacios
     */
    public function normalizePlate(): string
    {
        return strtoupper(str_replace(' ', '', trim($this->plate)));
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function setPlate(string $plate): void
    {
        $this->plate = $plate;
    }

    public function setOwnerName(string $ownerName): void
    {
        $this->ownerName = $ownerName;
    }

    public function setPhone(string $phone): void
    {
        $this->phone = $phone;
    }

    public function setVehicleType(string $vehicleType): void
    {
        if (! in_array($vehicleType, ['car', 'motorcycle', 'truck'])) {
            throw new \InvalidArgumentException("Invalid vehicle type: {$vehicleType}");
        }
        $this->vehicleType = $vehicleType;
    }
}
