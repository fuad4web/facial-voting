<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\{ UserService, VoteService, CategoryService, };

class AdminDashboardController extends Controller
{
    protected $categoryService, $voteService, $userService;

    public function __construct(CategoryService $categoryService, VoteService $voteService, UserService $userService)
    {
        $this->categoryService = $categoryService;
        $this->voteService = $voteService;
        $this->userService = $userService;
    }

    public function index()
    {
        $totalUsers = $this->userService?->countUsers();
        $totalVotes = $this->voteService?->countVoters();
        $totalCategories = $this->categoryService?->countCategories();
        $totalCandidates = $this->categoryService?->countCandidates();
        
        $recentVotes = $this->voteService?->recentVotes();
        
        $categories = $this->categoryService?->fetchCategoriesWithCount();
        
        return view('admin.dashboard', compact(
            'totalUsers', 'totalVotes', 'totalCategories', 'totalCandidates',
            'recentVotes', 'categories'
        ));
    }
}
