<?php

namespace App\Domain\Repositories;

use App\Domain\Entities\ParkingSpot;

interface ParkingSpotRepositoryInterface
{
    public function findById(int $id): ?ParkingSpot;

    public function findByLotId(int $parkingLotId): array;

    public function findAvailableByLot(int $parkingLotId): array;

    public function findByLotAndNumber(int $parkingLotId, string $spotNumber): ?ParkingSpot;

    public function update(int $id, array $data): bool;

    public function create(array $data): ParkingSpot;
}





