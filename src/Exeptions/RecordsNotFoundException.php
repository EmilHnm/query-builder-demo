<?php

namespace Hoangm\Query\Exeptions;

use RuntimeException;

class RecordsNotFoundException extends RuntimeException
{
    public function __construct($message = "", $code = 0, \Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}