<?php

namespace App\Application\UseCases\Parking;

use App\Domain\DTOs\PaymentDTO;
use App\Domain\Entities\ParkingTicket;
use App\Domain\Repositories\ParkingTicketRepositoryInterface;
use Illuminate\Support\Facades\DB;

class ProcessPaymentUseCase
{
    public function __construct(
        private ParkingTicketRepositoryInterface $parkingTicketRepository
    ) {
    }

    public function execute(PaymentDTO $dto): ParkingTicket
    {
        return DB::transaction(function () use ($dto) {
            $ticket = $this->parkingTicketRepository->findById($dto->ticketId);
            if (!$ticket) {
                throw new \Exception('Ticket no encontrado');
            }

            if ($ticket->isPaid()) {
                throw new \Exception('El ticket ya está pagado');
            }

            if ($ticket->isActive()) {
                throw new \Exception('El vehículo aún no ha salido');
            }

            // Validar monto
            $tolerance = 0.01; // Tolerancia de 1 centavo
            if (abs($dto->amount - $ticket->getTotalAmount()) > $tolerance) {
                throw new \Exception('El monto no coincide con el total a pagar');
            }

            // Procesar pago
            $this->parkingTicketRepository->update($ticket->getId(), [
                'is_paid' => true,
                'payment_method' => $dto->paymentMethod,
                'payment_time' => now()->format('Y-m-d H:i:s'),
            ]);

            return $this->parkingTicketRepository->findById($ticket->getId());
        });
    }
}




