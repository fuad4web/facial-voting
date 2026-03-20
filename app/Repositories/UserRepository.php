<?php
namespace App\Repositories;

use App\Models\{Candidate, User, };
use App\Repositories\Contracts\UserRepositoryInterface;

class UserRepository implements UserRepositoryInterface
{
    public function create(array $data): User
    {
        return User::create($data);
    }

    public function getAllUsers()
    {
        return User::all();
    }

    public function findById(int $id): ?User
    {
        return User::find($id);
    }

    public function countUsers()
    {
        return User::count();
    }

    public function updateUser(int $id, array $data)
    {
        $user = User::find($id);
        if ($user) {
            $user->update($data);
            return $user;
        }

        return false;
    }

    public function fetchFacialUsers() {
        $fetchFacialUsers = User::whereNotNull('facial_descriptors')->get();
        return $fetchFacialUsers;
    }

    public function candidateCategory(int $categoryId) {
        return Candidate::where('category_id', $categoryId)->get();
    }
}
