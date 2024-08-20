<?php
namespace App\Http\Controllers\Api\Traits;

use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;

trait ApiResponse
{
    /**
     * Success Response
     */
    protected function successResponse($data, string $message = 'Request successful', int $code = Response::HTTP_OK): JsonResponse
    {
        return response()->json([
            'status' => 'success',
            'message' => $message,
            'data' => $data,
            'code' => $code
        ], $code);
    }

    /**
     * Error Response
     */
    protected function errorResponse(string $message, array $errors = [], int $code = Response::HTTP_BAD_REQUEST): JsonResponse
    {
        return response()->json([
            'status' => 'error',
            'message' => $message,
            'errors' => $errors,
            'code' => $code
        ], $code);
    }

    /**
     * Validation Error Response
     */
    protected function validationErrorResponse(ValidationException $exception): JsonResponse
    {
        return response()->json([
            'status' => 'error',
            'message' => 'Validation failed.',
            'errors' => $exception->errors(),
            'code' => Response::HTTP_UNPROCESSABLE_ENTITY
        ], Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    /**
     * Authentication Error Response
     */
    protected function authenticationErrorResponse(string $message = 'Unauthorized', int $code = Response::HTTP_UNAUTHORIZED): JsonResponse
    {
        return response()->json([
            'status' => 'error',
            'message' => $message,
            'code' => $code
        ], $code);
    }

    /**
     * Forbidden Error Response
     */
    protected function forbiddenResponse(string $message = 'Forbidden', int $code = Response::HTTP_FORBIDDEN): JsonResponse
    {
        return response()->json([
            'status' => 'error',
            'message' => $message,
            'code' => $code
        ], $code);
    }

    /**
     * Not Found Error Response
     */
    protected function notFoundResponse(string $message = 'Resource not found', int $code = Response::HTTP_NOT_FOUND): JsonResponse
    {
        return response()->json([
            'status' => 'error',
            'message' => $message,
            'code' => $code
        ], $code);
    }
}
