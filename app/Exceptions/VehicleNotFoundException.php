<?php

namespace App\Exceptions;

use Exception;

class VehicleNotFoundException extends Exception
{
    protected $message = 'Vehicle not found';

    protected $code = 404;
}
