<?php

namespace App\Services;

use App\Models\Role;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class RoleService extends Service
{
    public function __construct(Role $role)
    {
        parent::__construct($role, 'role');
    }

    public function getAllRoles(int $perPage = self::DEFAULT_PER_PAGE): LengthAwarePaginator
    {
        return $this->remember('roles.all', fn () => $this->paginate($this->model->query(), $perPage));
    }

    public function getRoleById(int $id): Role
    {
        return $this->model->findOrFail($id);
    }

    public function createRole(array $data): Role
    {
        return $this->model->create($data);
    }

    public function updateRole(int $id, array $data): Role
    {
        $role = $this->getRoleById($id);
        $role->update($data);
        
        $this->clearModelCache($id, ['role', 'users']);
        
        return $role->fresh();
    }

    public function deleteRole(int $id): bool
    {
        return $this->getRoleById($id)->delete();
    }

    public function getRoleUsers(int $roleId): Collection
    {
        return $this->remember(
            $this->getCacheKey('users', $roleId),
            fn () => $this->getRoleById($roleId)->users
        );
    }

    public function assignRoleToUser(int $roleId, int $userId): bool
    {
        $role = $this->getRoleById($roleId);
        $attached = $role->users()->syncWithoutDetaching([$userId]);
        
        if (!empty($attached)) {
            $this->clearModelCache($roleId, ['role', 'users']);
        }
        
        return !empty($attached);
    }
}
