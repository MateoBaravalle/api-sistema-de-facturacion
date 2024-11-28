<?php

namespace App\Services;

use App\Models\Role;
use Illuminate\Pagination\LengthAwarePaginator;

class RoleService extends Service
{
    protected const MODEL = 'role';

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
        return $this->getById($id, self::MODEL);
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
