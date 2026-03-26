<?php

namespace App\Application\UseCases\Parking;

use App\Domain\Repositories\ParkingTicketRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class GetCurrentParkedVehiclesUseCase
{
    public function __construct(
        private ParkingTicketRepositoryInterface $parkingTicketRepository
    ) {}

    public function execute(
        ?int $parkingLotId = null,
        array|string|null $filters = [],
        ?string $search = null,
        bool $paginate = false,
        int $perPage = 15
    ): array|LengthAwarePaginator {
        if ($paginate) {
            return $this->parkingTicketRepository->paginateCurrentParkedVehicles($perPage, $parkingLotId, $filters, $search);
        }

        return $this->parkingTicketRepository->findCurrentParkedVehicles($parkingLotId, $filters, $search);
    }
}
