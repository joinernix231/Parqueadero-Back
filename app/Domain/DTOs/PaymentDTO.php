<?php

namespace App\Domain\DTOs;

class PaymentDTO
{
    public function __construct(
        public readonly int $ticketId = 0,
        public readonly float $amount = 0.0,
        public readonly string $paymentMethod = 'cash'
    ) {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            ticketId: $data['ticket_id'] ?? $data['ticketId'] ?? 0,
            amount: $data['amount'] ?? 0.0,
            paymentMethod: $data['payment_method'] ?? $data['paymentMethod'] ?? 'cash'
        );
    }

    public function toArray(): array
    {
        return [
            'ticket_id' => $this->ticketId,
            'amount' => $this->amount,
            'payment_method' => $this->paymentMethod,
        ];
    }
}





