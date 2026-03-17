<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Vote;
use App\Models\Category;
use App\Models\Candidate;
use Illuminate\Http\Request;

class VoteController extends Controller
{
    public function index(Request $request)
    {
        $query = Vote::with(['user', 'candidate', 'category']);
        
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }
        
        $votes = $query->latest('voted_at')->paginate(20);
        
        $categories = Category::orderBy('order')->get();
        
        // Calculate results per category
        $results = [];
        foreach ($categories as $category) {
            $candidates = Candidate::where('category_id', $category->id)
                ->withCount('votes')
                ->get();
            $totalVotesInCategory = $candidates->sum('votes_count');
            
            $results[$category->id] = [
                'category' => $category,
                'candidates' => $candidates,
                'total' => $totalVotesInCategory,
            ];
        }
        
        return view('admin.votes.index', compact('votes', 'categories', 'results'));
    }
}
