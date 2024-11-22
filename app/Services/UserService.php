<?php

namespace App\Services;

use App\Models\User;
use Exception;

class UserService
{
    /**
     * Get all users with pagination
     *
     * @param int $perPage
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function getAllUsers(int $perPage = 10)
    {
        return User::paginate($perPage);
    }

    /**
     * Get user by ID
     *
     * @param int $id
     * @return User
     */
    public function getUserById(int $id): User
    {
        return User::findOrFail($id);
    }

    /**
     * Create new user
     *
     * @param array $data
     * @return User
     */
    public function createUser(array $data): User
    {
        return User::create($data);
    }

    /**
     * Update user
     *
     * @param int $id
     * @param array $data
     * @return User
     */
    public function updateUser(int $id, array $data): User
    {
        $user = $this->getUserById($id);
        $user->update($data);
        return $user->fresh();
    }

    /**
     * Delete user
     *
     * @param int $id
     * @return bool
     */
    public function deleteUser(int $id): bool
    {
        return $this->getUserById($id)->delete();
    }
}