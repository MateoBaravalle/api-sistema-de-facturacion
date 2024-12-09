<?php

namespace App\Http\Controllers;

use App\Http\Requests\AuthRequest\LoginRequest;
use App\Http\Requests\AuthRequest\RegisterRequest;
use App\Services\AuthService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    protected AuthService $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    public function register(RegisterRequest $request): JsonResponse
    {
        try {
            $data = $request->validated();
            $data['role'] = 'admin'; // TODO: change to guest

            $result = $this->authService->register($data);

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
