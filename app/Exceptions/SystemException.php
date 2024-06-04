<?php

namespace App\Exceptions;

use Exception;

class SystemException extends Exception
{
    protected $message = 'An error occurred.';

    public function __construct($message = null)
    {
        parent::__construct($message ?: $this->message);
    }
}
