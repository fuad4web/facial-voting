<?php

namespace App\Services;

use App\Repositories\Contracts\UserRepositoryInterface;

class UserService
{
    protected $userRepository;

    /**
     * Create a new class instance.
     */
    public function __construct(UserRepositoryInterface $userRepository) {
        $this->userRepository = $userRepository;
    }

    // create users
    public function createUser($data) {
        $createUser = $this->userRepository?->create($data);
        return $createUser;
    }

    // update user
    public function updateUser($id, array $data)
    {
        $updateUser = $this->userRepository->updateUser($id, $data);
        return $updateUser ?: false;
    }

    // count user
    public function countUsers()
    {
        $countUsers = $this->userRepository->countUsers();
        return $countUsers ?: 0;
    }

    // get all facial users
    public function fetchFacialUsers() {
        $fetchUserFacialUsers = $this->userRepository?->fetchFacialUsers();
        return $fetchUserFacialUsers;
    }

    // get all candiate per category
    public function candidateCategory(int $categoryId) {
        $fetchUserFacialUsers = $this->userRepository?->candidateCategory($categoryId);
        return $fetchUserFacialUsers;
    }
}
