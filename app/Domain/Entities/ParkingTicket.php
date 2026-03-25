<?php

namespace App\Domain\Entities;

class ParkingTicket
{
    private ?Vehicle $vehicle = null;
    private ?ParkingLot $parkingLot = null;
    private ?ParkingSpot $parkingSpot = null;

    public function __construct(
        private ?int $id = null,
        private int $vehicleId = 0,
        private int $parkingSpotId = 0,
        private int $parkingLotId = 0,
        private string $entryTime = '',
        private ?string $exitTime = null,
        private int $entryGuardId = 0,
        private ?int $exitGuardId = null,
        private float $totalHours = 0.0,
        private float $hourlyRateApplied = 0.0,
        private float $totalAmount = 0.0,
        private bool $isPaid = false,
        private ?string $paymentMethod = null,
        private ?string $paymentTime = null,
        private ?string $createdAt = null,
        private ?string $updatedAt = null
    ) {
    }

    public function getVehicle(): ?Vehicle
    {
        return $this->vehicle;
    }

    public function getParkingLot(): ?ParkingLot
    {
        return $this->parkingLot;
    }

    public function getParkingSpot(): ?ParkingSpot
    {
        return $this->parkingSpot;
    }

    public function setVehicle(?Vehicle $vehicle): void
    {
        $this->vehicle = $vehicle;
    }

    public function setParkingLot(?ParkingLot $parkingLot): void
    {
        $this->parkingLot = $parkingLot;
    }

    public function setParkingSpot(?ParkingSpot $parkingSpot): void
    {
        $this->parkingSpot = $parkingSpot;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getVehicleId(): int
    {
        return $this->vehicleId;
    }

    public function getParkingSpotId(): int
    {
        return $this->parkingSpotId;
    }

    public function getParkingLotId(): int
    {
        return $this->parkingLotId;
    }

    public function getEntryTime(): string
    {
        return $this->entryTime;
    }

    public function getExitTime(): ?string
    {
        return $this->exitTime;
    }

    public function getEntryGuardId(): int
    {
        return $this->entryGuardId;
    }

    public function getExitGuardId(): ?int
    {
        return $this->exitGuardId;
    }

    public function getTotalHours(): float
    {
        return $this->totalHours;
    }

    public function getHourlyRateApplied(): float
    {
        return $this->hourlyRateApplied;
    }

    public function getTotalAmount(): float
    {
        return $this->totalAmount;
    }

    public function isPaid(): bool
    {
        return $this->isPaid;
    }

    public function getPaymentMethod(): ?string
    {
        return $this->paymentMethod;
    }

    public function getPaymentTime(): ?string
    {
        return $this->paymentTime;
    }

    public function getCreatedAt(): ?string
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): ?string
    {
        return $this->updatedAt;
    }

    public function isActive(): bool
    {
        return $this->exitTime === null;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function setVehicleId(int $vehicleId): void
    {
        $this->vehicleId = $vehicleId;
    }

    public function setParkingSpotId(int $parkingSpotId): void
    {
        $this->parkingSpotId = $parkingSpotId;
    }

    public function setParkingLotId(int $parkingLotId): void
    {
        $this->parkingLotId = $parkingLotId;
    }

    public function setEntryTime(string $entryTime): void
    {
        $this->entryTime = $entryTime;
    }

    public function setExitTime(?string $exitTime): void
    {
        $this->exitTime = $exitTime;
    }

    public function setEntryGuardId(int $entryGuardId): void
    {
        $this->entryGuardId = $entryGuardId;
    }

    public function setExitGuardId(?int $exitGuardId): void
    {
        $this->exitGuardId = $exitGuardId;
    }

    public function setTotalHours(float $totalHours): void
    {
        $this->totalHours = $totalHours;
    }

    public function setHourlyRateApplied(float $hourlyRateApplied): void
    {
        $this->hourlyRateApplied = $hourlyRateApplied;
    }

    public function setTotalAmount(float $totalAmount): void
    {
        $this->totalAmount = $totalAmount;
    }

    public function setIsPaid(bool $isPaid): void
    {
        $this->isPaid = $isPaid;
    }

    public function setPaymentMethod(?string $paymentMethod): void
    {
        if ($paymentMethod !== null && !in_array($paymentMethod, ['cash', 'card', 'transfer'])) {
            throw new \InvalidArgumentException("Invalid payment method: {$paymentMethod}");
        }
        $this->paymentMethod = $paymentMethod;
    }

    public function setPaymentTime(?string $paymentTime): void
    {
        $this->paymentTime = $paymentTime;
    }
}
