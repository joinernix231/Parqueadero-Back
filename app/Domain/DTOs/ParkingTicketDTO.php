<?php

namespace App\Domain\DTOs;

class ParkingTicketDTO
{
    public function __construct(
        public readonly ?int $id = null,
        public readonly int $vehicleId = 0,
        public readonly int $parkingSpotId = 0,
        public readonly int $parkingLotId = 0,
        public readonly string $entryTime = '',
        public readonly ?string $exitTime = null,
        public readonly int $entryGuardId = 0,
        public readonly ?int $exitGuardId = null,
        public readonly float $totalHours = 0.0,
        public readonly float $hourlyRateApplied = 0.0,
        public readonly float $totalAmount = 0.0,
        public readonly bool $isPaid = false,
        public readonly ?string $paymentMethod = null,
        public readonly ?string $paymentTime = null
    ) {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'] ?? null,
            vehicleId: $data['vehicle_id'] ?? $data['vehicleId'] ?? 0,
            parkingSpotId: $data['parking_spot_id'] ?? $data['parkingSpotId'] ?? 0,
            parkingLotId: $data['parking_lot_id'] ?? $data['parkingLotId'] ?? 0,
            entryTime: $data['entry_time'] ?? $data['entryTime'] ?? '',
            exitTime: $data['exit_time'] ?? $data['exitTime'] ?? null,
            entryGuardId: $data['entry_guard_id'] ?? $data['entryGuardId'] ?? 0,
            exitGuardId: $data['exit_guard_id'] ?? $data['exitGuardId'] ?? null,
            totalHours: $data['total_hours'] ?? $data['totalHours'] ?? 0.0,
            hourlyRateApplied: $data['hourly_rate_applied'] ?? $data['hourlyRateApplied'] ?? 0.0,
            totalAmount: $data['total_amount'] ?? $data['totalAmount'] ?? 0.0,
            isPaid: $data['is_paid'] ?? $data['isPaid'] ?? false,
            paymentMethod: $data['payment_method'] ?? $data['paymentMethod'] ?? null,
            paymentTime: $data['payment_time'] ?? $data['paymentTime'] ?? null
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'vehicle_id' => $this->vehicleId,
            'parking_spot_id' => $this->parkingSpotId,
            'parking_lot_id' => $this->parkingLotId,
            'entry_time' => $this->entryTime,
            'exit_time' => $this->exitTime,
            'entry_guard_id' => $this->entryGuardId,
            'exit_guard_id' => $this->exitGuardId,
            'total_hours' => $this->totalHours,
            'hourly_rate_applied' => $this->hourlyRateApplied,
            'total_amount' => $this->totalAmount,
            'is_paid' => $this->isPaid,
            'payment_method' => $this->paymentMethod,
            'payment_time' => $this->paymentTime,
        ];
    }
}




