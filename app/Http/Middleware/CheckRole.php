<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    private const ROLE_HIERARCHY = [
        'guest' => 0,
        'client' => 1,
        'seller' => 2,
        'supervisor' => 3,
        'admin' => 4,
    ];

    public function handle(Request $request, Closure $next, ...$roles)
    {
        try {
            $user = auth()->user();

            if (!$user) {
                return $this->unauthorized('Usuario no autenticado');
            }

            $userRoles = $user->roles->pluck('name')->toArray();
            
            $userMaxLevel = $this->getMaxLevel($userRoles);
            $requiredMaxLevel = $this->getMaxLevel($roles);

            $this->logRoleCheck($userRoles, $roles, $userMaxLevel, $requiredMaxLevel);

            if ($userMaxLevel < $requiredMaxLevel) {
                return $this->forbidden('No tienes permisos suficientes');
            }

            return $next($request);
        } catch (\Exception $e) {
            Log::error('Role check error: ' . $e->getMessage());
            return $this->serverError('Error en la verificación de roles');
        }
    }

    private function getMaxLevel(array $roles): int
    {
        return max(array_map(function ($role) {
            return self::ROLE_HIERARCHY[$role] ?? self::ROLE_HIERARCHY['guest'];
        }, $roles));
    }

    private function logRoleCheck(array $userRoles, array $roles, int $userMaxLevel, int $requiredMaxLevel): void
    {
        Log::info('Role check details:', [
            'user_roles' => $userRoles,
            'required_roles' => $roles,
            'user_level' => $userMaxLevel,
            'required_level' => $requiredMaxLevel,
        ]);
    }

    private function unauthorized(string $message): Response
    {
        return response()->json([
            'status' => 'error',
            'message' => $message,
        ], Response::HTTP_UNAUTHORIZED);
    }

    private function forbidden(string $message): Response
    {
        return response()->json([
            'status' => 'error',
            'message' => $message,
        ], Response::HTTP_FORBIDDEN);
    }

    private function serverError(string $message): Response
    {
        return response()->json([
            'status' => 'error',
            'message' => $message,
        ], Response::HTTP_INTERNAL_SERVER_ERROR);
    }
}
