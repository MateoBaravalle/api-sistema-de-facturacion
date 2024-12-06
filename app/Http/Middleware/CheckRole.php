<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CheckRole
{
    // Definir jerarquía de roles (mayor índice = mayor jerarquía)
    private const ROLE_HIERARCHY = [
        'guest' => 0,
        'client' => 1,
        'seller' => 2,
        'supervisor' => 3,
        'admin' => 4,
    ];

    public function handle(Request $request, Closure $next, ...$roles)
    {
        $user = auth()->user();
        $userRoles = $user->roles->pluck('name')->toArray();
        
        // Obtener el nivel más alto del usuario
        $userMaxLevel = $this->getMaxLevel($userRoles);
        // Obtener el nivel más alto requerido
        $requiredMaxLevel = $this->getMaxLevel($roles);

        Log::info('Role check:', [
            'user_roles' => $userRoles,
            'required_roles' => $roles,
            'user_level' => $userMaxLevel,
            'required_level' => $requiredMaxLevel,
        ]);

        // Usuario pasa si su nivel es mayor o igual al requerido
        if ($userMaxLevel < $requiredMaxLevel) {
            return response()->json([
                'message' => 'No tienes permisos suficientes',
            ], 403);
        }

        return $next($request);
    }

    private function getMaxLevel(array $roles): int
    {
        return max(array_map(function ($role) {
            return self::ROLE_HIERARCHY[$role] ?? self::ROLE_HIERARCHY['guest'];
        }, $roles));
    }
}
