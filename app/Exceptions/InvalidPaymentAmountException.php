<?php

namespace App\Exceptions;

use Exception;

class InvalidPaymentAmountException extends Exception
{
    protected $message = 'The payment amount is not valid';

    protected $code = 422;
}
