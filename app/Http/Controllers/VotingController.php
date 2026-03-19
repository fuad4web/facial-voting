<?php

namespace App\Http\Controllers;

use App\Events\VoteCast;
use App\Helpers\ActivityHelper;
use App\Models\Category;
use App\Models\Candidate;
use App\Models\Vote;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VotingController extends Controller
{
    // Show list of categories available for voting
    public function index()
    {
        $categories = Category::where('is_active', true)
            ->orderBy('order')
            ->get();
        
        // Get user's vote status for each category
        $user = Auth::user();
        $votes = Vote::where('user_id', $user?->id)
            ->pluck('category_id')
            ->toArray();

        return view('voting.index', compact('categories', 'votes'));
    }

    // Show candidates for a specific category with face verification
    public function show(Category $category)
    {
        if (!$category?->is_active) {
            return redirect()->route('voting.index')
                ->with('error', 'This category is not active.');
        }

        // Check if user already voted in this category
        if (Vote::where('user_id', Auth::id())->where('category_id', $category?->id)->exists()) {
            return redirect()->route('voting.index')
                ->with('error', 'You have already voted in this category.');
        }

        $candidates = Candidate::where('category_id', $category?->id)->get();

        return view('voting.show', compact('category', 'candidates'));
    }

    // Store vote after face verification
    public function store(Request $request, Category $category)
    {
        $request->validate([
            'candidate_id' => 'required|exists:candidates,id',
            'facial_descriptors' => 'required|string', // from face verification
            'fingerprint' => 'nullable|string|max:255', // for fimgerprint request and ist set to nullable
        ]);

        $user = Auth::user();

        // Check if user already voted in this category (fastest with exists query)
        if (Vote::where('user_id', $user?->id)->where('category_id', $category?->id)->exists()) {
            Log::warning('Duplicate vote attempt', [
                'user_id' => $user?->id,
                'category_id' => $category?->id,
                'ip' => $request->ip()
            ]);
            
            // log duplicate vote check
            ActivityHelper::logActivity('vote_attempt', 'duplicate', $user?->id, ['category_id' => $category?->id]);

            return response()->json([
                'success' => false,
                'message' => 'You have already voted in this category.'
            ], 403);
        }

        // Verify face matches the registered user
        $storedDescriptors = json_decode($user?->facial_descriptors, true);
        $loginDescriptors = json_decode($request->facial_descriptors, true);

        if (!$storedDescriptors || !$loginDescriptors) {
            Log::error('Face data missing', ['user_id' => $user?->id]);
            return response()->json([
                'success' => false,
                'message' => 'Face data missing. Please re-register.'
            ], 400);
        }

        $similarity = $this->calculateSimilarity($loginDescriptors, $storedDescriptors);

        $faceSimilarityThreshold = config('voting.face_threshold');
        if ($similarity < $faceSimilarityThreshold) {
            Log::warning('Face verification failed', [
                'user_id' => $user?->id,
                'similarity' => $similarity,
                'threshold' => $faceSimilarityThreshold
            ]);

            // log face verification failure
            ActivityHelper::logActivity('face_verify', 'failed', $user?->id, ['similarity' => $similarity]);

            return response()->json([
                'success' => false,
                'message' => 'Face verification failed. Please try again.'
            ], 401);
        }

        // Use transaction to ensure vote and user update are atomic
        try {
            DB::beginTransaction();

            $vote = Vote::create([
                'user_id' => $user?->id,
                'candidate_id' => $request->candidate_id,
                'category_id' => $category?->id,
                'voted_at' => now(),
                'fingerprint' => $request->fingerprint,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            // Update user's last vote timestamp (optional but useful)
            $user->update(['last_vote_at' => now()]);

            DB::commit();

            // Broadcast the event (if you have real-time)
            event(new VoteCast($vote));

            Log::info('Vote cast successfully', [
                'user_id' => $user?->id,
                'vote_id' => $vote?->id,
                'category' => $category?->id,
                'candidate' => $request->candidate_id
            ]);

            ActivityHelper::logActivity('vote', 'success', $user?->id, [
                'category_id' => $category?->id,
                'candidate_id' => $request->candidate_id
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Vote cast successfully!',
                'redirect' => route('voting.results', $category?->id)
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Vote casting failed', [
                'user_id' => $user?->id,
                'error' => $e->getMessage()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while casting your vote. Please try again.'
            ], 500);
        }
    }

    // Show results for a category or overall
    public function results(Category $category)
    {
        $candidates = $category?->candidates()
            ->withCount('votes')
            ->orderBy('votes_count', 'desc')
            ->get();

        return view('voting.results', compact('category', 'candidates'));
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

    // function for al time update of voting results in JSON format
    public function realTimeVoteUpdate(Category $category)
    {
        $candidates = $category?->candidates()
            ->withCount('votes')
            ->orderBy('votes_count', 'desc')
            ->get();

        return response()->json([
            'candidates' => $candidates,
            'total_votes' => $candidates->sum('votes_count')
        ]);
    }
}
