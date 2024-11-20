<?php

namespace App\Services;

use App\Models\User;
use Exception;
use Tymon\JWTAuth\Facades\JWTAuth;

/**
 * Authentication service handling user authentication operations
 */
class AuthService
{
    /**
     * Register a new user and return user data with JWT token
     *
     * @param array{username: string, name: string, lastname: string, email: string, password: string, phone?: string} $data
     * @return array{user: User, token: string}
     */
    public function register(array $data): array
    {
        $user = User::create($data);
        $token = JWTAuth::fromUser($user);

        return [
            'user' => $user,
            'token' => $token,
        ];
    }

    /**
     * Authenticate user and return JWT token
     *
     * @param array{login: string, password: string} $credentials
     * @return string
     */
    public function login(array $credentials): string
    {
        $credentials = $this->parseCredentials($credentials);
        $token = JWTAuth::attempt($credentials);

        if (!$token) {
            throw new Exception('Invalid credentials');
        }

        return $token;
    }

    /**
     * Invalidate the given token
     *
     * @param string $token
     */
    public function logout(string $token): void
    {
        JWTAuth::setToken($token)->invalidate();
    }

    /**
     * Refresh the given token
     *
     * @param string $token
     * @return string
     */
    public function refresh(string $token): string
    {
        return JWTAuth::setToken($token)->refresh();
    }

    /**
     * Parse login credentials to determine if email or username is used
     *
     * @param array{login: string, password: string} $credentials
     * @return array{email?: string, username?: string, password: string}
     */
    private function parseCredentials(array $credentials): array
    {
        $parsed = ['password' => $credentials['password']];

        if (str_contains($credentials['login'], '@')) {
            $parsed['email'] = $credentials['login'];
        } else {
            $parsed['username'] = $credentials['login'];
        }

        return $parsed;
    }
}
