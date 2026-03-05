<?php

namespace App\Exceptions;

use Exception;

class VehicleNotFoundException extends Exception
{
    protected $message = 'Vehículo no encontrado';
    protected $code = 404;
}




