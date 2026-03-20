<?php

namespace App\Services;

use App\Repositories\Contracts\CategoryRepositoryInterface;

class CategoryService
{
    protected $categoryRepository;

    /**
     * Create a new class instance.
     */
    public function __construct(CategoryRepositoryInterface $categoryRepository)
    {
        $this->categoryRepository = $categoryRepository;
    }

    public function fetchActiveCategories() {
        $fetchActiveCategories = $this->categoryRepository?->fetchActiveCategories();
        return $fetchActiveCategories;
    }

    public function fetchCategoriesWithCount() {
        $fetchCategoriesWithCount = $this->categoryRepository?->fetchCategoriesWithCount();
        return $fetchCategoriesWithCount;
    }
    
    // count categoryies
    public function countCategories()
    {
        $countCategories = $this->categoryRepository->countCategories();
        return $countCategories ?: 0;
    }
    
    // count candidates
    public function countCandidates()
    {
        $countCandidates = $this->categoryRepository->countCandidates();
        return $countCandidates ?: 0;
    }
}
