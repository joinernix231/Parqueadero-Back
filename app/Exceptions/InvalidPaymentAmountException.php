<?php

namespace App\Exceptions;

use Exception;

class InvalidPaymentAmountException extends Exception
{
    protected $message = 'El monto de pago no es válido';
    protected $code = 422;
}





