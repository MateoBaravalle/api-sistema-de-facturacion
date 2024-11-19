<?php

namespace App\Services;

use App\Models\User;
use Tymon\JWTAuth\Facades\JWTAuth;

/**
 * Authentication service handling user authentication operations
 * 
 * This service provides methods for user registration, login, logout and token refresh
 * using JWT authentication.
 */
class AuthService
{
    public function register(array $data): array
    {
        $user = User::create($data);
        $token = JWTAuth::fromUser($user);

        return [
            'user' => $user,
            'token' => $token
        ];
    }

    public function login(array $credentials): string
    {
        if (!$token = JWTAuth::attempt($credentials)) {
            throw new \Exception('Invalid credentials');
        }

        return $token;
    }

    public function logout(string $token): void
    {
        JWTAuth::setToken($token)->invalidate();
    }

    public function refresh(string $token): string
    {
        return JWTAuth::setToken($token)->refresh();
    }
}
