<?php

namespace App\Services;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class UserService extends Service
{
    protected const MODEL = 'user';

    public function __construct(User $user)
    {
        parent::__construct($user, self::MODEL);
    }

    public function getAllUsers(int $page, int $perPage = self::DEFAULT_PER_PAGE): LengthAwarePaginator
    {
        return $this->getAll($page, $perPage);
    }

    public function getUserById(int $id): Model
    {
        return $this->getById($id);
    }

    public function createUser(array $data): User
    {
        $role = $data['role'];
        unset($data['role']);

        return DB::transaction(function () use ($data, $role) {
            $user = $this->create($data);
            $user->roles()->attach(Role::where('name', $role)->first());
            // $this->clearModelCache($user->id, ['user', 'roles']);
            return $user;
        });
    }

    public function updateUser(int $id, array $data): Model
    {
        $role = $data['role'];
        unset($data['role']);
        
        $user = $this->update($id, $data);
        
        $user->roles()->sync(Role::where('name', $role)->first());
        
        // $this->clearModelCache($id, ['user', 'roles']);
        
        return $user;
    }

    public function deleteUser(int $id): bool
    {   
        // if ($deleted) {
        //     $this->clearModelCache($id, ['user', 'roles']);
        // }
        
        return $this->delete($id);
    }
}
