<?php

namespace App\Domain\Entities;

class ParkingLot
{
    public function __construct(
        private ?int $id = null,
        private string $name = '',
        private string $address = '',
        private int $totalSpots = 0,
        private float $hourlyRateDay = 0.0,
        private float $hourlyRateNight = 0.0,
        private string $dayStartTime = '06:00',
        private string $dayEndTime = '20:00',
        private bool $isActive = true,
        private ?string $createdAt = null,
        private ?string $updatedAt = null
    ) {
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getAddress(): string
    {
        return $this->address;
    }

    public function getTotalSpots(): int
    {
        return $this->totalSpots;
    }

    public function getHourlyRateDay(): float
    {
        return $this->hourlyRateDay;
    }

    public function getHourlyRateNight(): float
    {
        return $this->hourlyRateNight;
    }

    public function getDayStartTime(): string
    {
        return $this->dayStartTime;
    }

    public function getDayEndTime(): string
    {
        return $this->dayEndTime;
    }

    public function isActive(): bool
    {
        return $this->isActive;
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
     * Calcula la cantidad de espacios disponibles
     * Requiere que se pase la cantidad de espacios ocupados
     */
    public function getAvailableSpotsCount(int $occupiedSpots): int
    {
        return max(0, $this->totalSpots - $occupiedSpots);
    }

    /**
     * Verifica si un horario es diurno o nocturno
     */
    public function isDayTime(string $time): bool
    {
        $timeHour = (int) date('H', strtotime($time));
        $dayStartHour = (int) date('H', strtotime($this->dayStartTime));
        $dayEndHour = (int) date('H', strtotime($this->dayEndTime));

        if ($dayStartHour < $dayEndHour) {
            return $timeHour >= $dayStartHour && $timeHour < $dayEndHour;
        }

        // Si cruza medianoche
        return $timeHour >= $dayStartHour || $timeHour < $dayEndHour;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function setAddress(string $address): void
    {
        $this->address = $address;
    }

    public function setTotalSpots(int $totalSpots): void
    {
        if ($totalSpots < 0) {
            throw new \InvalidArgumentException("Total spots cannot be negative");
        }
        $this->totalSpots = $totalSpots;
    }

    public function setHourlyRateDay(float $hourlyRateDay): void
    {
        if ($hourlyRateDay < 0) {
            throw new \InvalidArgumentException("Hourly rate cannot be negative");
        }
        $this->hourlyRateDay = $hourlyRateDay;
    }

    public function setHourlyRateNight(float $hourlyRateNight): void
    {
        if ($hourlyRateNight < 0) {
            throw new \InvalidArgumentException("Hourly rate cannot be negative");
        }
        $this->hourlyRateNight = $hourlyRateNight;
    }

    public function setDayStartTime(string $dayStartTime): void
    {
        $this->dayStartTime = $dayStartTime;
    }

    public function setDayEndTime(string $dayEndTime): void
    {
        $this->dayEndTime = $dayEndTime;
    }

    public function setIsActive(bool $isActive): void
    {
        $this->isActive = $isActive;
    }
}





