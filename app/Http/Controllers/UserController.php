<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserRequest\CreateUserRequest;
use App\Http\Requests\UserRequest\UpdateUserRequest;
use App\Services\UserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserController extends Controller
{
    protected UserService $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    protected function getAllowedFilters(): array
    {
        return [
            'username',
            'name',
            'lastname',
            'email',
            'phone',
        ];
    }

    public function index(Request $request): JsonResponse
    {
        try {
            $users = $this->userService->getAllUsers(
                $this->getQueryParams($request)
            );

            return $this->successResponse('Usuarios recuperados', $users);
        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }

    public function show(int $id): JsonResponse
    {
        try {
            $user = $this->userService->getUserById($id);

            return $this->successResponse('Usuario recuperado', $user);
        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }

    public function store(CreateUserRequest $request): JsonResponse
    {
        try {
            $user = $this->userService->createUser(
                $request->validated()
            );

            return $this->successResponse('Usuario creado', $user, 201);
        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }

    public function update(UpdateUserRequest $request, int $id): JsonResponse
    {
        try {
            $user = $this->userService->updateUser(
                $id,
                $request->validated()
            );

            return $this->successResponse('Usuario actualizado', $user);
        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }

    public function destroy(int $id): JsonResponse
    {
        try {
            $this->userService->deleteUser($id);

            return $this->successResponse('Usuario eliminado');
        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }

    public function showProfile(): JsonResponse
    {
        return $this->show(
            auth()->id()
        );
    }

    public function updateProfile(UpdateUserRequest $request): JsonResponse
    {
        return $this->update(
            $request,
            auth()->id()
        );
    }
}
