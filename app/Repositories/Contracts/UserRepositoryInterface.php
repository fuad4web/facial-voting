<?php
    namespace App\Repositories\Contracts;

    use App\Models\User;

    interface UserRepositoryInterface
    {
        public function create(array $data): User;
        public function getAllUsers();
        public function updateUser(int $id, array $data);
        public function fetchFacialUsers();
        public function countUsers();
        public function findById(int $id): ?User;
        public function candidateCategory(int $categoryId);
    }
