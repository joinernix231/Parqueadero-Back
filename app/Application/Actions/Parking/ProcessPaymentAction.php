<?php

namespace App\Application\Actions\Parking;

use App\Application\UseCases\Parking\ProcessPaymentUseCase;
use App\Domain\DTOs\PaymentDTO;
use App\Domain\Entities\ParkingTicket;

class ProcessPaymentAction
{
    public function __construct(
        private ProcessPaymentUseCase $processPaymentUseCase
    ) {
    }

    public function execute(PaymentDTO $dto): ParkingTicket
    {
        return $this->processPaymentUseCase->execute($dto);
    }
}




