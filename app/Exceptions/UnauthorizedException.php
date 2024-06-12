<?php

namespace App\Exceptions;

use Exception;

class UnauthorizedException extends Exception
{
    protected $message = 'Unauthorized';

    protected $code = 401;
}
