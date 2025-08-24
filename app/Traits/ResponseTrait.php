<?php

namespace App\Traits;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

trait ResponseTrait
{
    /**
     * Return a successful response
     *
     * @param mixed $data
     * @param int $status
     * @param string $message
     * @param array $meta
     * @return JsonResponse
     */
    public function successResponse($data = null, int $status = 200, string $message = 'Success', array $meta = []): JsonResponse
    {
        $response = [
            'success' => true,
            'message' => $message,
            'data' => $data,
            'status' => $status
        ];

        if (!empty($meta)) {
            $response['meta'] = $meta;
        }

        return response()->json($response, $status);
    }

    /**
     * Return an error response
     *
     * @param mixed $data
     * @param int $status
     * @param string $message
     * @param string|null $debug
     * @return JsonResponse
     */
    public function errorResponse($data = null, int $status = 400, string $message = 'Error', ?string $debug = null): JsonResponse
    {
        $response = [
            'success' => false,
            'message' => $message,
            'data' => $data,
            'status' => $status
        ];

        // Only include debug info in development
        if ($debug && config('app.debug')) {
            $response['debug'] = $debug;
        }

        // Log error for debugging
        if ($status >= 400) {
            Log::error('API Error Response', [
                'status' => $status,
                'message' => $message,
                'data' => $data,
                'debug' => $debug
            ]);
        }

        return response()->json($response, $status);
    }

    /**
     * Return a validation error response
     *
     * @param array $errors
     * @param string $message
     * @return JsonResponse
     */
    public function validationErrorResponse(array $errors, string $message = 'Validation failed'): JsonResponse
    {
        return $this->errorResponse($errors, 422, $message);
    }

    /**
     * Return a not found response
     *
     * @param string $message
     * @return JsonResponse
     */
    public function notFoundResponse(string $message = 'Resource not found'): JsonResponse
    {
        return $this->errorResponse(null, 404, $message);
    }

    /**
     * Return an unauthorized response
     *
     * @param string $message
     * @return JsonResponse
     */
    public function unauthorizedResponse(string $message = 'Unauthorized'): JsonResponse
    {
        return $this->errorResponse(null, 401, $message);
    }

    /**
     * Return a forbidden response
     *
     * @param string $message
     * @return JsonResponse
     */
    public function forbiddenResponse(string $message = 'Forbidden'): JsonResponse
    {
        return $this->errorResponse(null, 403, $message);
    }
}
