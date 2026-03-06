<?php

namespace App\Application\Actions\Parking;

use App\Application\UseCases\Parking\GetCurrentParkedVehiclesUseCase;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class GetCurrentVehiclesAction
{
    public function __construct(
        private GetCurrentParkedVehiclesUseCase $getCurrentParkedVehiclesUseCase
    ) {
    }

    public function execute(
        ?int $parkingLotId = null,
        array|string|null $filters = [],
        ?string $search = null,
        bool $paginate = false,
        int $perPage = 15
    ): array|LengthAwarePaginator
    {
        return $this->getCurrentParkedVehiclesUseCase->execute($parkingLotId, $filters, $search, $paginate, $perPage);
    }
}




