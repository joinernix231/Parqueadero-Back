<?php

namespace App\Infrastructure\Repositories;

use App\Domain\Entities\Vehicle;
use App\Domain\Repositories\VehicleRepositoryInterface;
use App\Infrastructure\Repositories\Traits\AppliesFilters;
use App\Models\Vehicle as VehicleModel;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class EloquentVehicleRepository implements VehicleRepositoryInterface
{
    use AppliesFilters;
    public function findById(int $id): ?Vehicle
    {
        $model = VehicleModel::find($id);
        return $model ? $this->toEntity($model) : null;
    }

    public function findByPlate(string $plate): ?Vehicle
    {
        $normalizedPlate = strtoupper(str_replace(' ', '', trim($plate)));
        $model = VehicleModel::where('plate', $normalizedPlate)->first();
        return $model ? $this->toEntity($model) : null;
    }

    public function all(array $filters = []): array
    {
        $query = VehicleModel::query();
        $query = $this->applyFilters($query, $filters);

        return $query->get()->map(fn($model) => $this->toEntity($model))->toArray();
    }

    public function paginate(int $perPage = 15, array $filters = []): LengthAwarePaginator
    {
        $query = VehicleModel::query();
        $query = $this->applyFilters($query, $filters);

        $paginator = $query->paginate($perPage);

        $paginator->setCollection(
            $paginator->getCollection()->map(fn($model) => $this->toEntity($model))
        );

        return $paginator;
    }

    protected function getFilterableFields(): array
    {
        return ['plate', 'owner_name', 'phone', 'vehicle_type', 'created_at', 'updated_at'];
    }

    protected function getFilterableRelations(): array
    {
        return [];
    }

    protected function getDateFields(): array
    {
        return ['created_at', 'updated_at'];
    }

    public function create(array $data): Vehicle
    {
        // Normalizar placa
        if (isset($data['plate'])) {
            $data['plate'] = strtoupper(str_replace(' ', '', trim($data['plate'])));
        }

        $model = VehicleModel::create($data);
        return $this->toEntity($model);
    }

    public function update(int $id, array $data): bool
    {
        // Normalizar placa si existe
        if (isset($data['plate'])) {
            $data['plate'] = strtoupper(str_replace(' ', '', trim($data['plate'])));
        }

        return VehicleModel::where('id', $id)->update($data) > 0;
    }

    public function delete(int $id): bool
    {
        return VehicleModel::destroy($id) > 0;
    }

    private function toEntity(VehicleModel $model): Vehicle
    {
        return new Vehicle(
            id: $model->id,
            plate: $model->plate,
            ownerName: $model->owner_name,
            phone: $model->phone,
            vehicleType: $model->vehicle_type,
            createdAt: $model->created_at?->toDateTimeString(),
            updatedAt: $model->updated_at?->toDateTimeString()
        );
    }
}

