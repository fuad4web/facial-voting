<?php
namespace App\Repositories;

use App\Models\{Candidate, Category, };
use App\Repositories\Contracts\CategoryRepositoryInterface;

class CategoryRepository implements CategoryRepositoryInterface
{
    public function create(array $data): Category
    {
        return Category::create($data);
    }

    public function getAllCategories()
    {
        return Category::all();
    }

    public function countCategories()
    {
        return Category::count();
    }

    public function countCandidates()
    {
        return Candidate::count();
    }

    public function fetchCategoriesWithCount()
    {
        return Category::withCount('votes')->get();
    }

    public function fetchActiveCategories() {
        $activeCategories = Category::where('is_active', true)->orderBy('order')->get();
        return $activeCategories;
    }
}
