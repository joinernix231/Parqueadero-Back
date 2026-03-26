<?php

namespace App\Infrastructure\Repositories;

use App\Domain\Entities\ParkingSpot;
use App\Domain\Repositories\ParkingSpotRepositoryInterface;
use App\Models\ParkingSpot as ParkingSpotModel;

class EloquentParkingSpotRepository implements ParkingSpotRepositoryInterface
{
    public function findById(int $id): ?ParkingSpot
    {
        $model = ParkingSpotModel::find($id);

        return $model ? $this->toEntity($model) : null;
    }

    public function findByLotId(int $parkingLotId): array
    {
        return ParkingSpotModel::where('parking_lot_id', $parkingLotId)
            ->get()
            ->map(fn ($model) => $this->toEntity($model))
            ->toArray();
    }

    public function findAvailableByLot(int $parkingLotId): array
    {
        return ParkingSpotModel::where('parking_lot_id', $parkingLotId)
            ->where('is_active', true)
            ->where('is_occupied', false)
            ->get()
            ->map(fn ($model) => $this->toEntity($model))
            ->toArray();
    }

    public function findByLotAndNumber(int $parkingLotId, string $spotNumber): ?ParkingSpot
    {
        $model = ParkingSpotModel::where('parking_lot_id', $parkingLotId)
            ->where('spot_number', $spotNumber)
            ->first();

        return $model ? $this->toEntity($model) : null;
    }

    public function update(int $id, array $data): bool
    {
        return ParkingSpotModel::where('id', $id)->update($data) > 0;
    }

    public function create(array $data): ParkingSpot
    {
        $model = ParkingSpotModel::create($data);

        return $this->toEntity($model);
    }

    private function toEntity(ParkingSpotModel $model): ParkingSpot
    {
        return new ParkingSpot(
            id: $model->id,
            parkingLotId: $model->parking_lot_id,
            spotNumber: $model->spot_number,
            spotType: $model->spot_type,
            isOccupied: $model->is_occupied,
            isActive: $model->is_active,
            createdAt: $model->created_at?->toDateTimeString(),
            updatedAt: $model->updated_at?->toDateTimeString()
        );
    }
}
