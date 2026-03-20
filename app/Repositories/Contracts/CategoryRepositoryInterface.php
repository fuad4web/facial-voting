<?php
    namespace App\Repositories\Contracts;

    use App\Models\Category;

    interface CategoryRepositoryInterface
    {
        public function create(array $data): Category;
        public function getAllCategories();
        public function fetchActiveCategories();
        public function countCategories();
        public function countCandidates();
        public function fetchCategoriesWithCount();
    }
