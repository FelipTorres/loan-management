<?php

namespace App\Exceptions;

use Exception;

class UserNotFoundException extends \Exception
{
    public function __construct($message = "User not found", $code = 400, Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
