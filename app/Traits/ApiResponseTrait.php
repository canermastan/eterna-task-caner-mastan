<?php

namespace App\Traits;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

trait ApiResponseTrait
{
    protected function successResponse(
        mixed $data = null,
        string $message = 'Success',
        int $statusCode = Response::HTTP_OK
    ): JsonResponse {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data,
            'timestamp' => now()->toISOString(),
        ], $statusCode)->setEncodingOptions(
            JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE
        );
    }

    protected function errorResponse(
        string $message = 'Error occurred',
        int $statusCode = Response::HTTP_BAD_REQUEST,
        mixed $errors = null
    ): JsonResponse {
        $response = [
            'success' => false,
            'message' => $message,
            'timestamp' => now()->toISOString(),
        ];

        if ($errors) {
            $response['errors'] = $errors;
        }

        return response()->json($response, $statusCode)->setEncodingOptions(
            JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE
        );
    }

    protected function paginatedResponse(
        mixed $data,
        string $message = 'Data retrieved successfully',
        int $statusCode = Response::HTTP_OK
    ): JsonResponse {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data->items(),
            'pagination' => [
                'current_page' => $data->currentPage(),
                'per_page' => $data->perPage(),
                'total' => $data->total(),
                'last_page' => $data->lastPage(),
                'from' => $data->firstItem(),
                'to' => $data->lastItem(),
                'has_more_pages' => $data->hasMorePages(),
            ],
            'timestamp' => now()->toISOString(),
        ], $statusCode)->setEncodingOptions(
            JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE
        );
    }
}
