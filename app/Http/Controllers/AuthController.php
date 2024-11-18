<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\LoginRequest;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Http\JsonResponse;

class AuthController extends Controller
{
    private function successResponse($message, $data = [], $code = 200): JsonResponse
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
            $validated = $request->validated();
            $validated['password'] = Hash::make($validated['password']);

            $user = User::create($validated);
            $token = JWTAuth::fromUser($user);

            return $this->successResponse(
                'Successfully registered',
                ['data' => $user, 'token' => $token],
                201
            );
        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }

    public function login(LoginRequest $request): JsonResponse
    {
        try {
            if (!$token = JWTAuth::attempt($request->validated())) {
                return $this->errorResponse('Invalid credentials', null, 401);
            }

            return $this->successResponse('Successfully logged in', ['token' => $token]);
        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }

    public function logout(Request $request): JsonResponse
    {
        try {
            $token = $request->bearerToken();

            if (!$token) {
                return $this->errorResponse('No token provided', null, 401);
            }

            JWTAuth::invalidate($token);
            return $this->successResponse('Successfully logged out');
        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }

    public function refresh(): JsonResponse
    {
        try {
            $token = JWTAuth::refresh();
            return $this->successResponse('Token refreshed', ['token' => $token]);
        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }
}
