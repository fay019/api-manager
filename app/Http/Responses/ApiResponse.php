<?php

namespace App\Http\Responses;

use Illuminate\Http\JsonResponse;

class ApiResponse
{
    public static function success(
        mixed $data = null,
        array $meta = [],
        int $status = 200,
    ): JsonResponse {
        return response()->json([
            'success' => true,
            'data' => $data,
            'meta' => $meta,
        ], $status);
    }

    public static function error(
        string $code,
        string $message,
        array $details = [],
        int $status = 400,
    ): JsonResponse {
        return response()->json([
            'success' => false,
            'error' => [
                'code' => $code,
                'message' => $message,
                'details' => $details,
            ],
        ], $status);
    }

    public static function notFound(
        string $message = 'Resource not found',
    ): JsonResponse {
        return self::error('NOT_FOUND', $message, [], 404);
    }

    public static function unauthorized(
        string $message = 'Unauthorized',
    ): JsonResponse {
        return self::error('UNAUTHORIZED', $message, [], 401);
    }

    public static function forbidden(
        string $message = 'Forbidden',
    ): JsonResponse {
        return self::error('FORBIDDEN', $message, [], 403);
    }

    public static function tooManyRequests(
        string $message = 'Too many requests',
    ): JsonResponse {
        return self::error('RATE_LIMIT_EXCEEDED', $message, [], 429);
    }

    public static function unprocessable(
        array $errors,
    ): JsonResponse {
        return self::error('VALIDATION_ERROR', 'Validation failed', $errors, 422);
    }
}
