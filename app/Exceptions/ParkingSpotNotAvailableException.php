<?php

namespace App\Exceptions;

use Exception;

class ParkingSpotNotAvailableException extends Exception
{
    protected $message = 'El espacio de estacionamiento no está disponible';
    protected $code = 422;
}




