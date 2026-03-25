<?php

namespace App\Domain\Repositories;

use App\Domain\Entities\ParkingTicket;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface ParkingTicketRepositoryInterface
{
    public function findById(int $id): ?ParkingTicket;

    public function findActiveByVehicle(int $vehicleId): ?ParkingTicket;

    public function findActiveByPlate(string $plate): ?ParkingTicket;

    public function findActiveBySpot(int $parkingSpotId): ?ParkingTicket;

    public function findHistory(array $filters = []): array;

    public function paginateHistory(int $perPage = 15, array $filters = []): LengthAwarePaginator;

    public function findCurrentParkedVehicles(?int $parkingLotId = null, array|string|null $filters = [], ?string $search = null): array;

    public function paginateCurrentParkedVehicles(int $perPage = 15, ?int $parkingLotId = null, array|string|null $filters = [], ?string $search = null): LengthAwarePaginator;

    public function create(array $data): ParkingTicket;

    public function update(int $id, array $data): bool;
}

