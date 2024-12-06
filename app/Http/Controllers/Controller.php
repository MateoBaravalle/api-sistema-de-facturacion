<?php

namespace App\Http\Controllers;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller as BaseController;
use Tymon\JWTAuth\Exceptions\JWTException;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    protected function successResponse(
        string $message,
        array $data = [],
        int $code = 200
    ): JsonResponse {
        $response = [
            'status' => 'success',
            'message' => $message,
        ];
        
        if (!empty($data)) {
            $response['data'] = $data;
        }
        
        return response()->json($response, $code);
    }

    protected function errorResponse(
        string $message,
        ?string $error = null,
        int $code = 400
    ): JsonResponse {
        $response = [
            'status' => 'error',
            'message' => $message,
        ];
    
        if ($error) {
            $response['error'] = $error;
        }
    
        return response()->json($response, $code);
    }

    protected function handleException(\Exception $e, int $defaultCode = 400): JsonResponse
    {
        $errorData = match (true) {
            $e instanceof JWTException => [
                'message' => 'Token error',
                'code' => 401,
            ],
            $e instanceof ModelNotFoundException => [
                'message' => 'Resource not found',
                'code' => 404,
            ],
            $e instanceof AuthenticationException => [
                'message' => 'Authentication failed',
                'code' => 401,
            ],
            $e instanceof AuthorizationException => [
                'message' => 'Authorization failed',
                'code' => 403,
            ],
            $e instanceof QueryException => [
                'message' => 'Database error',
                'code' => 500,
            ],
            default => [
                'message' => 'Operation failed',
                'code' => $defaultCode,
            ]
        };

        return $this->errorResponse(
            $errorData['message'],
            $e->getMessage(),
            $errorData['code'] ?? $defaultCode
        );
    }
}
