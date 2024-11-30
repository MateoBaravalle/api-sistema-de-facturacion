<?php

namespace App\Services;

use App\Models\Role;
use App\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class UserService extends Service
{
    protected const MODEL = 'user';

    public function __construct(User $user)
    {
        parent::__construct($user, self::MODEL);
    }

    public function getAllUsers(int $perPage = self::DEFAULT_PER_PAGE): LengthAwarePaginator
    {
        return $this->getAll($perPage);
    }

    public function getUserById(int $id): User
    {
        return $this->getById($id, self::MODEL);
    }

    public function createUser(array $data): User
    {
        return DB::transaction(function () use ($data) {
            $user = $this->create($data);
            $user->roles()->attach(Role::where('name', 'guest')->first());
            $this->clearModelCache($user->id, ['user', 'roles']);
            return $user;
        });
    }

    public function updateUser(int $id, array $data): User
    {
        $user = $this->getUserById($id);
        $user->update($data);
        $this->clearModelCache($id, ['user', 'roles']);
        return $user->fresh();
    }

    public function deleteUser(int $id): bool
    {
        $user = $this->getUserById($id);
        $deleted = $user->delete();
        
        if ($deleted) {
            $this->clearModelCache($id, ['user', 'roles']);
        }
        
        return $deleted;
    }
}
