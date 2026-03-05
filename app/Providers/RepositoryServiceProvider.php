<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->bind(
            \App\Domain\Repositories\UserRepositoryInterface::class,
            \App\Infrastructure\Repositories\EloquentUserRepository::class
        );

        $this->app->bind(
            \App\Domain\Repositories\VehicleRepositoryInterface::class,
            \App\Infrastructure\Repositories\EloquentVehicleRepository::class
        );

        $this->app->bind(
            \App\Domain\Repositories\ParkingLotRepositoryInterface::class,
            \App\Infrastructure\Repositories\EloquentParkingLotRepository::class
        );

        $this->app->bind(
            \App\Domain\Repositories\ParkingSpotRepositoryInterface::class,
            \App\Infrastructure\Repositories\EloquentParkingSpotRepository::class
        );

        $this->app->bind(
            \App\Domain\Repositories\ParkingTicketRepositoryInterface::class,
            \App\Infrastructure\Repositories\EloquentParkingTicketRepository::class
        );
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}

