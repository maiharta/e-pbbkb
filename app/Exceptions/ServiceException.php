<?php

namespace App\Exceptions;

use Exception;

class ServiceException extends Exception
{
    public function __construct($message = "Service Exception", $code = 0, Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

    public function render($request)
    {
        return response()->json([
            'error' => $this->getMessage(),
        ], 500);
    }
}
