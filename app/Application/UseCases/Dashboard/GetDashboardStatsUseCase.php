<?php

namespace App\Application\UseCases\Dashboard;

use App\Domain\Repositories\ParkingLotRepositoryInterface;
use App\Domain\Repositories\ParkingTicketStatsRepositoryInterface;

class GetDashboardStatsUseCase
{
    // MySQL DAYOFWEEK: 1=Sun, 2=Mon, ..., 7=Sat
    // Our index: 0=Mon, ..., 6=Sun
    private const DAYS = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'];

    public function __construct(
        private ParkingTicketStatsRepositoryInterface $ticketStatsRepository,
        private ParkingLotRepositoryInterface $lotRepository
    ) {}

    public function execute(): array
    {
        $raw = $this->ticketStatsRepository->getDashboardRawStats();

        $activeLots = $this->lotRepository->all(['is_active' => true]);
        $totalSpots = (int) collect($activeLots)->sum(fn ($lot) => $lot->getTotalSpots());

        return [
            'active_vehicles' => $raw['active_vehicles'],
            'total_revenue' => $raw['total_revenue'],
            'total_tickets' => $raw['total_tickets'],
            'total_spots' => $totalSpots,
            'occupancy_rate' => $totalSpots > 0
                ? round(($raw['active_vehicles'] / $totalSpots) * 100)
                : 0,
            'week_occupancy' => $this->mapByDayOfWeek($raw['occupancy_by_dow']),
            'week_revenue' => $this->mapByDayOfWeek($raw['revenue_by_dow'], asFloat: true),
            'week_days' => self::DAYS,
        ];
    }

    private function mapByDayOfWeek(array $data, bool $asFloat = false): array
    {
        $result = array_fill(0, 7, $asFloat ? 0.0 : 0);
        foreach ($data as $mysqlDow => $value) {
            $index = $mysqlDow === 1 ? 6 : $mysqlDow - 2;
            if ($index >= 0 && $index < 7) {
                $result[$index] = $asFloat ? (float) $value : (int) $value;
            }
        }

        return $result;
    }
}
