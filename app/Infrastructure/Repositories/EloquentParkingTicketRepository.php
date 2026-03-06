<?php

namespace App\Infrastructure\Repositories;

use App\Domain\Entities\ParkingTicket;
use App\Domain\Repositories\ParkingTicketRepositoryInterface;
use App\Infrastructure\Repositories\Traits\AppliesFilters;
use App\Models\ParkingTicket as ParkingTicketModel;
use Illuminate\Database\Eloquent\Builder;
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
        return $this->buildCurrentParkedVehiclesQuery($parkingLotId, $filters, $search)
            ->orderBy('entry_time', 'desc')
            ->paginate($perPage);
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

    public function getDashboardStats(): array
    {
        // Usar agregaciones SQL para obtener solo números - MUY OPTIMIZADO
        $activeVehicles = ParkingTicketModel::whereNull('exit_time')->count();
        
        // Total de ingresos de tickets con salida (el pago se hace automáticamente al dar salida)
        $totalRevenue = ParkingTicketModel::whereNotNull('exit_time')
            ->whereNotNull('total_amount')
            ->sum('total_amount');
        
        // Total de tickets con salida
        $totalTickets = ParkingTicketModel::whereNotNull('exit_time')->count();
        
        // Estadísticas semanales para gráficas (últimos 7 días)
        $weekAgo = now()->subDays(7);
        
        // Ocupación diaria: contar tickets que estaban activos cada día de la semana
        // Un ticket está activo un día si entró ese día y aún no ha salido, o si salió después de ese día
        $occupancyByDay = ParkingTicketModel::selectRaw('
                DAYOFWEEK(DATE(entry_time)) as day_of_week,
                COUNT(*) as count
            ')
            ->where('entry_time', '>=', $weekAgo)
            ->where(function($query) {
                // Tickets que aún están activos (sin salida)
                $query->whereNull('exit_time')
                    // O tickets que salieron después de la semana (estaban activos durante la semana)
                    ->orWhere('exit_time', '>=', now()->subDays(7));
            })
            ->groupBy('day_of_week')
            ->get()
            ->pluck('count', 'day_of_week')
            ->toArray();
        
        // Ingresos diarios: sumar ingresos de tickets que salieron cada día de la semana
        $revenueByDay = ParkingTicketModel::selectRaw('
                DAYOFWEEK(DATE(exit_time)) as day_of_week,
                COALESCE(SUM(total_amount), 0) as revenue
            ')
            ->where('exit_time', '>=', $weekAgo)
            ->whereNotNull('exit_time')
            ->whereNotNull('total_amount')
            ->groupBy('day_of_week')
            ->get()
            ->pluck('revenue', 'day_of_week')
            ->toArray();
        
        // Mapear días de la semana
        // MySQL DAYOFWEEK: 1=Domingo, 2=Lunes, 3=Martes, 4=Miércoles, 5=Jueves, 6=Viernes, 7=Sábado
        // Necesitamos: 0=Lunes, 1=Martes, 2=Miércoles, 3=Jueves, 4=Viernes, 5=Sábado, 6=Domingo
        $days = ['Lun', 'Mar', 'Mié', 'Jue', 'Vie', 'Sáb', 'Dom'];
        $occupancy = array_fill(0, 7, 0);
        $revenue = array_fill(0, 7, 0.0);
        
        // Función para convertir DAYOFWEEK de MySQL a índice 0-6 (0=Lunes)
        $convertDayOfWeek = function($mysqlDay) {
            // MySQL: 1=Dom, 2=Lun, 3=Mar, 4=Mié, 5=Jue, 6=Vie, 7=Sáb
            // Array:  0=Lun, 1=Mar, 2=Mié, 3=Jue, 4=Vie, 5=Sáb, 6=Dom
            return ($mysqlDay == 1) ? 6 : ($mysqlDay - 2);
        };
        
        foreach ($occupancyByDay as $dayOfWeek => $count) {
            $index = $convertDayOfWeek((int) $dayOfWeek);
            if ($index >= 0 && $index < 7) {
                $occupancy[$index] = (int) $count;
            }
        }
        
        foreach ($revenueByDay as $dayOfWeek => $revenueAmount) {
            $index = $convertDayOfWeek((int) $dayOfWeek);
            if ($index >= 0 && $index < 7) {
                $revenue[$index] = (float) $revenueAmount;
            }
        }
        
        return [
            'active_vehicles' => $activeVehicles,
            'total_revenue' => (float) $totalRevenue,
            'total_tickets' => $totalTickets,
            'week_occupancy' => $occupancy,
            'week_revenue' => $revenue,
            'week_days' => $days
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
        // Formatear fechas en formato 'Y-m-d H:i:s' para consistencia con la zona horaria local
        // Las fechas en la BD ya están en la zona horaria local configurada
        $formatDate = function($date) {
            if (!$date) return null;
            if ($date instanceof \DateTime || $date instanceof \Carbon\Carbon) {
                return $date->format('Y-m-d H:i:s');
            }
            // Si ya es string, retornarlo tal cual (asumiendo que ya está en formato correcto)
            return $date;
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

