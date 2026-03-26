<?php

namespace App\Exceptions;

use Exception;

class ParkingSpotNotAvailableException extends Exception
{
    protected $message = 'The parking spot is not available';

    protected $code = 422;
}
