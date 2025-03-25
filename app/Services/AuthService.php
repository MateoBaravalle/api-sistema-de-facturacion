<?php

namespace App\Services;

use App\Exceptions\CredentialsException;
use Tymon\JWTAuth\Facades\JWTAuth;

/**
 * Authentication service handling user authentication operations
 */
class AuthService
{
    private readonly UserService $userService;
    
    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    /**
     * Register a new user and return user data with JWT token
     *
     * @param array{username: string, name: string, lastname: string, email: string, password: string, phone?: string} $data
     * @return array{user: \App\Models\User, token: string}
     */
    public function register(array $data): array
    {
        $user = $this->userService->createUser($data);
        return [
            'token' => JWTAuth::fromUser($user),
        ];
    }

    /**
     * Authenticate user and return JWT token
     *
     * @param array{login: string, password: string} $credentials
     * @return string
     * @throws CredentialsException
     */
    public function login(array $credentials): string
    {
        $credentials = $this->parseCredentials($credentials);
        $token = JWTAuth::attempt($credentials);
        
        if (!$token) {
            throw new CredentialsException('El usuario o la contraseña son incorrectos');
        }
        
        return $token;
    }

    /**
     * Invalidate the given token
     *
     * @param string $token
     */
    public function logout(): void
    {
        JWTAuth::invalidate(JWTAuth::getToken());
    }

    /**
     * Refresh the given token
     *
     * @param string $token
     * @return string
     */
    public function refresh(): string
    {
        return JWTAuth::refresh(JWTAuth::getToken());
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
