<?php

namespace App\Exception;

use RuntimeException;

class UncorrectPasswordException extends RuntimeException
{
    public function __construct()
    {
        parent::__construct('uncorrect password', 500);
    }

}