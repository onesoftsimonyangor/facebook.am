<?php

namespace App\Exceptions;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    public function render($request, Throwable $e): JsonResponse
    {
        $statusCode = 500;

        if ($e instanceof HttpException) {
            $statusCode = $e->getStatusCode();
        }

        // Handle validation exception separately
        if ($e instanceof ValidationException) {
            return $this->handleValidationException($e);
        }

        return response()->json(
            [
                'error' => [
                    'message' => $e->getMessage(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                    'status_code' => $statusCode,
                ],
            ],
            $statusCode
        );
    }

    protected function handleGeneralException(Throwable $exception): JsonResponse
    {
        return response()->json([
            'error' => 'Something went wrong',
            'message' => $exception->getMessage(),
            'line' => $exception->getLine(),
        ], 500);
    }

    protected function handleValidationException(ValidationException $exception): JsonResponse
    {
        // Get the validation errors
        $errors = $exception->errors();

        // Extract the first error message from the validation exception
        $firstError = reset($errors);

        return response()->json(['error' => ['message' => $firstError]], JsonResponse::HTTP_UNPROCESSABLE_ENTITY);
    }

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }
}
