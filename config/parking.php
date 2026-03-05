<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Parking Configuration
    |--------------------------------------------------------------------------
    |
    | Configuración por defecto para el sistema de estacionamiento.
    | Estos valores pueden ser sobrescritos por cada ParkingLot individual.
    |
    */

    'default_day_rate_per_hour' => 2.50,
    'default_night_rate_per_hour' => 3.00,
    'default_day_start_time' => '06:00',
    'default_day_end_time' => '20:00',
    'rounding_minutes' => 15, // Redondeo en minutos (15, 30, 60)
];




