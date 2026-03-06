<?php

namespace App\Domain\DTOs;

class ParkingLotDTO
{
    public function __construct(
        public readonly ?int $id = null,
        public readonly string $name = '',
        public readonly string $address = '',
        public readonly int $totalSpots = 0,
        public readonly float $hourlyRateDay = 0.0,
        public readonly float $hourlyRateNight = 0.0,
        public readonly string $dayStartTime = '06:00',
        public readonly string $dayEndTime = '20:00',
        public readonly bool $isActive = true
    ) {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'] ?? null,
            name: $data['name'] ?? '',
            address: $data['address'] ?? '',
            totalSpots: $data['total_spots'] ?? $data['totalSpots'] ?? 0,
            hourlyRateDay: $data['hourly_rate_day'] ?? $data['hourlyRateDay'] ?? 0.0,
            hourlyRateNight: $data['hourly_rate_night'] ?? $data['hourlyRateNight'] ?? 0.0,
            dayStartTime: $data['day_start_time'] ?? $data['dayStartTime'] ?? '06:00',
            dayEndTime: $data['day_end_time'] ?? $data['dayEndTime'] ?? '20:00',
            isActive: $data['is_active'] ?? $data['isActive'] ?? true
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'address' => $this->address,
            'total_spots' => $this->totalSpots,
            'hourly_rate_day' => $this->hourlyRateDay,
            'hourly_rate_night' => $this->hourlyRateNight,
            'day_start_time' => $this->dayStartTime,
            'day_end_time' => $this->dayEndTime,
            'is_active' => $this->isActive,
        ];
    }
}





