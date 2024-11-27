<?php

namespace App\Http\Controllers;

use App\Http\Requests\RoleRequest\StoreRoleRequest;
use App\Http\Requests\RoleRequest\UpdateRoleRequest;
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

    public function store(StoreRoleRequest $request): JsonResponse
    {
        try {
            $role = $this->roleService->createRole($request->validated());
            return $this->successResponse('Role created successfully', ['role' => $role], 201);
        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }

    public function show(int $id): JsonResponse
    {
        try {
            $role = $this->roleService->getRoleById($id);
            return $this->successResponse('Role retrieved successfully', ['role' => $role]);
        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }

    public function update(UpdateRoleRequest $request, int $id): JsonResponse
    {
        try {
            $role = $this->roleService->updateRole($id, $request->validated());
            return $this->successResponse('Role updated successfully', ['role' => $role]);
        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }

    public function destroy(int $id): JsonResponse
    {
        try {
            $this->roleService->deleteRole($id);
            return $this->successResponse('Role deleted successfully');
        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }

    public function users(int $id): JsonResponse
    {
        try {
            $users = $this->roleService->getRoleUsers($id);
            return $this->successResponse('Role users retrieved successfully', ['users' => $users]);
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
