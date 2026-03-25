<?php

namespace App\Application\UseCases\ParkingLot;

use App\Domain\DTOs\ParkingLotDTO;
use App\Domain\Entities\ParkingLot;
use App\Domain\Repositories\ParkingLotRepositoryInterface;

class CreateParkingLotUseCase
{
    public function __construct(private ParkingLotRepositoryInterface $parkingLotRepository) {}

    public function execute(ParkingLotDTO $dto): ParkingLot
    {
        return $this->parkingLotRepository->create([
            'name'              => $dto->name,
            'address'           => $dto->address,
            'total_spots'       => $dto->totalSpots,
            'hourly_rate_day'   => $dto->hourlyRateDay,
            'hourly_rate_night' => $dto->hourlyRateNight,
            'day_start_time'    => $dto->dayStartTime,
            'day_end_time'      => $dto->dayEndTime,
            'is_active'         => $dto->isActive,
        ]);
    }
}
