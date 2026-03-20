<?php
namespace App\Repositories;

use App\Models\{Category, Vote, };
use App\Repositories\Contracts\VoteRepositoryInterface;

class VoteRepository implements VoteRepositoryInterface
{
    public function create(array $data): Vote
    {
        return Vote::create($data);
    }

    public function voteCategoryId(int $userId)
    {
        return Vote::where('user_id', $userId)->pluck('category_id')->toArray();
    }

    public function checkUserVoteCategory(int $userId, int $categoryId) {
        return Vote::where('user_id', $userId)->where('category_id', $categoryId)->exists();
    }

    public function candidateVoteResult(Category $category) {
        return $category?->candidates()
            ->withCount('votes')
            ->orderBy('votes_count', 'desc')
            ->get();
    }

    public function countVoters() {
        return Vote::count();
    }

    public function recentVotes() {
        return Vote::with(['user', 'candidate', 'category'])
            ->latest('voted_at')
            ->take(10)
            ->get();
    }

    // public function fetchActiveCategories() {
    //     $activeCategories = Vote::where('is_active', true)->orderBy('order')->get();
    //     return $activeCategories;
    // }
}
