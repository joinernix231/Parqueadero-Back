<?php

namespace App\Infrastructure\Repositories;

use App\Domain\Entities\ParkingLot;
use App\Domain\Repositories\ParkingLotRepositoryInterface;
use App\Models\ParkingLot as ParkingLotModel;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class EloquentParkingLotRepository implements ParkingLotRepositoryInterface
{
    public function findById(int $id): ?ParkingLot
    {
        $model = ParkingLotModel::find($id);

        return $model ? $this->toEntity($model) : null;
    }

    public function findActive(): array
    {
        return ParkingLotModel::where('is_active', true)
            ->get()
            ->map(fn ($model) => $this->toEntity($model))
            ->toArray();
    }

    public function all(array $filters = []): array
    {
        $query = ParkingLotModel::query();

        if (isset($filters['is_active'])) {
            $query->where('is_active', $filters['is_active']);
        }

        return $query->get()->map(fn ($model) => $this->toEntity($model))->toArray();
    }

    public function paginate(int $perPage = 15, array $filters = []): LengthAwarePaginator
    {
        $query = ParkingLotModel::query();

        if (isset($filters['is_active'])) {
            $query->where('is_active', $filters['is_active']);
        }

        return $query->paginate($perPage);
    }

    public function create(array $data): ParkingLot
    {
        $model = ParkingLotModel::create($data);

        return $this->toEntity($model);
    }

    public function update(int $id, array $data): bool
    {
        return ParkingLotModel::where('id', $id)->update($data) > 0;
    }

    public function delete(int $id): bool
    {
        return ParkingLotModel::destroy($id) > 0;
    }

    private function toEntity(ParkingLotModel $model): ParkingLot
    {
        return new ParkingLot(
            id: $model->id,
            name: $model->name,
            address: $model->address,
            totalSpots: $model->total_spots,
            hourlyRateDay: (float) $model->hourly_rate_day,
            hourlyRateNight: (float) $model->hourly_rate_night,
            dayStartTime: $model->day_start_time->format('H:i'),
            dayEndTime: $model->day_end_time->format('H:i'),
            isActive: $model->is_active,
            createdAt: $model->created_at?->toDateTimeString(),
            updatedAt: $model->updated_at?->toDateTimeString()
        );
    }
}
