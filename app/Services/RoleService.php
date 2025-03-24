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

    public function getAllRoles(array $params): LengthAwarePaginator
    {
        return $this->getAll($params['page'], $params['per_page']);
    }

    public function getRoleById(int $id): Role
    {
        return $this->getById($id);
    }

    public function assignRoleToUser(int $roleId, int $userId): bool
    {
        $role = $this->getRoleById($roleId);
        $attached = $role->users()->syncWithoutDetaching([$userId]);
        
        // if (!empty($attached)) {
        //     $this->clearModelCache($roleId, ['role', 'users']);
        // }
        
        return !empty($attached);
    }
}
