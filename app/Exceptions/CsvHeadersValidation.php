<?php

namespace App\Exceptions;

use Exception;

class CsvHeadersValidation extends \Exception
{
    protected $message = 'Invalid CSV file.';

    public function __construct(?string $message = null, int $code = 0, Exception $previous = null)
    {
        parent::__construct($this->message, $code, $previous);
        $this->message = $message;
    }
}
