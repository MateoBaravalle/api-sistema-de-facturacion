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
        array $data = null,
        int $code = 200
    ): JsonResponse {
        $response = [
            'status' => 'success',
            'message' => $message,
        ];
        
        if ($data) {
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
                'message' => 'Error en token',
                'code' => 401,
            ],
            $e instanceof ModelNotFoundException => [
                'message' => 'Recurso no encontrado',
                'code' => 404,
            ],
            $e instanceof AuthenticationException => [
                'message' => 'Autenticación fallida',
                'code' => 401,
            ],
            $e instanceof AuthorizationException => [
                'message' => 'Autorización fallida',
                'code' => 403,
            ],
            $e instanceof QueryException => [
                'message' => 'Error en base de datos',
                'code' => 500,
            ],
            default => [
                'message' => 'Operación fallida',
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
