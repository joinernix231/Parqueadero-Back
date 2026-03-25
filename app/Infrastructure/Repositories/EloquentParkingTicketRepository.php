<?php

namespace App\Infrastructure\Repositories;

use App\Domain\Entities\ParkingTicket;
use App\Domain\Repositories\ParkingTicketRepositoryInterface;
use App\Domain\Repositories\ParkingTicketStatsRepositoryInterface;
use App\Infrastructure\Repositories\Traits\AppliesFilters;
use App\Models\ParkingTicket as ParkingTicketModel;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class EloquentParkingTicketRepository implements ParkingTicketRepositoryInterface, ParkingTicketStatsRepositoryInterface
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
        
        // Convertir filtros del request al formato correcto
        $convertedFilters = $this->convertRequestFilters($filters);
        
        // Aplicar filtros usando el sistema de criteria
        $query = $this->applyFilters($query, $convertedFilters);

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

        $convertedFilters = $this->convertRequestFilters($filters);
        $query = $this->applyFilters($query, $convertedFilters);

        if (isset($filters['status'])) {
            if ($filters['status'] === 'active') {
                $query->whereNull('exit_time');
            } elseif ($filters['status'] === 'completed') {
                $query->whereNotNull('exit_time');
            }
        }

        $paginator = $query->orderBy('entry_time', 'desc')->paginate($perPage);

        $paginator->setCollection(
            $paginator->getCollection()->map(fn($model) => $this->toEntity($model))
        );

        return $paginator;
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

    /**
     * Convert request filters to format expected by FiltersCriteria
     */
    protected function convertRequestFilters(array $filters): array
    {
        $converted = [];

        // Handle date_from - filter by entry_time >= date_from
        if (isset($filters['date_from']) && !empty($filters['date_from'])) {
            $converted[] = [
                'field' => 'entry_time',
                'operator' => 'gte',
                'value' => $filters['date_from'] . ' 00:00:00'
            ];
        }

        // Handle date_to - filter by entry_time <= date_to
        if (isset($filters['date_to']) && !empty($filters['date_to'])) {
            $converted[] = [
                'field' => 'entry_time',
                'operator' => 'lte',
                'value' => $filters['date_to'] . ' 23:59:59'
            ];
        }

        // Handle plate - filter by vehicle.plate
        if (isset($filters['plate']) && !empty($filters['plate'])) {
            $converted[] = [
                'field' => 'vehicle.plate',
                'operator' => 'like',
                'value' => $filters['plate']
            ];
        }

        // Handle parking_lot_id
        if (isset($filters['parking_lot_id']) && !empty($filters['parking_lot_id'])) {
            $converted[] = [
                'field' => 'parking_lot_id',
                'operator' => 'eq',
                'value' => (string)$filters['parking_lot_id']
            ];
        }

        // Note: 'status' is handled separately in findHistory and paginateHistory methods

        return $converted;
    }

    public function findCurrentParkedVehicles(?int $parkingLotId = null, array|string|null $filters = [], ?string $search = null): array
    {
        return $this->buildCurrentParkedVehiclesQuery($parkingLotId, $filters, $search)
            ->orderBy('entry_time', 'desc')
            ->get()
            ->map(fn($model) => $this->toEntity($model))
            ->toArray();
    }

    public function paginateCurrentParkedVehicles(int $perPage = 15, ?int $parkingLotId = null, array|string|null $filters = [], ?string $search = null): LengthAwarePaginator
    {
        $paginator = $this->buildCurrentParkedVehiclesQuery($parkingLotId, $filters, $search)
            ->orderBy('entry_time', 'desc')
            ->paginate($perPage);

        $paginator->setCollection(
            $paginator->getCollection()->map(fn($model) => $this->toEntity($model))
        );

        return $paginator;
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

    public function getDashboardRawStats(): array
    {
        $weekAgo = now()->subDays(7);

        $occupancyByDow = ParkingTicketModel::selectRaw('DAYOFWEEK(DATE(entry_time)) as dow, COUNT(*) as count')
            ->where('entry_time', '>=', $weekAgo)
            ->where(function ($q) {
                $q->whereNull('exit_time')->orWhere('exit_time', '>=', now()->subDays(7));
            })
            ->groupBy('dow')
            ->get()
            ->pluck('count', 'dow')
            ->toArray();

        $revenueByDow = ParkingTicketModel::selectRaw('DAYOFWEEK(DATE(exit_time)) as dow, COALESCE(SUM(total_amount), 0) as revenue')
            ->where('exit_time', '>=', $weekAgo)
            ->whereNotNull('exit_time')
            ->whereNotNull('total_amount')
            ->groupBy('dow')
            ->get()
            ->pluck('revenue', 'dow')
            ->toArray();

        return [
            'active_vehicles' => ParkingTicketModel::whereNull('exit_time')->count(),
            'total_revenue'   => (float) ParkingTicketModel::whereNotNull('exit_time')->whereNotNull('total_amount')->sum('total_amount'),
            'total_tickets'   => ParkingTicketModel::whereNotNull('exit_time')->count(),
            'occupancy_by_dow' => $occupancyByDow,
            'revenue_by_dow'   => $revenueByDow,
        ];
    }

    private function buildCurrentParkedVehiclesQuery(
        ?int $parkingLotId = null,
        array|string|null $filters = [],
        ?string $search = null
    ): Builder {
        $query = ParkingTicketModel::with(['vehicle', 'parkingSpot', 'parkingLot', 'entryGuard'])
            ->whereNull('exit_time');

        if ($parkingLotId !== null) {
            $query->where('parking_lot_id', $parkingLotId);
        }

        $query = $this->applyFilters($query, $filters);
        $this->applyCurrentVehicleSearch($query, $search);

        return $query;
    }

    private function applyCurrentVehicleSearch(Builder $query, ?string $search): void
    {
        if ($search === null || trim($search) === '') {
            return;
        }

        $trimmedSearch = trim($search);
        $normalizedPlateSearch = strtoupper(str_replace([' ', '-'], '', $trimmedSearch));

        $query->where(function (Builder $builder) use ($trimmedSearch, $normalizedPlateSearch) {
            $builder->where('id', 'like', "%{$trimmedSearch}%")
                ->orWhereHas('vehicle', function (Builder $vehicleQuery) use ($trimmedSearch, $normalizedPlateSearch) {
                    $vehicleQuery->where('plate', 'like', "%{$normalizedPlateSearch}%")
                        ->orWhere('owner_name', 'like', "%{$trimmedSearch}%");
                });
        });
    }

    private function toEntity(ParkingTicketModel $model): ParkingTicket
    {
        $formatDate = static function ($date): ?string {
            if (!$date) {
                return null;
            }
            if ($date instanceof \DateTimeInterface) {
                return $date->format('Y-m-d H:i:s');
            }
            return (string) $date;
        };

        $ticket = new ParkingTicket(
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

        if ($model->relationLoaded('vehicle') && $model->vehicle) {
            $v = $model->vehicle;
            $ticket->setVehicle(new \App\Domain\Entities\Vehicle(
                id: $v->id,
                plate: $v->plate,
                ownerName: $v->owner_name,
                phone: $v->phone,
                vehicleType: $v->vehicle_type,
            ));
        }

        if ($model->relationLoaded('parkingLot') && $model->parkingLot) {
            $pl = $model->parkingLot;
            $ticket->setParkingLot(new \App\Domain\Entities\ParkingLot(
                id: $pl->id,
                name: $pl->name,
                address: $pl->address,
                totalSpots: (int) $pl->total_spots,
                hourlyRateDay: (float) $pl->hourly_rate_day,
                hourlyRateNight: (float) $pl->hourly_rate_night,
                dayStartTime: $pl->day_start_time->format('H:i'),
                dayEndTime: $pl->day_end_time->format('H:i'),
                isActive: (bool) $pl->is_active,
            ));
        }

        if ($model->relationLoaded('parkingSpot') && $model->parkingSpot) {
            $ps = $model->parkingSpot;
            $ticket->setParkingSpot(new \App\Domain\Entities\ParkingSpot(
                id: $ps->id,
                parkingLotId: $ps->parking_lot_id,
                spotNumber: $ps->spot_number,
                spotType: $ps->spot_type,
                isOccupied: (bool) $ps->is_occupied,
                isActive: (bool) $ps->is_active,
            ));
        }

        return $ticket;
    }
}

