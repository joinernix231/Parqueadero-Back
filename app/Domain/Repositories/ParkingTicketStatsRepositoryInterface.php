<?php

namespace App\Domain\Repositories;

interface ParkingTicketStatsRepositoryInterface
{
    /**
     * Returns raw SQL aggregations for the dashboard.
     * Transformation into domain-meaningful structures is the UseCase's responsibility.
     * Keys: active_vehicles, total_revenue, total_tickets, occupancy_by_dow, revenue_by_dow
     */
    public function getDashboardRawStats(): array;
}
