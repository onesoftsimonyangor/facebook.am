<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Http\JsonResponse;

class ErrorException extends Exception
{
    protected mixed $statusCode;

    protected mixed $errorMessage;

    public function __construct($message = null, $statusCode = 500)
    {
        $this->statusCode = $statusCode;
        $this->errorMessage = $message ?? 'Something went wrong';
        parent::__construct($this->errorMessage, $this->statusCode);
    }

    public function render($request): JsonResponse
    {
        return response()->json(['error' => $this->errorMessage], $this->statusCode);
    }
}
