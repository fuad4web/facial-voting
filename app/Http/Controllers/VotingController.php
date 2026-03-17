<?php

namespace App\Http\Controllers;

use App\Events\VoteCast;
use App\Models\Category;
use App\Models\Candidate;
use App\Models\Vote;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VotingController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    // Show list of categories available for voting
    public function index()
    {
        $categories = Category::where('is_active', true)
            ->orderBy('order')
            ->get();
        
        // Get user's vote status for each category
        $user = Auth::user();
        $votes = Vote::where('user_id', $user->id)
            ->pluck('category_id')
            ->toArray();

        return view('voting.index', compact('categories', 'votes'));
    }

    // Show candidates for a specific category with face verification
    public function show(Category $category)
    {
        if (!$category->is_active) {
            return redirect()->route('voting.index')
                ->with('error', 'This category is not active.');
        }

        // Check if user already voted in this category
        if (Vote::where('user_id', Auth::id())->where('category_id', $category->id)->exists()) {
            return redirect()->route('voting.index')
                ->with('error', 'You have already voted in this category.');
        }

        $candidates = Candidate::where('category_id', $category->id)->get();

        return view('voting.show', compact('category', 'candidates'));
    }

    // Store vote after face verification
    public function store(Request $request, Category $category)
    {
        $request->validate([
            'candidate_id' => 'required|exists:candidates,id',
            'facial_descriptors' => 'required|string', // from face verification
        ]);

        $user = Auth::user();

        // Double-check if user already voted
        if (Vote::where('user_id', $user->id)->where('category_id', $category->id)->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'You have already voted in this category.'
            ], 403);
        }

        // Verify face matches the registered user
        $storedDescriptors = json_decode($user->facial_descriptors, true);
        $loginDescriptors = json_decode($request->facial_descriptors, true);

        if (!$storedDescriptors || !$loginDescriptors) {
            return response()->json([
                'success' => false,
                'message' => 'Face data missing. Please re-register.'
            ], 400);
        }

        $similarity = $this->calculateSimilarity($loginDescriptors, $storedDescriptors);
        $threshold = 0.6; // same as login

        if ($similarity < $threshold) {
            return response()->json([
                'success' => false,
                'message' => 'Face verification failed. Please try again.'
            ], 401);
        }

        // Create vote
        $vote = Vote::create([
            'user_id' => $user->id,
            'candidate_id' => $request->candidate_id,
            'category_id' => $category->id,
            'voted_at' => now(),
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        // Update user's has_voted? maybe not necessary because we check via votes table
        // But we can update last_vote_at if you want
        $user->update(['last_vote_at' => now()]);
        
        // Broadcast the event
        event(new VoteCast($vote));

        return response()->json([
            'success' => true,
            'message' => 'Vote cast successfully!',
            'redirect' => route('voting.results', $category?->id) // or results page
        ]);
    }

    // Show results for a category or overall
    public function results(Category $category)
    {
        $candidates = $category->candidates()
        ->withCount('votes')
        ->orderBy('votes_count', 'desc')
        ->get();
        
        $totalVotes = $candidates->sum('votes_count');

        return view('voting.results', compact('category', 'candidates', 'totalVotes'));
    }

    private function calculateSimilarity($descriptors1, $descriptors2)
    {
        // same as in FacialLoginController
        if (count($descriptors1) !== count($descriptors2)) {
            return 0;
        }

        $dotProduct = 0;
        $norm1 = 0;
        $norm2 = 0;

        for ($i = 0; $i < count($descriptors1); $i++) {
            $dotProduct += $descriptors1[$i] * $descriptors2[$i];
            $norm1 += pow($descriptors1[$i], 2);
            $norm2 += pow($descriptors2[$i], 2);
        }

        $norm1 = sqrt($norm1);
        $norm2 = sqrt($norm2);

        if ($norm1 == 0 || $norm2 == 0) {
            return 0;
        }

        return $dotProduct / ($norm1 * $norm2);
    }
}
