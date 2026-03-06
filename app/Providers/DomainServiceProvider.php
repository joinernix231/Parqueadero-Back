<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class DomainServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // Bindings de interfaces del dominio se registrarán aquí
        // cuando se creen las interfaces de Repositories
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}





