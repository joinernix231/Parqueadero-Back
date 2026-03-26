<?php

namespace App\Domain\Repositories;

use App\Domain\Entities\ParkingLot;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface ParkingLotRepositoryInterface
{
    public function findById(int $id): ?ParkingLot;

    public function findActive(): array;

    public function all(array $filters = []): array;

    public function paginate(int $perPage = 15, array $filters = []): LengthAwarePaginator;

    public function create(array $data): ParkingLot;

    public function update(int $id, array $data): bool;

    public function delete(int $id): bool;
}
