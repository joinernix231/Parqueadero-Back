<?php

namespace App\Application\UseCases\ParkingLot;

use App\Domain\Entities\ParkingLot;
use App\Domain\Repositories\ParkingLotRepositoryInterface;

class UpdateParkingLotUseCase
{
    public function __construct(private ParkingLotRepositoryInterface $parkingLotRepository) {}

    /**
     * Accepts only the fields that were actually sent in the request (partial update).
     * A full DTO would force defaults on unset fields — wrong for PATCH semantics.
     */
    public function execute(int $id, array $data): ParkingLot
    {
        $lot = $this->parkingLotRepository->findById($id);
        if (! $lot) {
            throw new \RuntimeException('Parking lot not found');
        }

        $this->parkingLotRepository->update($id, $data);

        return $this->parkingLotRepository->findById($id);
    }
}
