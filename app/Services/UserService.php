<?php

namespace App\Services;

use App\Models\Role;
use App\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class UserService
{
    private const DEFAULT_PER_PAGE = 10;

    private readonly User $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    public function getAllUsers(int $perPage = self::DEFAULT_PER_PAGE): LengthAwarePaginator
    {
        return $this->user->paginate($perPage);
    }

    public function getUserById(int $id): User
    {
        return $this->user->findOrFail($id);
    }

    public function createUser(array $data): User
    {
        return DB::transaction(function () use ($data) {
            $user = $this->user->create($data);
            $user->roles()->attach(Role::where('name', 'guest')->first());
            return $user;
        });
    }

    public function updateUser(int $id, array $data): User
    {
        $user = $this->getUserById($id);
        $user->update($data);
        return $user->fresh();
    }

    public function deleteUser(int $id): bool
    {
        return $this->getUserById($id)->delete();
    }
}
