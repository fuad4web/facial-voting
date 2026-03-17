<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Category;
use App\Models\Candidate;
use App\Models\Vote;

class AdminDashboardController extends Controller
{
    public function index()
    {
        $totalUsers = User::count();
        $totalVotes = Vote::count();
        $totalCategories = Category::count();
        $totalCandidates = Candidate::count();
        
        $recentVotes = Vote::with(['user', 'candidate', 'category'])
            ->latest('voted_at')
            ->take(10)
            ->get();
        
        $categories = Category::withCount('votes')->get();
        
        return view('admin.dashboard', compact(
            'totalUsers', 'totalVotes', 'totalCategories', 'totalCandidates',
            'recentVotes', 'categories'
        ));
    }
}
