<?php

namespace App\Domain\DTOs;

class ExitVehicleDTO
{
    public function __construct(
        public readonly ?int $ticketId = null,
        public readonly ?string $plate = null,
        public readonly ?string $exitTime = null
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            ticketId: $data['ticket_id'] ?? $data['ticketId'] ?? null,
            plate: $data['plate'] ?? null,
            exitTime: $data['exit_time'] ?? $data['exitTime'] ?? null
        );
    }

    public function toArray(): array
    {
        return [
            'ticket_id' => $this->ticketId,
            'plate' => $this->plate,
            'exit_time' => $this->exitTime,
        ];
    }
}
