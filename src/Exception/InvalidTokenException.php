<?php

namespace IanM\OAuthLine\Exception;

use RuntimeException;
use Throwable;

class InvalidTokenException extends RuntimeException
{
    public function __construct($message = "", $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
