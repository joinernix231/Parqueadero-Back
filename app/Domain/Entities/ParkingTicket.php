<?php

namespace App\Domain\Entities;

class ParkingTicket
{
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

    /**
     * Calcula las horas totales entre entry_time y exit_time
     */
    public function calculateTotalHours(): float
    {
        if ($this->exitTime === null) {
            // Si no hay salida, calcular hasta ahora
            $exitTime = date('Y-m-d H:i:s');
        } else {
            $exitTime = $this->exitTime;
        }

        $entryTimestamp = strtotime($this->entryTime);
        $exitTimestamp = strtotime($exitTime);

        if ($exitTimestamp < $entryTimestamp) {
            throw new \InvalidArgumentException("Exit time cannot be before entry time");
        }

        $hours = ($exitTimestamp - $entryTimestamp) / 3600;
        return round($hours, 2);
    }

    /**
     * Calcula el precio basado en las tarifas del estacionamiento
     * Requiere que se pase la entidad ParkingLot con las tarifas
     */
    public function calculatePrice(ParkingLot $lot): float
    {
        if ($this->exitTime === null) {
            throw new \RuntimeException("Cannot calculate price without exit time");
        }

        $entryTimestamp = strtotime($this->entryTime);
        $exitTimestamp = strtotime($this->exitTime);
        $totalSeconds = $exitTimestamp - $entryTimestamp;
        $totalHours = $totalSeconds / 3600;

        $price = 0.0;
        $currentTimestamp = $entryTimestamp;

        while ($currentTimestamp < $exitTimestamp) {
            $currentTime = date('H:i:s', $currentTimestamp);
            $isDayTime = $lot->isDayTime($currentTime);

            // Calcular horas hasta el próximo cambio o hasta la salida
            $nextChangeTimestamp = $this->getNextRateChangeTimestamp($currentTimestamp, $lot);
            $segmentEndTimestamp = min($nextChangeTimestamp, $exitTimestamp);
            $segmentHours = ($segmentEndTimestamp - $currentTimestamp) / 3600;

            $rate = $isDayTime ? $lot->getHourlyRateDay() : $lot->getHourlyRateNight();
            $price += $segmentHours * $rate;

            $currentTimestamp = $segmentEndTimestamp;
        }

        return round($price, 2);
    }

    /**
     * Obtiene el timestamp del próximo cambio de tarifa
     */
    private function getNextRateChangeTimestamp(int $currentTimestamp, ParkingLot $lot): int
    {
        $currentDate = date('Y-m-d', $currentTimestamp);
        $dayStartTimestamp = strtotime($currentDate . ' ' . $lot->getDayStartTime());
        $dayEndTimestamp = strtotime($currentDate . ' ' . $lot->getDayEndTime());

        // Si dayEnd es antes de dayStart, significa que cruza medianoche
        if ($dayEndTimestamp < $dayStartTimestamp) {
            $dayEndTimestamp = strtotime($currentDate . ' ' . $lot->getDayEndTime() . ' +1 day');
        }

        $isCurrentlyDayTime = $lot->isDayTime(date('H:i:s', $currentTimestamp));

        if ($isCurrentlyDayTime) {
            return $dayEndTimestamp;
        }

        // Si es noche, el próximo cambio es al inicio del día siguiente
        $nextDayStart = strtotime($currentDate . ' ' . $lot->getDayStartTime() . ' +1 day');
        return $nextDayStart;
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




