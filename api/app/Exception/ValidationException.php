<?php

namespace App\Exception;

use RuntimeException;

class ValidationException extends RuntimeException
{
    public function __construct()
    {
        parent::__construct('enter correct credentials', 500);
    }

}