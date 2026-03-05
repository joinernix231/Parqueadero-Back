<?php

namespace App\Application\Actions\Parking;

use App\Application\UseCases\Parking\EntryVehicleUseCase;
use App\Domain\DTOs\EntryVehicleDTO;
use App\Domain\Entities\ParkingTicket;

class EntryVehicleAction
{
    public function __construct(
        private EntryVehicleUseCase $entryVehicleUseCase
    ) {
    }

    public function execute(EntryVehicleDTO $dto, int $guardId): ParkingTicket
    {
        return $this->entryVehicleUseCase->execute($dto, $guardId);
    }
}




