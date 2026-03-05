<?php

namespace App\Application\UseCases\Parking;

use App\Domain\Entities\ParkingTicket;
use App\Domain\Repositories\ParkingTicketRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class GetParkingHistoryUseCase
{
    public function __construct(
        private ParkingTicketRepositoryInterface $parkingTicketRepository
    ) {
    }

    public function execute(array $filters = [], bool $paginate = true, int $perPage = 15): array|LengthAwarePaginator
    {
        if ($paginate) {
            return $this->parkingTicketRepository->paginateHistory($perPage, $filters);
        }

        return $this->parkingTicketRepository->findHistory($filters);
    }
}




