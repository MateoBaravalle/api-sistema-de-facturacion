<?php

namespace App\Http\Controllers;

use App\Services\RoleService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    protected RoleService $roleService;

    public function __construct(RoleService $roleService)
    {
        $this->roleService = $roleService;
    }

    public function index(Request $request): JsonResponse
    {
        try {
            $perPage = $request->get('per_page', 10);
            $roles = $this->roleService->getAllRoles($perPage);
            return $this->successResponse('Roles retrieved successfully', ['roles' => $roles]);
        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }

    public function assignUser(int $roleId, Request $request): JsonResponse
    {
        try {
            dd($request->all());
            $validated = $request->validate([
                'user_id' => 'required|integer|exists:users,id',
            ]);

            $assigned = $this->roleService->assignRoleToUser($roleId, $validated['user_id']);
            
            return $this->successResponse(
                $assigned ? 'Role assigned successfully' : 'Role was already assigned',
                ['assigned' => $assigned]
            );
        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }
}
