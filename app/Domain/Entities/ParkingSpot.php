<?php

namespace App\Domain\Entities;

class ParkingSpot
{
    public function __construct(
        private ?int $id = null,
        private int $parkingLotId = 0,
        private string $spotNumber = '',
        private string $spotType = 'regular',
        private bool $isOccupied = false,
        private bool $isActive = true,
        private ?string $createdAt = null,
        private ?string $updatedAt = null
    ) {}

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getParkingLotId(): int
    {
        return $this->parkingLotId;
    }

    public function getSpotNumber(): string
    {
        return $this->spotNumber;
    }

    public function getSpotType(): string
    {
        return $this->spotType;
    }

    public function isOccupied(): bool
    {
        return $this->isOccupied;
    }

    public function isActive(): bool
    {
        return $this->isActive;
    }

    public function isAvailable(): bool
    {
        return $this->isActive && ! $this->isOccupied;
    }

    public function getCreatedAt(): ?string
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): ?string
    {
        return $this->updatedAt;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function setParkingLotId(int $parkingLotId): void
    {
        $this->parkingLotId = $parkingLotId;
    }

    public function setSpotNumber(string $spotNumber): void
    {
        $this->spotNumber = $spotNumber;
    }

    public function setSpotType(string $spotType): void
    {
        if (! in_array($spotType, ['regular', 'disabled', 'vip'])) {
            throw new \InvalidArgumentException("Invalid spot type: {$spotType}");
        }
        $this->spotType = $spotType;
    }

    public function setIsOccupied(bool $isOccupied): void
    {
        $this->isOccupied = $isOccupied;
    }

    public function setIsActive(bool $isActive): void
    {
        $this->isActive = $isActive;
    }

    public function occupy(): void
    {
        if ($this->isOccupied) {
            throw new \RuntimeException('Spot is already occupied');
        }
        if (! $this->isActive) {
            throw new \RuntimeException('Spot is not active');
        }
        $this->isOccupied = true;
    }

    public function release(): void
    {
        if (! $this->isOccupied) {
            throw new \RuntimeException('Spot is not occupied');
        }
        $this->isOccupied = false;
    }
}
