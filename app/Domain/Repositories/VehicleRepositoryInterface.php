<?php

namespace App\Domain\Repositories;

use App\Domain\Entities\Vehicle;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface VehicleRepositoryInterface
{
    public function findById(int $id): ?Vehicle;

    public function findByPlate(string $plate): ?Vehicle;

    public function all(array $filters = []): array;

    public function paginate(int $perPage = 15, array $filters = []): LengthAwarePaginator;

    public function create(array $data): Vehicle;

    public function update(int $id, array $data): bool;

    public function delete(int $id): bool;
}





