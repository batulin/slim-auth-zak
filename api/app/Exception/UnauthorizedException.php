<?php

namespace App\Exception;

use RuntimeException;

class UnauthorizedException extends RuntimeException
{
    public function __construct()
    {
        parent::__construct('Unauthorized user', 401);
    }

}