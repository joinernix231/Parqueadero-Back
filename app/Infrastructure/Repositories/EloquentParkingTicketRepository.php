<?php

namespace App\Infrastructure\Repositories;

use App\Domain\Entities\ParkingTicket;
use App\Domain\Repositories\ParkingTicketRepositoryInterface;
use App\Infrastructure\Repositories\Traits\AppliesFilters;
use App\Models\ParkingTicket as ParkingTicketModel;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class EloquentParkingTicketRepository implements ParkingTicketRepositoryInterface
{
    use AppliesFilters;
    public function findById(int $id): ?ParkingTicket
    {
        $model = ParkingTicketModel::with(['vehicle', 'parkingSpot', 'parkingLot', 'entryGuard', 'exitGuard'])->find($id);
        return $model ? $this->toEntity($model) : null;
    }

    public function findActiveByVehicle(int $vehicleId): ?ParkingTicket
    {
        $model = ParkingTicketModel::with(['vehicle', 'parkingSpot', 'parkingLot', 'entryGuard'])
            ->where('vehicle_id', $vehicleId)
            ->whereNull('exit_time')
            ->first();
        return $model ? $this->toEntity($model) : null;
    }

    public function findActiveByPlate(string $plate): ?ParkingTicket
    {
        $model = ParkingTicketModel::with(['vehicle', 'parkingSpot', 'parkingLot', 'entryGuard'])
            ->whereHas('vehicle', function ($query) use ($plate) {
                $query->where('plate', strtoupper(str_replace([' ', '-'], '', trim($plate))));
            })
            ->whereNull('exit_time')
            ->first();
        return $model ? $this->toEntity($model) : null;
    }

    public function findActiveBySpot(int $parkingSpotId): ?ParkingTicket
    {
        $model = ParkingTicketModel::where('parking_spot_id', $parkingSpotId)
            ->whereNull('exit_time')
            ->first();
        return $model ? $this->toEntity($model) : null;
    }

    public function findHistory(array $filters = []): array
    {
        $query = ParkingTicketModel::with(['vehicle', 'parkingSpot', 'parkingLot']);
        
        // Aplicar filtros usando el sistema de criteria
        $query = $this->applyFilters($query, $filters);

        // Manejar filtros especiales de status
        if (isset($filters['status'])) {
            if ($filters['status'] === 'active') {
                $query->whereNull('exit_time');
            } elseif ($filters['status'] === 'completed') {
                $query->whereNotNull('exit_time');
            }
        }

        return $query->orderBy('entry_time', 'desc')
            ->get()
            ->map(fn($model) => $this->toEntity($model))
            ->toArray();
    }

    public function paginateHistory(int $perPage = 15, array $filters = []): LengthAwarePaginator
    {
        $query = ParkingTicketModel::with(['vehicle', 'parkingSpot', 'parkingLot']);
        
        // Aplicar filtros usando el sistema de criteria
        $query = $this->applyFilters($query, $filters);

        // Manejar filtros especiales de status
        if (isset($filters['status'])) {
            if ($filters['status'] === 'active') {
                $query->whereNull('exit_time');
            } elseif ($filters['status'] === 'completed') {
                $query->whereNotNull('exit_time');
            }
        }

        return $query->orderBy('entry_time', 'desc')->paginate($perPage);
    }

    protected function getFilterableFields(): array
    {
        return [
            'id', 'vehicle_id', 'parking_spot_id', 'parking_lot_id',
            'entry_time', 'exit_time', 'entry_guard_id', 'exit_guard_id',
            'total_hours', 'hourly_rate_applied', 'total_amount',
            'is_paid', 'payment_method', 'payment_time',
            'created_at', 'updated_at'
        ];
    }

    protected function getFilterableRelations(): array
    {
        return ['vehicle', 'parkingSpot', 'parkingLot'];
    }

    protected function getDateFields(): array
    {
        return ['entry_time', 'exit_time', 'payment_time', 'created_at', 'updated_at'];
    }

    public function findCurrentParkedVehicles(int $parkingLotId = null): array
    {
        $query = ParkingTicketModel::with(['vehicle', 'parkingSpot', 'parkingLot', 'entryGuard'])
            ->whereNull('exit_time');

        if ($parkingLotId !== null) {
            $query->where('parking_lot_id', $parkingLotId);
        }

        return $query->orderBy('entry_time', 'desc')
            ->get()
            ->map(fn($model) => $this->toEntity($model))
            ->toArray();
    }

    public function create(array $data): ParkingTicket
    {
        $model = ParkingTicketModel::create($data);
        return $this->toEntity($model);
    }

    public function update(int $id, array $data): bool
    {
        return ParkingTicketModel::where('id', $id)->update($data) > 0;
    }

    private function toEntity(ParkingTicketModel $model): ParkingTicket
    {
        // Formatear fechas en formato ISO 8601 para compatibilidad con frontend
        $formatDate = function($date) {
            if (!$date) return null;
            return $date instanceof \DateTime ? $date->format('Y-m-d\TH:i:s') : $date;
        };

        return new ParkingTicket(
            id: $model->id,
            vehicleId: $model->vehicle_id,
            parkingSpotId: $model->parking_spot_id,
            parkingLotId: $model->parking_lot_id,
            entryTime: $formatDate($model->entry_time),
            exitTime: $formatDate($model->exit_time),
            entryGuardId: $model->entry_guard_id,
            exitGuardId: $model->exit_guard_id,
            totalHours: (float) ($model->total_hours ?? 0.0),
            hourlyRateApplied: (float) ($model->hourly_rate_applied ?? 0.0),
            totalAmount: (float) ($model->total_amount ?? 0.0),
            isPaid: (bool) ($model->is_paid ?? false),
            paymentMethod: $model->payment_method,
            paymentTime: $formatDate($model->payment_time),
            createdAt: $formatDate($model->created_at),
            updatedAt: $formatDate($model->updated_at)
        );
    }
}

