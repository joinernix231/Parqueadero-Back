<?php

namespace App\Application\UseCases\Parking;

use App\Domain\DTOs\PaymentDTO;
use App\Domain\Entities\ParkingTicket;
use App\Domain\Repositories\ParkingTicketRepositoryInterface;
use App\Domain\Services\DateTimeService;
use Illuminate\Support\Facades\DB;

class ProcessPaymentUseCase
{
    public function __construct(
        private ParkingTicketRepositoryInterface $parkingTicketRepository,
        private DateTimeService $dateTimeService
    ) {}

    public function execute(PaymentDTO $dto): ParkingTicket
    {
        return DB::transaction(function () use ($dto) {
            $ticket = $this->parkingTicketRepository->findById($dto->ticketId);
            if (! $ticket) {
                throw new \RuntimeException('Ticket not found');
            }

            if ($ticket->isPaid()) {
                throw new \RuntimeException('This ticket is already paid');
            }

            if ($ticket->isActive()) {
                throw new \RuntimeException('The vehicle has not exited yet');
            }

            $tolerance = 0.01;
            if (abs($dto->amount - $ticket->getTotalAmount()) > $tolerance) {
                throw new \RuntimeException('The amount does not match the total due');
            }

            $this->parkingTicketRepository->update($ticket->getId(), [
                'is_paid' => true,
                'payment_method' => $dto->paymentMethod,
                'payment_time' => $this->dateTimeService->now(),
            ]);

            return $this->parkingTicketRepository->findById($ticket->getId());
        });
    }
}
