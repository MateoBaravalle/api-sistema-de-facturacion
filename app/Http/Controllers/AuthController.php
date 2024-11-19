<?php

namespace App\Http\Controllers;

use App\Services\AuthService;
use App\Http\Requests\AuthRequest\RegisterRequest;
use App\Http\Requests\AuthRequest\LoginRequest;
use Tymon\JWTAuth\Exceptions\JWTException;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class AuthController extends Controller
{
    protected AuthService $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    private function successResponse(string $message, array $data = [], int $code = 200): JsonResponse
    {
        return response()->json([
            'status' => 'success',
            'message' => $message,
            ...$data
        ], $code);
    }

    private function errorResponse(string $message, ?string $error = null, int $code = 400): JsonResponse
    {
        return response()->json([
            'status' => 'error',
            'message' => $message,
            'error' => $error
        ], $code);
    }

    protected function handleException(\Exception $e): JsonResponse
    {
        $code = $e instanceof JWTException ? 401 : 500;
        $message = $e instanceof JWTException ? 'Token error' : 'Operation failed';

        return $this->errorResponse($message, $e->getMessage(), $code);
    }

    public function register(RegisterRequest $request): JsonResponse
    {
        try {
            $result = $this->authService->register($request->validated());

            return $this->successResponse(
                'Successfully registered',
                [...$result],
                201
            );
        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }

    public function login(LoginRequest $request): JsonResponse
    {
        try {
            $token = $this->authService->login($request->validated());
            return $this->successResponse('Successfully logged in', ['token' => $token]);
        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }

    public function logout(Request $request): JsonResponse
    {
        try {
            $this->authService->logout($request->bearerToken());
            return $this->successResponse('Successfully logged out');
        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }

    public function refresh(Request $request): JsonResponse
    {
        try {
            $newToken = $this->authService->refresh($request->bearerToken());
            return $this->successResponse('Token refreshed', ['token' => $newToken]);
        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }
}
