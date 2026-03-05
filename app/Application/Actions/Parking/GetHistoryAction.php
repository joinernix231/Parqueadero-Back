<?php

namespace App\Application\Actions\Parking;

use App\Application\UseCases\Parking\GetParkingHistoryUseCase;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class GetHistoryAction
{
    public function __construct(
        private GetParkingHistoryUseCase $getParkingHistoryUseCase
    ) {
    }

    public function execute(array $filters = [], bool $paginate = true, int $perPage = 15): array|LengthAwarePaginator
    {
        return $this->getParkingHistoryUseCase->execute($filters, $paginate, $perPage);
    }
}




