<?php

namespace App\Services;

use App\Models\User;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Hash;

class AuthService
{
    public function register(array $data): array
    {
        $data['password'] = Hash::make($data['password']);
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
        JWTAuth::invalidate($token);
    }

    public function refresh(): string
    {
        return JWTAuth::refresh();
    }
}
